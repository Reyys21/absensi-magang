@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Profil</h1>
        <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Foto Profil</h2>

        <div class="flex items-center mb-6">
            {{-- Display current profile photo --}}
            <img class="h-24 w-24 rounded-full object-cover mr-4 border-2 border-gray-300"
                src="{{
                    optional($user)->profile_photo_path
                    ? (
                        Str::startsWith(optional($user)->profile_photo_path, 'profile_photos/')
                        ? asset(optional($user)->profile_photo_path)
                        : asset('storage/' . optional($user)->profile_photo_path)
                    )
                    : asset('profile_photos/avatar_1 (1).jpg')
                }}"
                alt="{{ optional($user)->name ?: 'Pengguna' }}">
            <div>
                <p class="text-gray-600">Foto profil saat ini.</p>
                <form action="{{ route('profile.delete-photo') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil? Ini akan mengembalikan ke avatar default pertama.');">
                    @csrf
                    <button type="submit" class="text-red-500 hover:underline text-sm mt-1">Hapus Foto Profil</button>
                </form>
            </div>
        </div>

        {{-- Form Unggah Foto Baru (tanpa cropping) --}}
        <h3 class="text-xl font-semibold mb-3 text-gray-700">Unggah Foto Baru</h3>
        <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="mb-4">
                <label for="profile_photo" class="block text-gray-700 text-sm font-bold mb-2">Pilih Gambar:</label>
                <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">Maksimal 2MB, format JPG, PNG, GIF.</p>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Unggah Foto
            </button>
        </form>


        <h3 class="text-xl font-semibold mt-8 mb-3 text-gray-700">Pilih Avatar Default</h3>
        <form action="{{ route('profile.update-photo') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($defaultAvatars as $avatarPath)
                    <div class="flex flex-col items-center p-2 border rounded-lg hover:bg-gray-100 cursor-pointer
                        {{ optional($user)->profile_photo_path === $avatarPath ? 'border-blue-500 ring-2 ring-blue-300 bg-blue-50' : 'border-gray-200' }}"
                        onclick="document.getElementById('radio-{{ Str::slug($avatarPath, '-') }}').checked = true; this.closest('form').submit();">
                        {{-- Menggunakan asset() karena ini adalah file langsung di folder public --}}
                        <img src="{{ asset($avatarPath) }}" alt="Avatar Default" class="h-20 w-20 rounded-full object-cover mb-2">
                        <input type="radio" name="default_avatar" value="{{ $avatarPath }}"
                            id="radio-{{ Str::slug($avatarPath, '-') }}" class="hidden"
                            {{ optional($user)->profile_photo_path === $avatarPath ? 'checked' : '' }}>
                        <span class="text-sm text-gray-600">Pilih</span>
                    </div>
                @endforeach
            </div>
            {{-- Tombol submit akan tersembunyi karena kita akan submit form via JS onclick --}}
            <button type="submit" class="hidden"></button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">Informasi Dasar Profil</h2>
            <p class="text-gray-600">Untuk mengedit informasi dasar seperti nama atau email, Anda dapat menambahkan form di sini.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Tidak ada script Cropper.js di sini lagi --}}
@endpush