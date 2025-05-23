<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // Pastikan Carbon diimport jika digunakan untuk manipulasi tanggal/waktu
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class AttendanceController extends Controller
{
    public function checkinForm()
    {
        return view('checkin');
    }

    public function storeCheckin(Request $request)
    {
        $userId = Auth::id();
        $today = $request->local_date; // Menggunakan local_date dari request

        // Cari data kehadiran untuk hari ini yang mungkin sudah ada
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            // Jika sudah ada record untuk hari ini, update check_in jika masih kosong
            if (empty($attendance->check_in)) {
                $attendance->update([
                    'check_in' => $request->local_time,
                    // Karena kita akan menghitung status secara dinamis di model,
                    // kita tidak perlu mengatur 'status' di sini kecuali ada logika status khusus lainnya.
                ]);
            } else {
                // Jika sudah check-in, Anda bisa menambahkan notifikasi atau melewatkannya
                return redirect()->route('dashboard')->with('info', 'Anda sudah Check-in hari ini.');
            }
        } else {
            // Jika belum ada record untuk hari ini, buat record baru
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'check_in' => $request->local_time,
                // Status awal bisa diset null atau default lainnya jika diperlukan
                // atau biarkan kosong agar accessor di model yang menentukan
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
        $today = now()->toDateString(); // Menggunakan tanggal hari ini

        // Cari data kehadiran untuk hari ini
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first(); // Gunakan first() karena hanya perlu satu entri per hari

        if ($attendance) {
            // Jika record ditemukan, update check_out dan aktivitas
            // Pastikan check_out belum terisi untuk menghindari penimpaan
            if (empty($attendance->check_out)) {
                $attendance->update([
                    'check_out' => $request->current_time,
                    'activity_title' => $request->activity_title,
                    'activity_description' => $request->activity_description,
                    // Status akan dihitung otomatis oleh accessor di model
                ]);
                return redirect()->route('dashboard')->with('success', 'Check-out berhasil!');
            } else {
                return redirect()->route('dashboard')->with('info', 'Anda sudah Check-out hari ini.');
            }
        } else {
            // Ini adalah kasus di mana user hanya melakukan check-out tanpa check-in di hari yang sama.
            // Buat record baru dengan check_out saja.
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'check_out' => $request->current_time,
                'activity_title' => $request->activity_title,
                'activity_description' => $request->activity_description,
                // Check-in dibiarkan null, yang akan membuat status 'Belum Check-In'
            ]);
            return redirect()->route('dashboard')->with('warning', 'Check-out berhasil. Anda belum melakukan Check-in hari ini.');
        }
    }

 

    ## My Attendance List

    public function myAttendance(Request $request)
    {
        $query = Attendance::where('user_id', auth()->id());

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Urutkan berdasarkan tanggal
        $sort = $request->get('sort', 'desc');
        $query->orderBy('date', $sort);

        // Ambil data kehadiran
        $attendances = $query->paginate(10); // Gunakan paginate jika Anda menampilkan banyak data

        // Tidak perlu memodifikasi data attendances di controller ini.
        // Logic status akan otomatis diakses dari model menggunakan accessor.

        return view('attendances.myattendance', compact('attendances'));
    }

 

    ## Export Attendance

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $userId = auth()->id();

        $query = Attendance::where('user_id', $userId);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $attendances = $query->orderBy('date', $request->get('sort', 'desc'))->get();

        // Pastikan Anda sudah membuat class export yang sesuai,
        // yang di dalamnya juga menggunakan accessor untuk status.
        if ($format === 'csv' || $format === 'xlsx') {
            // Contoh penggunaan: class App\Exports\AttendancesExport
            // Pastikan Anda mengimplementasikan logika status di dalam class export ini
            // agar data yang diekspor juga mengandung status yang benar.
            $export = new \App\Exports\AttendancesExport($attendances);
            $fileName = 'attendance_' . now()->format('Ymd_His') . '.' . $format;
            return Excel::download($export, $fileName);
        } elseif ($format === 'pdf') {
            // Pastikan view 'attendance.export_pdf' menggunakan accessor status yang sama.
            $pdf = PDF::loadView('attendance.export_pdf', compact('attendances'));
            return $pdf->download('attendance_' . now()->format('Ymd_His') . '.pdf');
        }

        return redirect()->back()->with('error', 'Format export tidak didukung.');
    }
}