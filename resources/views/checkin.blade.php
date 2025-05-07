@extends('layouts.app')

@section('content')
<div class="relative min-h-screen flex flex-col items-center justify-center bg-white overflow-hidden">
    <!-- Background Blobs -->
    <img src="{{ asset('assets/images/img-blob-yellow.png') }}" alt="Yellow Blob"
        class="absolute top-[-150px] left-[-150px] w-[500px] opacity-90 z-0">
    <img src="{{ asset('assets/images/img-blob-blue.png') }}" alt="Blue Blob"
        class="absolute bottom-[-150px] right-[-150px] w-[550px] opacity-90 z-0">

    <!-- Title Card -->
    <div class="bg-[#0B849F] text-white rounded-2xl py-6 px-12 shadow-md w-[350px] md:w-[500px] mb-4 z-10">
        <h1 class="text-center font-semibold text-xl">Check In Attendance</h1>
    </div>

    <!-- Main Form Card -->
    <div class="bg-[#0B849F] text-white rounded-2xl p-6 shadow-lg w-[350px] md:w-[500px] z-10">
        <form action="{{ route('checkin.store') }}" method="POST" id="checkin-form">
            @csrf

            <!-- Hidden inputs for local time/date -->
            <input type="hidden" name="local_time" id="local_time_input">
            <input type="hidden" name="local_date" id="local_date_input">

            <!-- Time and Date -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-10 mb-8">
                <div class="flex items-center gap-3">
                    <i class="fa-regular fa-clock text-3xl"></i>
                    <p id="current-time" class="text-3xl font-bold">--:--</p>
                </div>
                <div class="hidden sm:block h-12 border-l-2 border-white"></div>
                <div class="flex items-center gap-3">
                    <i class="fa-regular fa-calendar text-3xl"></i>
                    <div class="leading-tight">
                        <p id="current-day" class="text-2xl font-bold">--,</p>
                        <p id="current-date" class="text-xl">-- -- ----</p>
                    </div>
                </div>
            </div>

            <!-- Nama -->
            <div class="mb-6">
                <label class="block text-sm mb-1">Nama</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" readonly
                    class="w-full px-3 py-2 rounded bg-[#3AA7B1] text-white focus:outline-none">
            </div>

            <!-- Daily Activity (Opsional) -->
            <div class="flex items-center justify-center gap-2 mb-6">
                <i class="fa-regular fa-thumbs-up text-lg"></i>
                <span class="text-sm font-semibold">Daily Activity</span>
            </div>

            <!-- Tombol Kirim -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-white text-black px-6 py-2 rounded-md text-sm font-semibold hover:bg-gray-200 transition">
                    Send
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Clock Script -->
<script>
function updateTimeAndDate() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');

    const fullTime = `${hours}:${minutes}:${seconds}`;
    const fullDate = now.toISOString().slice(0, 10); // format: YYYY-MM-DD

    document.getElementById('current-time').textContent = `${hours}:${minutes}`;
    document.getElementById('current-day').textContent = now.toLocaleDateString('en-GB', {
        weekday: 'long'
    }) + ',';
    document.getElementById('current-date').textContent = now.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    document.getElementById('local_time_input').value = fullTime;
    document.getElementById('local_date_input').value = fullDate;
}

updateTimeAndDate();
setInterval(updateTimeAndDate, 60000);

document.getElementById('checkin-form').addEventListener('submit', function() {
    updateTimeAndDate(); // make sure values are fresh
});
</script>
@endsection