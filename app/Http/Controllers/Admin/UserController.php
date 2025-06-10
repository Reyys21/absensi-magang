<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // Method indexMonitoring dan indexManagement tidak berubah, tetap sama seperti sebelumnya.
    public function indexMonitoring(Request $request)
    {
        $query = User::query()->whereHas('roles', function ($q) {
            $q->where('name', 'user'); });

        // Filter pencarian dan role (yang sudah ada)
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); }); });
        $query->when($request->filter_role, function ($q, $role) {
            $q->where('role', $role); });

        // REVISI: Menambahkan logika untuk sorting
        $sortBy = $request->input('sort_by', 'created_at'); // Default sort berdasarkan waktu dibuat
        $sortDirection = $request->input('sort_direction', 'desc'); // Default sort descending (terbaru)

        // Daftar kolom yang diizinkan untuk di-sort untuk keamanan
        $sortableColumns = ['name', 'email'];
        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->latest(); // Fallback ke default
        }

        $users = $query->select('id', 'name', 'email', 'phone', 'role')->paginate(15)->appends($request->query());

        if ($request->ajax()) {
            return view('admin._users-table', compact('users'))->render();
        }

        return view('admin.users', compact('users'));
    }

    public function indexManagement(Request $request)
    {
        $query = User::query()->whereHas('roles', function ($q) {
            $q->where('name', 'user'); });
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); }); });
        $query->when($request->filter_role, function ($q, $role) {
            $q->where('role', $role); });
        $users = $query->select('id', 'profile_photo_path', 'name', 'email', 'phone', 'role', 'nim', 'asal_kampus')->latest()->paginate(15)->appends($request->query());
        if ($request->ajax()) {
            return view('admin._account-table', compact('users'))->render();
        }
        return view('admin.account', compact('users'));
    }


    public function showMonitoring(User $user, Request $request)
    {
        // === Query untuk Riwayat Absensi ===
        $attendancesQuery = Attendance::where('user_id', $user->id);

        // Logika filter baru untuk Absensi
        switch ($request->input('filter_type_absensi')) {
            case 'terlama':
                $attendancesQuery->orderBy('date', 'asc');
                break;
            case 'tanggal':
                $attendancesQuery->when($request->filter_tanggal_absensi, function ($q, $date) {
                    $q->whereDate('date', $date);
                });
                // Default sort untuk tanggal yang dipilih adalah terbaru
                $attendancesQuery->orderBy('date', 'desc');
                break;
            default: // Termasuk 'terbaru' dan kasus awal
                $attendancesQuery->orderBy('date', 'desc');
                break;
        }

        $attendances = $attendancesQuery->paginate(10, ['*'], 'attendances_page')->appends($request->query());

        // === Query untuk Riwayat Pengajuan Koreksi ===
        $correctionRequestsQuery = CorrectionRequest::where('user_id', $user->id);

        // Logika filter baru untuk Koreksi
        switch ($request->input('filter_type_koreksi')) {
            case 'terlama':
                $correctionRequestsQuery->orderBy('created_at', 'asc');
                break;
            case 'tanggal':
                $correctionRequestsQuery->when($request->filter_tanggal_koreksi, function ($q, $date) {
                    $q->whereDate('attendance_date', $date);
                });
                $correctionRequestsQuery->orderBy('created_at', 'desc');
                break;
            default: // Termasuk 'terbaru' dan kasus awal
                $correctionRequestsQuery->orderBy('created_at', 'desc');
                break;
        }

        $correctionRequests = $correctionRequestsQuery->paginate(10, ['*'], 'corrections_page')->appends($request->query());

        return view('admin.users-show', compact('user', 'attendances', 'correctionRequests'));
    }
}