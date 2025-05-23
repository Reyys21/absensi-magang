<button id="mobile-menu-toggle"
    class="md:hidden fixed bottom-4 right-4 p-3 z-50 bg-[#2C3E50] text-white rounded-full shadow-lg hover:bg-[#3C5A6D] focus:outline-none">
    <i class="fa-solid fa-bars text-xl"></i>
</button>
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black opacity-50 z-30 md:hidden"></div>

<aside id="sidebar"
    class="bg-[#2C3E50] text-white flex flex-col justify-between shadow-lg h-screen overflow-y-auto
           fixed inset-y-0 left-0
           transform -translate-x-full 
           md:relative md:translate-x-0
           md:w-64
           transition-all duration-300 ease-in-out z-40">
    <div>
        <div class="p-4 flex items-center justify-between space-x-3 border-b border-[#1F2A36]">
            <div id="pln-logo-text" class="flex items-center space-x-3">
                <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="w-12 h-12 object-contain" />
                <div id="pln-text">
                    <p class="text-base font-bold leading-5">PLN</p>
                    <p class="text-xs text-gray-300">UID KALSELTENG</p>
                </div>
            </div>
            <button id="desktop-sidebar-toggle" class="hidden md:block p-2 text-white rounded-md hover:bg-[#3C5A6D] focus:outline-none transition duration-200">
                <i class="fa-solid fa-bars transition-transform duration-300"></i>
            </button>
        </div>

        <nav class="mt-6 px-4 space-y-2 pb-4">
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#FFD100] text-black active-link-indicator' : 'hover:bg-[#3C5A6D]' }}">
                <i class="fa-solid fa-house"></i> <span class="nav-text">Dashboard</span>
            </a>

            <div class="relative">
                <button id="btn-attendance" type="button"
                    class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 cursor-pointer
                {{ request()->routeIs('attendance.*') ? 'bg-[#FFD100] text-black active-link-indicator' : 'hover:bg-[#3C5A6D]' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-user-check"></i> <span class="nav-text">Attendance</span>
                    </div>
                    <i class="fa-solid fa-chevron-down transition-transform duration-300 arrow-icon"></i>
                </button>

                <div id="attendanceDropdown"
                    class="{{ request()->routeIs('attendance.*') ? 'mt-2 space-y-1 rounded-xl bg-[#34495E] block' : 'hidden mt-2 space-y-1 rounded-xl bg-[#34495E]' }}">
                    <a href="#"
                        class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100] rounded">History</a>
                    <a href="{{ route('attendance.my') }}"
                        class="block px-6 py-2 text-sm {{ request()->routeIs('attendance.my') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">My
                        Attendance</a>
                    <a href="#"
                        class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100] rounded">Attendance Records</a>
                </div>
            </div>

            <div class="relative">
                <button id="btn-approval" type="button"
                    class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 cursor-pointer
                    {{ request()->routeIs('approval.*') ? 'bg-[#FFD100] text-black active-link-indicator' : 'hover:bg-[#3C5A6D]' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-thumbs-up"></i> <span class="nav-text">Approval</span>
                    </div>
                    <i class="fa-solid fa-chevron-down transition-transform duration-300 arrow-icon"></i>
                </button>
                <div id="approvalDropdown"
                    class="{{ request()->routeIs('approval.*') ? 'mt-2 space-y-1 rounded-xl bg-[#34495E] block' : 'hidden mt-2 space-y-1 rounded-xl bg-[#34495E]' }}">
                    <a href="#"
                        class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100] rounded">Attendance
                        Approval</a>
                </div>
            </div>

            <a href="#"
                class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 hover:bg-[#3C5A6D]">
                <i class="fa-solid fa-cog"></i> <span class="nav-text">Settings</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-sm transition duration-150 hover:bg-[#3C5A6D]">
                    <i class="fa-solid fa-right-from-bracket"></i> <span class="nav-text">Log Out</span>
                </button>
            </form>
        </nav>
    </div>
</aside>

<script>
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const desktopSidebarToggle = document.getElementById('desktop-sidebar-toggle');
    const plnText = document.getElementById('pln-text');
    const navTexts = document.querySelectorAll('.nav-text');
    const arrowIcons = document.querySelectorAll('.arrow-icon');

    let isDesktopSidebarOpen = true; // Default terbuka di desktop

    function setSidebarState(isOpen) {
        const isDesktop = window.innerWidth >= 768;

        if (isDesktop) {
            sidebar.classList.remove('fixed', '-translate-x-full');
            sidebarOverlay.classList.add('hidden'); 
            sidebar.classList.add('md:relative', 'md:translate-x-0'); 

            sidebar.classList.toggle('md:w-64', isOpen);
            sidebar.classList.toggle('md:w-20', !isOpen);
            
            plnText.classList.toggle('hidden', !isOpen);
            navTexts.forEach(span => span.classList.toggle('hidden', !isOpen));
            arrowIcons.forEach(icon => icon.classList.toggle('hidden', !isOpen));
            // Toggle hamburger icon if sidebar is collapsed, otherwise keep it as hamburger
            desktopSidebarToggle.querySelector('i').classList.toggle('fa-bars', isOpen);
            desktopSidebarToggle.querySelector('i').classList.toggle('fa-chevron-left', !isOpen); // Mengganti ikon
            desktopSidebarToggle.querySelector('i').classList.toggle('rotate-180', !isOpen); // Putar jika jadi panah dan kolaps

            // Tutup dropdown jika sidebar kolaps
            if (!isOpen) {
                document.getElementById('attendanceDropdown').classList.add('hidden');
                document.getElementById('approvalDropdown').classList.add('hidden');
                document.querySelector('#btn-attendance .fa-chevron-down').classList.remove('rotate-180');
                document.querySelector('#btn-approval .fa-chevron-down').classList.remove('rotate-180');
            }
            
            isDesktopSidebarOpen = isOpen;

        } else { // Mobile
            sidebar.classList.remove('md:relative', 'md:w-64', 'md:w-20', 'md:translate-x-0');
            sidebar.classList.add('fixed');
            sidebar.classList.toggle('-translate-x-full', !isOpen);
            sidebarOverlay.classList.toggle('hidden', !isOpen);
            
            // Di mobile, pastikan sidebar selalu full dan semua teks/ikon terlihat
            sidebar.classList.add('w-64');
            plnText.classList.remove('hidden');
            navTexts.forEach(span => span.classList.remove('hidden'));
            arrowIcons.forEach(icon => icon.classList.remove('hidden'));
        }
    }

    function initializeSidebar() {
        if (window.innerWidth >= 768) {
            setSidebarState(isDesktopSidebarOpen);
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        } else {
            setSidebarState(false);
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
            setSidebarState(sidebar.classList.contains('md:w-20'));
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
                // Lebarkan sidebar terlebih dahulu
                setSidebarState(true); 
                // Beri sedikit delay agar transisi sidebar selesai sebelum dropdown dibuka
                setTimeout(() => {
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

<style>
    /* Rotate transition */
    .rotate-180 {
        transform: rotate(180deg);
    }

    /* Scrollbar styling */
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