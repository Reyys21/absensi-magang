<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use App\Models\User;
use App\Models\CorrectionRequest;
use App\Models\Bidang; // Import Bidang model
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreCorrectionRequest;

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

        $today = Carbon::now($appTimezone)->toDateString();
        $checkInTimeInput = $request->local_time ?? Carbon::now($appTimezone)->format('H:i:s');
        $newCheckInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $checkInTimeInput, $appTimezone);

        $attendance = Attendance::firstOrNew([
            'user_id' => $userId,
            'date' => $today,
        ]);

        if (!$attendance->check_in || $newCheckInDateTime->lt($attendance->check_in)) {
            $attendance->check_in = $newCheckInDateTime;
            $attendance->save();
            return redirect()->route('dashboard')->with('success', 'Check-in Anda telah dicatat pada ' . $newCheckInDateTime->format('H:i'));
        }

        return redirect()->route('dashboard')->with('info', 'Waktu check-in paling awal Anda hari ini sudah tersimpan.');
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

        if (!$attendance || !$attendance->check_in) {
            return redirect()->route('dashboard')->with('error', 'Anda harus melakukan Check-in terlebih dahulu.');
        }

        if (!$attendance->check_out || $newCheckOutDateTime->gt($attendance->check_out)) {
            $attendance->check_out = $newCheckOutDateTime;
            $attendance->activity_title = $request->activity_title;
            $attendance->activity_description = $request->activity_description;
            $attendance->save();
            return redirect()->route('dashboard')->with('success', 'Check-out Anda telah diperbarui pada ' . $newCheckOutDateTime->format('H:i'));
        }

        return redirect()->route('dashboard')->with('info', 'Waktu check-out terakhir Anda sudah tersimpan.');
    }

    /**
     * Menampilkan dashboard dengan data absensi hari ini dan total kehadiran.
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
                ->keyBy(function ($item) {
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
            ->keyBy(function ($item) {
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
            $monthlyAttendances->put($currentLoopDateString, (object) ['attendance_status' => $status]);
        }
        $selectedDate = $request->input('selected_date');
        $dailyAttendances = collect();
        if ($selectedDate) {
            $dailyAttendances = Attendance::where('user_id', $userId)
                ->whereDate('date', Carbon::parse($selectedDate, $appTimezone)->toDateString())
                ->get();
            if ($dailyAttendances->isEmpty() && Carbon::parse($selectedDate, $appTimezone)->lte(Carbon::now($appTimezone)) && Carbon::parse($selectedDate, $appTimezone)->gte($registrationDate)) {
                $dummyAttendance = (object) [
                    'date' => Carbon::parse($selectedDate, $appTimezone),
                    'check_in' => null,
                    'check_out' => null,
                    'activity_title' => null,
                    'activity_description' => null,
                    'user_id' => $userId,
                    'is_dummy' => true,
                    'attendance_status' => 'Tidak Hadir (Belum Lengkap)',
                    'day_name' => Carbon::parse($selectedDate, $appTimezone)->translatedFormat('l'),
                    'formatted_date' => Carbon::parse($selectedDate, $appTimezone)->translatedFormat('d F Y'),
                ];
                $dailyAttendances->push($dummyAttendance);
            }
        } else {
            $sevenDaysAgo = Carbon::now($appTimezone)->subDays(6)->startOfDay();
            $rawDailyAttendances = Attendance::where('user_id', $userId)
                ->whereBetween('date', [$sevenDaysAgo, Carbon::now($appTimezone)->endOfDay()])
                ->orderBy('date', 'desc')->get();
            $currentDailyAttendancesMap = [];
            foreach ($rawDailyAttendances as $att) {
                $currentDailyAttendancesMap[$att->date->toDateString()] = $att;
            }
            $processedDailyAttendances = collect();
            for ($d = Carbon::now($appTimezone)->startOfDay(); $d->gte($sevenDaysAgo); $d->subDay()) {
                $dateString = $d->toDateString();
                $attendanceRecord = $currentDailyAttendancesMap[$dateString] ?? null;
                if ($d->lt($registrationDate->copy()->startOfDay()))
                    continue;
                if ($attendanceRecord) {
                    $processedDailyAttendances->push($attendanceRecord);
                } else {
                    $dummyAttendance = (object) [
                        'date' => $d,
                        'check_in' => null,
                        'check_out' => null,
                        'activity_title' => null,
                        'activity_description' => null,
                        'user_id' => $userId,
                        'is_dummy' => true,
                        'attendance_status' => 'Tidak Hadir (Belum Lengkap)',
                        'day_name' => $d->translatedFormat('l'),
                        'formatted_date' => $d->translatedFormat('d F Y'),
                    ];
                    $processedDailyAttendances->push($dummyAttendance);
                }
            }
            $dailyAttendances = $processedDailyAttendances;
        }
        return view('fold_history.history', compact('date', 'monthlyAttendances', 'dailyAttendances', 'selectedDate'));
    }

    /**
     * Export data absensi.
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
                Log::error('Invalid date provided: ' . $request->input('date') . ' - ' . $e->getMessage());
            }
        }
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $dateToCorrect->toDateString())->first();
        $pendingOrRejectedCorrection = CorrectionRequest::where('user_id', $userId)->where('attendance_date', $dateToCorrect->toDateString())
            ->whereIn('status', ['pending', 'rejected'])->latest()->first();
        $oldCheckIn = ($pendingOrRejectedCorrection && $pendingOrRejectedCorrection->new_check_in) ? $pendingOrRejectedCorrection->new_check_in->format('H:i') : ($attendance && $attendance->check_in ? $attendance->check_in->format('H:i') : '');
        $oldCheckOut = ($pendingOrRejectedCorrection && $pendingOrRejectedCorrection->new_check_out) ? $pendingOrRejectedCorrection->new_check_out->format('H:i') : ($attendance && $attendance->check_out ? $attendance->check_out->format('H:i') : '');
        $oldActivityTitle = ($pendingOrRejectedCorrection && $pendingOrRejectedCorrection->new_activity_title) ? $pendingOrRejectedCorrection->new_activity_title : ($attendance ? $attendance->activity_title : '');
        $oldActivityDescription = ($pendingOrRejectedCorrection && $pendingOrRejectedCorrection->new_activity_description) ? $pendingOrRejectedCorrection->new_activity_description : ($attendance ? $attendance->activity_description : '');
        return view('fold_history.correction_form', compact('dateToCorrect', 'oldCheckIn', 'oldCheckOut', 'oldActivityTitle', 'oldActivityDescription', 'attendance'));
    }

    /**
     * Menyimpan permintaan koreksi absensi.
     */
    public function storeCorrectionRequest(StoreCorrectionRequest $request)
    {
        $userId = Auth::id();
        $appTimezone = config('app.timezone');
        $validated = $request->validated();
        $dateToCorrect = Carbon::parse($validated['date_to_correct'], $appTimezone)->startOfDay();
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $dateToCorrect->toDateString())->first();
        $newCheckInTime = !empty($validated['new_check_in']) ? Carbon::createFromFormat('Y-m-d H:i', $dateToCorrect->toDateString() . ' ' . $validated['new_check_in'], $appTimezone) : null;
        $newCheckOutTime = !empty($validated['new_check_out']) ? Carbon::createFromFormat('Y-m-d H:i', $dateToCorrect->toDateString() . ' ' . $validated['new_check_out'], $appTimezone) : null;
        if ($newCheckInTime && $newCheckOutTime && $newCheckInTime->greaterThanOrEqualTo($newCheckOutTime)) {
            return redirect()->back()->withErrors(['new_check_out' => 'Waktu check-out baru harus setelah waktu check-in baru.'])->withInput();
        }
        $existingCorrectionRequest = CorrectionRequest::where('user_id', $userId)->where('attendance_date', $dateToCorrect->toDateString())->whereIn('status', ['pending', 'rejected'])->latest()->first();
        $currentOldCheckIn = $attendance ? $attendance->check_in : null;
        $currentOldCheckOut = $attendance ? $attendance->check_out : null;
        $currentOldActivityTitle = $attendance ? $attendance->activity_title : null;
        $currentOldActivityDescription = $attendance ? $attendance->activity_description : null;
        if ($existingCorrectionRequest) {
            $currentOldCheckIn = $existingCorrectionRequest->new_check_in;
            $currentOldCheckOut = $existingCorrectionRequest->new_check_out;
            $currentOldActivityTitle = $existingCorrectionRequest->new_activity_title;
            $currentOldActivityDescription = $existingCorrectionRequest->new_activity_description;
        }
        $currentOldCheckInFormatted = $currentOldCheckIn ? $currentOldCheckIn->format('H:i') : null;
        $currentOldCheckOutFormatted = $currentOldCheckOut ? $currentOldCheckOut->format('H:i') : null;
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
        $correctionData = [
            'user_id' => $userId,
            'attendance_date' => $dateToCorrect->toDateString(),
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
            $existingCorrectionRequest->update($correctionData);
            return redirect()->route('dashboard')->with('success', 'Permintaan koreksi Anda telah diperbarui dan dikirim ulang.');
        } else {
            CorrectionRequest::create($correctionData);
            return redirect()->route('dashboard')->with('success', 'Data koreksi sudah terkirim, silahkan menunggu admin mengkonfirmasinya.');
        }
    }

    /**
     * Placeholder untuk manajemen absensi oleh admin atau untuk entri manual.
     */
    public function create()
    {
        return view('attendances.create');
    }

    /**
     * Placeholder untuk menyimpan absensi manual.
     */
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
            $validated['check_in'] = Carbon::createFromFormat('Y-m-d H:i:s', $validated['date'] . ' ' . $validated['check_in'], $appTimezone);
        }
        if ($validated['check_out']) {
            $validated['check_out'] = Carbon::createFromFormat('Y-m-d H:i:s', $validated['date'] . ' ' . $validated['check_out'], $appTimezone);
        }
        Attendance::create($validated);
        return redirect()->route('dashboard')->with('success', 'Absensi berhasil ditambahkan.');
    }

    /**
     * Menampilkan daftar permintaan koreksi.
     */
    public function showApprovalRequests(Request $request)
    {
        $user = Auth::user();
        
        // Query awal TIDAK lagi memfilter berdasarkan status
        $query = CorrectionRequest::with(['user', 'user.bidang']);
    
        // Filter search tetap berlaku untuk semua
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('attendance_date', 'like', "%{$search}%");
            });
        }
    
        // Cek apakah yang login adalah admin/superadmin
        if (Gate::allows('access-admin-pages')) {
            // === BAGIAN UNTUK ADMIN ===
            // Filter 'pending' HANYA diterapkan untuk admin di sini
            $query->where('status', 'pending');
    
            if ($user->hasRole('superadmin') || $user->can('approve all requests')) {
                if ($request->filled('bidang_filter')) {
                    $bidangId = $request->input('bidang_filter');
                    $query->whereHas('user', function ($userQuery) use ($bidangId) {
                        $userQuery->where('bidang_id', $bidangId);
                    });
                }
            } else {
                $adminBidangId = $user->bidang_id;
                $query->whereHas('user', function ($userQuery) use ($adminBidangId) {
                    $userQuery->where('bidang_id', $adminBidangId);
                });
            }
    
            $requests = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
            $bidangs = Bidang::orderBy('name')->get();
            return view('admin.approval', compact('requests', 'bidangs'));

        } else {
            // === BAGIAN UNTUK USER BIASA ===
            // Query untuk user biasa tidak difilter berdasarkan status
            $query->where('user_id', $user->id);
            $requests = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
            
            // Mengarahkan ke view yang benar untuk user
            return view('fold_AttendanceApproval.Attendance Approval', compact('requests'));
        }
    }


    /**
     * Menyetujui permintaan koreksi absensi.
     */
    public function approveCorrection(CorrectionRequest $correctionRequest)
    {
        Gate::authorize('access-admin-pages');

        // ▼▼▼ PERUBAHAN LOGIKA OTORISASI ▼▼▼
        $admin = Auth::user();
        // Tolak jika dia admin biasa (tanpa izin global) DAN mencoba mengakses data dari bidang lain
        if ($admin->hasRole('admin') && !$admin->can('approve all requests') && $admin->bidang_id != $correctionRequest->user->bidang_id) {
            abort(403, 'Anda tidak berwenang menyetujui permintaan dari bidang ini.');
        }
        // ▲▲▲ AKHIR PERUBAHAN LOGIKA ▲▲▲

        $attendance = Attendance::firstOrNew(['user_id' => $correctionRequest->user_id, 'date' => $correctionRequest->attendance_date]);
        if ($correctionRequest->new_check_in)
            $attendance->check_in = $correctionRequest->new_check_in;
        if ($correctionRequest->new_check_out)
            $attendance->check_out = $correctionRequest->new_check_out;
        if ($correctionRequest->new_activity_title)
            $attendance->activity_title = $correctionRequest->new_activity_title;
        if ($correctionRequest->new_activity_description)
            $attendance->activity_description = $correctionRequest->new_activity_description;
        $attendance->save();
        $correctionRequest->update(['status' => 'approved', 'approved_by' => Auth::id(), 'approved_at' => now(), 'admin_notes' => 'Disetujui.']);
        return redirect()->route('admin.approval.requests')->with('success', 'Permintaan koreksi berhasil disetujui.');
    }

    /**
     * Menolak permintaan koreksi absensi.
     */
    public function rejectCorrection(Request $request, CorrectionRequest $correctionRequest)
    {
        Gate::authorize('access-admin-pages');

        // ▼▼▼ PERUBAHAN LOGIKA OTORISASI ▼▼▼
        $admin = Auth::user();
        // Tolak jika dia admin biasa (tanpa izin global) DAN mencoba mengakses data dari bidang lain
        if ($admin->hasRole('admin') && !$admin->can('approve all requests') && $admin->bidang_id != $correctionRequest->user->bidang_id) {
            abort(403, 'Anda tidak berwenang menolak permintaan dari bidang ini.');
        }
        // ▲▲▲ AKHIR PERUBAHAN LOGIKA ▲▲▲

        $validated = $request->validate(['admin_notes' => 'required|string|max:1000']);
        $correctionRequest->update(['status' => 'rejected', 'approved_by' => Auth::id(), 'approved_at' => now(), 'admin_notes' => $validated['admin_notes']]);
        return redirect()->route('admin.approval.requests')->with('success', 'Permintaan koreksi berhasil ditolak.');
    }
}