@extends('layouts.app')

@section('content')
    {{-- Ini adalah div pembungkus utama untuk sidebar dan konten --}}
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

        <div class="flex-1 flex flex-col">

            <header class="bg-white flex flex-row justify-between items-center py-2 px-4 sm:px-6 md:px-8 border-b border-gray-200 sticky top-0 z-10">
                <div>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Dashboard</h1>
                </div>
                @include('layouts.profile')
            </header>

            {{-- Konten utama dimulai di sini --}}
            <main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50/50">

                {{-- ▼▼▼ KODE GRID DIPERBARUI DI SINI ▼▼▼ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">
                {{-- ▲▲▲ AKHIR PERUBAHAN GRID ▲▲▲ --}}
                    
                    <div class="bg-[#0B849F] text-white rounded-[20px] px-4 sm:px-6 py-5 shadow-lg border flex flex-col justify-between relative overflow-hidden min-h-[340px] md:min-h-0 md:col-span-2">
                        <h2 class="text-white text-base sm:text-lg font-semibold mb-4">Absensi Hari Ini</h2>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex flex-col gap-4 sm:gap-6">
                                <div class="flex flex-col items-start">
                                    <p class="text-3xl sm:text-4xl font-bold leading-none pl-1 sm:pl-2">
                                        {{ $firstCheckIn ? \Carbon\Carbon::parse($firstCheckIn)->format('H.i') : '--.--' }}
                                    </p>
                                    <form action="{{ route('checkin.form') }}" method="GET">
                                        <button type="submit"
                                            class="bg-black text-white px-5 py-1.5 mt-1 rounded-lg text-sm font-semibold shadow hover:bg-gray-800">Check-In</button>
                                    </form>
                                </div>
                                <div class="flex flex-col items-start">
                                    <p class="text-3xl sm:text-4xl font-bold leading-none pl-1 sm:pl-2">
                                        {{ $lastCheckOut ? \Carbon\Carbon::parse($lastCheckOut)->format('H.i') : '--.--' }}
                                    </p>
                                    <form action="{{ route('checkout.form') }}" method="GET">
                                        <button type="submit"
                                            class="bg-black text-white px-5 py-1.5 mt-1 rounded-lg text-sm font-semibold shadow hover:bg-gray-800">Check-Out</button>
                                    </form>
                                </div>
                            </div>
                             <div class="block absolute bottom-0 right-0 w-[45%] md:w-[50%] lg:w-[40%] animate-bounce-slow">
                                <img src="{{ asset('assets/images/undraw_relaxed-reading_wfkr.svg') }}" alt="Membaca"
                                    class="w-full" style="transform: scaleX(-1) ">
                            </div>
                        </div>
                    </div>

                    {{-- ▼▼▼ BLOK KARTU BARU DITAMBAHKAN DI SINI ▼▼▼ --}}
                    <div class="grid grid-rows-2 gap-4 sm:gap-6">
                        <div class="bg-[#14BDEB] text-white rounded-[20px] p-4 sm:p-6 shadow-lg flex flex-col items-center justify-center text-center">
                            <h2 class="text-base font-semibold mb-2">Bidang Penempatan</h2>
                            <p class="text-xl font-bold">{{ Auth::user()->bidang->name ?? 'Belum Ditugaskan' }}</p>
                        </div>
                        <div class="bg-[#FFD100] text-[#2A2B2A] rounded-[20px] p-4 sm:p-6 shadow-lg flex flex-col items-center justify-center text-center">
                            <a class="flex flex-col items-center justify-center w-full h-full" href="{{ route('attendance.my') }}">
                                <h2 class="text-base font-semibold mb-2">Total Kehadiran</h2>
                                <p class="text-3xl font-bold">{{ $attendanceCount }} hari</p>
                            </a>
                        </div>
                    </div>
                    {{-- ▲▲▲ AKHIR BLOK KARTU BARU ▲▲▲ --}}
                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- JavaScript tambahan yang spesifik untuk halaman ini --}}
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const clockElement = document.getElementById("clock");
            if (clockElement) {
                clockElement.textContent = `${hours}.${minutes}`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }
    </script>
    
    {{-- Gaya CSS tambahan yang spesifik untuk halaman ini --}}
    <style>
        @keyframes bounce-slow {
            0%, 100% {
                transform: translateY(0) scaleX(1);
            }
            50% {
                transform: translateY(-10px) scaleX(1);
            }
        }
        .animate-bounce-slow {
            animation: bounce-slow 3s infinite;
        }
    </style>
@endpush