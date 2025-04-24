<?php


namespace App\Http\Controllers; use Illuminate\Http\Request; use App\Models\Attendance; use
    Illuminate\Support\Facades\Auth; use Carbon\Carbon; class AttendanceController extends Controller { public function
    checkinForm() { return view('checkin'); } public function storeCheckin(Request $request) { $today=Carbon::today()->
    toDateString();
    $now = Carbon::now()->toTimeString();

    Attendance::updateOrCreate(
    ['user_id' => Auth::id(), 'date' => $today],
    ['check_in' => $now]
    );

    return redirect()->route('dashboard')->with('success', 'Check-in berhasil!');
    }

    public function checkoutForm()
    {
    return view('checkout');
    }

    public function storeCheckout(Request $request)
    {
    $request->validate([
    'activity_title' => 'required|string|max:255',
    'activity_description' => 'required|string'
    ]);

    $today = Carbon::today()->toDateString();
    $now = Carbon::now()->toTimeString();

    Attendance::where([
    'user_id' => Auth::id(),
    'date' => $today
    ])->update([
    'check_out' => $now,
    'activity_title' => $request->activity_title,
    'activity_description' => $request->activity_description
    ]);

    return redirect()->route('dashboard')->with('success', 'Check-out berhasil!');
    }
    }