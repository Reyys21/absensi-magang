{{-- Tombol toggle untuk mobile --}}
<button id="mobile-menu-toggle"
    class="md:hidden fixed bottom-4 right-4 p-3 z-50 bg-[#2C3E50] text-white rounded-full shadow-lg hover:bg-[#3C5A6D] focus:outline-none">
    <i class="fa-solid fa-bars text-xl"></i>
</button>

{{-- Overlay untuk mobile --}}
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black opacity-50 z-30 md:hidden"></div>

{{-- PERUBAHAN UTAMA ADA PADA STRUKTUR <aside> DI BAWAH INI --}}
<aside id="sidebar"
    class="bg-[#2A2B2A] text-white flex flex-col justify-between shadow-lg h-screen overflow-y-auto
           fixed inset-y-0 left-0
           transform -translate-x-full
           md:relative md:translate-x-0
           md:w-64
           transition-all duration-300 ease-in-out z-40">
    
    {{-- Blok 1: Header dan semua menu navigasi utama --}}
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
        <nav class="mt-6 px-2 space-y-1 pb-4">
            @auth
                {{-- MENU UNTUK USER BIASA (MAHASISWA/SISWA) --}}
                @can('access-user-pages')
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-house w-5 text-center"></i><span class="nav-text">Dasboard</span>
                    </a>
                    <a href="{{ route('attendance.history') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('attendance.history') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i> <span class="nav-text">Riwayat</span>
                    </a>
                    <a href="{{ route('attendance.my') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('attendance.my') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-user-check w-5 text-center"></i> <span class="nav-text">Absensi Saya</span>
                    </a>
                    <a href="{{ route('user.approval.requests') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('user.approval.requests') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-thumbs-up w-5 text-center"></i> <span class="nav-text">Permintaan koreksi</span>
                    </a>
                @endcan

                {{-- MENU UNTUK ADMIN & SUPERADMIN --}}
                @can('access-admin-pages')
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-user-gear w-5 text-center"></i> <span class="nav-text">Admin Dashboard</span>
                    </a>
                    
                   
                    <a href="{{ route('admin.monitoring.users.index') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('admin.monitoring.*') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i> <span class="nav-text">Monitoring User</span>
                    </a>

                     <a href="{{ route('admin.approval.requests') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('admin.approval.*') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-clipboard-check w-5 text-center"></i> <span class="nav-text">Permintaan Koreksi</span>
                    </a>
                     <a href="{{ route('admin.management.accounts.index') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('admin.management.*') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-address-card w-5 text-center"></i> <span class="nav-text"> Info User</span>
                    </a>
                   
                @endcan

                {{-- Menu Logout TELAH DIPINDAHKAN DARI SINI --}}
            @endauth
        </nav>
    </div>

    {{-- Blok 2: Tombol Keluar (Logout) --}}
    {{-- Tombol ini sekarang menjadi blok terpisah, sehingga akan didorong ke bawah oleh `justify-between` --}}
    <div class="p-4 border-t border-[#1F2A36]">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-4 px-4 py-2.5 rounded-xl text-sm transition duration-150 hover:bg-[#3C5A6D]">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> <span class="nav-text">Keluar</span>
            </button>
        </form>
    </div>

</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const desktopSidebarToggle = document.getElementById('desktop-sidebar-toggle');
        
        if (!sidebar || !mobileMenuToggle || !desktopSidebarToggle || !sidebarOverlay) {
            console.error("Satu atau lebih elemen sidebar penting tidak ditemukan.");
            return;
        }

        let isDesktopSidebarOpen = localStorage.getItem('isDesktopSidebarOpen') !== 'false';

        const setSidebarState = (isOpen) => {
            const plnText = document.getElementById('pln-text');
            const navTexts = document.querySelectorAll('.nav-text');

            sidebar.classList.toggle('md:w-64', isOpen);
            sidebar.classList.toggle('md:w-20', !isOpen);
            
            if(plnText) plnText.classList.toggle('hidden', !isOpen);
            navTexts.forEach(span => span.classList.toggle('hidden', !isOpen));
        };

        desktopSidebarToggle.addEventListener('click', () => {
            isDesktopSidebarOpen = !isDesktopSidebarOpen;
            localStorage.setItem('isDesktopSidebarOpen', isDesktopSidebarOpen);
            setSidebarState(isDesktopSidebarOpen);
        });

        const toggleMobileMenu = () => {
            const isSidebarHidden = sidebar.classList.contains('-translate-x-full');
            
            // Toggle posisi sidebar
            sidebar.classList.toggle('-translate-x-full', !isSidebarHidden);
            
            // REVISI LOGIKA OVERLAY:
            // Jika sidebar akan ditampilkan (isHidden = true), maka HAPUS class 'hidden' dari overlay.
            // Jika sidebar akan disembunyikan (isHidden = false), maka TAMBAHKAN class 'hidden' ke overlay.
            sidebarOverlay.classList.toggle('hidden', !isSidebarHidden);
        };

        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
        sidebarOverlay.addEventListener('click', toggleMobileMenu);
        
        // Inisialisasi sidebar saat halaman dimuat
        if (window.innerWidth >= 768) {
            setSidebarState(isDesktopSidebarOpen);
        }
    });
</script>



<style>
    #sidebar.md\:w-20 .nav-text,
    #sidebar.md\:w-20 #pln-text {
        display: none;
    }
    #sidebar.md\:w-20 #pln-logo-text,
    #sidebar.md\:w-20 nav a,
    #sidebar.md\:w-20 nav button {
        justify-content: center;
    }
    #sidebar.md\:w-20 nav a,
    #sidebar.md\:w-20 nav button {
        padding-left: 1rem; /* 16px */
        padding-right: 1rem;
    }
</style>