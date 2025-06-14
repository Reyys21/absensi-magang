{{-- Tombol 'hamburger' untuk membuka sidebar di mobile --}}
<button id="mobile-menu-toggle"
    class="md:hidden fixed bottom-4 right-4 w-14 h-14 flex items-center justify-center z-50 bg-[#2C3E50] text-white rounded-full shadow-lg hover:bg-[#3C5A6D] focus:outline-none transition-transform transform active:scale-90">
    <i class="fa-solid fa-bars text-xl"></i>
</button>

{{-- Overlay gelap di belakang sidebar saat terbuka di mobile --}}
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-60 z-30 md:hidden transition-opacity duration-300 ease-in-out opacity-0"></div>

{{-- STRUKTUR UTAMA SIDEBAR --}}
<aside id="sidebar"
    class="bg-[#2A2B2A] text-white flex flex-col justify-between shadow-lg h-screen overflow-y-auto
           fixed inset-y-0 left-0
           transform -translate-x-full
           md:relative md:translate-x-0
           w-60 {{-- <<< 1. LEBAR DIUBAH MENJADI w-60 --}}
           transition-all duration-300 ease-in-out z-40">
    
    {{-- Bagian Atas: Logo, Menu Navigasi --}}
    <div>
        <div class="p-4 flex items-center justify-between space-x-3 border-b border-gray-700 sidebar-header">
            <div id="pln-logo-text" class="flex items-center space-x-3 transition-opacity duration-200">
                <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="w-12 h-12 object-contain" />
                <div id="pln-text" class="transition-opacity duration-200">
                    <p class="text-base font-bold leading-5">PLN</p>
                    <p class="text-xs text-gray-300">UID KALSELTENG</p>
                </div>
            </div>
            <button id="desktop-sidebar-toggle"
                class="hidden md:block p-2 text-white rounded-md hover:bg-[#3C5A6D] focus:outline-none transition-colors duration-200">
                <i class="fa-solid fa-bars transition-transform duration-300"></i>
            </button>
        </div>

        {{-- Menu Navigasi --}}
        <nav class="mt-4 px-3 space-y-1 pb-4">
            @auth
                @can('access-user-pages')
                    <div class="flex items-center px-1 pt-3 pb-2 space-x-2 heading-container">
                        <span class="text-xs uppercase text-gray-400 font-semibold tracking-wider nav-text whitespace-nowrap transition-opacity duration-200">Menu Utama</span>
                        <span class="flex-grow border-t border-gray-700 heading-line transition-all duration-300"></span>
                    </div>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#3C5A6D] text-[#FFD100]' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-house w-5 text-center"></i><span class="nav-text transition-opacity duration-200">Dasboard</span>
                    </a>

                    <div class="flex items-center px-1 pt-4 pb-2 space-x-2 heading-container">
                        <span class="text-xs uppercase text-gray-400 font-semibold tracking-wider nav-text whitespace-nowrap transition-opacity duration-200">Manajemen Absensi</span>
                        <span class="flex-grow border-t border-gray-700 heading-line transition-all duration-300"></span>
                    </div>
                    <a href="{{ route('attendance.history') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('attendance.history') ? 'bg-[#3C5A6D] text-[#FFD100]' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i> <span class="nav-text transition-opacity duration-200">Riwayat</span>
                    </a>
                    <a href="{{ route('attendance.my') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('attendance.my') ? 'bg-[#3C5A6D] text-[#FFD100]' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-user-check w-5 text-center"></i> <span class="nav-text transition-opacity duration-200">Absensi Saya</span>
                    </a>
                    <a href="{{ route('user.approval.requests') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('user.approval.requests') ? 'bg-[#3C5A6D] text-[#FFD100]' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-thumbs-up w-5 text-center"></i> <span class="nav-text transition-opacity duration-200">Permintaan Koreksi</span>
                    </a>
                @endcan

                @can('access-admin-pages')
                    <div class="flex items-center px-1 pt-3 pb-2 space-x-2 heading-container">
                        <span class="text-xs uppercase text-gray-400 font-semibold tracking-wider nav-text whitespace-nowrap transition-opacity duration-200">Admin</span>
                        <span class="flex-grow border-t border-gray-700 heading-line transition-all duration-300"></span>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-[#3C5A6D] text-[#FFD100]' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-user-gear w-5 text-center"></i> <span class="nav-text transition-opacity duration-200">Admin Dashboard</span>
                    </a>
                    
                    <div class="flex items-center px-1 pt-4 pb-2 space-x-2 heading-container">
                        <span class="text-xs uppercase text-gray-400 font-semibold tracking-wider nav-text whitespace-nowrap transition-opacity duration-200">Manajemen User</span>
                        <span class="flex-grow border-t border-gray-700 heading-line transition-all duration-300"></span>
                    </div>
                    <a href="{{ route('admin.monitoring.users.index') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('admin.monitoring.*') ? 'bg-[#3C5A6D] text-[#FFD100]' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i> <span class="nav-text transition-opacity duration-200">Monitoring User</span>
                    </a>
                     <a href="{{ route('admin.approval.requests') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('admin.approval.*') ? 'bg-[#3C5A6D] text-[#FFD100]' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-clipboard-check w-5 text-center"></i> <span class="nav-text transition-opacity duration-200">Permintaan Koreksi</span>
                    </a>
                     <a href="{{ route('admin.management.accounts.index') }}" class="flex items-center gap-4 pl-4 pr-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('admin.management.*') ? 'bg-[#3C5A6D] text-[#FFD100]' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-address-card w-5 text-center"></i> <span class="nav-text transition-opacity duration-200"> Info User</span>
                    </a>
                @endcan
            @endauth
        </nav>
    </div>

    {{-- Bagian Bawah: Profil & Tombol Keluar --}}
    <div class="p-3 border-t border-gray-700">
        <button onclick="window.location.href='{{ route('profile.edit') }}'" class="w-full flex items-center gap-3 p-2 rounded-lg transition duration-150 hover:bg-[#3C5A6D] text-left">
            <img class="h-10 w-10 rounded-full object-cover" src="{{ optional(Auth::user())->profile_photo_path ? (Str::startsWith(optional(Auth::user())->profile_photo_path, 'profile_photos/') ? asset(optional(Auth::user())->profile_photo_path) : asset('storage/' . optional(Auth::user())->profile_photo_path)) : asset('profile_photos/avatar_1 (1).jpg') }}" alt="Foto Profil">
            <div class="nav-text transition-opacity duration-200">
                <p class="text-sm font-semibold text-white leading-4">{{ Str::limit(optional(Auth::user())->name, 15) }}</p>
                <p class="text-xs text-gray-400">{{ optional(Auth::user())->role ? Str::ucfirst(optional(Auth::user())->role) : 'User' }}</p>
            </div>
        </button>
        <div class="border-t border-gray-700 my-3"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-4 px-4 py-2.5 rounded-lg text-sm transition duration-150 hover:bg-[#3C5A6D]">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> <span class="nav-text transition-opacity duration-200">Keluar</span>
            </button>
        </form>
    </div>
</aside>

{{-- SCRIPT RESPONSIF --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const desktopSidebarToggle = document.getElementById('desktop-sidebar-toggle');
    
    const toggleMobileSidebar = () => {
        const isHidden = sidebar.classList.contains('-translate-x-full');
        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
            setTimeout(() => sidebarOverlay.classList.add('opacity-100'), 10);
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.remove('opacity-100');
            setTimeout(() => sidebarOverlay.classList.add('hidden'), 300);
            document.body.style.overflow = '';
        }
    };
    if(mobileMenuToggle) mobileMenuToggle.addEventListener('click', toggleMobileSidebar);
    if(sidebarOverlay) sidebarOverlay.addEventListener('click', toggleMobileSidebar);
    
    if (desktopSidebarToggle) {
        let isDesktopSidebarOpen = localStorage.getItem('isDesktopSidebarOpen') !== 'false';
        const setDesktopSidebarState = (isOpen) => {
            const plnText = document.getElementById('pln-text');
            const navTexts = document.querySelectorAll('.nav-text');
            sidebar.classList.toggle('md:w-60', isOpen); // <<< LEBAR DISINI JUGA DIUBAH MENJADI w-60
            sidebar.classList.toggle('md:w-20', !isOpen);
            if(plnText) plnText.classList.toggle('hidden', !isOpen);
            navTexts.forEach(span => span.classList.toggle('hidden', !isOpen));
        };
        desktopSidebarToggle.addEventListener('click', () => {
            isDesktopSidebarOpen = !isDesktopSidebarOpen;
            localStorage.setItem('isDesktopSidebarOpen', isDesktopSidebarOpen);
            setDesktopSidebarState(isDesktopSidebarOpen);
        });
        if (window.innerWidth >= 768) {
            setDesktopSidebarState(isDesktopSidebarOpen);
        }
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 768) {
            toggleMobileSidebar();
        }
    });
});
</script>

{{-- STYLE UNTUK MODE CIUT/COLLAPSE --}}
<style>
    .nav-text, #pln-text, #pln-logo-text {
        transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
    }
    #sidebar.md\:w-20 .nav-text,
    #sidebar.md\:w-20 #pln-text,
    #sidebar.md\:w-20 #pln-logo-text {
        opacity: 0;
        visibility: hidden;
    }
    #sidebar.md\:w-20 nav a,
    #sidebar.md\:w-20 nav button,
    #sidebar.md\:w-20 .sidebar-header > button {
        justify-content: center;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    #sidebar.md\:w-20 .sidebar-header {
        justify-content: flex-end;
    }
    
    /* ▼▼▼ PERBAIKAN UNTUK GARIS TENGAH ▼▼▼ */
    #sidebar.md\:w-20 .heading-container {
        position: relative;
        height: 1rem; /* Memberi ruang untuk garis */
    }
    #sidebar.md\:w-20 .heading-line {
        display: none; /* Sembunyikan garis panjang asli */
    }
    #sidebar.md\:w-20 .heading-container::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%); /* Trik untuk sentering sempurna */
        width: 1.5rem; 
        height: 1px;
        background-color: #4a5568; /* Warna abu-abu gelap */
    }
</style>