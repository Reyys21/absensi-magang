@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-[#2C3E50] text-white flex flex-col justify-between">
        <div>
            <div class="p-4 flex items-center space-x-2">
                <span class="text-yellow-400 text-xl">
                    <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN"
                        class="w-12 h-12 object-contain" />
                </span>
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
    <main class="flex-1 p-6 sm:p-10 bg-white rounded-t-3xl md:rounded-l-3xl shadow-lg">
        <h1 class="text-2xl font-bold mb-6">Welcome, Mahasiswa/Siswa Magang!</h1>

        <!-- Attendance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Today's Attendance Card -->
            <div
                class="bg-[#0B849F] text-white rounded-2xl p-6 shadow-md border flex flex-col justify-between md:col-span-2">
                <h2 class="text-base font-semibold text-center mb-4">Today’s Attendance</h2>
                <div class="flex justify-center gap-16 items-center">
                    <!-- Check-In -->
                    <div class="text-center">
                        <p class="text-3xl font-bold">
                            {{ optional($attendances->where('date', now()->toDateString())->first())->check_in ?? '--.--' }}
                        </p>
                        <form action="{{ route('checkin.form') }}" method="GET">
                            <button @if($attendances->where('date', now()->toDateString())->first()?->check_in) disabled
                                @endif
                                class="bg-green-500 text-white px-6 py-2 mt-2 rounded-lg shadow hover:bg-green-600
                                disabled:opacity-50 disabled:cursor-not-allowed">
                                Check-In
                            </button>
                        </form>
                    </div>

                    <!-- Check-Out -->
                    <div class="text-center">
                        <p class="text-3xl font-bold">
                            {{ optional($attendances->where('date', now()->toDateString())->first())->check_out ?? '--.--' }}
                        </p>
                        <form action="{{ route('checkout.form') }}" method="GET">
                            <button @if(!$attendances->where('date', now()->toDateString())->first()?->check_in ||
                                $attendances->where('date', now()->toDateString())->first()?->check_out) disabled @endif
                                class="bg-yellow-400 text-black px-6 py-2 mt-2 rounded-lg shadow hover:bg-yellow-500
                                disabled:opacity-50 disabled:cursor-not-allowed">
                                Check-Out
                            </button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-4 text-sm">
                    Jam sekarang: <span id="clock">--.--.--</span>
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
                            <th class="px-4 py-2 whitespace-nowrap">Date</th>
                            <th class="px-4 py-2 whitespace-nowrap">Check-In</th>
                            <th class="px-4 py-2 whitespace-nowrap">Check-Out</th>
                            <th class="px-4 py-2">Activity</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#13B4D8] text-gray-800">
                        @forelse ($attendances as $item)
                        <tr class="border-b align-top">
                            <td class="px-4 py-2">{{ $item->date }}</td>
                            <td class="px-4 py-2">{{ $item->check_in ?? '--:--' }}</td>
                            <td class="px-4 py-2">{{ $item->check_out ?? '--:--' }}</td>
                            <td class="px-4 py-2">
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

<!-- Local Time Script -->
<script>
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    document.getElementById("clock").textContent = `${h}.${m}.${s}`;
}
setInterval(updateClock, 1000);
updateClock();
</script>
@endsection