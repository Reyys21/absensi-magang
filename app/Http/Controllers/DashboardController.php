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

        // Ambil data absensi dengan pagination 10 per halaman
        $attendances = Attendance::where('user_id', $user->id)
                            ->orderBy('date', 'desc')
                            ->paginate(10);

        // Hitung jumlah hari unik
        $attendanceCount = Attendance::where('user_id', $user->id)
                                ->pluck('date')
                                ->unique()
                                ->count();

        return view('dashboard', compact('attendances', 'attendanceCount'));
    }
}