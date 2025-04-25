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
$attendances = Attendance::where('user_id', $user->id)->orderBy('date', 'desc')->get();

// hitung jumlah hari absensi unik
$attendanceCount = $attendances->pluck('date')->unique()->count();

return view('dashboard', compact('attendances', 'attendanceCount'));
}
}