{{-- dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

        {{-- sidebar --}}
        @include('layouts.sidebar')

        <main class="flex-1 p-4 sm:p-6 md:p-10 bg-white shadow-lg">
            {{-- Flex container untuk judul dan profil --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold">Dashboard Mahasiswa/Siswa</h1>
                {{-- Ini dia! Sertakan komponen profil di sini --}}
                @include('layouts.profile')
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <div
                    class="bg-[#0B849F] text-white rounded-[20px] px-4 sm:px-6 py-5 shadow-lg border flex flex-col justify-between md:col-span-2 relative overflow-hidden">
                    <h2 class="text-white text-base sm:text-lg font-semibold mb-4">Today’s Attendance</h2>
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex flex-col gap-4 sm:gap-6">
                            <div class="flex flex-col items-start">
                                <p class="text-3xl sm:text-4xl font-bold leading-none pl-1 sm:pl-2">
                                    {{ $firstCheckIn ? \Carbon\Carbon::parse($firstCheckIn)->format('H.i') : '--.--' }}</p>
                                <form action="{{ route('checkin.form') }}" method="GET">
                                    <button type="submit"
                                        class="bg-black text-white px-5 py-1.5 mt-1 rounded-lg text-sm font-semibold shadow hover:bg-gray-800">Check-In</button>
                                </form>
                            </div>
                            <div class="flex flex-col items-start">
                                <p class="text-3xl sm:text-4xl font-bold leading-none pl-1 sm:pl-2">
                                    {{ $lastCheckOut ? \Carbon\Carbon::parse($lastCheckOut)->format('H.i') : '--.--' }}</p>
                                <form action="{{ route('checkout.form') }}" method="GET">
                                    <button type="submit"
                                        class="bg-black text-white px-5 py-1.5 mt-1 rounded-lg text-sm font-semibold shadow hover:bg-gray-800">Check-Out</button>
                                </form>
                            </div>
                        </div>
                        <div class="block absolute bottom-0 right-0 w-[45%] md:w-[30%] animate-bounce-slow">
                            <img src="{{ asset('assets/images/undraw_relaxed-reading_wfkr.svg') }}" alt="Reading"
                                class="w-full" style="transform: scaleX(1) ">
                        </div>
                    </div>
                </div>

                <div
                    class="bg-[#0B849F] text-white rounded-[20px] p-4 sm:p-6 shadow-lg flex flex-col items-center justify-center text-center">
                    <a class="flex flex-col items-center justify-center w-full h-full" href="{{ route('attendance.my') }}">
                        <h2 class="text-base font-semibold mb-2">My Attendance</h2>
                        <p class="text-3xl font-bold">{{ $attendanceCount }} days</p>
                    </a>
                </div>
            </div>


        </main>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById("clock").textContent = `${hours}.${minutes}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('hidden');
        }
    </script>

    <style>
        #attendanceDropdown,
        #approvalDropdown {
            transition: all 0.3s ease;
        }

        @keyframes bounce-slow {

            0%,
            100% {
                transform: translateY(0) scaleX(-1);
            }

            50% {
                transform: translateY(-10px) scaleX(-1);
            }
        }

        .animate-bounce-slow {
            animation: bounce-slow 3s infinite;
        }

        /* Custom Responsive Illustration Fixes */
        @media only screen and (min-width: 320px) {
            .reading-illustration {
                display: block;
                position: relative;
                width: 70%;
                margin: 1rem auto 0 auto;
            }
        }

        @media only screen and (min-width: 480px) {
            .reading-illustration {
                width: 60%;
            }
        }

        @media only screen and (min-width: 768px) {
            .reading-illustration {
                width: 50%;
                position: absolute;
                right: 0;
                bottom: 0;
            }
        }

        @media only screen and (min-width: 992px) {
            .reading-illustration {
                width: 35%;
            }
        }

        @media only screen and (min-width: 1200px) {
            .reading-illustration {
                width: 30%;
            }
        }
    </style>
@endsection