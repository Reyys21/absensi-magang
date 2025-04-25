@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row min-h-screen">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-[#2C3E50] text-white flex flex-col justify-between">
        <div>
            <div class="p-4 flex items-center space-x-2">
                <span class="text-yellow-400 text-xl">⚡</span>
                <div>
                    <p class="text-sm font-bold leading-4">PLN</p>
                    <p class="text-xs">UID KALSELTENG</p>
                </div>
            </div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('dashboard') }}">
                    <button class="w-full bg-[#FFD100] text-black py-2 rounded font-semibold">Dashboard</button>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-white">Log Out</button>
                </form>
            </nav>
        </div>
        <div class="p-4 text-xs text-blue-300">
            <a href="{{ route('helpdesk') }}" class="underline">Helpdesk</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-5 sm:p-8 md:p-10 bg-white rounded-t-3xl md:rounded-l-3xl shadow-lg">
        <h1 class="text-2xl font-bold mb-6">Welcome, Mahasiswa/Siswa Magang!</h1>

        <!-- Attendance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            <!-- Today’s Attendance Card -->
            <div
                class="bg-[#0B849F] text-white rounded-2xl p-6 shadow-md border border-blue-300 flex flex-col justify-between md:col-span-2">
                <h2 class="text-base font-semibold text-center mb-4">Today’s Attendance</h2>

                <div class="flex flex-col sm:flex-row justify-center items-center gap-10 sm:gap-20">
                    <!-- Check-In Time + Button -->
                    <div class="text-center flex flex-col items-center space-y-2">
                        <p class="text-3xl font-bold">
                            {{ \Carbon\Carbon::parse($attendances->where('date', now()->toDateString())->first()?->check_in)->format('H.i') ?? '--.--' }}
                        </p>
                        <form action="{{ route('checkin.form') }}" method="GET">
                            <button @if ($attendances->where('date', now()->toDateString())->first()?->check_in)
                                disabled @endif
                                class="bg-green-500 text-white px-6 py-1.5 rounded-md shadow hover:bg-green-600
                                disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium">
                                Check-In
                            </button>
                        </form>
                    </div>

                    <!-- Check-Out Time + Button -->
                    <div class="text-center flex flex-col items-center space-y-2">
                        <p class="text-3xl font-bold">
                            {{ \Carbon\Carbon::parse($attendances->where('date', now()->toDateString())->first()?->check_out)->format('H.i') ?? '--.--' }}
                        </p>
                        <form action="{{ route('checkout.form') }}" method="GET">
                            <button @if ($attendances->where('date', now()->toDateString())->first()?->check_out)
                                disabled @endif
                                class="bg-yellow-400 text-black px-6 py-1.5 rounded-md shadow hover:bg-yellow-500
                                disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium">
                                Check-Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>


            <!-- My Attendance Card -->
            <div class="bg-[#0B849F] text-white rounded-2xl p-6 shadow-md flex flex-col items-center justify-center">
                <h2 class="text-base font-semibold mb-2">My Attendance</h2>
                <p class="text-3xl font-bold">{{ $attendanceCount }} days</p>
            </div>
        </div>

        <!-- Attendance Table -->
        <section>
            <h2 class="text-xl font-bold mb-4">Attendance Records</h2>
            <div class="overflow-x-auto rounded-2xl shadow-md">
                <table class="w-full text-sm text-left border-separate border-spacing-0 overflow-hidden rounded-2xl">
                    <thead>
                        <tr class="bg-[#0B849F] text-white">
                            <th class="px-6 py-3 rounded-tl-2xl border-r border-white whitespace-nowrap text-center">
                                Date</th>
                            <th class="px-6 py-3 border-r border-white whitespace-nowrap text-center">Check-In</th>
                            <th class="px-6 py-3 border-r border-white whitespace-nowrap text-center">Check-Out</th>
                            <th class="px-6 py-3 rounded-tr-2xl whitespace-nowrap text-center">Activity</th>
                        </tr>
                    </thead>

                    <tbody class="bg-[#0B849F] text-white">
                        @forelse ($attendances as $index => $item)
                        <tr class="{{ $loop->last ? 'rounded-b-2xl' : '' }}">
                            <td class="px-4 py-3 border-t border-white border-r">{{ $item->date }}</td>
                            <td class="px-4 py-3 border-t border-white border-r">{{ $item->check_in ?? '--:--' }}</td>
                            <td class="px-4 py-3 border-t border-white border-r">{{ $item->check_out ?? '--:--' }}</td>
                            <td class="px-4 py-3 border-t border-white">
                                @if ($item->activity_title)
                                <p class="font-bold">{{ $item->activity_title }}</p>
                                <p class="text-sm">{{ $item->activity_description }}</p>
                                @else
                                <span>—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-white">Belum ada data absensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Footer -->
        <div class="text-center text-xs text-blue-500 mt-10">
            by <a href="#" class="underline">PKL TRKJ POLITALA</a>
        </div>

    </main>
</div>
@endsection