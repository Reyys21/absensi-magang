<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PDF; // pastikan sudah import alias PDF kalau menggunakan package dompdf

class AttendanceController extends Controller
{
    public function checkinForm()
    {
        return view('checkin');
    }

    public function storeCheckin(Request $request)
    {
        $userId = Auth::id();
        $today = $request->local_date;

        $existingCheckin = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->first();

        if (!$existingCheckin) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'check_in' => $request->local_time,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Check-in berhasil!');
    }

    public function checkoutForm()
    {
        return view('checkout');
    }

    public function storeCheckout(Request $request)
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->orderByDesc('id')
            ->first();

        if ($attendance) {
            $attendance->update([
                'check_out' => $request->current_time,
                'activity_title' => $request->activity_title,
                'activity_description' => $request->activity_description,
            ]);
        } else {
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'check_out' => $request->current_time,
                'activity_title' => $request->activity_title,
                'activity_description' => $request->activity_description,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Check-out berhasil!');
    }

    public function myAttendance(Request $request)
    {
        $query = Attendance::where('user_id', auth()->id());

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $sort = $request->get('sort', 'desc');
        $query->orderBy('date', $sort);

        $attendances = $query->paginate(10);

        return view('attendances.myattendance', compact('attendances'));

    }

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
            $fileName = 'attendance_' . now()->format('Ymd_His') . '.' . $format;
            return Excel::download($export, $fileName);
        } elseif ($format === 'pdf') {
            $pdf = PDF::loadView('attendance.export_pdf', compact('attendances'));
            return $pdf->download('attendance_' . now()->format('Ymd_His') . '.pdf');
        }

        return redirect()->back()->with('error', 'Format export tidak didukung.');
    }
}