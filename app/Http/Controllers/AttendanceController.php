<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function checkinForm()
    {
        return view('checkin');
    }

    public function storeCheckin(Request $request)
{
    $user = auth()->user();
    $localDate = $request->input('local_date'); // Format: YYYY-MM-DD
    $localTime = $request->input('local_time'); // Format: HH:mm:ss

    if (!$localDate || !$localTime) {
        return back()->with('error', 'Data waktu tidak tersedia.');
    }

    // Cek apakah sudah check-in hari ini
    $existing = Attendance::where('user_id', $user->id)->where('date', $localDate)->first();

    if ($existing) {
        return redirect()->route('checkin.form')->with('info', 'Sudah check-in hari ini.');
    }

    Attendance::create([
        'user_id' => $user->id,
        'date' => $localDate,
        'check_in' => $localTime,
    ]);

    return redirect()->route('dashboard')->with('success', 'Check-in berhasil!');
}

    

    public function checkoutForm()
    {
        return view('checkout');
    }

    public function storeCheckout(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();
    
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();
    
        if (!$attendance) {
            return redirect()->route('checkin.form')->with('error', 'Belum check-in hari ini.');
        }
    
        // Ambil jam dari input hidden browser user (pastikan valid)
        $checkoutTime = $request->input('current_time'); // format: HH:mm:ss
    
        // Validasi format (opsional tapi direkomendasikan)
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $checkoutTime)) {
            return back()->with('error', 'Format waktu tidak valid.');
        }
    
        $attendance->update([
            'check_out' => $checkoutTime,
            'activity_title' => $request->input('activity_title'),
            'activity_description' => $request->input('activity_description'),
        ]);
    
        return redirect()->route('dashboard')->with('success', 'Check-out berhasil!');
    }
    
}