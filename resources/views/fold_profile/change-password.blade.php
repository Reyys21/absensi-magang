@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">

        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-800 dark:text-white leading-tight mb-4 sm:mb-0">
                Ubah Password
            </h1>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-sm
                       bg-slate-100 text-slate-700 hover:bg-slate-200
                       dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-900
                       transition-colors duration-200 ease-in-out">
                <svg class="w-5 h-5 mr-2 -ml-1 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali
            </a>
        </div>

        {{-- Status / Error Messages --}}
        @if (session('status'))
            <div class="bg-green-50 dark:bg-green-900/40 border border-green-200 dark:border-green-700 border-l-4 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300 p-4 mb-6 rounded-lg shadow-sm"
                role="alert">
                <div class="flex items-center">
                    <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 dark:text-green-400 mr-3"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path
                                d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z" />
                        </svg></div>
                    <div>
                        <p class="font-semibold text-lg">Berhasil!</p>
                        <p class="text-sm">{{ session('status') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/40 border border-red-200 dark:border-red-700 border-l-4 border-red-500 dark:border-red-600 text-red-700 dark:text-red-300 p-4 mb-6 rounded-lg shadow-sm"
                role="alert">
                <div class="flex items-center">
                    <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 dark:text-red-400 mr-3"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path
                                d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z" />
                        </svg></div>
                    <div>
                        <p class="font-semibold text-lg">Ada Kesalahan!</p>
                        <ul class="mt-1 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- KONTEN UTAMA DARI GRID 3 KOLOM --}}
        <div
            class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 grid grid-cols-1 md:grid-cols-3 gap-10 min-w-0">
            {{-- KOLOM KIRI (FOTO PROFIL & NAVIGASI LINK) --}}
            <div class="md:col-span-1 flex flex-col items-center md:items-center space-y-6">
                <div id="profile-image-wrapper"
                    class="relative h-48 w-48 rounded-full overflow-hidden ring-4 ring-offset-4 ring-indigo-500 dark:ring-offset-slate-800 cursor-pointer shadow-lg group">
                    <img id="main-profile-image" class="w-full h-full object-cover"
                        src="{{ optional($user)->profile_photo_path
                            ? (Str::startsWith(optional($user)->profile_photo_path, 'profile_photos/')
                                ? asset(optional($user)->profile_photo_path)
                                : asset('storage/' . optional($user)->profile_photo_path))
                            : asset('profile_photos/avatar_1 (1).jpg') }}"
                        alt="{{ optional($user)->name ?: 'Pengguna' }}">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-white text-base font-medium">
                        <svg class="w-9 h-9 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ganti Foto
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 text-center md:text-left">
                    {{ optional($user)->name }}</p>
                <span
                    class="inline-block px-4 py-1.5 rounded-full text-sm font-semibold bg-rose-100 dark:bg-rose-700 text-rose-700 dark:text-rose-100">
                    {{ optional($user)->role ? Str::ucfirst(optional($user)->role) : 'N/A' }}
                </span>

                {{-- NAVIGASI LINK --}}
                <div class="w-full flex flex-col space-y-3 mt-6">
                    <a href="{{ route('profile.edit') }}"
                        class="w-full text-left py-3 px-5 rounded-xl font-semibold transition-colors duration-200 ease-in-out
                        bg-slate-100 text-slate-700 hover:bg-slate-200 shadow-sm
                        dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600
                        flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi Umum
                    </a>
                    <a href="{{ route('profile.change-password') }}"
                        class="w-full text-left py-3 px-5 rounded-xl font-semibold transition-colors duration-200 ease-in-out
                        bg-indigo-600 text-white shadow-md
                        hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:text-white
                        flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2v5a2 2 0 01-2 2h-5a2 2 0 01-2-2V9a2 2 0 012-2h5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6"></path>
                        </svg>
                        Ubah Password
                    </a>
                </div>
            </div>

            {{-- KOLOM KANAN (FORM UBAH PASSWORD) --}}
            <div class="md:col-span-2 space-y-8">
                <div
                    class="bg-slate-50 dark:bg-slate-700/50 p-7 rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
                    <h2
                        class="text-2xl font-bold mb-6 text-slate-700 dark:text-slate-200 border-b border-slate-300 dark:border-slate-600 pb-4">
                        Perbarui Password
                    </h2>
                    <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-6">
                        @csrf
                        @method('patch')

                        <div>
                            <label for="current_password"
                                class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Password Saat
                                Ini:</label>
                            <input type="password" name="current_password" id="current_password"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                       transition duration-150 ease-in-out text-base"
                                required>
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password"
                                class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Password
                                Baru:</label>
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                       transition duration-150 ease-in-out text-base"
                                required>
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Konfirmasi
                                Password Baru:</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                       transition duration-150 ease-in-out text-base"
                                required>
                            @error('password_confirmation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg shadow-md
                                   bg-indigo-600 text-white hover:bg-indigo-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800
                                   transition-colors duration-200 ease-in-out">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 11V7a4 4 0 118 0v4m-4 2v5m-4-5h8m-4-5h8m-4-5h8m-4-5h8m-4-5h8"></path>
                                </svg>
                                Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL UBAH FOTO PROFIL (Tetap sama, karena ini modal global) --}}
    <div id="photoModal" class="fixed inset-0 bg-gray-900 bg-opacity-80 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-7 sm:p-9 max-w-2xl w-full transform transition-all duration-300 ease-out scale-95 opacity-0"
            id="modal-content-wrapper">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Ubah Foto Profil</h2>
                <button id="closeModalBtn"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors duration-200 ease-in-out">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Bagian: Unggah Foto Baru --}}
                <div>
                    <h3 class="text-xl font-semibold mb-5 text-slate-700 dark:text-slate-200">Unggah Foto Baru</h3>
                    <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data"
                        id="uploadPhotoForm" class="space-y-5">
                        @csrf
                        <div>
                            <label for="profile_photo"
                                class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Pilih
                                Gambar:</label>
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                                class="block w-full text-sm text-slate-600 dark:text-slate-300
                                       file:mr-4 file:py-2.5 file:px-5
                                       file:rounded-md file:border-0
                                       file:text-sm file:font-semibold
                                       file:bg-indigo-100 dark:file:bg-indigo-800 file:text-indigo-700 dark:file:text-indigo-200
                                       hover:file:bg-indigo-200 dark:hover:file:bg-indigo-700 cursor-pointer
                                       border border-slate-300 dark:border-slate-600 rounded-lg p-1 transition-all duration-200 ease-in-out">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Maksimal 2MB, format JPG, PNG, GIF.
                            </p>
                        </div>
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg shadow-sm
                                   bg-indigo-600 text-white hover:bg-indigo-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800
                                   transition-colors duration-200 ease-in-out">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Unggah & Terapkan
                        </button>
                    </form>
                </div>

                {{-- Bagian: Pilih Avatar Default --}}
                <div>
                    <h3 class="text-xl font-semibold mb-5 text-slate-700 dark:text-slate-200">Pilih Avatar Default</h3>
                    <form action="{{ route('profile.update-photo') }}" method="POST" id="defaultAvatarForm">
                        @csrf
                        <div class="grid grid-cols-3 gap-4 max-h-64 overflow-y-auto pr-3 custom-scrollbar">
                            @foreach ($defaultAvatars as $avatarPath)
                                <div class="flex flex-col items-center p-3 border-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer transition-all duration-150 ease-in-out
                                {{ optional($user)->profile_photo_path === $avatarPath ? 'border-indigo-500 ring-2 ring-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 dark:border-indigo-600' : 'border-slate-200 dark:border-slate-600' }}"
                                    onclick="selectDefaultAvatar('{{ Str::slug($avatarPath, '-') }}', this)">
                                    <img src="{{ asset($avatarPath) }}" alt="Avatar Default"
                                        class="h-20 w-20 rounded-full object-cover mb-3 shadow-sm">
                                    <input type="radio" name="default_avatar" value="{{ $avatarPath }}"
                                        id="radio-{{ Str::slug($avatarPath, '-') }}" class="hidden"
                                        {{ optional($user)->profile_photo_path === $avatarPath ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">Pilih</span>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>

            <div
                class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-end items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <form action="{{ route('profile.delete-photo') }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil? Ini akan mengembalikan ke avatar default pertama.');"
                    class="w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-base font-semibold rounded-lg shadow-sm
                               bg-red-600 text-white hover:bg-red-700
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-slate-800
                               transition-colors duration-200 ease-in-out">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Hapus Foto Profil
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom scrollbar for better aesthetics in avatar selection */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #94a3b8;
            /* slate-400 */
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
            /* slate-500 */
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
            /* slate-600 */
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #334155;
            /* slate-700 */
        }

        /* Animasi modal */
        #photoModal.hidden #modal-content-wrapper {
            transform: scale(0.95);
            opacity: 0;
        }

        #photoModal:not(.hidden) #modal-content-wrapper {
            transform: scale(1);
            opacity: 1;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileImageWrapper = document.getElementById('profile-image-wrapper');
            const photoModal = document.getElementById('photoModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const uploadPhotoForm = document.getElementById('uploadPhotoForm');
            const profilePhotoInput = document.getElementById('profile_photo');
            const defaultAvatarForm = document.getElementById('defaultAvatarForm');
            const modalContentWrapper = document.getElementById('modal-content-wrapper');

            if (profileImageWrapper) {
                profileImageWrapper.addEventListener('click', function() {
                    if (photoModal) {
                        photoModal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                        modalContentWrapper.offsetWidth; // Trigger reflow
                        modalContentWrapper.classList.remove('opacity-0', 'scale-95');
                        modalContentWrapper.classList.add('opacity-100', 'scale-100');
                    }
                });
            }

            function closeModal() {
                if (photoModal) {
                    modalContentWrapper.classList.remove('opacity-100', 'scale-100');
                    modalContentWrapper.classList.add('opacity-0', 'scale-95');
                    modalContentWrapper.addEventListener('transitionend', function handler() {
                        photoModal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                        modalContentWrapper.removeEventListener('transitionend', handler);
                    });
                }
            }

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', closeModal);
            }

            if (photoModal) {
                photoModal.addEventListener('click', function(e) {
                    if (e.target === photoModal) {
                        closeModal();
                    }
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !photoModal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            if (uploadPhotoForm && profilePhotoInput) {
                uploadPhotoForm.addEventListener('submit', function(e) {
                    const file = profilePhotoInput.files[0];
                    if (file) {
                        if (!file.type.startsWith('image/')) {
                            e.preventDefault();
                            alert("File yang dipilih bukan gambar. Mohon pilih file gambar.");
                            profilePhotoInput.value = '';
                            return;
                        }
                        const maxSize = 2 * 1024 * 1024; // 2MB
                        if (file.size > maxSize) {
                            e.preventDefault();
                            alert("Ukuran gambar maksimal 2MB. Mohon pilih gambar yang lebih kecil.");
                            profilePhotoInput.value = '';
                            return;
                        }
                    } else {
                        e.preventDefault();
                        alert("Mohon pilih gambar untuk diunggah.");
                    }
                });
            }

            window.selectDefaultAvatar = function(radioId, N_this_element) {
                const avatarContainers = defaultAvatarForm.querySelectorAll('[type="radio"]').forEach(radio => {
                    radio.closest('div').classList.remove('border-indigo-500', 'ring-2',
                        'ring-indigo-300',
                        'bg-indigo-50', 'dark:bg-indigo-900/50', 'dark:border-indigo-600');
                    radio.closest('div').classList.add('border-slate-200', 'dark:border-slate-600');
                });

                N_this_element.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-300', 'bg-indigo-50',
                    'dark:bg-indigo-900/50', 'dark:border-indigo-600');
                N_this_element.classList.remove('border-slate-200', 'dark:border-slate-600');


                const radioInput = document.getElementById('radio-' + radioId);
                if (radioInput) {
                    radioInput.checked = true;
                }
                if (defaultAvatarForm) {
                    defaultAvatarForm.submit();
                }
            }
        });
    </script>
@endpush
