@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

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
                    <button type="submit" class="w-full text-left text-white mt-2">Log Out</button>
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

                <div class="flex justify-center gap-16 items-center">
                    <!-- Check-In Time -->
                    <div class="text-center">
                        <p class="text-3xl font-bold">
                            {{ \Carbon\Carbon::parse($attendances->where('date', now()->toDateString())->first()?->check_in)->format('H.i') ?? '--.--' }}
                        </p>
                        @if (!$attendances->where('date', now()->toDateString())->first()?->check_in)
                        <form action="{{ route('checkin.form') }}" method="GET">
                            <button class="mt-2 bg-green-500 text-white px-4 py-1 rounded shadow hover:bg-green-600">
                                Check-In
                            </button>
                        </form>
                        @else
                        <button class="mt-2 bg-green-500 text-white px-4 py-1 rounded opacity-50 cursor-not-allowed"
                            disabled>
                            Check-In
                        </button>
                        @endif
                    </div>

                    <!-- Check-Out Time -->
                    <div class="text-center">
                        <p class="text-3xl font-bold">
                            {{ \Carbon\Carbon::parse($attendances->where('date', now()->toDateString())->first()?->check_out)->format('H.i') ?? '--.--' }}
                        </p>
                        @if (
                        $attendances->where('date', now()->toDateString())->first()?->check_in &&
                        !$attendances->where('date', now()->toDateString())->first()?->check_out
                        )
                        <form action="{{ route('checkout.form') }}" method="GET">
                            <button class="mt-2 bg-yellow-400 text-black px-4 py-1 rounded shadow hover:bg-yellow-500">
                                Check-Out
                            </button>
                        </form>
                        @else
                        <button class="mt-2 bg-yellow-400 text-black px-4 py-1 rounded opacity-50 cursor-not-allowed"
                            disabled>
                            Check-Out
                        </button>
                        @endif
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
            <div class="overflow-x-auto rounded-xl">
                <table class="w-full text-sm text-left border-collapse table-auto">
                    <thead class="bg-[#0B849F] text-white">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Check-In</th>
                            <th class="px-4 py-2">Check-Out</th>
                            <th class="px-4 py-2">Activity</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#0B849F]/60 text-white">
                        @forelse ($attendances as $item)
                        <tr class="border-b border-white/30 align-top">
                            <td class="px-4 py-2">{{ $item->date }}</td>
                            <td class="px-4 py-2">{{ $item->check_in ?? '--:--' }}</td>
                            <td class="px-4 py-2">{{ $item->check_out ?? '--:--' }}</td>
                            <td class="px-4 py-2">
                                @if ($item->activity_title)
                                <p><strong>{{ $item->activity_title }}</strong></p>
                                <p class="text-sm text-white/80 leading-snug">
                                    {{ $item->activity_description }}
                                </p>
                                @else
                                <span>—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada data absensi</td>
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