<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use App\Models\User;
use App\Models\CorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk logging

class AttendanceController extends Controller
{
    /**
     * Menampilkan form check-in.
     *
     * @return \Illuminate\View\View
     */
    public function checkinForm()
    {
        // Path baru: resources/views/fold_dashboard/checkin.blade.php
        return view('fold_dashboard.checkin');
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
        $today = $request->local_date; // Menggunakan local_date dari request (format YYYY-MM-DD)

        // Waktu check-in baru dari request, dikonversi ke objek Carbon
        // Menggabungkan tanggal dari request dengan waktu dari request
        $newCheckInDateTime = Carbon::parse($today . ' ' . $request->local_time);

        // Cari data kehadiran untuk hari ini yang mungkin sudah ada
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            // Jika sudah ada record untuk hari ini
            if (empty($attendance->check_in)) {
                // Jika check_in masih kosong, catat waktu check-in ini
                $attendance->update([
                    'check_in' => $newCheckInDateTime, // Simpan sebagai objek Carbon, akan di-cast oleh model
                ]);
                return redirect()->route('dashboard')->with('success', 'Check-in berhasil!');
            } else {
                // Jika check_in sudah ada, bandingkan dengan waktu check-in yang sudah ada
                // $attendance->check_in adalah objek Carbon karena $casts di model
                $existingCheckInTime = $attendance->check_in;

                // Jika waktu check-in yang baru LEBIH AWAL dari yang sudah ada, update
                if ($newCheckInDateTime->lt($existingCheckInTime)) {
                    $attendance->update([
                        'check_in' => $newCheckInDateTime,
                    ]);
                    return redirect()->route('dashboard')->with('success', 'Check-in Anda telah diperbarui ke waktu yang lebih awal: ' . $newCheckInDateTime->format('H:i'));
                } else {
                    return redirect()->route('dashboard')->with('info', 'Anda sudah Check-in. Waktu check-in awal Anda tetap tercatat.');
                }
            }
        } else {
            // Jika belum ada record untuk hari ini, buat record baru
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'check_in' => $newCheckInDateTime,
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
        // Path baru: resources/views/fold_dashboard/checkout.blade.php
        return view('fold_dashboard.checkout');
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

        // Waktu check-out baru dari request, dikonversi ke objek Carbon
        $newCheckOutDateTime = Carbon::parse($today . ' ' . $request->current_time);

        // Cari data kehadiran untuk hari ini
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            // Jika record ditemukan
            if (empty($attendance->check_out)) {
                // Jika check_out masih kosong, catat waktu check-out ini
                $attendance->update([
                    'check_out' => $newCheckOutDateTime,
                    'activity_title' => $request->activity_title,
                    'activity_description' => $request->activity_description,
                ]);
                return redirect()->route('dashboard')->with('success', 'Check-out berhasil!');
            } else {
                // Jika check_out sudah ada, bandingkan dengan waktu check-out yang sudah ada
                $existingCheckOutTime = $attendance->check_out;

                // Jika waktu check-out yang baru LEBIH AKHIR dari yang sudah ada, update
                if ($newCheckOutDateTime->gt($existingCheckOutTime)) {
                    $attendance->update([
                        'check_out' => $newCheckOutDateTime,
                        'activity_title' => $request->activity_title,
                        'activity_description' => $request->activity_description,
                    ]);
                    return redirect()->route('dashboard')->with('success', 'Check-out Anda telah diperbarui ke waktu yang lebih akhir: ' . $newCheckOutDateTime->format('H:i'));
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
                'check_out' => $newCheckOutDateTime,
                'activity_title' => $request->activity_title,
                'activity_description' => $request->activity_description,
            ]);
            return redirect()->route('dashboard')->with('warning', 'Check-out berhasil. Anda belum melakukan Check-in hari ini.');
        }
    }

    /**
     * Menampilkan dashboard dengan data absensi hari ini dan total kehadiran.
     * Asumsi ini adalah method untuk route 'dashboard'.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $userId = Auth::id();
        $today = Carbon::now()->toDateString();

        $attendanceToday = Attendance::where('user_id', $userId)
                                     ->whereDate('date', $today)
                                     ->first();

        $firstCheckIn = $attendanceToday ? $attendanceToday->check_in : null;
        $lastCheckOut = $attendanceToday ? $attendanceToday->check_out : null;

        $user = Auth::user();
        $registrationDate = $user->created_at->startOfDay(); // Dari created_at di model User

        $attendanceCount = 0;
        if ($registrationDate) {
            $currentDate = Carbon::now()->endOfDay();

            $allDatesInRange = collect();
            for ($date = $registrationDate->copy(); $date->lte($currentDate); $date->addDay()) {
                $allDatesInRange->push($date->toDateString());
            }

            $userAttendances = Attendance::where('user_id', $userId)
                                         ->whereBetween('date', [$registrationDate, $currentDate])
                                         ->get()
                                         ->keyBy(function($item) {
                                             return $item->date->toDateString();
                                         });

            foreach ($allDatesInRange as $dateString) {
                $attendanceRecord = $userAttendances->get($dateString);
                if ($attendanceRecord && $attendanceRecord->attendance_status === 'Complete') {
                    $attendanceCount++;
                }
            }
        }

        // Path baru: resources/views/fold_dashboard/dashboard.blade.php
        return view('fold_dashboard.dashboard', compact('firstCheckIn', 'lastCheckOut', 'attendanceCount'));
    }

    /**
     * Menampilkan daftar absensi pengguna saat ini (biasanya tabel).
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

        // Path baru: resources/views/fold_my_attendance/myattendance.blade.php
        return view('fold_my_attendance.myattendance', compact('attendances'));
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
        $user = Auth::user();
        $registrationDate = $user->created_at->startOfDay();

        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $date = Carbon::createFromDate($year, $month, 1);
        $currentDay = Carbon::now()->startOfDay();

        $existingAttendances = Attendance::where('user_id', $userId)
                                         ->whereYear('date', $year)
                                         ->whereMonth('date', $month)
                                         ->get()
                                         ->keyBy(function($item) {
                                             return $item->date->toDateString();
                                         });

        $monthlyAttendances = collect();

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
            $currentLoopDateString = $d->toDateString();

            $status = '';
            if ($d->lt($registrationDate)) {
                $status = 'N/A';
            } elseif ($d->gt($currentDay)) {
                $status = 'Future';
            } else {
                $attendance = $existingAttendances->get($currentLoopDateString);
                if ($attendance) {
                    $status = $attendance->attendance_status;
                } else {
                    $status = 'Absent (Belum Lengkap)';
                }
            }
            $monthlyAttendances->put($currentLoopDateString, (object)['attendance_status' => $status]);
        }

        $selectedDate = $request->input('selected_date');
        $dailyAttendances = collect();

        if ($selectedDate) {
            $dailyAttendances = Attendance::where('user_id', $userId)
                                          ->whereDate('date', $selectedDate)
                                          ->get();
            if ($dailyAttendances->isEmpty() && Carbon::parse($selectedDate)->lte(Carbon::now()) && Carbon::parse($selectedDate)->gte($registrationDate)) {
                $dummyAttendance = (object)[
                    'date' => Carbon::parse($selectedDate),
                    'check_in' => null,
                    'check_out' => null,
                    'activity_title' => null,
                    'activity_description' => null,
                    'user_id' => $userId,
                    'is_dummy' => true,
                ];
                $dummyAttendance->attendance_status = 'Absent (Belum Lengkap)';
                $dummyAttendance->day_name = Carbon::parse($selectedDate)->translatedFormat('l');
                $dummyAttendance->formatted_date = Carbon::parse($selectedDate)->translatedFormat('d F Y');
                $dailyAttendances->push($dummyAttendance);
            }
        } else {
            $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();
            $rawDailyAttendances = Attendance::where('user_id', $userId)
                                             ->whereBetween('date', [$sevenDaysAgo, Carbon::now()->endOfDay()])
                                             ->orderBy('date', 'desc')
                                             ->get();

            $currentDailyAttendancesMap = [];
            foreach ($rawDailyAttendances as $att) {
                $currentDailyAttendancesMap[$att->date->toDateString()] = $att;
            }

            $processedDailyAttendances = collect();
            for ($d = Carbon::now()->startOfDay(); $d->gte($sevenDaysAgo); $d->subDay()) {
                $dateString = $d->toDateString();
                $attendanceRecord = $currentDailyAttendancesMap[$dateString] ?? null;

                if ($d->lt($registrationDate)) {
                    continue;
                }

                if ($attendanceRecord) {
                    $processedDailyAttendances->push($attendanceRecord);
                } else {
                    $dummyAttendance = (object)[
                        'date' => $d,
                        'check_in' => null,
                        'check_out' => null,
                        'activity_title' => null,
                        'activity_description' => null,
                        'user_id' => $userId,
                        'is_dummy' => true,
                    ];
                    $dummyAttendance->attendance_status = 'Absent (Belum Lengkap)';
                    $dummyAttendance->day_name = $d->translatedFormat('l');
                    $dummyAttendance->formatted_date = $d->translatedFormat('d F Y');
                    $processedDailyAttendances->push($dummyAttendance);
                }
            }
            $dailyAttendances = $processedDailyAttendances;
        }

        // Path baru: resources/views/fold_history/history.blade.php
        return view('fold_history.history', compact('date', 'monthlyAttendances', 'dailyAttendances', 'selectedDate'));
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

        if ($format === 'csv' || $format === 'xlsx') {
            // Asumsi Anda memiliki App\Exports\AttendancesExport
            $export = new \App\Exports\AttendancesExport($attendances);
            $fileName = 'attendance_' . Carbon::now()->format('Ymd_His') . '.' . $format;
            return Excel::download($export, $fileName);
        } elseif ($format === 'pdf') {
            return redirect()->back()->with('error', 'Fitur export PDF belum diaktifkan.');
        }

        return redirect()->back()->with('error', 'Format export tidak didukung.');
    }

    /**
     * Menampilkan form pop-up untuk koreksi absensi.
     * Dipanggil via AJAX atau redirect dengan flash data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function showCorrectionForm(Request $request)
    {
        $userId = Auth::id();
        $dateToCorrect = Carbon::now()->tz('Asia/Makassar'); // Default to current date in WITA timezone

        // Try to parse the date from the request
        if ($request->filled('date')) {
            try {
                // Ensure the date is parsed correctly, assuming it comes as YYYY-MM-DD
                $dateToCorrect = Carbon::parse($request->input('date'))->tz('Asia/Makassar');
            } catch (\Exception $e) {
                Log::error('Invalid date provided for correction form: ' . $request->input('date') . ' - ' . $e->getMessage());
                // Stick with Carbon::now() if invalid date
            }
        }

        // Dapatkan data absensi untuk tanggal tersebut
        $attendance = Attendance::where('user_id', $userId)
                                ->whereDate('date', $dateToCorrect->toDateString()) // Use toDateString() for comparison
                                ->first();

        // Siapkan data lama untuk form
        // Data ini akan digunakan sebagai nilai default untuk input "new"
        // dan ditampilkan sebagai "old" untuk referensi
        $oldCheckIn = $attendance ? ($attendance->check_in ? $attendance->check_in->tz('Asia/Makassar')->format('H:i') : '') : ''; // Kosongkan jika null untuk input time
        $oldCheckOut = $attendance ? ($attendance->check_out ? $attendance->check_out->tz('Asia/Makassar')->format('H:i') : '') : ''; // Kosongkan jika null
        $oldActivityTitle = $attendance ? $attendance->activity_title : ''; // Kosongkan jika null
        $oldActivityDescription = $attendance ? $attendance->activity_description : ''; // Kosongkan jika null

        // Untuk tampilan di "lama":
        $displayOldCheckIn = $attendance ? ($attendance->check_in ? $attendance->check_in->tz('Asia/Makassar')->format('H:i') : '--.--') : '--.--';
        $displayOldCheckOut = $attendance ? ($attendance->check_out ? $attendance->check_out->tz('Asia/Makassar')->format('H:i') : '--.--') : '--.--';
        $displayOldActivityTitle = $attendance ? ($attendance->activity_title ?: '--') : '--';
        $displayOldActivityDescription = $attendance ? ($attendance->activity_description ?: '--') : '--';

        // Path baru: resources/views/fold_history/correction_form.blade.php (sesuai folder history)
        return view('fold_history.correction_form', compact(
            'dateToCorrect', // Ini yang akan menjadi nilai default di input tanggal
            'oldCheckIn', // Ini akan menjadi nilai default di input new_check_in
            'oldCheckOut', // Ini akan menjadi nilai default di input new_check_out
            'oldActivityTitle', // Ini akan menjadi nilai default di input new_activity_title
            'oldActivityDescription', // Ini akan menjadi nilai default di input new_activity_description
            'displayOldCheckIn', // Untuk tampilan 'lama'
            'displayOldCheckOut', // Untuk tampilan 'lama'
            'displayOldActivityTitle', // Untuk tampilan 'lama'
            'displayOldActivityDescription', // Untuk tampilan 'lama'
            'attendance' // Kirim objek attendance jika ada
        ));
    }

    /**
     * Menyimpan permintaan koreksi absensi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCorrectionRequest(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'date_to_correct' => 'required|date_format:Y-m-d|before_or_equal:today', // Tanggal yang dikoreksi tidak boleh di masa depan
            'new_check_in' => 'nullable|date_format:H:i',
            'new_check_out' => 'nullable|date_format:H:i',
            'new_activity_title' => 'nullable|string|max:255',
            'new_activity_description' => 'nullable|string',
            'reason' => 'required|string|max:1000',
        ]);

        $dateToCorrect = Carbon::parse($validated['date_to_correct'])->tz('Asia/Makassar');

        // Dapatkan data absensi lama untuk perbandingan dan penyimpanan di CorrectionRequest
        $attendance = Attendance::where('user_id', $userId)
                                ->whereDate('date', $dateToCorrect->toDateString())
                                ->first();

        // Validasi waktu check-in/check-out baru
        if (!empty($validated['new_check_in']) && !empty($validated['new_check_out'])) {
            $newCheckInTime = Carbon::parse($validated['new_check_in'])->tz('Asia/Makassar');
            $newCheckOutTime = Carbon::parse($validated['new_check_out'])->tz('Asia/Makassar');
            if ($newCheckInTime->greaterThanOrEqualTo($newCheckOutTime)) {
                return redirect()->back()->withErrors(['new_check_out' => 'Waktu check-out baru harus setelah waktu check-in baru.'])->withInput();
            }
        }
        
        // Cek apakah ada perubahan yang diajukan. Jika tidak ada perubahan, tidak perlu membuat permintaan.
        $isSameCheckIn = ($attendance && $attendance->check_in && $attendance->check_in->tz('Asia/Makassar')->format('H:i') === $validated['new_check_in']) || (!$attendance || (!$attendance->check_in && empty($validated['new_check_in'])));
        $isSameCheckOut = ($attendance && $attendance->check_out && $attendance->check_out->tz('Asia/Makassar')->format('H:i') === $validated['new_check_out']) || (!$attendance || (!$attendance->check_out && empty($validated['new_check_out'])));
        $isSameActivityTitle = ($attendance && $attendance->activity_title === $validated['new_activity_title']) || (!$attendance || (empty($attendance->activity_title) && empty($validated['new_activity_title'])));
        $isSameActivityDescription = ($attendance && $attendance->activity_description === $validated['new_activity_description']) || (!$attendance || (empty($attendance->activity_description) && empty($validated['new_activity_description'])));

        if ($isSameCheckIn && $isSameCheckOut && $isSameActivityTitle && $isSameActivityDescription) {
            return redirect()->back()->with('info', 'Tidak ada perubahan yang diajukan untuk koreksi.');
        }

        // Siapkan waktu check-in/out baru dengan tanggal lengkap untuk disimpan
        $newCheckInDateTime = !empty($validated['new_check_in']) ?
                               Carbon::parse($dateToCorrect->toDateString() . ' ' . $validated['new_check_in'])->tz('Asia/Makassar') : null;
        $newCheckOutDateTime = !empty($validated['new_check_out']) ?
                               Carbon::parse($dateToCorrect->toDateString() . ' ' . $validated['new_check_out'])->tz('Asia/Makassar') : null;

        // Buat permintaan koreksi baru
        CorrectionRequest::create([
            'user_id' => $userId,
            'attendance_date' => $dateToCorrect->toDateString(),
            'old_check_in' => $attendance ? $attendance->check_in : null,
            'old_check_out' => $attendance ? $attendance->check_out : null,
            'old_activity_title' => $attendance ? $attendance->activity_title : null,
            'old_activity_description' => $attendance ? $attendance->activity_description : null,
            'new_check_in' => $newCheckInDateTime,
            'new_check_out' => $newCheckOutDateTime,
            'new_activity_title' => $validated['new_activity_title'],
            'new_activity_description' => $validated['new_activity_description'],
            'reason' => $validated['reason'],
            'status' => 'pending', // Status awal selalu 'pending'
        ]);

        return redirect()->route('dashboard')->with('success', 'Data koreksi sudah terkirim, silahkan menunggu admin mengkonfirmasinya.');
    }

    // Ini adalah placeholder untuk metode create dan store,
    // asumsikan ini untuk manajemen absensi oleh admin atau untuk entri manual.
    public function create()
    {
        // Path baru: resources/views/attendances/create.blade.php
        // Perhatikan: Dalam struktur folder yang Anda berikan, tidak ada folder 'attendances' langsung di bawah 'views',
        // jadi saya mengasumsikan ini seharusnya ada di tempat lain atau Anda perlu membuat folder 'attendances'
        // atau memindahkannya ke salah satu folder 'fold_' yang sudah ada.
        // Jika Anda memiliki folder 'attendances' di level yang sama dengan 'fold_dashboard', maka ini benar.
        // Jika tidak, perlu penyesuaian lagi.
        return view('attendances.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i:s',
            'check_out' => 'nullable|date_format:H:i:s|after:check_in',
            'activity_title' => 'nullable|string|max:255',
            'activity_description' => 'nullable|string',
        ]);

        // Gabungkan tanggal dengan waktu untuk check_in/check_out jika ada
        if ($validated['check_in']) {
            $validated['check_in'] = Carbon::parse($validated['date'] . ' ' . $validated['check_in']);
        }
        if ($validated['check_out']) {
            $validated['check_out'] = Carbon::parse($validated['date'] . ' ' . $validated['check_out']);
        }

        Attendance::create($validated);

        return redirect()->route('dashboard')->with('success', 'Absensi berhasil ditambahkan.');
    }
}