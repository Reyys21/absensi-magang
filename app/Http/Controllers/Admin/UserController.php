<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use App\Models\Bidang; // Pastikan model Bidang di-import
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function indexMonitoring(Request $request)
    {
        $query = User::query()->whereHas('roles', function ($q) {
            $q->where('name', 'user');
        });
        
        $currentUser = Auth::user();

        // Logika untuk Super Admin dan Admin
        if ($currentUser->hasRole('superadmin')) {
            // Super Admin dapat memfilter berdasarkan bidang
            if ($request->filled('bidang_filter')) {
                $query->where('bidang_id', $request->bidang_filter);
            }
        } else {
            // Admin biasa hanya melihat pengguna dari bidangnya sendiri
            $query->where('bidang_id', $currentUser->bidang_id);
        }

        // Filter pencarian dan role yang sudah ada
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        });
        $query->when($request->filter_role, function ($q, $role) {
            $q->where('role', $role);
        });

        // Logika sorting yang sudah ada
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['name', 'email'];
        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->latest();
        }

        $users = $query->with('bidang')->select('id', 'name', 'email', 'phone', 'role', 'bidang_id')->paginate(15)->appends($request->query());

        if ($request->ajax()) {
            return view('admin._users-table', compact('users'))->render();
        }

        // Ambil data bidang untuk dropdown filter, HANYA jika yang login adalah superadmin
        $bidangs = $currentUser->hasRole('superadmin') ? Bidang::orderBy('name')->get() : collect();

        return view('admin.users', compact('users', 'bidangs'));
    }

    public function indexManagement(Request $request)
    {
        $query = User::query()->whereHas('roles', function ($q) {
            $q->where('name', 'user');
        });
        
        $currentUser = Auth::user();

        if ($currentUser->hasRole('superadmin')) {
            if ($request->filled('bidang_filter')) {
                $query->where('bidang_id', $request->bidang_filter);
            }
        } else {
            $query->where('bidang_id', $currentUser->bidang_id);
        }
        
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        });
        $query->when($request->filter_role, function ($q, $role) {
            $q->where('role', $role);
        });
        
        $users = $query->with('bidang')->select('id', 'profile_photo_path', 'name', 'email', 'phone', 'role', 'nim', 'asal_kampus', 'bidang_id')->latest()->paginate(15)->appends($request->query());
        
        if ($request->ajax()) {
            return view('admin._account-table', compact('users'));
        }
        
        $bidangs = $currentUser->hasRole('superadmin') ? Bidang::orderBy('name')->get() : collect();

        return view('admin.account', compact('users', 'bidangs'));
    }


    public function showMonitoring(User $user, Request $request)
    {
        // Otorisasi: Pastikan admin tidak bisa melihat detail user dari bidang lain
        if (Auth::user()->hasRole('admin') && Auth::user()->bidang_id != $user->bidang_id) {
            abort(403, 'AKSES DITOLAK. Anda tidak berwenang melihat data user dari bidang lain.');
        }

        // Sisa method tidak berubah
        $attendancesQuery = Attendance::where('user_id', $user->id);
        
        switch ($request->input('filter_type_absensi')) {
            case 'terlama':
                $attendancesQuery->orderBy('date', 'asc');
                break;
            case 'tanggal':
                $attendancesQuery->when($request->filter_tanggal_absensi, function ($q, $date) {
                    $q->whereDate('date', $date);
                });
                $attendancesQuery->orderBy('date', 'desc');
                break;
            default:
                $attendancesQuery->orderBy('date', 'desc');
                break;
        }

        $attendances = $attendancesQuery->paginate(10, ['*'], 'attendances_page')->appends($request->query());

        $correctionRequestsQuery = CorrectionRequest::where('user_id', $user->id);

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
            default:
                $correctionRequestsQuery->orderBy('created_at', 'desc');
                break;
        }

        $correctionRequests = $correctionRequestsQuery->paginate(10, ['*'], 'corrections_page')->appends($request->query());

        return view('admin.users-show', compact('user', 'attendances', 'correctionRequests'));
    }
}