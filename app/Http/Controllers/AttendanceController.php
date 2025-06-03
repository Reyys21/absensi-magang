<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // Pastikan menggunakan alias ini
use App\Models\Attendance;
use App\Models\User;
use App\Models\CorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Menampilkan form check-in.
     *
     * @return \Illuminate\View\View
     */
    public function checkinForm()
    {
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
        $appTimezone = config('app.timezone'); // Dapatkan timezone aplikasi

        // Menggunakan local_date dari request (format YYYY-MM-DD)
        // Menggabungkan tanggal dari request dengan waktu dari request
        // Pastikan input form Anda mengirimkan 'local_date' dan 'local_time'
        // Jika tidak, Anda mungkin ingin menggunakan Carbon::now($appTimezone)->toDateString()
        // untuk $today dan $request->current_time untuk waktu.
        $today = $request->local_date ?? Carbon::now($appTimezone)->toDateString();
        $checkInTimeInput = $request->local_time ?? Carbon::now($appTimezone)->format('H:i:s'); // Default jika tidak ada input waktu

        // Waktu check-in baru, dibuat dengan tanggal dan waktu input, serta timezone aplikasi
        $newCheckInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $checkInTimeInput, $appTimezone);

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
                // $attendance->check_in adalah objek Carbon karena $casts di model, sudah dalam timezone aplikasi
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
        $appTimezone = config('app.timezone'); // Dapatkan timezone aplikasi

        // Menggunakan Carbon untuk tanggal hari ini di timezone aplikasi
        $today = Carbon::now($appTimezone)->toDateString();
        $checkOutTimeInput = $request->current_time ?? Carbon::now($appTimezone)->format('H:i:s'); // Default jika tidak ada input waktu

        // Waktu check-out baru, dibuat dengan tanggal dan waktu input, serta timezone aplikasi
        $newCheckOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $checkOutTimeInput, $appTimezone);

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
                // $attendance->check_out adalah objek Carbon karena $casts di model, sudah dalam timezone aplikasi
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
        $appTimezone = config('app.timezone');

        // Gunakan Carbon::now() dengan timezone aplikasi
        $today = Carbon::now($appTimezone)->toDateString();

        $attendanceToday = Attendance::where('user_id', $userId)
                                     ->whereDate('date', $today)
                                     ->first();

        // Karena model memiliki $casts='datetime', $attendanceToday->check_in dan check_out
        // sudah menjadi objek Carbon dalam timezone aplikasi.
        $firstCheckIn = $attendanceToday ? $attendanceToday->check_in : null;
        $lastCheckOut = $attendanceToday ? $attendanceToday->check_out : null;

        $user = Auth::user();
        // created_at dari model User juga akan di-cast otomatis ke timezone aplikasi jika ada di $casts User model
        $registrationDate = $user->created_at->startOfDay();

        $attendanceCount = 0;
        if ($registrationDate) {
            $currentDate = Carbon::now($appTimezone)->endOfDay(); // Gunakan timezone aplikasi

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

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $sort = $request->get('sort', 'desc');
        $query->orderBy('date', $sort);

        $attendances = $query->paginate(10);

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
        $appTimezone = config('app.timezone');

        // Pastikan created_at di model User juga di-cast agar timezone-aware
        $registrationDate = $user->created_at->startOfDay();

        $year = $request->input('year', Carbon::now($appTimezone)->year);
        $month = $request->input('month', Carbon::now($appTimezone)->month);

        $date = Carbon::createFromDate($year, $month, 1, $appTimezone); // Buat tanggal di timezone aplikasi
        $currentDay = Carbon::now($appTimezone)->startOfDay(); // Hari ini di timezone aplikasi

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
            // Perbandingan tanggal harus selalu timezone-aware
            if ($d->lt($registrationDate->copy()->startOfDay())) { // Copy to avoid modifying original
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
                                          ->whereDate('date', Carbon::parse($selectedDate, $appTimezone)->toDateString())
                                          ->get();

            // Ini harus diperbaiki agar konsisten dengan timezone
            if ($dailyAttendances->isEmpty() && Carbon::parse($selectedDate, $appTimezone)->lte(Carbon::now($appTimezone)) && Carbon::parse($selectedDate, $appTimezone)->gte($registrationDate)) {
                $dummyAttendance = (object)[
                    'date' => Carbon::parse($selectedDate, $appTimezone),
                    'check_in' => null,
                    'check_out' => null,
                    'activity_title' => null,
                    'activity_description' => null,
                    'user_id' => $userId,
                    'is_dummy' => true,
                ];
                $dummyAttendance->attendance_status = 'Absent (Belum Lengkap)';
                $dummyAttendance->day_name = Carbon::parse($selectedDate, $appTimezone)->translatedFormat('l');
                $dummyAttendance->formatted_date = Carbon::parse($selectedDate, $appTimezone)->translatedFormat('d F Y');
                $dailyAttendances->push($dummyAttendance);
            }
        } else {
            $sevenDaysAgo = Carbon::now($appTimezone)->subDays(6)->startOfDay();
            $rawDailyAttendances = Attendance::where('user_id', $userId)
                                             ->whereBetween('date', [$sevenDaysAgo, Carbon::now($appTimezone)->endOfDay()])
                                             ->orderBy('date', 'desc')
                                             ->get();

            $currentDailyAttendancesMap = [];
            foreach ($rawDailyAttendances as $att) {
                // Ensure date is a Carbon instance before toDateString()
                $currentDailyAttendancesMap[$att->date->toDateString()] = $att;
            }

            $processedDailyAttendances = collect();
            for ($d = Carbon::now($appTimezone)->startOfDay(); $d->gte($sevenDaysAgo); $d->subDay()) {
                $dateString = $d->toDateString();
                $attendanceRecord = $currentDailyAttendancesMap[$dateString] ?? null;

                if ($d->lt($registrationDate->copy()->startOfDay())) {
                    continue;
                }

                if ($attendanceRecord) {
                    $processedDailyAttendances->push($attendanceRecord);
                } else {
                    $dummyAttendance = (object)[
                        'date' => $d, // $d already a Carbon instance in app timezone
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
            $export = new \App\Exports\AttendancesExport($attendances);
            $fileName = 'attendance_' . Carbon::now(config('app.timezone'))->format('Ymd_His') . '.' . $format;
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
        $appTimezone = config('app.timezone');

        // Default ke tanggal hari ini di timezone aplikasi
        $dateToCorrect = Carbon::now($appTimezone);

        // Coba parse tanggal dari request jika ada
        if ($request->filled('date')) {
            try {
                // Pastikan tanggal diparse dengan timezone aplikasi
                $dateToCorrect = Carbon::parse($request->input('date'), $appTimezone);
            } catch (\Exception $e) {
                Log::error('Invalid date provided for correction form: ' . $request->input('date') . ' - ' . $e->getMessage());
                // Tetap gunakan Carbon::now() jika tanggal tidak valid
            }
        }

        // Dapatkan data absensi untuk tanggal tersebut.
        // Karena model memiliki $casts, check_in/out akan otomatis dalam timezone aplikasi.
        $attendance = Attendance::where('user_id', $userId)
                                ->whereDate('date', $dateToCorrect->toDateString())
                                ->first();

        // Siapkan data lama untuk form input HTML (value) dan tampilan
        $oldCheckIn = $attendance && $attendance->check_in ? $attendance->check_in->format('H:i') : ''; // Format untuk input type="time"
        $oldCheckOut = $attendance && $attendance->check_out ? $attendance->check_out->format('H:i') : '';

        $oldActivityTitle = $attendance ? $attendance->activity_title : '';
        $oldActivityDescription = $attendance ? $attendance->activity_description : '';

        // Untuk tampilan di "lama": (tidak lagi digunakan di view correction_form tapi tetap di sini)
        $displayOldCheckIn = $attendance && $attendance->check_in ? $attendance->check_in->format('H:i') : '--.--';
        $displayOldCheckOut = $attendance && $attendance->check_out ? $attendance->check_out->format('H:i') : '--.--';
        $displayOldActivityTitle = $attendance ? ($attendance->activity_title ?: '--') : '--';
        $displayOldActivityDescription = $attendance ? ($attendance->activity_description ?: '--') : '--';


        return view('fold_history.correction_form', compact(
            'dateToCorrect',
            'oldCheckIn',
            'oldCheckOut',
            'oldActivityTitle',
            'oldActivityDescription',
            'displayOldCheckIn',
            'displayOldCheckOut',
            'displayOldActivityTitle',
            'displayOldActivityDescription',
            'attendance'
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
        $appTimezone = config('app.timezone');

        $validated = $request->validate([
            'date_to_correct' => 'required|date_format:Y-m-d|before_or_equal:today',
            'new_check_in' => 'nullable|date_format:H:i',
            'new_check_out' => 'nullable|date_format:H:i',
            'new_activity_title' => 'nullable|string|max:255',
            'new_activity_description' => 'nullable|string',
            'reason' => 'required|string|max:1000',
        ]);

        // Parse tanggal koreksi, pastikan di timezone aplikasi
        $dateToCorrect = Carbon::parse($validated['date_to_correct'], $appTimezone)->startOfDay();

        // Dapatkan data absensi lama untuk perbandingan
        // Ini akan menjadi objek Carbon di timezone aplikasi karena $casts di model
        $attendance = Attendance::where('user_id', $userId)
                                ->whereDate('date', $dateToCorrect->toDateString())
                                ->first();

        // Siapkan waktu check-in/out baru dengan tanggal lengkap dan timezone yang benar
        $newCheckInTime = null;
        if (!empty($validated['new_check_in'])) {
            $newCheckInTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $dateToCorrect->toDateString() . ' ' . $validated['new_check_in'],
                $appTimezone
            );
        }

        $newCheckOutTime = null;
        if (!empty($validated['new_check_out'])) {
            $newCheckOutTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $dateToCorrect->toDateString() . ' ' . $validated['new_check_out'],
                $appTimezone
            );
        }

        // Validasi waktu check-in/check-out baru
        if ($newCheckInTime && $newCheckOutTime && $newCheckInTime->greaterThanOrEqualTo($newCheckOutTime)) {
            return redirect()->back()->withErrors(['new_check_out' => 'Waktu check-out baru harus setelah waktu check-in baru.'])->withInput();
        }

        // Cek apakah ada perubahan yang diajukan.
        // Format waktu lama dari objek Carbon ($attendance->check_in) agar konsisten dengan input form ('H:i').
        $oldCheckInFormatted = $attendance && $attendance->check_in ? $attendance->check_in->format('H:i') : null;
        $oldCheckOutFormatted = $attendance && $attendance->check_out ? $attendance->check_out->format('H:i') : null;

        $isSameCheckIn = ( $oldCheckInFormatted === $validated['new_check_in'] ) || ( !$oldCheckInFormatted && empty($validated['new_check_in']) );
        $isSameCheckOut = ( $oldCheckOutFormatted === $validated['new_check_out'] ) || ( !$oldCheckOutFormatted && empty($validated['new_check_out']) );
        $isSameActivityTitle = ($attendance && $attendance->activity_title === $validated['new_activity_title']) || (!$attendance || (empty($attendance->activity_title) && empty($validated['new_activity_title'])));
        $isSameActivityDescription = ($attendance && $attendance->activity_description === $validated['new_activity_description']) || (!$attendance || (empty($attendance->activity_description) && empty($validated['new_activity_description'])));

        if ($isSameCheckIn && $isSameCheckOut && $isSameActivityTitle && $isSameActivityDescription) {
            return redirect()->back()->with('info', 'Tidak ada perubahan yang diajukan untuk koreksi.');
        }

        // Buat permintaan koreksi baru
        CorrectionRequest::create([
            'user_id' => $userId,
            'attendance_date' => $dateToCorrect->toDateString(), // Cukup simpan tanggal
            'old_check_in' => $attendance ? $attendance->check_in : null, // Ini sudah objek Carbon dari DB, akan disimpan sebagai UTC
            'old_check_out' => $attendance ? $attendance->check_out : null, // Ini sudah objek Carbon dari DB, akan disimpan sebagai UTC
            'new_check_in' => $newCheckInTime, // Ini adalah objek Carbon di timezone aplikasi, akan disimpan sebagai UTC
            'new_check_out' => $newCheckOutTime, // Ini adalah objek Carbon di timezone aplikasi, akan disimpan sebagai UTC
            'new_activity_title' => $validated['new_activity_title'],
            'new_activity_description' => $validated['new_activity_description'],
            'reason' => $validated['reason'],
            'status' => 'pending',
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
        $appTimezone = config('app.timezone');

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
            $validated['check_in'] = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $validated['date'] . ' ' . $validated['check_in'],
                $appTimezone
            );
        }
        if ($validated['check_out']) {
            $validated['check_out'] = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $validated['date'] . ' ' . $validated['check_out'],
                $appTimezone
            );
        }

        Attendance::create($validated);

        return redirect()->route('dashboard')->with('success', 'Absensi berhasil ditambahkan.');
    }

    /**
     * Menampilkan daftar permintaan koreksi absensi yang diajukan oleh user.
     *
     * @return \Illuminate\View\View
     */
    public function showApprovalRequests()
    {
        $userId = Auth::id();
        // Ketika mengambil dari database, karena $casts di model CorrectionRequest,
        // Laravel akan secara otomatis mengonversi dari UTC ke timezone aplikasi (Asia/Makassar).
        $requests = CorrectionRequest::where('user_id', $userId)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(10);

        return view('fold_AttendanceApproval.Attendance Approval', compact('requests'));
    }
}