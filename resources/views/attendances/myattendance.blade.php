@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-[#2C3E50] text-white flex flex-col justify-between shadow-lg">
            <div>
                <div class="p-4 flex items-center justify-center space-x-3 border-b border-[#1F2A36]">
                    <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="w-12 h-12 object-contain" />
                    <div>
                        <p class="text-base font-bold leading-5">PLN</p>
                        <p class="text-xs text-gray-300">UID KALSELTENG</p>
                    </div>
                </div>

                <nav class="mt-6 px-4 space-y-2">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                    </a>

                    <div class="relative">
                        <button onclick="toggleDropdown('attendanceDropdown')"
                            class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('attendance.*') ? 'bg-[#3C5A6D]' : 'hover:bg-[#3C5A6D]' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-user-check"></i> <span>Attendance</span>
                            </div>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div id="attendanceDropdown"
                            class="{{ request()->routeIs('attendance.*') ? 'mt-2 space-y-1 rounded-xl bg-[#34495E] overflow-hidden transition-all' : 'hidden mt-2 space-y-1 rounded-xl bg-[#34495E] overflow-hidden transition-all' }}">
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">History</a>
                            <a href="{{ route('attendance.my') }}"
                                class="block px-6 py-2 text-sm {{ request()->routeIs('attendance.my') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }}">
                                My Attendance
                            </a>
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">Attendance
                                Records</a>
                        </div>
                    </div>

                    <div class="relative">
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

                    <a href="#"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 hover:bg-[#3C5A6D]">
                        <i class="fa-solid fa-cog"></i> <span>Settings</span>
                    </a>

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
        <main class="flex-1 p-4 md:p-6 bg-gray-100">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="text-2xl font-bold text-gray-800">My Attendance</h1>
            </div>

            <!-- Filter & Export -->
            <div class="flex flex-wrap gap-2 mb-6 items-center">
                <button
                    class="bg-[#A74FDE] text-white px-4 py-2 rounded hover:bg-[#913DBD] text-sm border-2 border-black">Export</button>

                <div class="relative">
                    <button onclick="toggleDropdown('filterDropdown')"
                        class="bg-[#3E25FF] text-white px-4 py-2 rounded hover:bg-[#321EC7] text-sm border-2 border-black">
                        Filter
                    </button>

                    <form id="filterDropdown" action="{{ route('attendance.my') }}" method="GET"
                        class="hidden absolute mt-2 bg-[#3E25FF] text-white px-4 py-2 rounded text-sm border-2 border-black rounded p-4 w-64 z-10 space-y-3">

                        <button type="submit" name="sort" value="desc"
                            class="w-full text-left text-sm text-white hover:bg-gray-100 px-2 py-1 rounded">Terbaru</button>

                        <button type="submit" name="sort" value="asc"
                            class="w-full text-left text-sm text-white hover:bg-gray-100 px-2 py-1 rounded">Terlama</button>

                        <div class="flex flex-col">
                            <label class="text-sm text-white mb-1">Pilih Tanggal</label>
                            <input type="date" name="date"
                                class="border text-black border-white rounded px-2 py-1 text-sm"
                                onchange="this.form.submit()" value="{{ request('date') }}" />
                        </div>

                        <!-- Tombol Clear Filter -->
                        <a href="{{ route('attendance.my') }}"
                            class="block text-center mt-2 bg-red-500 hover:bg-red-600 text-white rounded px-2 py-1 text-sm cursor-pointer">
                            Clear Filter
                        </a>
                    </form>


                </div>
            </div>

            <!-- Attendance Table -->

            <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
                <table class="min-w-full text-sm text-left table-auto">
                    <thead class="bg-[#0B849F] text-white uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-4 whitespace-nowrap">No</th>
                            <th class="py-3 px-4 whitespace-nowrap">Date</th>
                            <th class="py-3 px-4 whitespace-nowrap">Check-In</th>
                            <th class="py-3 px-4 whitespace-nowrap">Check-Out</th>
                            <th class="py-3 px-4 whitespace-nowrap w-[40%]">Activity</th>
                            <th class="py-3 px-4 whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y divide-gray-200">
                        @forelse($attendances as $index => $item)
                            <tr class=" align-top border hover:bg-gray-50 transition-all duration-200">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="py-2 px-4">{{ \Carbon\Carbon::parse($item->check_in)->format('H:i') }}</td>
                                <td class="py-2 px-4">{{ \Carbon\Carbon::parse($item->check_out)->format('H:i') }}</td>
                                <td class="py-2 px-4 text-justify">
                                    @if ($item->activity_title)
                                        <p class="font-semibold text-gray-800">{{ $item->activity_title }}</p>
                                        <p class="text-sm">{!! nl2br(e($item->activity_description)) !!}</p>

                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    @if ($item->status === 'on_time')
                                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">On
                                            Time</span>
                                    @elseif($item->status === 'late')
                                        <span
                                            class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">Late</span>
                                    @else
                                        <span
                                            class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">Absent</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-gray-500 italic">No attendance records
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <!-- Scripts -->
            <script>
                function toggleDropdown(id) {
                    const dropdown = document.getElementById(id);
                    dropdown.classList.toggle('hidden');
                }

                // Optional: close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    const filterBtn = document.querySelector('button[onclick*="filterDropdown"]');
                    const filterDropdown = document.getElementById('filterDropdown');

                    if (!filterBtn.contains(e.target) && !filterDropdown.contains(e.target)) {
                        filterDropdown.classList.add('hidden');
                    }
                });
            </script>
        @endsection
