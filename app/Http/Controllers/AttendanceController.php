<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $appTimezone = config('app.timezone');

        $today = $request->local_date ?? Carbon::now($appTimezone)->toDateString();
        $checkInTimeInput = $request->local_time ?? Carbon::now($appTimezone)->format('H:i:s');

        $newCheckInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $checkInTimeInput, $appTimezone);

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            if (empty($attendance->check_in)) {
                $attendance->update([
                    'check_in' => $newCheckInDateTime,
                ]);
                return redirect()->route('dashboard')->with('success', 'Check-in berhasil!');
            } else {
                $existingCheckInTime = $attendance->check_in;

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
        $appTimezone = config('app.timezone');

        $today = Carbon::now($appTimezone)->toDateString();
        $checkOutTimeInput = $request->current_time ?? Carbon::now($appTimezone)->format('H:i:s');

        $newCheckOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $checkOutTimeInput, $appTimezone);

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            if (empty($attendance->check_out)) {
                $attendance->update([
                    'check_out' => $newCheckOutDateTime,
                    'activity_title' => $request->activity_title,
                    'activity_description' => $request->activity_description,
                ]);
                return redirect()->route('dashboard')->with('success', 'Check-out berhasil!');
            } else {
                $existingCheckOutTime = $attendance->check_out;

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

        $today = Carbon::now($appTimezone)->toDateString();

        $attendanceToday = Attendance::where('user_id', $userId)
                                    ->whereDate('date', $today)
                                    ->first();

        $firstCheckIn = $attendanceToday ? $attendanceToday->check_in : null;
        $lastCheckOut = $attendanceToday ? $attendanceToday->check_out : null;

        $user = Auth::user();
        $registrationDate = $user->created_at->startOfDay();

        $attendanceCount = 0;
        if ($registrationDate) {
            $currentDate = Carbon::now($appTimezone)->endOfDay();

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
                if ($attendanceRecord && $attendanceRecord->attendance_status === 'Lengkap') {
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

        $registrationDate = $user->created_at->startOfDay();

        $year = $request->input('year', Carbon::now($appTimezone)->year);
        $month = $request->input('month', Carbon::now($appTimezone)->month);

        $date = Carbon::createFromDate($year, $month, 1, $appTimezone);
        $currentDay = Carbon::now($appTimezone)->startOfDay();

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
            if ($d->lt($registrationDate->copy()->startOfDay())) {
                $status = 'N/A';
            } elseif ($d->gt($currentDay)) {
                $status = 'Future';
            } else {
                $attendance = $existingAttendances->get($currentLoopDateString);
                if ($attendance) {
                    $status = $attendance->attendance_status;
                } else {
                    $status = 'Tidak Hadir (Belum Lengkap)';
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
                $dummyAttendance->attendance_status = 'Tidak Hadir (Belum Lengkap)';
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
                        'date' => $d,
                        'check_in' => null,
                        'check_out' => null,
                        'activity_title' => null,
                        'activity_description' => null,
                        'user_id' => $userId,
                        'is_dummy' => true,
                    ];
                    $dummyAttendance->attendance_status = 'Tidak Hadir (Belum Lengkap)';
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

        $dateToCorrect = Carbon::now($appTimezone);

        if ($request->filled('date')) {
            try {
                $dateToCorrect = Carbon::parse($request->input('date'), $appTimezone);
            } catch (\Exception $e) {
                Log::error('Invalid date provided for correction form: ' . $request->input('date') . ' - ' . $e->getMessage());
            }
        }

        $attendance = Attendance::where('user_id', $userId)
                                ->whereDate('date', $dateToCorrect->toDateString())
                                ->first();

        // Ambil data dari permintaan koreksi yang 'pending' atau 'rejected' jika ada, untuk menampilkan data terbaru.
        $pendingOrRejectedCorrection = CorrectionRequest::where('user_id', $userId)
                                                        ->where('attendance_date', $dateToCorrect->toDateString())
                                                        ->whereIn('status', ['pending', 'rejected'])
                                                        ->latest() // Ambil yang terbaru
                                                        ->first();

        // Gunakan data dari permintaan koreksi jika ada, jika tidak, gunakan data absensi
        $oldCheckIn = ($pendingOrRejectedCorrection && $pendingOrRejectedCorrection->new_check_in) ? $pendingOrRejectedCorrection->new_check_in->format('H:i') : ($attendance && $attendance->check_in ? $attendance->check_in->format('H:i') : '');
        $oldCheckOut = ($pendingOrRejectedCorrection && $pendingOrRejectedCorrection->new_check_out) ? $pendingOrRejectedCorrection->new_check_out->format('H:i') : ($attendance && $attendance->check_out ? $attendance->check_out->format('H:i') : '');
        $oldActivityTitle = ($pendingOrRejectedCorrection && $pendingOrRejectedCorrection->new_activity_title) ? $pendingOrRejectedCorrection->new_activity_title : ($attendance ? $attendance->activity_title : '');
        $oldActivityDescription = ($pendingOrRejectedCorrection && $pendingOrRejectedCorrection->new_activity_description) ? $pendingOrRejectedCorrection->new_activity_description : ($attendance ? $attendance->activity_description : '');


        return view('fold_history.correction_form', compact(
            'dateToCorrect',
            'oldCheckIn',
            'oldCheckOut',
            'oldActivityTitle',
            'oldActivityDescription',
            'attendance' // Tetap sertakan attendance untuk referensi data asli jika diperlukan
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

        $dateToCorrect = Carbon::parse($validated['date_to_correct'], $appTimezone)->startOfDay();

        // Dapatkan data absensi lama untuk perbandingan
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

        // Dapatkan data permintaan koreksi yang sudah ada (pending atau rejected) untuk tanggal ini
        // Kita perlu mencari yang terbaru jika ada lebih dari satu yang ditolak
        $existingCorrectionRequest = CorrectionRequest::where('user_id', $userId)
            ->where('attendance_date', $dateToCorrect->toDateString())
            ->whereIn('status', ['pending', 'rejected'])
            ->latest() // Ambil yang terbaru jika ada beberapa yang rejected
            ->first();

        // Tentukan nilai "old" yang akan digunakan untuk perbandingan.
        // Jika ada existingCorrectionRequest, gunakan 'new_check_in'/'new_check_out' dari situ
        // sebagai nilai "lama" yang sedang dikoreksi ulang.
        // Jika tidak ada, gunakan data dari attendance asli.
        $currentOldCheckIn = $attendance ? $attendance->check_in : null;
        $currentOldCheckOut = $attendance ? $attendance->check_out : null;
        $currentOldActivityTitle = $attendance ? $attendance->activity_title : null;
        $currentOldActivityDescription = $attendance ? $attendance->activity_description : null;

        if ($existingCorrectionRequest) {
            // Jika ada permintaan koreksi yang sudah ada, ambil nilai 'new' dari permintaan itu sebagai 'old'
            // untuk perbandingan saat ini. Ini memastikan kita membandingkan dengan permintaan koreksi terakhir.
            $currentOldCheckIn = $existingCorrectionRequest->new_check_in;
            $currentOldCheckOut = $existingCorrectionRequest->new_check_out;
            $currentOldActivityTitle = $existingCorrectionRequest->new_activity_title;
            $currentOldActivityDescription = $existingCorrectionRequest->new_activity_description;
        }

        // Format waktu 'old' saat ini untuk perbandingan dengan input form
        $currentOldCheckInFormatted = $currentOldCheckIn ? $currentOldCheckIn->format('H:i') : null;
        $currentOldCheckOutFormatted = $currentOldCheckOut ? $currentOldCheckOut->format('H:i') : null;

        // Tentukan apakah ada perubahan yang diajukan dengan membandingkan semua field
        $hasChanges = false;
        if (
            ($currentOldCheckInFormatted !== $validated['new_check_in'] && !($currentOldCheckInFormatted === null && empty($validated['new_check_in']))) ||
            ($currentOldCheckOutFormatted !== $validated['new_check_out'] && !($currentOldCheckOutFormatted === null && empty($validated['new_check_out']))) ||
            ($currentOldActivityTitle !== $validated['new_activity_title'] && !($currentOldActivityTitle === null && empty($validated['new_activity_title']))) ||
            ($currentOldActivityDescription !== $validated['new_activity_description'] && !($currentOldActivityDescription === null && empty($validated['new_activity_description'])))
        ) {
            $hasChanges = true;
        }

        if (!$hasChanges) {
            return redirect()->back()->with('info', 'Tidak ada perubahan yang diajukan untuk koreksi.');
        }

        // Data yang akan disimpan/diperbarui
        $correctionData = [
            'user_id' => $userId,
            'attendance_date' => $dateToCorrect->toDateString(),
            // Simpan 'old_check_in' dan 'old_check_out' berdasarkan data absensi ASLI.
            // Ini penting untuk audit trail, selalu bandingkan dengan data kehadiran yang tercatat.
            'old_check_in' => $attendance ? $attendance->check_in : null,
            'old_check_out' => $attendance ? $attendance->check_out : null,
            'new_check_in' => $newCheckInTime,
            'new_check_out' => $newCheckOutTime,
            'new_activity_title' => $validated['new_activity_title'],
            'new_activity_description' => $validated['new_activity_description'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ];

        if ($existingCorrectionRequest) {
            // Jika ada permintaan koreksi yang sudah ada (pending atau rejected), update saja
            $existingCorrectionRequest->update($correctionData);
            return redirect()->route('dashboard')->with('success', 'Permintaan koreksi Anda telah diperbarui dan dikirim ulang.');
        } else {
            // Jika tidak ada, buat permintaan koreksi baru
            CorrectionRequest::create($correctionData);
            return redirect()->route('dashboard')->with('success', 'Data koreksi sudah terkirim, silahkan menunggu admin mengkonfirmasinya.');
        }
    }

    // Ini adalah placeholder untuk metode create dan store,
    // asumsikan ini untuk manajemen absensi oleh admin atau untuk entri manual.
    public function create()
    {
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
        $requests = CorrectionRequest::where('user_id', $userId)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        return view('fold_AttendanceApproval.Attendance Approval', compact('requests'));
    }
}