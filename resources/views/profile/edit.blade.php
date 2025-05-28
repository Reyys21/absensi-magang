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

        {{-- Form Unggah Foto Baru dengan Cropping --}}
        <h3 class="text-xl font-semibold mb-3 text-gray-700">Unggah Foto Kustom (Crop)</h3>
        <div class="mb-4">
            <label for="upload_profile_photo" class="block text-gray-700 text-sm font-bold mb-2">Pilih Gambar:</label>
            <input type="file" name="upload_profile_photo" id="upload_profile_photo" accept="image/*"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <p class="text-xs text-gray-500 mt-1">Maksimal 2MB, format JPG, PNG.</p>
        </div>

        {{-- Modal untuk Cropping --}}
        <div id="cropModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-lg w-full">
                <h2 class="text-2xl font-bold mb-4">Pangkas Gambar</h2>
                <div class="img-container mb-4" style="max-height: 400px; overflow: hidden;"> {{-- Batasi tinggi container --}}
                    <img id="imageToCrop" src="" alt="Image to crop" class="max-w-full h-auto">
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" id="cropCancelBtn" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Batal</button>
                    <button type="button" id="cropApplyBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Pangkas & Unggah</button>
                </div>
            </div>
        </div>

        {{-- Ini adalah hidden input untuk mengirim data gambar hasil crop --}}
        <form action="{{ route('profile.update-photo') }}" method="POST" id="cropUploadForm">
            @csrf
            <input type="hidden" name="cropped_image_data" id="croppedImageData">
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

@push('scripts') {{-- Jika Anda menggunakan @stack('scripts') di layout utama --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadProfilePhoto = document.getElementById('upload_profile_photo');
        const cropModal = document.getElementById('cropModal');
        const imageToCrop = document.getElementById('imageToCrop');
        const cropCancelBtn = document.getElementById('cropCancelBtn');
        const cropApplyBtn = document.getElementById('cropApplyBtn');
        const croppedImageData = document.getElementById('croppedImageData');
        const cropUploadForm = document.getElementById('cropUploadForm');

        // Pastikan semua elemen yang dibutuhkan ditemukan
        if (!uploadProfilePhoto || !cropModal || !imageToCrop || !cropCancelBtn || !cropApplyBtn || !croppedImageData || !cropUploadForm) {
            console.error("ERROR: Satu atau lebih elemen yang dibutuhkan untuk fitur cropping tidak ditemukan. Periksa ID HTML Anda.");
            return;
        }

        let cropper; // Variabel untuk menyimpan instance Cropper

        uploadProfilePhoto.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];

                if (!file.type.startsWith('image/')) {
                    console.error("File yang dipilih bukan gambar.");
                    alert("File yang dipilih bukan gambar. Mohon pilih file gambar.");
                    uploadProfilePhoto.value = '';
                    return;
                }

                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    console.error("Ukuran file gambar melebihi 2MB.");
                    alert("Ukuran gambar maksimal 2MB. Mohon pilih gambar yang lebih kecil.");
                    uploadProfilePhoto.value = '';
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {
                    console.log("FileReader selesai membaca gambar.");
                    imageToCrop.src = event.target.result;

                    // Pastikan cropper dihancurkan sebelum inisialisasi ulang
                    if (cropper) {
                        cropper.destroy();
                        console.log("Cropper sebelumnya dihancurkan.");
                    }

                    // Inisialisasi Cropper setelah gambar benar-benar dimuat ke elemen <img>
                    // Menggunakan event 'load' atau timeout untuk memastikan gambar sudah di-render
                    imageToCrop.addEventListener('load', function handler() {
                        console.log("imageToCrop.src berhasil dimuat.");
                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 1,
                            viewMode: 1,
                            background: false,
                        });
                        console.log("Cropper diinisialisasi.");
                        cropModal.classList.remove('hidden'); // Tampilkan modal setelah Cropper siap
                        imageToCrop.removeEventListener('load', handler); // Hapus listener setelah terpicu
                    });

                    // Fallback jika gambar sudah di-cache dan 'load' event tidak terpicu
                    if (imageToCrop.complete && imageToCrop.naturalWidth > 0) {
                        console.log("Gambar sudah di-cache, inisialisasi Cropper langsung.");
                        if (cropper) {
                            cropper.destroy();
                        }
                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 1,
                            viewMode: 1,
                            background: false,
                        });
                        console.log("Cropper diinisialisasi (cache).");
                        cropModal.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        cropCancelBtn.addEventListener('click', function() {
            console.log("Tombol Batal diklik.");
            cropModal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
            }
            uploadProfilePhoto.value = '';
        });

        cropApplyBtn.addEventListener('click', function() {
            console.log("Tombol Pangkas & Unggah diklik.");
            if (cropper) {
                try {
                    const croppedCanvas = cropper.getCroppedCanvas({
                        width: 256,
                        height: 256,
                        fillColor: '#fff',
                    });
                    const imageData = croppedCanvas.toDataURL('image/png');

                    croppedImageData.value = imageData;
                    cropUploadForm.submit();
                    console.log("Form disubmit.");
                    cropModal.classList.add('hidden');
                    if (cropper) {
                        cropper.destroy();
                    }
                } catch (error) {
                    console.error("Error saat mendapatkan data crop atau submit form:", error);
                    alert("Gagal memproses gambar. Coba lagi atau gunakan gambar lain.");
                }
            } else {
                console.warn("Cropper belum diinisialisasi saat tombol Apply diklik.");
                alert("Mohon pilih gambar terlebih dahulu.");
            }
        });

        cropModal.addEventListener('click', function(e) {
            if (e.target === cropModal) {
                console.log("Klik di luar modal terdeteksi.");
                cropCancelBtn.click();
            }
        });
    });
</script>
@endpush