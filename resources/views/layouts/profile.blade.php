<div class="relative inline-block text-left">
    <button id="profileToggle"
        class="flex items-center focus:outline-none hover:bg-gray-300 px-3 py-2 rounded-md transition duration-200"
        aria-haspopup="true" aria-expanded="false">
        {{-- FOTO PROFIL --}}
        <img class="h-10 w-10 rounded-full object-cover mr-2"
            src="{{
                optional(Auth::user())->profile_photo_path
                ? (
                    // Jika path dimulai dengan 'profile_photos/', itu adalah gambar default dari folder public
                    Str::startsWith(optional(Auth::user())->profile_photo_path, 'profile_photos/')
                    ? asset(optional(Auth::user())->profile_photo_path)
                    // Jika tidak, itu adalah gambar unggahan dari storage/app/public
                    : asset('storage/' . optional(Auth::user())->profile_photo_path)
                )
                // Jika profile_photo_path kosong, gunakan default fallback
                : asset('profile_photos/avatar_1 (1).jpg')
            }}"
            alt="{{ optional(Auth::user())->name ?: 'Pengguna' }}">
        {{-- END FOTO PROFIL --}}
        <h2 class="text-lg font-semibold text-black">{{ optional(Auth::user())->name ?: 'Guest User' }}</h2>
        <svg class="ml-2 h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div id="profileDropdown"
        class="hidden origin-top-right absolute mt-2 bg-gray-900 rounded-lg shadow-xl z-50 p-6 text-gray-200
               ring-1 ring-black ring-opacity-5 focus:outline-none transition transform scale-95 opacity-0
               w-auto min-w-[14rem] max-w-[calc(100vw-2rem)] right-0
               sm:w-72 sm:max-w-none
               md:w-80
               lg:w-96"
        role="menu" aria-orientation="vertical" aria-labelledby="profileToggle">
        <div class="flex items-start space-x-4">
            {{-- FOTO PROFIL DI DALAM DROPDOWN --}}
            <img class="h-16 w-16 rounded-full object-cover flex-shrink-0"
                src="{{
                    optional(Auth::user())->profile_photo_path
                    ? (
                        Str::startsWith(optional(Auth::user())->profile_photo_path, 'profile_photos/')
                        ? asset(optional(Auth::user())->profile_photo_path)
                        : asset('storage/' . optional(Auth::user())->profile_photo_path)
                    )
                    : asset('profile_photos/avatar_1 (1).jpg')
                }}"
                alt="{{ optional(Auth::user())->name ?: 'Pengguna' }}">
            <div>
                <h2 class="text-xl font-semibold text-white leading-tight">{{ optional(Auth::user())->name ?: 'Guest User' }}</h2>
                <span class="inline-block mt-1 px-2 py-0.5 rounded-sm text-xs font-medium bg-red-600 text-white">
                    {{ optional(Auth::user())->role ?: 'N/A' }}
                </span>
                <div class="mt-2 text-sm text-gray-400 break-words">
                    <div>{{ optional(Auth::user())->email ?: 'N/A' }}</div>
                    <div>{{ optional(Auth::user())->nim ?: 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-700 flex flex-col space-y-3 sm:flex-row sm:justify-between sm:space-y-0 text-sm">
            <a href="{{ route('profile.edit') }}" class="text-blue-500 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-400 rounded text-center sm:text-left">Edit Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto"> {{-- Tambah w-full sm:w-auto DI SINI --}}
                @csrf
                <button type="submit"
                    class="text-red-500 hover:underline focus:outline-none focus:ring-2 focus:ring-red-400 rounded w-full text-center sm:w-auto sm:text-right">Log Out</button> {{-- Tambah w-full sm:w-auto DI SINI --}}
            </form>
        </div>
    </div>
</div>

<script>
    // Ini adalah script untuk dropdown profil, bukan cropper.js
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