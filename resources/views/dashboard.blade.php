@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-[#2C3E50] text-white flex flex-col justify-between shadow-lg">
            <div>
                <!-- Logo -->
                <div class="p-4 flex items-center justify-center space-x-3 border-b border-[#1F2A36]">
                    <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="w-12 h-12 object-contain" />
                    <div>
                        <p class="text-base font-bold leading-5">PLN</p>
                        <p class="text-xs text-gray-300">UID KALSELTENG</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-6 px-4 space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 
                {{ request()->routeIs('dashboard') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                    </a>

                    <!-- Attendance -->
                    <div class="relative group">
                        <button onclick="toggleDropdown('attendanceDropdown')"
                            class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 hover:bg-[#3C5A6D]">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-user-check"></i> <span>Attendance</span>
                            </div>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div id="attendanceDropdown"
                            class="hidden mt-2 space-y-1 rounded-xl bg-[#34495E] overflow-hidden transition-all">
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">History</a>
                            <a href="#" class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">My
                                Attendance</a>
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">Attendance
                                Records</a>
                        </div>
                    </div>

                    <!-- Approval -->
                    <div class="relative group">
                        <button onclick="toggleDropdown('approvalDropdown')"
                            class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 hover:bg-[#3C5A6D]">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-thumbs-up"></i> <span>Approval</span>
                            </div>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div id="approvalDropdown"
                            class="hidden mt-2 space-y-1 rounded-xl bg-[#34495E] overflow-hidden transition-all">
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">Attendance
                                Approval</a>
                        </div>
                    </div>

                    <!-- Settings -->
                    <a href="#"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 hover:bg-[#3C5A6D]">
                        <i class="fa-solid fa-cog"></i> <span>Settings</span>
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-sm transition duration-150 hover:bg-[#3C5A6D]">
                            <i class="fa-solid fa-right-from-bracket"></i> <span>Log Out</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>


        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 md:p-10 bg-white shadow-lg">
            <h1 class="text-xl sm:text-2xl font-bold mb-6">Dashboard Mahasiswa/Siswa</h1>

            <!-- Attendance Summary Cards -->
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
                                class="w-full" style="transform: scaleX(-1) ">
                        </div>
                    </div>
                </div>

                <!-- My Attendance Card -->
                <div
                    class="bg-[#0B849F] text-white rounded-[20px] p-4 sm:p-6 shadow-lg flex flex-col items-center justify-center">
                    <h2 class="text-base font-semibold mb-2">My Attendance</h2>
                    <p class="text-3xl font-bold">{{ $attendanceCount }} days</p>
                </div>
            </div>

            <!-- Attendance Table -->
            <section>
                <h2 class="text-xl font-bold mb-4">Attendance Records</h2>
                <div class="overflow-auto max-h-[450px] rounded-xl">
                    <table class="w-full text-sm text-left table-auto border border-gray-300">
                        <thead class="bg-[#0B849F] text-white">
                            <tr>
                                <th class="px-4 py-2 border border-gray-300 text-center">No</th>
                                <th class="px-4 py-2 border border-gray-300 text-center">Date</th>
                                <th class="px-4 py-2 border border-gray-300 text-center">Check-In</th>
                                <th class="px-4 py-2 border border-gray-300 text-center">Check-Out</th>
                                <th class="px-4 py-2 border border-gray-300 text-center">Activity</th>
                            </tr>
                        </thead>
                        <tbody class="bg-[#13B4D8] text-gray-800">
                            @forelse ($attendances as $item)
                                <tr class="align-top border border-gray-300">
                                    <td class="px-4 py-2 border border-gray-300 text-center">
                                        {{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-2 border border-gray-300 text-center">{{ $item->date }}</td>
                                    <td class="px-4 py-2 border border-gray-300 text-center">
                                        {{ $item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('H.i') : '--.--' }}
                                    </td>
                                    <td class="px-4 py-2 border border-gray-300 text-center">
                                        {{ $item->check_out ? \Carbon\Carbon::parse($item->check_out)->format('H.i') : '--.--' }}
                                    </td>
                                    <td class="px-4 py-2 border border-gray-300 text-justify">
                                        @if ($item->activity_title)
                                            <p><strong>{{ $item->activity_title }}</strong></p>
                                            <p class="text-sm">{{ $item->activity_description }}</p>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 border border-gray-300">Belum ada data
                                        absensi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                <div class="mt-6 flex justify-center space-x-2">
                    {{ $attendances->onEachSide(1)->links('pagination::tailwind') }}
                </div>
                <!-- Footer -->
                <div class="text-center text-xs text-blue-500 mt-10">
                    by <a href="#" class="underline">PKL TRKJ POLITALA</a>
                </div>
            </section>
        </main>
    </div>

    <!-- Local Time Script -->
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
