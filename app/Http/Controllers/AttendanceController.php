<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function checkinForm()
    {
        return view('checkin');
    }

    public function storeCheckin(Request $request)
{
    $userId = Auth::id();
    $today = $request->local_date;

    // Cek apakah sudah ada check-in hari ini
    $existingCheckin = Attendance::where('user_id', $userId)
        ->whereDate('date', $today)
        ->whereNotNull('check_in')
        ->first();

    // Jika belum ada, simpan check-in baru
    if (!$existingCheckin) {
        Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => $request->local_time,
        ]);
    }

    return redirect()->route('dashboard')->with('success', 'Check-in berhasil!');
}

    public function checkoutForm()
    {
        return view('checkout');
    }

    public function storeCheckout(Request $request)
{
    $userId = Auth::id();
    $today = now()->toDateString(); // Atau pakai $request->tanggal kalau dikirim

    // Cari data attendance hari ini
    $attendance = Attendance::where('user_id', $userId)
        ->whereDate('date', $today)
        ->orderByDesc('id') // untuk memastikan ambil data terakhir
        ->first();

    if ($attendance) {
        // Update check_out dan simpan aktivitas
        $attendance->update([
            'check_out' => $request->current_time,
            'activity_title' => $request->activity_title,
            'activity_description' => $request->activity_description,
        ]);
    } else {
        // Jika tidak ada data check-in sebelumnya, buat data baru (opsional)
        Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_out' => $request->current_time,
            'activity_title' => $request->activity_title,
            'activity_description' => $request->activity_description,
        ]);
    }

    return redirect()->route('dashboard')->with('success', 'Check-out berhasil!');
}

}