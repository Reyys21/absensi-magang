@extends('layouts.app')

@section('content')
    {{-- Ini adalah div pembungkus utama yang akan memiliki tata letak flex --}}
    {{-- Ini sangat penting untuk sidebarn dan main content agar sejajar --}}
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

   

        {{-- Main content area --}}
        <main id="main-content" class="flex-1 p-4 md:p-6 transition-all duration-300 ease-in-out">
            {{-- Isi konten utama Anda dari sini --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-0">My Attendance</h1>
                @include('layouts.profile') {{-- Asumsi ini adalah file yang menampilkan profil --}}
            </div>

            {{-- History Section --}}
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">History</h2>

                <div class="mb-4">
                    <button class="bg-purple-500 text-white px-4 py-2 rounded-md text-sm font-medium w-full sm:w-auto">Correction</button>
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full" style="background-color: #28CB6E;" title="Complete"></div>
                        <span>Complete</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full" style="background-color: #f86917;" title="Absent (Belum Lengkap)"></div>
                        <span>Absent (Belum Lengkap)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full" style="background-color: #E61126;" title="Absent"></div>
                        <span>Absent</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-2 sm:gap-0">
                    <a href="{{ route('attendance.history', ['year' => $date->copy()->subMonth()->year, 'month' => $date->copy()->subMonth()->month, 'selected_date' => Request::get('selected_date')]) }}"
                       class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 w-full text-center sm:w-auto">
                        &larr; Previous
                    </a>

                    <button class="bg-black text-white px-6 py-2 rounded-md text-sm font-medium w-full sm:w-auto">
                        {{ $date->format('F Y') }}
                    </button>

                    <a href="{{ route('attendance.history', ['year' => $date->copy()->addMonth()->year, 'month' => $date->copy()->addMonth()->month, 'selected_date' => Request::get('selected_date')]) }}"
                       class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 w-full text-center sm:w-auto">
                        Next &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-7 gap-0.5 sm:gap-1 text-center">
                    @php
                        use Carbon\Carbon;

                        $startOfMonth = $date->copy()->startOfMonth();
                        $firstDayOfWeek = $startOfMonth->dayOfWeekIso;
                        $offset = $firstDayOfWeek - 1;

                        $calendarStart = $startOfMonth->copy()->subDays($offset);
                        $today = Carbon::now()->toDateString();
                    @endphp

                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                        <div class="text-gray-500 font-semibold py-2 text-xs sm:text-base">{{ $dayName }}</div>
                    @endforeach

                    @for ($i = 0; $i < 42; $i++)
                        @php
                            $loopDate = $calendarStart->copy()->addDays($i);
                            $currentDayString = $loopDate->format('Y-m-d');
                            $attendance = $monthlyAttendances->get($currentDayString);
                            $attendanceStatus = $attendance ? $attendance->attendance_status : '';

                            $bgColor = '';
                            $textColor = 'text-gray-700';

                            if ($loopDate->month != $date->month) {
                                $textColor = 'text-gray-400';
                            } elseif ($loopDate->isSaturday()) {
                                $textColor = 'text-blue-600';
                            } elseif ($loopDate->isSunday()) {
                                $textColor = 'text-red-600';
                            }

                            if ($attendance) {
                                switch ($attendanceStatus) {
                                    case 'Complete':
                                        $bgColor = '#28CB6E';
                                        break;
                                    case 'Absent (Belum Lengkap)': // Status baru
                                        $bgColor = '#f86917'; // Warna untuk status belum lengkap
                                        break;
                                    case 'Absent':
                                        $bgColor = '#E61126';
                                        break;
                                    default:
                                        $bgColor = '#A0AEC0'; // Warna default jika ada status yang tidak terdefinisi
                                        break;
                                }
                                $textColor = 'text-white';
                            }

                            $isSelected = (Request::get('selected_date') == $currentDayString);
                        @endphp

                        <div class="py-1">
                            <a href="{{ route('attendance.history', ['year' => $date->year, 'month' => $date->month, 'selected_date' => $currentDayString]) }}"
                               class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-full text-xs sm:text-base
                                       {{ $isSelected ? 'bg-black text-white' : '' }}
                                       font-medium cursor-pointer relative
                                       {{ $textColor }}"
                                       @if (!$isSelected && $bgColor) style="background-color: {{ $bgColor }}; color: white;" @endif>
                                {{ $loopDate->day }}
                            </a>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                <h2 class="text-lg font-semibold mb-4">Daily History</h2>
                @if ($dailyAttendances->isEmpty())
                    <p class="text-gray-600 text-center text-sm">Tidak ada data absensi untuk periode ini.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($dailyAttendances as $attendance)
                            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 flex items-center justify-between border border-gray-200">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                        <span class="text-sm font-semibold">{{ $attendance->date->day }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">{{ $attendance->day_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $attendance->formatted_date }}</p>
                                    </div>
                                </div>
                                <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full"
                                    @php
                                        $indicatorColor = '';
                                        switch ($attendance->attendance_status) {
                                            case 'Complete':
                                                $indicatorColor = '#28CB6E';
                                                break;
                                            case 'Absent (Belum Lengkap)': // Status baru
                                                $indicatorColor = '#f86917';
                                                break;
                                            case 'Absent':
                                                $indicatorColor = '#E61126';
                                                break;
                                            default:
                                                $indicatorColor = '#A0AEC0';
                                                break;
                                        }
                                    @endphp
                                    style="background-color: {{ $indicatorColor }};">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div> {{-- Penutup div flex --}}
@endsection

@section('Script')
    {{-- KOSONGKAN SEPENUHNYA ATAU ISI DENGAN HANYA SCRIPT JS KHUSUS HISTORY --}}
    {{-- Contoh:
    <script>
        console.log("Halaman history dimuat!");
        // Skrip khusus history di sini
    </script>
    --}}
@endsection