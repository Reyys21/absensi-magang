@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white flex items-center justify-center">
    <div
        class="w-full max-w-6xl flex flex-col md:flex-row items-center justify-between px-6 md:px-12 py-12 space-y-8 md:space-y-0">

        <!-- Left Side: Text -->
        <div class="w-full md:w-1/2 space-y-4">
            <h1 class="text-2xl md:text-3xl font-extrabold text-black leading-tight">
                Welcome To Attendance<br>Check-In
            </h1>
            <p class="text-sm md:text-base text-gray-600">
                Just Press the Check-In Button to<br>Fill Your Attendance Today :)
            </p>

            <form action="{{ route('checkin.store') }}" method="POST" id="checkin-form" class="mt-4">
                @csrf
                <input type="hidden" name="local_time" id="local_time_input">
                <input type="hidden" name="local_date" id="local_date_input">
                <input type="hidden" name="name" value="{{ auth()->user()->name }}">

                <button type="submit"
                    class="bg-black text-white px-5 py-2 rounded-md text-sm font-semibold hover:bg-gray-800 transition">
                    Check-In
                </button>
            </form>
        </div>

        <!-- Right Side: Image -->
        <div class="w-full md:w-1/2 flex justify-center">
            <img src="{{ asset('assets/images/undraw_filing-system_e3yo.svg') }}" alt="Check-In Illustration"
                class="w-full max-w-md object-contain">
        </div>
    </div>
</div>

<script>
function updateTimeAndDate() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');

    const fullTime = `${hours}:${minutes}:${seconds}`;
    const fullDate = now.toISOString().slice(0, 10);

    document.getElementById('local_time_input').value = fullTime;
    document.getElementById('local_date_input').value = fullDate;
}

updateTimeAndDate();
setInterval(updateTimeAndDate, 60000);

document.getElementById('checkin-form').addEventListener('submit', function() {
    updateTimeAndDate();
});
</script>
@endsection