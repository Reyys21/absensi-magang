{{-- resources/views/attendances/history.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

        <main id="main-content" class="flex-1 p-4 md:p-6 transition-all duration-300 ease-in-out">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-0">Riwayat Absensi Saya</h1>
                @include('layouts.profile')
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Riwayat</h2>

                <div class="mb-4">
                    {{-- Tombol Koreksi --}}
                    <button id="openCorrectionModalButton"
                            class="bg-purple-500 text-white px-4 py-2 rounded-md text-sm font-medium w-full sm:w-auto hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50">
                        Koreksi
                    </button>
                </div>

                {{-- Legenda Status Absensi --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full" style="background-color: #28CB6E;" title="Lengkap"></div>
                        <span>Lengkap</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full" style="background-color: #f86917;" title="Absen (Belum Lengkap)"></div>
                        <span>Absen (Belum Lengkap)</span>
                    </div>
                </div>

                {{-- Navigasi Bulan --}}
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-2 sm:gap-0">
                    <a href="{{ route('attendance.history', ['year' => $date->copy()->subMonth()->year, 'month' => $date->copy()->subMonth()->month, 'selected_date' => Request::get('selected_date')]) }}"
                       class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 w-full text-center sm:w-auto">
                        &larr; Sebelumnya
                    </a>

                    <button class="bg-black text-white px-6 py-2 rounded-md text-sm font-medium w-full sm:w-auto">
                        {{ $date->translatedFormat('F Y') }}
                    </button>

                    <a href="{{ route('attendance.history', ['year' => $date->copy()->addMonth()->year, 'month' => $date->copy()->addMonth()->month, 'selected_date' => Request::get('selected_date')]) }}"
                       class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 w-full text-center sm:w-auto">
                        Berikutnya &rarr;
                    </a>
                </div>

                {{-- Kalender --}}
                <div class="grid grid-cols-7 gap-0.5 sm:gap-1 text-center">
                    @php
                        use Carbon\Carbon;

                        $startOfMonth = $date->copy()->startOfMonth();
                        $firstDayOfWeek = $startOfMonth->dayOfWeekIso;
                        $offset = $firstDayOfWeek - 1;
                        $todayCarbon = Carbon::now()->tz('Asia/Makassar'); // Pastikan timezone
                        $userRegistrationDate = Auth::user()->created_at->startOfDay()->tz('Asia/Makassar'); // Pastikan timezone
                    @endphp

                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                        <div class="text-gray-500 font-semibold py-2 text-xs sm:text-base">{{ $dayName }}</div>
                    @endforeach

                    @for ($i = 0; $i < 42; $i++)
                        @php
                            $loopDate = $startOfMonth->copy()->subDays($offset)->addDays($i);
                            $currentDayString = $loopDate->toDateString();

                            $attendanceData = $monthlyAttendances->get($currentDayString);

                            $status = $attendanceData ? $attendanceData->attendance_status : '';

                            $bgColor = '';
                            $textColor = 'text-gray-700';
                            $isCurrentMonth = ($loopDate->month == $date->month);
                            $isToday = ($currentDayString == $todayCarbon->toDateString());
                            $isSelected = (Request::get('selected_date') == $currentDayString);
                            $isBeforeRegistration = $loopDate->lt($userRegistrationDate);
                            $isFutureDate = $loopDate->gt($todayCarbon->endOfDay());

                            if (!$isCurrentMonth || $isBeforeRegistration || $isFutureDate) {
                                $textColor = 'text-gray-400';
                                $bgColor = 'transparent';
                                $status_display = ($isBeforeRegistration || $isFutureDate) ? 'N/A' : ''; // Future dates also N/A for display
                            } else {
                                $status_display = $status;
                                switch ($status) {
                                    case 'Complete':
                                        $bgColor = '#28CB6E';
                                        $textColor = 'text-white';
                                        break;
                                    case 'Absent (Belum Lengkap)':
                                        $bgColor = '#f86917';
                                        $textColor = 'text-white';
                                        break;
                                    case 'Incomplete': // Added for 'Incomplete' status
                                        $bgColor = '#FFD700';
                                        $textColor = 'text-gray-800';
                                        break;
                                    default:
                                        $bgColor = 'transparent';
                                        $textColor = 'text-gray-700';
                                        break;
                                }

                                if ($loopDate->isSaturday() && $isCurrentMonth && !$isBeforeRegistration && !$isFutureDate) {
                                    $textColor = 'text-blue-600';
                                } elseif ($loopDate->isSunday() && $isCurrentMonth && !$isBeforeRegistration && !$isFutureDate) {
                                    $textColor = 'text-red-600';
                                }
                            }
                        @endphp

                        <div class="py-1">
                            <a href="{{ ($status_display === 'N/A') ? '#' : route('attendance.history', ['year' => $date->year, 'month' => $date->month, 'selected_date' => $currentDayString]) }}"
                               class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-full text-xs sm:text-base
                                       font-medium cursor-pointer relative
                                       {{ $isSelected ? 'bg-black text-white' : '' }}
                                       {{ $isToday && !$isSelected && !$isBeforeRegistration && !$isFutureDate ? 'border-2 border-black' : '' }}
                                       {{ $textColor }}"
                               data-date="{{ $currentDayString }}"
                               @if (!$isSelected && $bgColor && $bgColor != 'transparent') style="background-color: {{ $bgColor }}; color: white;" @endif>
                                {{ $loopDate->day }}
                            </a>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Bagian Riwayat Harian --}}
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                <h2 class="text-lg font-semibold mb-4">Riwayat Harian</h2>
                @if ($dailyAttendances->isEmpty() && !Request::filled('selected_date'))
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
                                        <p class="text-xs text-gray-500">Check-In: {{ $attendance->check_in ? $attendance->check_in->tz('Asia/Makassar')->format('H:i') : '--.--' }}</p>
                                        <p class="text-xs text-gray-500">Check-Out: {{ $attendance->check_out ? $attendance->check_out->tz('Asia/Makassar')->format('H:i') : '--.--' }}</p>
                                        @if ($attendance->activity_title)
                                            <p class="text-xs text-gray-500">Aktivitas: {{ $attendance->activity_title }}</p>
                                        @endif
                                        @if ($attendance->activity_description)
                                            <p class="text-xs text-gray-500">Deskripsi: {{ $attendance->activity_description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full"
                                    @php
                                        $indicatorColor = '';
                                        switch ($attendance->attendance_status) {
                                            case 'Complete':
                                                $indicatorColor = '#28CB6E';
                                                break;
                                            case 'Absent (Belum Lengkap)':
                                                $indicatorColor = '#f86917';
                                                break;
                                            case 'Incomplete':
                                                $indicatorColor = '#FFD700';
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
    </div>

    {{-- MODAL KOREKSI ABSENSI --}}
    <div id="correctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50 p-4">
        {{-- Kontainer Konten Modal --}}
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg md:max-w-xl lg:max-w-2xl mx-auto overflow-y-auto max-h-[90vh] flex flex-col">
            {{-- Header Modal --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                <h3 class="text-lg font-bold text-gray-800">Ajukan Koreksi Absensi</h3>
                <button id="closeCorrectionModalButton" class="text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none">&times;</button>
            </div>
            {{-- Badan Modal (tempat konten formulir dimuat) --}}
            <div id="correctionFormContent" class="flex-grow p-6 md:p-8">
                <p class="text-center text-gray-600 py-8">Memuat formulir koreksi...</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const correctionModal = document.getElementById('correctionModal');
    const openCorrectionModalButton = document.getElementById('openCorrectionModalButton');
    const closeCorrectionModalButton = document.getElementById('closeCorrectionModalButton');
    const correctionFormContent = document.getElementById('correctionFormContent');

    // Pastikan modal tersembunyi saat halaman dimuat
    if (correctionModal) {
        correctionModal.classList.add('hidden');
        correctionModal.classList.remove('flex');
    }

    if (openCorrectionModalButton) {
        openCorrectionModalButton.addEventListener('click', function() {
            console.log('Tombol Koreksi diklik!');

            let defaultDate = '{{ \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d') }}'; // Gunakan zona waktu server

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('selected_date')) {
                defaultDate = urlParams.get('selected_date');
            }

            console.log('Mengambil formulir koreksi untuk tanggal:', defaultDate);

            // Tampilkan status memuat dan modal
            if (correctionFormContent) {
                correctionFormContent.innerHTML = '<p class="text-center text-gray-600 py-8">Memuat formulir koreksi...</p>';
            }
            if (correctionModal) {
                correctionModal.classList.remove('hidden');
                correctionModal.classList.add('flex');
            }

            fetch(`{{ route('correction.form') }}?date=${defaultDate}`)
                .then(response => {
                    console.log('Status Respons AJAX:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Kesalahan HTTP! status: ${response.status}, pesan: ${text}`);
                        });
                    }
                    return response.text();
                })
                .then(html => {
                    console.log('AJAX Berhasil! HTML diterima. Panjang:', html.length);
                    if (correctionFormContent) {
                        if (html.trim().length === 0) {
                            correctionFormContent.innerHTML = '<p class="text-red-500 text-center py-8">Formulir koreksi kosong atau tidak valid.</p>';
                            console.warn('Menerima HTML kosong untuk formulir koreksi.');
                        } else {
                            correctionFormContent.innerHTML = html;
                            attachFormSubmitListener(); // Lampirkan kembali listener setelah pembaruan konten
                        }
                    }
                })
                .catch(error => {
                    console.error('Gagal memuat formulir koreksi:', error);
                    if (correctionFormContent) {
                        correctionFormContent.innerHTML = `<p class="text-red-500 text-center py-8">Gagal memuat formulir koreksi.<br>Detail: ${error.message}. Periksa konsol browser untuk lebih lanjut.</p>`;
                    }
                });
        });
    } else {
        console.error('Error: Elemen openCorrectionModalButton tidak ditemukan, tidak dapat melampirkan event listener.');
    }


    if (closeCorrectionModalButton) {
        closeCorrectionModalButton.addEventListener('click', function() {
            console.log('Tombol Tutup diklik.');
            if (correctionModal) {
                correctionModal.classList.add('hidden');
                correctionModal.classList.remove('flex');
            }
        });
    }

    function attachFormSubmitListener() {
        const form = correctionFormContent.querySelector('form');
        if (form) {
            console.log('Formulir ditemukan, melampirkan listener submit.');
            // Hapus listener submit yang ada untuk mencegah beberapa panggilan
            form.removeEventListener('submit', handleFormSubmit);
            form.addEventListener('submit', handleFormSubmit);

            // Tambahkan listener event ke input tanggal untuk memuat ulang konten formulir
            const dateToCorrectInput = document.getElementById('date_to_correct');
            if (dateToCorrectInput) {
                // Hapus listener perubahan yang ada untuk mencegah beberapa panggilan
                dateToCorrectInput.removeEventListener('change', handleDateChange);
                dateToCorrectInput.addEventListener('change', handleDateChange);
            }
        } else {
            console.warn('Formulir tidak ditemukan di dalam correctionFormContent.');
        }
    }

    function handleFormSubmit(event) {
        event.preventDefault(); // Mencegah pengiriman formulir default

        const confirmSend = confirm('Apakah Anda yakin ingin mengirimkan koreksinya?');
        if (confirmSend) {
            event.target.submit();
        }
    }

    function handleDateChange() {
        const selectedDate = this.value;
        console.log('Input tanggal diubah menjadi:', selectedDate);

        if (correctionFormContent) {
            correctionFormContent.innerHTML = '<p class="text-center text-gray-600 py-8">Memuat data absensi...</p>';
        }
        fetch(`{{ route('correction.form') }}?date=${selectedDate}`)
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text); });
                }
                return response.text();
            })
            .then(html => {
                if (correctionFormContent) {
                    correctionFormContent.innerHTML = html;
                    attachFormSubmitListener();
                    const newDateToCorrectInput = document.getElementById('date_to_correct');
                    if (newDateToCorrectInput) {
                        newDateToCorrectInput.setAttribute('max', '{{ \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d') }}');
                    }
                }
            })
            .catch(error => {
                console.error('Gagal memuat ulang formulir untuk tanggal baru:', error);
                if (correctionFormContent) {
                    correctionFormContent.innerHTML = `<p class="text-red-500 text-center py-8">Gagal memuat data koreksi untuk tanggal ini.<br>Detail: ${error.message}</p>`;
                }
            });
    }

});
</script>
@endsection