<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $todayAttendances = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->orderBy('created_at')
            ->get();

        // Logika untuk mengambil check_in terawal dan check_out terakhir sudah benar.
        $firstCheckIn = $todayAttendances->whereNotNull('check_in')->first()?->check_in;
        $lastCheckOut = $todayAttendances->whereNotNull('check_out')->last()?->check_out;

        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        // REVISI: Hitung hanya hari dimana absensi sudah lengkap
        $attendanceCount = Attendance::where('user_id', $user->id)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->distinct('date')
            ->count();

        return view('fold_dashboard.dashboard', compact(
            'attendances',
            'attendanceCount',
            'firstCheckIn',
            'lastCheckOut'
        ));
    }
}