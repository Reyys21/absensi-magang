{{-- resources/views/layouts/sidebar.blade.php --}}

{{-- Tombol toggle untuk mobile (di luar sidebar, fixed di pojok kanan bawah) --}}
{{-- Class 'fixed bottom-4 right-4 z-50' sangat penting --}}
<button id="mobile-menu-toggle"
    class="md:hidden fixed bottom-4 right-4 p-3 z-50 bg-[#2C3E50] text-white rounded-full shadow-lg hover:bg-[#3C5A6D] focus:outline-none">
    <i class="fa-solid fa-bars text-xl"></i>
</button>

{{-- Overlay untuk mobile (tersembunyi secara default, muncul saat sidebar mobile terbuka) --}}
{{-- Z-index harus lebih rendah dari sidebar tapi di atas konten utama --}}
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black opacity-50 z-30 md:hidden"></div>

<aside id="sidebar"
    class="bg-[#2C3E50] text-white flex flex-col justify-between shadow-lg h-screen overflow-y-auto
           fixed inset-y-0 left-0
           transform -translate-x-full {{-- Selalu mulai tersembunyi di mobile --}}
           md:relative md:translate-x-0 {{-- Di desktop, jadi relatif dan tidak tersembunyi --}}
           md:w-64 {{-- Lebar default di desktop, JS akan toggle ke w-20 --}}
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
            <button id="desktop-sidebar-toggle"
                class="hidden md:block p-2 text-white rounded-md hover:bg-[#3C5A6D] focus:outline-none transition duration-200">
                <i class="fa-solid fa-bars transition-transform duration-300"></i>
            </button>
        </div>

        <nav class="mt-6 px-4 space-y-2 pb-4">
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#FFD100] text-black active-link-indicator' : 'hover:bg-[#3C5A6D]' }}">
                <i class="fa-solid fa-house"></i> <span class="nav-text">Dasboard</span>
            </a>

            <div class="relative">
                <button id="btn-attendance" type="button"
                    class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 cursor-pointer
                    {{ request()->routeIs('attendance.*') ? 'bg-[#FFD100] text-black active-link-indicator' : 'hover:bg-[#3C5A6D]' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-user-check"></i> <span class="nav-text">Absensi</span>
                    </div>
                    <i class="fa-solid fa-chevron-down transition-transform duration-300 arrow-icon"></i>
                </button>

                <div id="attendanceDropdown"
                    class="{{ request()->routeIs('attendance.*') ? 'mt-2 space-y-1 rounded-xl bg-[#34495E] block' : 'hidden mt-2 space-y-1 rounded-xl bg-[#34495E]' }}">
                    <a href="{{ route('attendance.history') }}"
                        class="block px-6 py-2 text-sm {{ request()->routeIs('attendance.history') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Riwayat</a>
                    <a href="{{ route('attendance.my') }}"
                        class="block px-6 py-2 text-sm {{ request()->routeIs('attendance.my') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Absensi
                        Saya</a>
                    
                </div>
            </div>

            <div class="relative">
                <button id="btn-approval" type="button"
                    class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 cursor-pointer
                    {{ request()->routeIs('approval.*') ? 'bg-[#FFD100] text-black active-link-indicator' : 'hover:bg-[#3C5A6D]' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-thumbs-up"></i> <span class="nav-text">Persetujuan</span>
                    </div>
                    <i class="fa-solid fa-chevron-down transition-transform duration-300 arrow-icon"></i>
                </button>
                <div id="approvalDropdown"
                    class="{{ request()->routeIs('approval.*') ? 'mt-2 space-y-1 rounded-xl bg-[#34495E] block' : 'hidden mt-2 space-y-1 rounded-xl bg-[#34495E]' }}">
                    <a href="{{ route('approval.requests') }}"
                        class="block px-6 py-2 text-sm {{ request()->routeIs('approval.requests') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }} rounded">Persetujuan
                        Absensi</a>
                </div>
            </div>

            

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-sm transition duration-150 hover:bg-[#3C5A6D]">
                    <i class="fa-solid fa-right-from-bracket"></i> <span class="nav-text">Keluar</span>
                </button>
            </form>
        </nav>
    </div>
</aside>

<script>
    // Pastikan DOM sudah sepenuhnya dimuat sebelum menjalankan skrip
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const desktopSidebarToggle = document.getElementById('desktop-sidebar-toggle');
        const plnText = document.getElementById('pln-text');
        const navTexts = document.querySelectorAll('.nav-text');
        const arrowIcons = document.querySelectorAll('.arrow-icon');
        const mainContent = document.getElementById('main-content'); // Dapatkan elemen main-content

        // Hanya inisialisasi jika elemen-elemen penting ditemukan
        if (!sidebar || !mainContent || !mobileMenuToggle || !desktopSidebarToggle || !sidebarOverlay) {
            console.warn("Satu atau lebih elemen sidebar penting tidak ditemukan. Fungsionalitas sidebar mungkin terbatas atau tidak ada.");
            return; // Hentikan eksekusi jika elemen penting tidak ada
        }

        let isDesktopSidebarOpen = localStorage.getItem('isDesktopSidebarOpen') === 'false' ? false : true;

        function setSidebarState(isOpen) {
            const isDesktop = window.innerWidth >= 768;

            if (isDesktop) {
                // Logika untuk desktop
                sidebar.classList.remove('-translate-x-full'); // Pastikan tidak tersembunyi
                sidebar.classList.add('fixed'); // Pastikan fixed di desktop

                sidebarOverlay.classList.add('hidden'); // Selalu sembunyikan overlay di desktop

                sidebar.classList.toggle('md:w-64', isOpen);
                sidebar.classList.toggle('md:w-20', !isOpen);

                // Toggle visibilitas teks dan ikon
                if (plnText) plnText.classList.toggle('hidden', !isOpen);
                navTexts.forEach(span => span.classList.toggle('hidden', !isOpen));
                arrowIcons.forEach(icon => icon.classList.toggle('hidden', !isOpen));

                // Atur ikon desktop toggle
                if (isOpen) {
                    desktopSidebarToggle.querySelector('i').classList.remove('fa-chevron-left', 'rotate-180');
                    desktopSidebarToggle.querySelector('i').classList.add('fa-bars');
                } else {
                    desktopSidebarToggle.querySelector('i').classList.remove('fa-bars');
                    desktopSidebarToggle.querySelector('i').classList.add('fa-chevron-left', 'rotate-180');
                }

                // Tutup dropdown saat sidebar kolaps di desktop
                if (!isOpen) {
                    const attendanceDropdown = document.getElementById('attendanceDropdown');
                    const approvalDropdown = document.getElementById('approvalDropdown');
                    if (attendanceDropdown) attendanceDropdown.classList.add('hidden');
                    if (approvalDropdown) approvalDropdown.classList.add('hidden');

                    const attendanceArrow = document.querySelector('#btn-attendance .fa-chevron-down');
                    const approvalArrow = document.querySelector('#btn-approval .fa-chevron-down');
                    if (attendanceArrow) attendanceArrow.classList.remove('rotate-180');
                    if (approvalArrow) approvalArrow.classList.remove('rotate-180');
                }

                isDesktopSidebarOpen = isOpen;
                localStorage.setItem('isDesktopSidebarOpen', isDesktopSidebarOpen);

                // Atur margin kiri main content
                if (mainContent) {
                    mainContent.style.marginLeft = '0';
                }

            } else { // Mobile
                sidebar.classList.remove('md:relative', 'md:w-64', 'md:w-20', 'md:translate-x-0'); // Hapus kelas desktop
                sidebar.classList.add('fixed'); // Sidebar fixed di mobile

                sidebar.classList.toggle('-translate-x-full', !isOpen); // Toggle untuk menyembunyikan/menampilkan drawer
                sidebarOverlay.classList.toggle('hidden', !isOpen); // Toggle overlay

                if (mainContent) {
                    mainContent.style.marginLeft = '0'; // Tidak ada margin di mobile
                }

                // Pastikan sidebar selalu terlihat penuh di mobile (w-64)
                sidebar.classList.add('w-64');
                if (plnText) plnText.classList.remove('hidden');
                navTexts.forEach(span => span.classList.remove('hidden'));
                arrowIcons.forEach(icon => icon.classList.remove('hidden'));

                // Atur ikon mobile toggle
                if (isOpen) { // Sidebar terbuka, tampilkan ikon silang
                    mobileMenuToggle.querySelector('i').classList.remove('fa-bars');
                    mobileMenuToggle.querySelector('i').classList.add('fa-times');
                } else { // Sidebar tertutup, tampilkan ikon hamburger
                    mobileMenuToggle.querySelector('i').classList.remove('fa-times');
                    mobileMenuToggle.querySelector('i').classList.add('fa-bars');
                }
                // Pastikan desktop toggle ikon kembali ke bars jika terlihat karena suatu alasan
                if (desktopSidebarToggle) {
                    desktopSidebarToggle.querySelector('i').classList.remove('fa-chevron-left', 'rotate-180');
                    desktopSidebarToggle.querySelector('i').classList.add('fa-bars');
                }
            }
        }

        function initializeSidebar() {
            if (window.innerWidth >= 768) {
                // Tampilan desktop
                isDesktopSidebarOpen = localStorage.getItem('isDesktopSidebarOpen') === 'false' ? false : true;
                setSidebarState(isDesktopSidebarOpen);
                sidebar.classList.remove('-translate-x-full'); // Pastikan sidebar tidak tersembunyi
                sidebarOverlay.classList.add('hidden'); // Pastikan overlay tersembunyi
            } else {
                // Tampilan mobile
                setSidebarState(false); // Selalu tutup sidebar di mobile saat inisialisasi
            }
        }

        // Event Listeners
        mobileMenuToggle.addEventListener('click', () => {
            // Cek status saat ini dari sidebar untuk menentukan state berikutnya
            const currentStateIsHidden = sidebar.classList.contains('-translate-x-full');
            setSidebarState(currentStateIsHidden);
        });

        desktopSidebarToggle.addEventListener('click', () => {
            isDesktopSidebarOpen = !isDesktopSidebarOpen; // Toggle state
            localStorage.setItem('isDesktopSidebarOpen', isDesktopSidebarOpen);
            setSidebarState(isDesktopSidebarOpen);
        });

        sidebarOverlay.addEventListener('click', () => {
            setSidebarState(false); // Tutup sidebar mobile saat overlay diklik
        });

        // Fungsi untuk menangani klik tombol dropdown
        function handleDropdownClick(buttonId, dropdownId) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.addEventListener('click', function(event) {
                    event.stopPropagation(); // Mencegah event bubbling ke parent

                    const dropdown = document.getElementById(dropdownId);
                    const isDesktop = window.innerWidth >= 768;

                    if (isDesktop && !isDesktopSidebarOpen) { // Jika di desktop dan sidebar kolaps
                        setSidebarState(true); // Lebarkan sidebar terlebih dahulu
                        setTimeout(() => {
                            if (dropdown) dropdown.classList.toggle('hidden');
                            const arrow = this.querySelector('i.fa-chevron-down');
                            if (arrow) arrow.classList.toggle('rotate-180');
                        }, 300); // Sesuaikan dengan durasi transisi sidebar
                    } else {
                        // Langsung toggle dropdown (baik di mobile atau sidebar sudah terbuka di desktop)
                        if (dropdown) dropdown.classList.toggle('hidden');
                        const arrow = this.querySelector('i.fa-chevron-down');
                        if (arrow) arrow.classList.toggle('rotate-180');
                    }
                });
            }
        }

        initializeSidebar(); // Panggil inisialisasi saat DOM siap
        handleDropdownClick('btn-attendance', 'attendanceDropdown');
        handleDropdownClick('btn-approval', 'approvalDropdown');

        // Tutup sidebar saat mengklik item navigasi di mobile (kecuali dropdown itu sendiri)
        const navLinks = document.querySelectorAll('#sidebar nav a, #sidebar nav button');
        navLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                // Jangan tutup sidebar jika klik pada tombol dropdown itu sendiri
                if (this.id === 'btn-attendance' || this.id === 'btn-approval') {
                    return;
                }

                // Juga, jangan tutup sidebar jika klik pada item di dalam dropdown
                let parent = this.parentElement;
                let isInsideDropdown = false;
                while(parent) {
                    if (parent.id === 'attendanceDropdown' || parent.id === 'approvalDropdown') {
                        isInsideDropdown = true;
                        break;
                    }
                    parent = parent.parentElement;
                }
                if (isInsideDropdown) {
                    return;
                }

                // Jika di mobile (lebar < 768px), tutup sidebar
                if (window.innerWidth < 768) {
                    setTimeout(() => {
                        setSidebarState(false);
                    }, 100);
                }
            });
        });

        // Panggil initializeSidebar lagi saat ukuran jendela berubah
        window.addEventListener('resize', () => {
            // Memberikan sedikit delay agar transisi ukuran jendela selesai sebelum menyesuaikan sidebar
            setTimeout(initializeSidebar, 150);
        });

        // --- Tambahan untuk fungsi umum seperti updateClock() jika masih ada ---
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const clockElement = document.getElementById("clock");
            if (clockElement) {
                clockElement.textContent = `${hours}.${minutes}`;
            }
        }
        // Jika 'clock' ada di layout.profile atau tempat lain yang selalu ada, panggil ini:
        // (Pastikan ID 'clock' ada di HTML Anda jika menggunakan fungsi ini)
        // updateClock();
        // setInterval(updateClock, 1000);
    }); // Akhir dari DOMContentLoaded
</script>

<style>
    /* -- MULAI CSS SIDEBAR -- */

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

    /* Pastikan main content memiliki z-index yang lebih rendah agar tidak menutupi sidebar mobile */
    main {
        z-index: 10;
    }
    /* -- AKHIR CSS SIDEBAR -- */
</style>