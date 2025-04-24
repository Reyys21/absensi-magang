@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row min-h-screen">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-[#1E2A38] text-white flex flex-col justify-between">
        <div>
            <div class="p-4 flex items-center space-x-2">
                <span class="text-yellow-400 text-xl">⚡</span>
                <div>
                    <p class="text-sm font-bold leading-4">PLN</p>
                    <p class="text-xs">UID KALSELTENG</p>
                </div>
            </div>
            <nav class="mt-6 px-4">
                <a href="{{ route('dashboard') }}">
                    <button class="w-full bg-yellow-400 text-black py-2 rounded font-semibold">Dashboard</button>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
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



        <!-- Attendance Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

            <!-- Today’s Attendance -->
            <div class="bg-cyan-600 text-white rounded-xl p-6 shadow-lg flex flex-col items-center justify-center">
                <h2 class="text-md font-medium mb-2">Today’s Attendance</h2>
                <div class="flex justify-between items-center w-full px-6 text-2xl font-bold gap-4 mb-4">
                    <div class="text-center">
                        <p>{{ $attendances->first()?->check_in ?? '--:--' }}</p>
                        <p class="text-sm font-normal">Check-In ⬇</p>
                    </div>
                    <div class="text-center">
                        <p>{{ $attendances->first()?->check_out ?? '--:--' }}</p>
                        <p class="text-sm font-normal">Check-Out ⬇</p>
                    </div>
                </div>

                <!-- Tombol di dalam card -->
                <div class="flex gap-3">
                    <a href="{{ route('checkin.form') }}"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow">Check-In</a>
                    <a href="{{ route('checkout.form') }}"
                        class="bg-yellow-400 hover:bg-yellow-500 text-black px-4 py-2 rounded-lg shadow">Check-Out</a>
                </div>
            </div>

            <!-- My Attendance -->
            <div
                class="bg-cyan-600 text-white rounded-xl p-6 shadow-lg flex flex-col items-center justify-center col-span-1 sm:col-span-2 lg:col-span-1">
                <h2 class="text-md font-medium mb-2">My Attendance</h2>
                <p class="text-3xl font-bold">{{ $attendanceCount }} Days</p>
            </div>
        </div>

        <!-- Attendance Table -->
        <section>
            <h2 class="text-xl font-bold mb-4">Attendance Records</h2>
            <div class="overflow-x-auto rounded-xl">
                <table class="w-full min-w-[600px] text-sm text-left border-collapse">
                    <thead class="bg-cyan-700 text-white">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Check-In</th>
                            <th class="px-4 py-2">Check-Out</th>
                            <th class="px-4 py-2">Activity</th>
                        </tr>
                    </thead>
                    <tbody class="bg-cyan-100 text-gray-700">
                        @forelse ($attendances as $item)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $item->date }}</td>
                            <td class="px-4 py-2">{{ $item->check_in ?? '--:--' }}</td>
                            <td class="px-4 py-2">{{ $item->check_out ?? '--:--' }}</td>
                            <td class="px-4 py-2">
                                @if ($item->activity_title)
                                <strong>{{ $item->activity_title }}</strong><br>
                                <span>{{ $item->activity_description }}</span>
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