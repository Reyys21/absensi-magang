<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
// use PDF; // Uncomment this line if you are using a PDF library like Barryvdh/Laravel-DomPDF

class AttendanceController extends Controller
{
    /**
     * Menampilkan form check-in.
     *
     * @return \Illuminate\View\View
     */
    public function checkinForm()
    {
        return view('checkin');
    }

    /**
     * Menyimpan data check-in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCheckin(Request $request)
    {
        $userId = Auth::id();
        $today = $request->local_date; // Menggunakan local_date dari request

        // --- DEBUGGING SEMENTARA ---
        // Anda bisa mengaktifkan ini untuk melihat data yang masuk
        // dd([
        //     'message' => 'Inside storeCheckin method',
        //     'user_id' => $userId,
        //     'request_all' => $request->all(),
        //     'local_date' => $request->local_date,
        //     'local_time' => $request->local_time,
        //     'carbon_parsed_time' => Carbon::parse($request->local_time),
        // ]);
        // ---------------------------

        // Cari data kehadiran untuk hari ini yang mungkin sudah ada
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        // Waktu check-in baru dari request, dikonversi ke objek Carbon untuk perbandingan
        $newCheckInTime = Carbon::parse($request->local_time);

        if ($attendance) {
            // Jika sudah ada record untuk hari ini
            if (empty($attendance->check_in)) {
                // Jika check_in masih kosong, catat waktu check-in ini
                $attendance->update([
                    'check_in' => $newCheckInTime->format('H:i:s'), // Simpan sebagai string H:i:s
                ]);
                return redirect()->route('dashboard')->with('success', 'Check-in berhasil!');
            } else {
                // Jika check_in sudah ada, bandingkan dengan waktu check-in yang sudah ada
                // $attendance->check_in sudah menjadi objek Carbon karena $casts di model (asumsi)
                $existingCheckInTime = $attendance->check_in;

                // Jika waktu check-in yang baru LEBIH AWAL dari yang sudah ada, update
                if ($newCheckInTime->lt($existingCheckInTime)) { // 'lt' stands for 'less than'
                    $attendance->update([
                        'check_in' => $newCheckInTime->format('H:i:s'),
                    ]);
                    return redirect()->route('dashboard')->with('success', 'Check-in Anda telah diperbarui ke waktu yang lebih awal: ' . $newCheckInTime->format('H:i:s'));
                } else {
                    return redirect()->route('dashboard')->with('info', 'Anda sudah Check-in. Waktu check-in awal Anda tetap tercatat.');
                }
            }
        } else {
            // Jika belum ada record untuk hari ini, buat record baru
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'check_in' => $newCheckInTime->format('H:i:s'),
            ]);
            return redirect()->route('dashboard')->with('success', 'Check-in berhasil!');
        }
    }

    /**
     * Menampilkan form check-out.
     *
     * @return \Illuminate\View\View
     */
    public function checkoutForm()
    {
        return view('checkout');
    }

    /**
     * Menyimpan data check-out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCheckout(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::now()->toDateString(); // Menggunakan Carbon untuk tanggal hari ini

        // Cari data kehadiran untuk hari ini
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first(); // Gunakan first() karena hanya perlu satu entri per hari

        // Waktu check-out baru dari request, dikonversi ke objek Carbon untuk perbandingan
        $newCheckOutTime = Carbon::parse($request->current_time);

        if ($attendance) {
            // Jika record ditemukan
            if (empty($attendance->check_out)) {
                // Jika check_out masih kosong, catat waktu check-out ini
                $attendance->update([
                    'check_out' => $newCheckOutTime->format('H:i:s'),
                    'activity_title' => $request->activity_title,
                    'activity_description' => $request->activity_description,
                ]);
                return redirect()->route('dashboard')->with('success', 'Check-out berhasil!');
            } else {
                // Jika check_out sudah ada, bandingkan dengan waktu check-out yang sudah ada
                // $attendance->check_out sudah menjadi objek Carbon karena $casts di model
                $existingCheckOutTime = $attendance->check_out;

                // Jika waktu check-out yang baru LEBIH AKHIR dari yang sudah ada, update
                if ($newCheckOutTime->gt($existingCheckOutTime)) { // 'gt' stands for 'greater than'
                    $attendance->update([
                        'check_out' => $newCheckOutTime->format('H:i:s'),
                        'activity_title' => $request->activity_title, // Update aktivitas juga jika check-out diperbarui
                        'activity_description' => $request->activity_description,
                    ]);
                    return redirect()->route('dashboard')->with('success', 'Check-out Anda telah diperbarui ke waktu yang lebih akhir: ' . $newCheckOutTime->format('H:i:s'));
                } else {
                    return redirect()->route('dashboard')->with('info', 'Anda sudah Check-out. Waktu check-out terakhir Anda tetap tercatat.');
                }
            }
        } else {
            // Ini adalah kasus di mana user hanya melakukan check-out tanpa check-in di hari yang sama.
            // Buat record baru dengan check_out saja.
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'check_out' => $newCheckOutTime->format('H:i:s'),
                'activity_title' => $request->activity_title,
                'activity_description' => $request->activity_description,
            ]);
            return redirect()->route('dashboard')->with('warning', 'Check-out berhasil. Anda belum melakukan Check-in hari ini.');
        }
    }

    /**
     * Menampilkan daftar absensi pengguna saat ini.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
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

        return view('attendances.myattendance', compact('attendances'));
    }

    /**
     * Menampilkan halaman history absensi dengan data kalender.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function history(Request $request)
    {
        $userId = Auth::id();

        // Ambil bulan dan tahun dari request, default ke bulan dan tahun saat ini
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        // Buat objek Carbon untuk bulan dan tahun yang dipilih
        $date = Carbon::createFromDate($year, $month, 1);

        // Dapatkan semua data absensi untuk user_id dan bulan/tahun yang dipilih
        // Ini akan digunakan untuk menandai tanggal di kalender
        $monthlyAttendances = Attendance::where('user_id', $userId)
                                     ->whereYear('date', $year)
                                     ->whereMonth('date', $month)
                                     ->get()
                                     ->keyBy(function($item) {
                                            // Karena 'date' sekarang otomatis di-cast ke Carbon di model
                                            return $item->date->format('Y-m-d');
                                        });

        // Ambil data absensi untuk daftar vertikal (jika ada tanggal terpilih)
        // Jika tidak ada tanggal terpilih, tampilkan absensi untuk hari ini atau 7 hari terakhir
        $selectedDate = $request->input('selected_date');
        $dailyAttendances = collect();

        if ($selectedDate) {
            $dailyAttendances = Attendance::where('user_id', $userId)
                                         ->whereDate('date', $selectedDate)
                                         ->get();
        } else {
            // Default: Tampilkan absensi untuk 7 hari terakhir jika tidak ada tanggal yang dipilih
            $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay(); // 7 hari termasuk hari ini
            $dailyAttendances = Attendance::where('user_id', $userId)
                                         ->whereBetween('date', [$sevenDaysAgo, Carbon::now()->endOfDay()])
                                         ->orderBy('date', 'desc')
                                         ->get();
        }

        return view('attendances.history', compact('date', 'monthlyAttendances', 'dailyAttendances'));
    }

    /**
     * Export data absensi.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
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
            $fileName = 'attendance_' . Carbon::now()->format('Ymd_His') . '.' . $format;
            return Excel::download($export, $fileName);
        } elseif ($format === 'pdf') {
            // Pastikan view 'attendance.export_pdf' menggunakan accessor status yang sama.
            // Anda perlu meng-uncomment 'use PDF;' di atas jika menggunakan pustaka PDF.
            // $pdf = PDF::loadView('attendance.export_pdf', compact('attendances'));
            // return $pdf->download('attendance_' . Carbon::now()->format('Ymd_His') . '.pdf');
            return redirect()->back()->with('error', 'Fitur export PDF belum diaktifkan.'); // Atau tangani error
        }

        return redirect()->back()->with('error', 'Format export tidak didukung.');
    }

    // Ini adalah placeholder untuk metode create dan store,
    // asumsikan ini untuk manajemen absensi oleh admin atau untuk entri manual.
    public function create()
    {
        // Logika untuk menampilkan form pembuatan absensi
        return view('attendances.create');
    }

    public function store(Request $request)
    {
        // Logika untuk menyimpan absensi manual
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i:s',
            'check_out' => 'nullable|date_format:H:i:s|after:check_in',
            'activity_title' => 'nullable|string|max:255',
            'activity_description' => 'nullable|string',
        ]);

        Attendance::create($validated);

        return redirect()->route('dashboard')->with('success', 'Absensi berhasil ditambahkan.');
    }
}