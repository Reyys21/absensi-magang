{{-- Tombol toggle untuk mobile --}}
<button id="mobile-menu-toggle"
    class="md:hidden fixed bottom-4 right-4 p-3 z-50 bg-[#2C3E50] text-white rounded-full shadow-lg hover:bg-[#3C5A6D] focus:outline-none">
    <i class="fa-solid fa-bars text-xl"></i>
</button>

{{-- Overlay untuk mobile --}}
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black opacity-50 z-30 md:hidden"></div>

<aside id="sidebar"
    class="bg-[#2C3E50] text-white flex flex-col justify-between shadow-lg h-screen overflow-y-auto
           fixed inset-y-0 left-0
           transform -translate-x-full
           md:relative md:translate-x-0
           md:w-64
           transition-all duration-300 ease-in-out z-40">
    <div>
        {{-- Header Sidebar --}}
        <div class="p-4 flex items-center justify-between space-x-3 border-b border-[#1F2A36]">
            <div id="pln-logo-text" class="flex items-center space-x-3">
                <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="w-12 h-12 object-contain" />
                <div id="pln-text">
                    <p class="text-base font-bold leading-5">PLN</p>
                    <p class="text-xs text-gray-300">UID KALSELTENG</p>
                </div>
            </div>
            <button id="desktop-sidebar-toggle"
                class="hidden md:block p-2 text-white rounded-md hover:bg-[#3C5A6D] focus:outline-none transition duration-200">
                <i class="fa-solid fa-bars transition-transform duration-300"></i>
            </button>
        </div>

        {{-- Awal Menu Navigasi --}}
        <nav class="mt-6 px-4 space-y-2 pb-4">
            @auth

                {{-- ========================================= --}}
                {{-- MENU UNTUK USER BIASA (MAHASISWA/SISWA)  --}}
                {{-- ========================================= --}}
                @can('access-user-pages')
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-house"></i> <span class="nav-text">Dasboard</span>
                    </a>

                    <div class="relative">
                        <button id="btn-attendance" type="button"
                            class="dropdown-toggle flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 cursor-pointer {{ request()->routeIs('attendance.*') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-user-check"></i> <span class="nav-text">Absensi</span>
                            </div>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 arrow-icon"></i>
                        </button>
                        <div id="attendanceDropdown" class="dropdown-menu hidden mt-2 space-y-1 rounded-xl bg-[#34495E]">
                            <a href="{{ route('attendance.history') }}"
                                class="block px-6 py-2 text-sm {{ request()->routeIs('attendance.history') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Riwayat</a>
                            <a href="{{ route('attendance.my') }}"
                                class="block px-6 py-2 text-sm {{ request()->routeIs('attendance.my') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Absensi
                                Saya</a>
                        </div>
                    </div>

                    <div class="relative">
                        <button id="btn-approval" type="button"
                            class="dropdown-toggle flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 cursor-pointer {{ request()->routeIs('user.approval.requests') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-thumbs-up"></i> <span class="nav-text">Persetujuan</span>
                            </div>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 arrow-icon"></i>
                        </button>
                        <div id="approvalDropdown" class="dropdown-menu hidden mt-2 space-y-1 rounded-xl bg-[#34495E]">
                            <a href="{{ route('user.approval.requests') }}"
                                class="block px-6 py-2 text-sm {{ request()->routeIs('user.approval.requests') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Persetujuan
                                Absensi</a>
                        </div>
                    </div>
                @endcan

                {{-- ========================================= --}}
                {{-- MENU UNTUK ADMIN & SUPERADMIN            --}}
                {{-- ========================================= --}}
                @can('access-admin-pages')
                    {{-- Dashboard Superadmin (Hanya terlihat oleh Superadmin) --}}
                    @can('access-superadmin-pages')
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('superadmin.dashboard') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                            <i class="fa-solid fa-user-shield"></i> <span class="nav-text">Superadmin Dashboard</span>
                        </a>
                    @endcan

                    {{-- Dashboard Admin (Terlihat oleh Admin & Superadmin) --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-user-gear"></i> <span class="nav-text">Admin Dashboard</span>
                    </a>

                    {{-- Dropdown Monitoring --}}
                    <div class="relative">
                        <button id="btn-monitoring" type="button"
                            class="dropdown-toggle flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 cursor-pointer {{ request()->routeIs('admin.monitoring.*') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-desktop"></i> <span class="nav-text">Monitoring</span>
                            </div>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 arrow-icon"></i>
                        </button>
                        <div id="monitoringDropdown" class="dropdown-menu hidden mt-2 space-y-1 rounded-xl bg-[#34495E]">
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100] rounded">LOG</a>
                            <a href="{{ route('admin.monitoring.users.index') }}"
                                class="block px-6 py-2 text-sm {{ request()->routeIs('admin.monitoring.*') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Users</a>
                        </div>
                    </div>

                    {{-- Dropdown Manajemen (Termasuk Approval Khusus Admin) --}}
                    <div class="relative">
                        {{-- Tambahkan kelas aktif pada tombol dropdown utama --}}
                        <button id="btn-manajemen" type="button"
                            class="dropdown-toggle flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 cursor-pointer {{ request()->routeIs('admin.approval.*') || request()->routeIs('admin.management.*') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-list-check"></i> <span class="nav-text">Manajemen</span>
                            </div>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 arrow-icon"></i>
                        </button>
                        <div id="manajemenDropdown" class="dropdown-menu hidden mt-2 space-y-1 rounded-xl bg-[#34495E]">
                            <a href="{{ route('admin.management.accounts.index') }}"
                                class="block px-6 py-2 text-sm {{ request()->routeIs('admin.management.*') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Account</a>
                            <a href="{{ route('admin.approval.requests') }}"
                                class="block px-6 py-2 text-sm {{ request()->routeIs('admin.approval.*') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Persetujuan</a>
                        </div>
                    </div>
                @endcan

                {{-- Menu Logout (untuk semua role) --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-sm transition duration-150 hover:bg-[#3C5A6D]">
                        <i class="fa-solid fa-right-from-bracket"></i> <span class="nav-text">Keluar</span>
                    </button>
                </form>

            @endauth
        </nav>
    </div>
</aside>

<script>
    // Script Javascript tidak ada perubahan
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const desktopSidebarToggle = document.getElementById('desktop-sidebar-toggle');
        const plnText = document.getElementById('pln-text');
        const navTexts = document.querySelectorAll('.nav-text');

        if (!sidebar || !mobileMenuToggle || !desktopSidebarToggle || !sidebarOverlay) {
            console.warn("Satu atau lebih elemen sidebar penting tidak ditemukan.");
            return;
        }

        let isDesktopSidebarOpen = localStorage.getItem('isDesktopSidebarOpen') === 'true';

        const setSidebarState = (isOpen) => {
            const isDesktop = window.innerWidth >= 768;
            if (isDesktop) {
                sidebar.classList.toggle('md:w-20', !isOpen);
                sidebar.classList.toggle('md:w-64', isOpen);
                if (plnText) plnText.classList.toggle('hidden', !isOpen);
                navTexts.forEach(span => span.classList.toggle('hidden', !isOpen));
                document.querySelectorAll('.arrow-icon').forEach(icon => icon.classList.toggle('hidden', !
                    isOpen));
                if (!isOpen) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add(
                        'hidden'));
                    document.querySelectorAll('.arrow-icon').forEach(arrow => arrow.classList.remove(
                        'rotate-180'));
                }
            } else {
                sidebar.classList.toggle('-translate-x-full', !isOpen);
                sidebarOverlay.classList.toggle('hidden', !isOpen);
            }
        };

        const initializeSidebar = () => {
            if (window.innerWidth >= 768) {
                setSidebarState(isDesktopSidebarOpen);
            } else {
                setSidebarState(false);
            }
        };

        desktopSidebarToggle.addEventListener('click', () => {
            isDesktopSidebarOpen = !sidebar.classList.contains('md:w-20');
            localStorage.setItem('isDesktopSidebarOpen', isDesktopSidebarOpen);
            setSidebarState(isDesktopSidebarOpen);
        });

        mobileMenuToggle.addEventListener('click', () => setSidebarState(sidebar.classList.contains(
            '-translate-x-full')));
        sidebarOverlay.addEventListener('click', () => setSidebarState(false));

        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', (event) => {
                const dropdownMenu = button.nextElementSibling;
                const arrowIcon = button.querySelector('.arrow-icon');
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    const isThisMenuOpen = !dropdownMenu.classList.contains('hidden');
                    // Sembunyikan semua menu lain sebelum menampilkan yang ini
                    if (!isThisMenuOpen) {
                        document.querySelectorAll('.dropdown-menu').forEach(menu => {
                            if (menu !== dropdownMenu) {
                                menu.classList.add('hidden');
                                menu.previousElementSibling.querySelector('.arrow-icon')
                                    ?.classList.remove('rotate-180');
                            }
                        });
                    }
                    dropdownMenu.classList.toggle('hidden');
                }
                if (arrowIcon) {
                    arrowIcon.classList.toggle('rotate-180');
                }
            });
        });

        // Agar menu dropdown yang aktif terbuka saat halaman dimuat
        const activeDropdownButton = document.querySelector('.dropdown-toggle.bg-\\[\\#FFD100\\]');
        if (activeDropdownButton) {
            const dropdownMenu = activeDropdownButton.nextElementSibling;
            const arrowIcon = activeDropdownButton.querySelector('.arrow-icon');
            if (dropdownMenu) dropdownMenu.classList.remove('hidden');
            if (arrowIcon) arrowIcon.classList.add('rotate-180');
        }


        initializeSidebar();
        window.addEventListener('resize', initializeSidebar);
    });
</script>

<style>
    /* Style tidak ada perubahan */
    .rotate-180 {
        transform: rotate(180deg);
    }

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

    #sidebar.md\:w-20 .arrow-icon,
    #sidebar.md\:w-20 .nav-text,
    #sidebar.md\:w-20 #pln-text {
        display: none;
    }

    #sidebar.md\:w-20 #pln-logo-text,
    #sidebar.md\:w-20 nav a,
    #sidebar.md\:w-20 nav button {
        justify-content: center;
    }
</style>
