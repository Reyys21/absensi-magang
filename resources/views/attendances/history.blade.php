@extends('layouts.app')

@section('content')
    {{-- Ini adalah div pembungkus utama yang akan memiliki tata letak flex --}}
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

        {{-- Ini adalah kode sidebar Anda --}}
        {{-- Pastikan ini di-include atau ditempel langsung di sini --}}
        @include('layouts.sidebar') {{-- Asumsi layouts.sidebar adalah file blade terpisah --}}

        {{-- Main content area --}}
        {{-- id="main-content" penting untuk JavaScript --}}
        <main id="main-content" class="flex-1 p-4 md:p-6 transition-all duration-300 ease-in-out">
            {{-- Isi konten utama Anda dari sini --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-0">My Attendance</h1>
                @include('layouts.profile')
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
                        <div class="w-4 h-4 rounded-full" style="background-color: #E7E015;" title="Not Checked In"></div>
                        <span>Not Checked In</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full" style="background-color: #f86917;" title="Not Checked Out"></div>
                        <span>Not Checked Out</span>
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
                                    case 'Not Checked In':
                                        $bgColor = '#E7E015';
                                        break;
                                    case 'Not Checked Out':
                                        $bgColor = '#f86917';
                                        break;
                                    case 'Absent':
                                        $bgColor = '#E61126';
                                        break;
                                    default:
                                        $bgColor = '#A0AEC0';
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
                                            case 'Not Checked In':
                                                $indicatorColor = '#E7E015';
                                                break;
                                            case 'Not Checked Out':
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
    </div>
@endsection

@section('Script')
    {{-- JavaScript sidebar dan margin ada di sini, di bagian bawah body --}}
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const desktopSidebarToggle = document.getElementById('desktop-sidebar-toggle');
        const plnText = document.getElementById('pln-text');
        const navTexts = document.querySelectorAll('.nav-text');
        const arrowIcons = document.querySelectorAll('.arrow-icon');
        const mainContent = document.getElementById('main-content'); // Dapatkan elemen main-content

        // Ambil nilai isDesktopSidebarOpen dari localStorage atau set default
        let isDesktopSidebarOpen = localStorage.getItem('isDesktopSidebarOpen') === 'false' ? false : true;

        function setSidebarState(isOpen) {
            const isDesktop = window.innerWidth >= 768;

            if (isDesktop) {
                // Untuk desktop, sidebar akan selalu fixed
                sidebar.classList.remove('md:relative', 'md:translate-x-0'); // Pastikan ini dihapus
                sidebar.classList.add('fixed'); // Ini harus fixed

                sidebarOverlay.classList.add('hidden'); // Selalu sembunyikan overlay di desktop

                sidebar.classList.toggle('md:w-64', isOpen);
                sidebar.classList.toggle('md:w-20', !isOpen);

                // Atur margin kiri main content
                if (mainContent) {
                    mainContent.style.marginLeft = isOpen ? '16rem' : '5rem'; // 16rem = 256px (w-64), 5rem = 80px (w-20)
                }

                plnText.classList.toggle('hidden', !isOpen);
                navTexts.forEach(span => span.classList.toggle('hidden', !isOpen));
                arrowIcons.forEach(icon => icon.classList.toggle('hidden', !isOpen));
                
                if (isOpen) {
                    desktopSidebarToggle.querySelector('i').classList.remove('fa-chevron-left', 'rotate-180');
                    desktopSidebarToggle.querySelector('i').classList.add('fa-bars');
                } else {
                    desktopSidebarToggle.querySelector('i').classList.remove('fa-bars');
                    desktopSidebarToggle.querySelector('i').classList.add('fa-chevron-left', 'rotate-180');
                }
                
                if (!isOpen) {
                    document.getElementById('attendanceDropdown').classList.add('hidden');
                    document.getElementById('approvalDropdown').classList.add('hidden');
                    document.querySelector('#btn-attendance .fa-chevron-down').classList.remove('rotate-180');
                    document.querySelector('#btn-approval .fa-chevron-down').classList.remove('rotate-180');
                }

                isDesktopSidebarOpen = isOpen;
                localStorage.setItem('isDesktopSidebarOpen', isDesktopSidebarOpen); // Simpan status
            } else { // Mobile
                sidebar.classList.remove('md:relative', 'md:w-64', 'md:w-20', 'md:translate-x-0');
                sidebar.classList.add('fixed'); // Ini juga fixed untuk drawer
                sidebar.classList.toggle('-translate-x-full', !isOpen);
                sidebarOverlay.classList.toggle('hidden', !isOpen);

                // Di mobile, main content tidak perlu margin kiri karena sidebar adalah drawer
                if (mainContent) {
                    mainContent.style.marginLeft = '0';
                }

                sidebar.classList.add('w-64'); // Atur lebar tetap untuk mobile drawer
                plnText.classList.remove('hidden');
                navTexts.forEach(span => span.classList.remove('hidden'));
                arrowIcons.forEach(icon => icon.classList.remove('hidden'));
                desktopSidebarToggle.querySelector('i').classList.remove('fa-chevron-left', 'rotate-180');
                desktopSidebarToggle.querySelector('i').classList.add('fa-bars');
            }
        }

        function initializeSidebar() {
            if (window.innerWidth >= 768) {
                // Baca status dari localStorage saat inisialisasi desktop
                isDesktopSidebarOpen = localStorage.getItem('isDesktopSidebarOpen') === 'false' ? false : true;
                setSidebarState(isDesktopSidebarOpen);
                // Pastikan sidebar tidak tersembunyi jika di desktop
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            } else {
                setSidebarState(false); // Selalu tutup sidebar di mobile saat inisialisasi
            }
        }

        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => {
                setSidebarState(sidebar.classList.contains('-translate-x-full'));
            });
        }

        if (desktopSidebarToggle) {
            desktopSidebarToggle.addEventListener('click', () => {
                // Toggle state berdasarkan apakah sidebar sedang kolaps (w-20)
                isDesktopSidebarOpen = !isDesktopSidebarOpen; // Toggle state
                localStorage.setItem('isDesktopSidebarOpen', isDesktopSidebarOpen); // Simpan state
                setSidebarState(isDesktopSidebarOpen); // Panggil setSidebarState dengan state baru
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => setSidebarState(false)); // Tutup sidebar mobile
        }

        // Fungsi untuk menangani klik tombol dropdown
        function handleDropdownClick(buttonId, dropdownId) {
            document.getElementById(buttonId).addEventListener('click', function() {
                const dropdown = document.getElementById(dropdownId);
                const isDesktop = window.innerWidth >= 768;

                if (isDesktop && !isDesktopSidebarOpen) { // Jika di desktop dan sidebar kolaps
                    setSidebarState(true); // Lebarkan sidebar terlebih dahulu
                    setTimeout(() => { // Beri sedikit delay agar transisi sidebar selesai
                        dropdown.classList.toggle('hidden');
                        this.querySelector('i.fa-chevron-down').classList.toggle('rotate-180');
                    }, 300); // Sesuaikan dengan durasi transisi sidebar
                } else {
                    // Langsung toggle dropdown (baik di mobile atau sidebar sudah terbuka di desktop)
                    dropdown.classList.toggle('hidden');
                    this.querySelector('i.fa-chevron-down').classList.toggle('rotate-180');
                }
            });
        }

        handleDropdownClick('btn-attendance', 'attendanceDropdown');
        handleDropdownClick('btn-approval', 'approvalDropdown');

        // Opsional: Tutup sidebar saat mengklik item navigasi di mobile
        const navLinks = document.querySelectorAll('#sidebar nav a, #sidebar nav button');
        navLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                if (this.id === 'btn-attendance' || this.id === 'btn-approval') {
                    return; // Jangan tutup sidebar jika klik pada tombol dropdown itu sendiri
                }

                if (window.innerWidth < 768) {
                    setTimeout(() => {
                        setSidebarState(false);
                    }, 100);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', initializeSidebar);
        window.addEventListener('resize', initializeSidebar);
    </script>

    {{-- Gaya CSS tambahan untuk sidebar (dari _sidebar.blade.php Anda sebelumnya) --}}
    <style>
        /* Rotate transition */
        .rotate-180 {
            transform: rotate(180deg);
        }

        /* Scrollbar styling for sidebar */
        #sidebar::-webkit-scrollbar {
            width: 8px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: #2C3E50;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background-color: #555;
            border-radius: 4px;
            border: 2px solid #2C3E50;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background-color: #777;
        }

        /* Memastikan dropdown tidak terlihat jika sidebar kolaps */
        #sidebar.md\:w-20 #attendanceDropdown,
        #sidebar.md\:w-20 #approvalDropdown {
            position: absolute;
            left: 100%;
            top: 0;
            min-width: 180px;
            background-color: #34495E;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
            z-index: 50;
            opacity: 0;
            visibility: hidden;
            transform: translateX(-10px);
            transition: opacity 0.2s ease-out, transform 0.2s ease-out, visibility 0.2s ease-out;
        }

        /* Saat dropdown aktif, tampilkan */
        #sidebar.md\:w-20 #attendanceDropdown:not(.hidden),
        #sidebar.md\:w-20 #approvalDropdown:not(.hidden) {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        /* Padding dan warna hover untuk item dropdown saat sidebar kolaps */
        #sidebar.md\:w-20 #attendanceDropdown a,
        #sidebar.md\:w-20 #approvalDropdown a {
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
        }

        #sidebar.md\:w-20 #attendanceDropdown a:hover,
        #sidebar.md\:w-20 #approvalDropdown a:hover {
            background-color: #2C3E50;
            color: #FFD100;
        }

        /* Sembunyikan ikon panah dropdown saat sidebar kolaps di desktop */
        #sidebar.md\:w-20 .arrow-icon {
            display: none !important;
        }

        /* Sesuaikan logo/teks PLN saat sidebar kolaps */
        #sidebar.md\:w-20 #pln-text {
            display: none;
        }

        #sidebar.md\:w-20 #pln-logo-text {
            justify-content: center;
            width: 100%;
        }

        /* Mengatur tampilan item navigasi saat sidebar kolaps */
        #sidebar.md\:w-20 .nav-text {
            display: none;
        }

        #sidebar.md\:w-20 nav a,
        #sidebar.md\:w-20 nav button {
            justify-content: center;
            padding: 0.75rem 0.5rem;
            border-radius: 0.75rem;
        }

        /* Visual indicator for active main menu item when sidebar is collapsed */
        #sidebar.md\:w-20 nav a.active-link-indicator,
        #sidebar.md\:w-20 nav button.active-link-indicator {
            position: relative;
            overflow: hidden;
        }

        #sidebar.md\:w-20 nav a.active-link-indicator::before,
        #sidebar.md\:w-20 nav button.active-link-indicator::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 80%;
            background-color: #FFD100;
            border-radius: 0 4px 4px 0;
        }
    </style>
@endsection