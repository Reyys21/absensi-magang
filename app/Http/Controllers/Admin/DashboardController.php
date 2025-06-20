<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use App\Models\Bidang; // Import model Bidang
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function adminDashboard(Request $request)
    {
        $adminBidangId = Auth::user()->bidang_id;

        $filter = $request->input('filter', 'last_7_days');
        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay();
        $comparisonStartDate = $startDate->copy()->subDay();
        $comparisonEndDate = $endDate->copy()->subDay();

        switch ($filter) {
            case 'today':
                break;
            case 'last_7_days':
                $startDate = Carbon::today()->subDays(6)->startOfDay();
                $comparisonStartDate = $startDate->copy()->subDays(7);
                $comparisonEndDate = $endDate->copy()->subDays(7);
                break;
            case 'this_month':
                $startDate = Carbon::today()->startOfMonth();
                $comparisonStartDate = $startDate->copy()->subMonth();
                $comparisonEndDate = $endDate->copy()->subMonth();
                break;
            case 'custom':
                if ($request->has(['start_date', 'end_date'])) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                    $duration = $endDate->diffInDays($startDate);
                    $comparisonStartDate = $startDate->copy()->subDays($duration + 1);
                    $comparisonEndDate = $endDate->copy()->subDays($duration + 1);
                }
                break;
        }

        // Data untuk KPI Cards dengan Tren (DIBERI SCOPE)
        $totalUsers = User::whereHas('roles', function($q){ $q->where('name', 'user'); })
                          ->where('bidang_id', $adminBidangId) // SCOPE
                          ->count();
                          
        $pendingCorrections = CorrectionRequest::where('status', 'pending')
                                                ->whereHas('user', function ($q) use ($adminBidangId) {
                                                    $q->where('bidang_id', $adminBidangId); // SCOPE
                                                })
                                                ->count();

        $baseAttendanceQuery = Attendance::whereHas('user', function($q) use ($adminBidangId) {
            $q->where('bidang_id', $adminBidangId); // SCOPE
        });

        $activeInRange = (clone $baseAttendanceQuery)->whereBetween('date', [$startDate, $endDate])->distinct('user_id')->count();
        $completedInRange = (clone $baseAttendanceQuery)->whereBetween('date', [$startDate, $endDate])->whereNotNull('check_in')->whereNotNull('check_out')->count();
        $activePreviousRange = (clone $baseAttendanceQuery)->whereBetween('date', [$comparisonStartDate, $comparisonEndDate])->distinct('user_id')->count();
        
        $calculateTrend = function($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return (($current - $previous) / $previous) * 100;
        };
        $activeTrend = $calculateTrend($activeInRange, $activePreviousRange);

        // Data untuk Grafik Komposisi Pengguna (DIBERI SCOPE)
        $userComposition = User::whereHas('roles', function($q){ $q->where('name', 'user'); })
            ->where('bidang_id', $adminBidangId) // SCOPE
            ->select('role', DB::raw('count(*) as total'))->groupBy('role')->pluck('total', 'role');
        
        $compositionData = [
            'labels' => $userComposition->keys()->map(fn($role) => ucfirst($role))->toArray(),
            'series' => $userComposition->values()->toArray(),
        ];
        
        // Data untuk Grafik Aktivitas (DIBERI SCOPE)
        $attendanceQuery = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->where('users.bidang_id', $adminBidangId); // SCOPE

        $roleFilter = $request->input('role_filter');
        $attendanceQuery->when($roleFilter, function ($q, $role) {
            $q->where('users.role', $role);
        });

        $attendanceTrend = $attendanceQuery->select(DB::raw('DATE(attendances.date) as attendance_date'), 'users.role', DB::raw('count(DISTINCT attendances.user_id) as total'))
            ->groupBy('attendance_date', 'users.role')
            ->orderBy('attendance_date', 'asc')
            ->get();
            
        $dateRange = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateRange->put($date->toDateString(), ['mahasiswa' => 0, 'siswa' => 0]);
        }

        foreach ($attendanceTrend as $trend) {
            $dateKey = $trend->attendance_date;
            if ($dateRange->has($dateKey)) {
                $dailyData = $dateRange->get($dateKey);
                $dailyData[$trend->role] = $trend->total;
                $dateRange->put($dateKey, $dailyData);
            }
        }

        $trendData = ['labels' => [], 'mahasiswa' => [], 'siswa' => []];
        foreach ($dateRange as $date => $data) {
            $trendData['labels'][] = Carbon::parse($date)->translatedFormat('d M');
            $trendData['mahasiswa'][] = $data['mahasiswa'];
            $trendData['siswa'][] = $data['siswa'];
        }

        // Data untuk Tabel Permintaan Koreksi Terbaru (DIBERI SCOPE)
        $latestCorrections = CorrectionRequest::with('user')->where('status', 'pending')
            ->whereHas('user', function($q) use ($adminBidangId) {
                $q->where('bidang_id', $adminBidangId); // SCOPE
            })
            ->latest()->take(5)->get();

        // Tidak mengirim 'hideSidebar' untuk Admin Dashboard
        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'activeInRange' => $activeInRange,
            'completedInRange' => $completedInRange,
            'pendingCorrections' => $pendingCorrections,
            'activeTrend' => $activeTrend,
            'compositionData' => $compositionData,
            'trendData' => $trendData,
            'latestCorrections' => $latestCorrections,
            'currentFilter' => $filter,
            'currentRoleFilter' => $roleFilter,
        ]);
    }

    public function superadminDashboard(Request $request) // Tambahkan Request $request
    {
        // Variabel untuk menyembunyikan sidebar di tampilan superadmin dashboard
        $hideSidebar = true; //

        $filter = $request->input('filter', 'last_7_days');
        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay();
        $comparisonStartDate = $startDate->copy()->subDay();
        $comparisonEndDate = $endDate->copy()->subDay();

        switch ($filter) {
            case 'today':
                break;
            case 'last_7_days':
                $startDate = Carbon::today()->subDays(6)->startOfDay();
                $comparisonStartDate = $startDate->copy()->subDays(7);
                $comparisonEndDate = $endDate->copy()->subDays(7);
                break;
            case 'this_month':
                $startDate = Carbon::today()->startOfMonth();
                $comparisonStartDate = $startDate->copy()->subMonth();
                $comparisonEndDate = $endDate->copy()->subMonth();
                break;
            case 'custom':
                if ($request->has(['start_date', 'end_date'])) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                    $duration = $endDate->diffInDays($startDate);
                    $comparisonStartDate = $startDate->copy()->subDays($duration + 1);
                    $comparisonEndDate = $endDate->copy()->subDays($duration + 1);
                }
                break;
        }

        // Data untuk KPI Cards dengan Tren (Tanpa Scope Bidang)
        $totalUsers = User::whereHas('roles', function($q){ $q->where('name', 'user'); })
                          ->count(); // Tanpa scope bidang
                          
        $pendingCorrections = CorrectionRequest::where('status', 'pending')->count(); // Tanpa scope bidang

        $baseAttendanceQuery = Attendance::query(); // Query dasar tanpa scope bidang

        $activeInRange = (clone $baseAttendanceQuery)->whereBetween('date', [$startDate, $endDate])->distinct('user_id')->count();
        $completedInRange = (clone $baseAttendanceQuery)->whereBetween('date', [$startDate, $endDate])->whereNotNull('check_in')->whereNotNull('check_out')->count();
        $activePreviousRange = (clone $baseAttendanceQuery)->whereBetween('date', [$comparisonStartDate, $comparisonEndDate])->distinct('user_id')->count();
        
        $calculateTrend = function($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return (($current - $previous) / $previous) * 100;
        };
        $activeTrend = $calculateTrend($activeInRange, $activePreviousRange);

        // Data untuk Grafik Komposisi Pengguna (Tanpa Scope Bidang)
        $userComposition = User::whereHas('roles', function($q){ $q->where('name', 'user'); })
            ->select('role', DB::raw('count(*) as total'))->groupBy('role')->pluck('total', 'role');
        
        $compositionData = [
            'labels' => $userComposition->keys()->map(fn($role) => ucfirst($role))->toArray(),
            'series' => $userComposition->values()->toArray(),
        ];
        
        // Data untuk Grafik Aktivitas (dengan filter bidang opsional untuk superadmin)
        $attendanceQuery = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->whereBetween('attendances.date', [$startDate, $endDate]);

        $roleFilter = $request->input('role_filter');
        $attendanceQuery->when($roleFilter, function ($q, $role) {
            $q->where('users.role', $role);
        });

        // Filter bidang untuk superadmin
        $bidangFilter = $request->input('bidang_filter');
        $attendanceQuery->when($bidangFilter, function ($q, $bidangId) {
            $q->where('users.bidang_id', $bidangId);
        });

        $attendanceTrend = $attendanceQuery->select(DB::raw('DATE(attendances.date) as attendance_date'), 'users.role', DB::raw('count(DISTINCT attendances.user_id) as total'))
            ->groupBy('attendance_date', 'users.role')
            ->orderBy('attendance_date', 'asc')
            ->get();
            
        $dateRange = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateRange->put($date->toDateString(), ['mahasiswa' => 0, 'siswa' => 0]);
        }

        foreach ($attendanceTrend as $trend) {
            $dateKey = $trend->attendance_date;
            if ($dateRange->has($dateKey)) {
                $dailyData = $dateRange->get($dateKey);
                $dailyData[$trend->role] = $trend->total;
                $dateRange->put($dateKey, $dailyData);
            }
        }

        $trendData = ['labels' => [], 'mahasiswa' => [], 'siswa' => []];
        foreach ($dateRange as $date => $data) {
            $trendData['labels'][] = Carbon::parse($date)->translatedFormat('d M');
            $trendData['mahasiswa'][] = $data['mahasiswa'];
            $trendData['siswa'][] = $data['siswa'];
        }

        // Data untuk Tabel Permintaan Koreksi Terbaru (Tanpa Scope Bidang)
        $latestCorrections = CorrectionRequest::with(['user', 'user.bidang']) // Load bidang user juga
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get(); // Tanpa scope bidang

        // Ambil semua bidang untuk filter
        $bidangs = Bidang::orderBy('name')->get(); //

        return view('superadmin.dashboard', [
            'totalUsers' => $totalUsers,
            'activeInRange' => $activeInRange,
            'completedInRange' => $completedInRange,
            'pendingCorrections' => $pendingCorrections,
            'activeTrend' => $activeTrend,
            'compositionData' => $compositionData,
            'trendData' => $trendData,
            'latestCorrections' => $latestCorrections,
            'currentFilter' => $filter,
            'currentRoleFilter' => $roleFilter,
            'hideSidebar' => $hideSidebar, // Kirim variabel ini
            'bidangs' => $bidangs, // Kirim daftar bidang
            'selectedBidangFilter' => $bidangFilter // Kirim bidang yang dipilih
        ]);
    }
}