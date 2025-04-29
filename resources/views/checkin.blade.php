@extends('layouts.app')

@section('content')
<div class="relative min-h-screen flex flex-col items-center justify-center bg-white overflow-hidden">

    <!-- Background Blob Images -->
    <img src="{{ asset('assets/images/img-blob-yellow.png') }}" alt="Yellow Blob"
        class="absolute top-[-150px] left-[-150px] w-[500px] opacity-90 z-0">
    <img src="{{ asset('assets/images/img-blob-blue.png') }}" alt="Blue Blob"
        class="absolute bottom-[-150px] right-[-150px] w-[550px] opacity-90 z-0">

    <!-- Title -->
    <div class="bg-[#0B849F] text-white rounded-2xl py-4 px-14 shadow-lg mb-8 z-10 w-[350px] md:w-[430px]">
        <h1 class="text-center font-semibold text-xl tracking-wide">Check In Attendance</h1>
    </div>

    <!-- Form Card -->
    <div class="bg-[#0B849F] text-white rounded-2xl p-8 shadow-xl w-[350px] md:w-[430px] z-10">
        <form action="{{ route('checkin.store') }}" method="POST">
            @csrf

            <!-- Jam & Tanggal -->
            <div class="flex justify-between items-center mb-8">
                <!-- Jam -->
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-clock text-2xl"></i>
                    <p id="current-time" class="text-2xl font-bold tracking-wide">--:--</p>
                </div>

                <!-- Garis Pembatas -->
                <div class="h-14 border-l-2 border-white mx-6"></div>

                <!-- Tanggal -->
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-2xl"></i>
                    <p id="current-date" class="text-base font-semibold leading-tight">Loading...</p>
                </div>
            </div>

            <!-- Nama -->
            <div class="mb-6">
                <label class="block text-sm mb-1">Nama</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" readonly
                    class="w-full px-3 py-2 rounded bg-[#3AA7B1] text-white focus:outline-none">
            </div>

            <!-- Daily Activity -->
            <div class="flex items-center justify-center gap-2 mb-6">
                <i class="fa-regular fa-thumbs-up text-2xl"></i>
                <span class="text-sm font-semibold">Daily Activity</span>
            </div>

            <!-- Tombol Send -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-white text-black px-6 py-2 rounded-md text-sm font-semibold hover:bg-gray-200 transition-all">
                    Send
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/2d9ecd9e37.js" crossorigin="anonymous"></script>

<!-- Script Jam & Tanggal Lokal -->
<script>
function updateLocalTimeAndDate() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const day = now.toLocaleDateString('en-GB', {
        weekday: 'long'
    });
    const date = now.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    document.getElementById('current-time').textContent = `${hours}.${minutes}`;
    document.getElementById('current-date').textContent = `${day}, ${date}`;
}

updateLocalTimeAndDate();
setInterval(updateLocalTimeAndDate, 60000);
</script>
@endsection