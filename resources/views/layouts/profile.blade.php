<div class="relative inline-block text-left">
    <button id="profileToggle"
        class="flex items-center focus:outline-none hover:bg-gray-300 px-3 py-2 rounded-md transition duration-200"
        aria-haspopup="true" aria-expanded="false">
        <h2 class="text-lg font-semibold text-black">{{ Auth::user()->name }}</h2>
        <svg class="ml-2 h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div id="profileDropdown"
        class="hidden origin-top-right absolute right-0 mt-2 bg-gray-900 rounded-lg shadow-xl z-50 p-6 text-gray-200
               ring-1 ring-black ring-opacity-5 focus:outline-none transition transform scale-95 opacity-0
               w-full sm:w-72 md:w-80 lg:w-96"
        role="menu" aria-orientation="vertical" aria-labelledby="profileToggle">
        <div class="flex items-center space-x-4">
            <div>
                <h2 class="text-xl font-semibold text-white">{{ Auth::user()->name }}</h2>
                <span class="inline-block mt-1 px-2 py-0.5 rounded-sm text-xs font-medium bg-red-600 text-white">
                    {{ Auth::user()->role }}
                </span>
                <div class="mt-2 text-sm text-gray-400">
                    <div>{{ Auth::user()->email }}</div>
                    <div>{{ Auth::user()->nim }}</div>
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            <div class="flex items-start space-x-3">
                <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-lg">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-sm">Complete Absen</span>
                        <span class="text-xs text-green-400 font-bold">80%</span>
                    </div>
                    <div class="text-xs text-gray-500">16 of 20 days</div>
                    <div class="w-full h-1.5 bg-gray-700 rounded mt-1 overflow-hidden">
                        <div class="h-1.5 bg-green-500 rounded" style="width: 80%"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div
                    class="w-9 h-9 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 text-lg">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-sm">Belum Check-In</span>
                        <span class="text-xs text-yellow-400 font-bold">2 hari</span>
                    </div>
                    <div class="text-xs text-gray-500">Tidak hadir pagi</div>
                    <div class="w-full h-1.5 bg-gray-700 rounded mt-1 overflow-hidden">
                        <div class="h-1.5 bg-yellow-500 rounded" style="width: 10%"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-lg">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-sm">Belum Check-Out</span>
                        <span class="text-xs text-red-400 font-bold">1 hari</span>
                    </div>
                    <div class="text-xs text-gray-500">Lupa keluar sistem</div>
                    <div class="w-full h-1.5 bg-gray-700 rounded mt-1 overflow-hidden">
                        <div class="h-1.5 bg-red-500 rounded" style="width: 5%"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 text-lg">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-sm">Absent</span>
                        <span class="text-xs text-gray-400 font-bold">1 hari</span>
                    </div>
                    <div class="text-xs text-gray-500">Tidak hadir sama sekali</div>
                    <div class="w-full h-1.5 bg-gray-700 rounded mt-1 overflow-hidden">
                        <div class="h-1.5 bg-gray-500 rounded" style="width: 5%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-700 flex justify-between text-sm text-blue-500">
            <a href="#" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-400 rounded">Edit
                Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-red-500 hover:underline focus:outline-none focus:ring-2 focus:ring-red-400 rounded">Log
                    Out</button>
            </form>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>

<script>
    const toggleBtn = document.getElementById('profileToggle');
    const dropdown = document.getElementById('profileDropdown');

    toggleBtn.addEventListener('click', () => {
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            setTimeout(() => {
                dropdown.classList.remove('opacity-0', 'scale-95');
                dropdown.classList.add('opacity-100', 'scale-100');
            }, 10);
        } else {
            dropdown.classList.add('opacity-0', 'scale-95');
            dropdown.classList.remove('opacity-100', 'scale-100');
            setTimeout(() => dropdown.classList.add('hidden'), 150);
        }
    });

    document.addEventListener('click', (e) => {
        if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('opacity-0', 'scale-95');
            dropdown.classList.remove('opacity-100', 'scale-100');
            setTimeout(() => dropdown.classList.add('hidden'), 150);
        }
    });
</script>