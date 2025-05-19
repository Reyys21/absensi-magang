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

        $firstCheckIn = $todayAttendances->whereNotNull('check_in')->first()?->check_in;
        $lastCheckOut = $todayAttendances->whereNotNull('check_out')->last()?->check_out;

        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        $attendanceCount = Attendance::where('user_id', $user->id)
            ->pluck('date')
            ->unique()
            ->count();

        return view('dashboard', compact(
            'attendances',
            'attendanceCount',
            'firstCheckIn',
            'lastCheckOut'
        ));
    }
}