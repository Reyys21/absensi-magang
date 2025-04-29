<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkinForm()
    {
        return view('checkin');
    }

    public function storeCheckin(Request $request)
    {
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $now = Carbon::now('Asia/Jakarta')->toTimeString();

        $attendance = Attendance::where('user_id', Auth::id())->where('date', $today)->first();

        if ($attendance) {
            // Kalau sudah Check-In hari ini, larang Check-In lagi
            return redirect()->route('dashboard')->with('error', 'Kamu sudah Check-In hari ini!');
        }

        // Kalau belum ada, buat baru
        Attendance::create([
            'user_id' => Auth::id(),
            'date' => $today,
            'check_in' => $now,
        ]);

        return redirect()->route('dashboard')->with('success', 'Check-In berhasil!');
    }

    public function checkoutForm()
    {
        $today = Carbon::now('Asia/Jakarta')->toDateString();

        $attendance = Attendance::where('user_id', Auth::id())->where('date', $today)->first();

        if (!$attendance || !$attendance->check_in) {
            // Kalau belum Check-In hari ini, tidak bisa Check-Out
            return redirect()->route('dashboard')->with('error', 'Kamu belum Check-In hari ini!');
        }

        if ($attendance->check_out) {
            // Kalau sudah Check-Out, tidak bisa lagi
            return redirect()->route('dashboard')->with('error', 'Kamu sudah Check-Out hari ini!');
        }

        return view('checkout');
    }

    public function storeCheckout(Request $request)
    {
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $now = Carbon::now('Asia/Jakarta')->toTimeString();

        $attendance = Attendance::where('user_id', Auth::id())->where('date', $today)->first();

        if (!$attendance || !$attendance->check_in) {
            return redirect()->route('dashboard')->with('error', 'Kamu belum Check-In hari ini!');
        }

        if ($attendance->check_out) {
            return redirect()->route('dashboard')->with('error', 'Kamu sudah Check-Out hari ini!');
        }

        $request->validate([
            'activity_title' => 'required|string|max:255',
            'activity_description' => 'required|string',
        ]);

        $attendance->update([
            'check_out' => $now,
            'activity_title' => $request->activity_title,
            'activity_description' => $request->activity_description,
        ]);

        return redirect()->route('dashboard')->with('success', 'Check-Out berhasil!');
    }
}