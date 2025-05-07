@extends('layouts.app')

@section('content')
<div class="relative min-h-screen flex flex-col items-center justify-center bg-white overflow-hidden">
    <!-- Background Blob Images -->
    <img src="{{ asset('assets/images/img-blob-yellow.png') }}" alt="Yellow Blob"
        class="absolute top-[-140px] left-[-140px] w-[500px] opacity-90 z-0">
    <img src="{{ asset('assets/images/img-blob-blue.png') }}" alt="Blue Blob"
        class="absolute bottom-[-150px] right-[-130px] w-[520px] opacity-90 z-0">

    <!-- Combined Title + Clock & Date Card -->
    <div class="bg-[#0B849F] text-white rounded-xl px-8 py-6 shadow-md mb-6 z-10 w-[360px] md:w-[600px]">
        <!-- Title -->
        <h1 class="text-center text-3xl font-semibold mb-4">Check Out Attendance</h1>

        <!-- Clock & Date Row -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-10">
            <!-- Clock -->
            <div class="flex items-center gap-3">
                <i class="fa-regular fa-clock text-3xl"></i>
                <p id="current-time" class="text-3xl font-bold">--:--</p>
            </div>

            <!-- Divider -->
            <div class="hidden sm:block h-12 border-l-2 border-white"></div>

            <!-- Calendar -->
            <div class="flex items-center gap-3">
                <i class="fa-regular fa-calendar text-3xl"></i>
                <div class="leading-tight">
                    <p id="current-day" class="text-2xl font-bold">Monday,</p>
                    <p id="current-date" class="text-xl">-- -- ----</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Activity Card -->
    <div class="bg-[#0B849F] text-white rounded-xl px-8 py-6 shadow-lg z-10 w-[360px] md:w-[600px]">
        <h2 class="text-lg font-bold mb-1">Daily Activity</h2>
        <p class="text-sm text-[#B0DDE4] mb-4">Silahkan isi kegiatan anda hari ini disini.</p>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            <!-- Hidden input untuk waktu lokal -->
            <input type="hidden" id="current_time" name="current_time">

            <!-- Judul Aktivitas -->
            <div class="mb-4">
                <label for="activity_title" class="block font-semibold mb-1">Judul Aktivitas</label>
                <input type="text" name="activity_title" id="activity_title" required
                    class="w-full px-3 py-2 rounded bg-[#3AA7B1] text-white focus:outline-none">
            </div>

            <!-- Deskripsi -->
            <div class="mb-6">
                <label for="activity_description" class="block font-semibold mb-1">Deskripsi Singkat</label>
                <textarea name="activity_description" id="activity_description" rows="4" required
                    class="w-full px-3 py-2 rounded bg-[#3AA7B1] text-white focus:outline-none resize-none"></textarea>
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-white text-black px-6 py-2 rounded-md font-semibold hover:bg-gray-200 transition-all">
                    Send
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script Jam & Tanggal Lokal -->
<script>
function pad(num) {
    return num < 10 ? '0' + num : num;
}

function updateTimeAndDate() {
    const now = new Date();
    const hours = pad(now.getHours());
    const minutes = pad(now.getMinutes());
    const day = now.toLocaleDateString('en-GB', {
        weekday: 'long'
    });
    const date = now.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    document.getElementById('current-time').textContent = `${hours}:${minutes}`;
    document.getElementById('current-day').textContent = `${day},`;
    document.getElementById('current-date').textContent = date;
}

updateTimeAndDate();
setInterval(updateTimeAndDate, 60000);

// Set waktu saat tombol diklik (bukan saat halaman dimuat)
document.querySelector('form').addEventListener('submit', function() {
    const now = new Date();
    const hours = pad(now.getHours());
    const minutes = pad(now.getMinutes());
    const seconds = pad(now.getSeconds());
    document.getElementById('current_time').value = `${hours}:${minutes}:${seconds}`;
});
</script>
@endsection