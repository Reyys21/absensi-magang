<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminDashboard(Request $request)
    {
        // === 1. Logika Filter Rentang Tanggal (Date Range) ===
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

        // === 2. Data untuk KPI Cards dengan Tren ===
        $totalUsers = User::whereHas('roles', function($q){ $q->where('name', 'user'); })->count();
        $pendingCorrections = CorrectionRequest::where('status', 'pending')->count();
        $activeInRange = Attendance::whereBetween('date', [$startDate, $endDate])->distinct('user_id')->count();
        $completedInRange = Attendance::whereBetween('date', [$startDate, $endDate])->whereNotNull('check_in')->whereNotNull('check_out')->count();
        $activePreviousRange = Attendance::whereBetween('date', [$comparisonStartDate, $comparisonEndDate])->distinct('user_id')->count();
        $calculateTrend = function($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return (($current - $previous) / $previous) * 100;
        };
        $activeTrend = $calculateTrend($activeInRange, $activePreviousRange);

        // === 3. Data untuk Grafik Komposisi Pengguna (Donut Chart) ===
        $userComposition = User::whereHas('roles', function($q){ $q->where('name', 'user'); })
            ->select('role', DB::raw('count(*) as total'))->groupBy('role')->pluck('total', 'role');
        $compositionData = [
            'labels' => $userComposition->keys()->map(fn($role) => ucfirst($role))->toArray(),
            'series' => $userComposition->values()->toArray(),
        ];
        
        // === 4. Data untuk Grafik Aktivitas (Stacked Bar Chart) ===
        $attendanceTrend = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->select(DB::raw('DATE(attendances.date) as attendance_date'), 'users.role', DB::raw('count(DISTINCT attendances.user_id) as total'))
            ->groupBy('attendance_date', 'users.role')
            ->orderBy('attendance_date', 'asc')
            ->get();

        $dateRange = collect(); // Membuat Laravel Collection
        $trendData = ['labels' => [], 'mahasiswa' => [], 'siswa' => []];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();
            // Inisialisasi collection dengan data awal
            $dateRange->put($dateString, ['mahasiswa' => 0, 'siswa' => 0]);
            $trendData['labels'][] = $date->translatedFormat('d M');
        }

        // =======================================================
        // === PERBAIKAN LOGIKA ADA DI SINI ===
        // =======================================================
        foreach ($attendanceTrend as $trend) {
            $dateKey = $trend->attendance_date;

            // Gunakan method has() untuk memeriksa kunci
            if ($dateRange->has($dateKey)) {
                // 1. Ambil data array yang ada ke variabel sementara
                $dailyData = $dateRange->get($dateKey);
                
                // 2. Ubah data di variabel sementara tersebut
                $dailyData[$trend->role] = $trend->total;
                
                // 3. Masukkan kembali array yang sudah diubah ke dalam collection
                $dateRange->put($dateKey, $dailyData);
            }
        }
        // =======================================================
        // === AKHIR DARI PERBAIKAN ===
        // =======================================================

        foreach ($dateRange as $data) {
            $trendData['mahasiswa'][] = $data['mahasiswa'];
            $trendData['siswa'][] = $data['siswa'];
        }

        // === 5. Data untuk Tabel Permintaan Koreksi Terbaru ===
        $latestCorrections = CorrectionRequest::with('user')->where('status', 'pending')->latest()->take(5)->get();

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
        ]);
    }


    public function superadminDashboard()
    {
        return view('superadmin.dashboard');
    }
}