@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-[#2C3E50] text-white flex flex-col justify-between  shadow-sm">
        <div>
            <!-- Logo -->
            <div class="p-4 flex items-center justify-center space-x-2">
                <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="w-12 h-12 object-contain" />
                <div>
                    <p class="text-sm font-bold leading-4">PLN</p>
                    <p class="text-xs text-gray-300">UID KALSELTENG</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-4 px-4  space-y-2">
                <a href="{{ route('dashboard') }}">
                    <div
                        class="@if (request()->routeIs('dashboard')) bg-[#FFD100] text-black @else hover:bg-[#3C5A6D] text-white @endif w-full flex items-center gap-2 px-4 py-2 rounded-xl font-medium transition">
                        <i class="fa-solid fa-house"></i>
                        Dashboard
                    </div>
                </a>

                <a href="{{ route('helpdesk') }}">
                    <div
                        class="@if (request()->routeIs('helpdesk')) bg-[#FFD100] text-black @else hover:bg-[#3C5A6D] text-white @endif w-full flex items-center gap-2 px-4 py-2 rounded-xl font-medium transition">
                        <i class="fa-solid fa-circle-question"></i>
                        Helpdesk
                    </div>
                </a>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 text-sm text-white px-4 py-2 hover:bg-[#3C5A6D] rounded-xl transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Log Out
                    </button>
                </form>
            </nav>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 sm:p-10 bg-white shadow-lg">
        <h1 class="text-2xl font-bold mb-6">Welcome, Mahasiswa/Siswa Magang!</h1>

        <!-- Attendance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div
                class="bg-[#0B849F] text-white rounded-2xl p-6 shadow-md border flex flex-col justify-between md:col-span-2">
                <h2 class="text-base font-semibold text-center mb-4">Today’s Attendance</h2>
                <div class="flex justify-center gap-16 items-center">
                    <!-- Check-In -->
                    <div class="text-center">
                        <p class="text-3xl font-bold">
                            {{ optional($attendances->where('date', now()->toDateString())->first())->check_in 
                                ? \Carbon\Carbon::parse($attendances->where('date', now()->toDateString())->first()->check_in)->format('H.i') 
                                : '--.--' }}
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
                            {{ optional($attendances->where('date', now()->toDateString())->first())->check_out 
                                ? \Carbon\Carbon::parse($attendances->where('date', now()->toDateString())->first()->check_out)->format('H.i') 
                                : '--.--' }}
                        </p>
                        <form action="{{ route('checkout.form') }}" method="GET">
                            <button @if( !$attendances->where('date', now()->toDateString())->first()?->check_in ||
                                $attendances->where('date', now()->toDateString())->first()?->check_out
                                ) disabled @endif
                                class="bg-yellow-400 text-black px-6 py-2 mt-2 rounded-lg shadow hover:bg-yellow-500
                                disabled:opacity-50 disabled:cursor-not-allowed">
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
                            <td colspan="5" class="text-center py-4 border border-gray-300">Belum ada data absensi</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="mt-4 flex justify-center">
                {{ $attendances->links() }}
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
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById("clock").textContent = `${hours}.${minutes}`;
}
setInterval(updateClock, 1000);
updateClock();
</script>
@endsection