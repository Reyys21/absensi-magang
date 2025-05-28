<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
// !!! PENTING UNTUK INTERVENTION IMAGE V3 !!!
// Hapus baris ini jika ada: use Intervention\Image\Facades\Image;

// Import class ImageManager dan Driver yang sesuai
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // <-- Pastikan ekstensi GD aktif di php.ini
// use Intervention\Image\Drivers\Imagick\Driver; // <-- Gunakan ini jika Anda pakai Imagick

class ProfileController extends Controller
{
    // Definisikan daftar avatar default sebagai properti kelas
    protected $defaultAvatars = [
        'profile_photos/avatar_1 (1).jpg',
        'profile_photos/avatar_1 (2).jpg',
        'profile_photos/avatar_1 (3).jpg',
        'profile_photos/avatar_1 (4).jpg',
        'profile_photos/avatar_1 (5).jpg',
        'profile_photos/avatar_1 (6).jpg',
    ];

    /**
     * Menampilkan formulir edit profil pengguna.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', [
            'user' => $user,
            'defaultAvatars' => $this->defaultAvatars,
        ]);
    }

    /**
     * Memperbarui foto profil pengguna (mencakup upload kustom dan pilih default).
     */
    public function updateProfilePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'cropped_image_data' => 'nullable|string', // Data Base64 dari gambar yang di-crop
            'default_avatar' => 'nullable|string',     // Path ke avatar default
        ]);

        // Prioritas pertama: Unggah foto kustom yang sudah di-crop (Base64)
        if ($request->filled('cropped_image_data')) {
            $imageData = $request->input('cropped_image_data');

            // Validasi format Base64 yang lebih ketat
            if (!Str::startsWith($imageData, 'data:image/')) {
                \Log::error("Invalid Base64 image data format received for profile photo.");
                return redirect()->back()->withErrors(['cropped_image_data' => 'Format data gambar tidak valid atau kosong.']);
            }

            try {
                // Pisahkan tipe data dari base64 string
                list($type, $imageData) = explode(';', $imageData);
                list(, $imageData)      = explode(',', $imageData);

                // Decode base64
                $imageData = base64_decode($imageData);

                // !!! PENTING UNTUK INTERVENTION IMAGE V3 !!!
                // Inisialisasi ImageManager dengan Driver yang sesuai (Gd atau Imagick)
                $manager = new ImageManager(new Driver()); // <-- Menggunakan GD Driver

                // Baca gambar menggunakan ImageManager
                $image = $manager->read($imageData); // <-- Gunakan method read()

                // Tentukan ekstensi file
                $extension = 'png';
                if (Str::contains($type, 'jpeg')) {
                    $extension = 'jpg';
                } elseif (Str::contains($type, 'gif')) {
                    $extension = 'gif';
                }

                $fileName = 'profile_' . $user->id . '_' . time() . '.' . $extension;
                $path = 'avatars/' . $fileName; // Path di dalam storage/app/public

                // Hapus foto lama jika ada dan BUKAN salah satu default
                if ($user->profile_photo_path &&
                    !in_array($user->profile_photo_path, $this->defaultAvatars) &&
                    Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Simpan gambar ke storage
                $image->encode($extension, 90); // Encode sebelum menyimpan
                $saved = Storage::disk('public')->put($path, $image->stream()); // Gunakan stream() untuk menyimpan

                if (!$saved) {
                    \Log::error("Failed to save image to storage for user ID: " . $user->id . " at path: " . $path);
                    return redirect()->back()->withErrors(['profile_photo' => 'Gagal menyimpan gambar.']);
                }

                $user->profile_photo_path = $path;
                $user->save();

                return redirect()->back()->with('status', 'Foto profil kustom berhasil diunggah dan di-crop!');

            } catch (\Exception $e) {
                // Untuk debugging, Anda bisa log pesan error ke storage/logs/laravel.log
                \Log::error("Error processing custom profile photo for user ID: " . $user->id . " - Message: " . $e->getMessage() . " - Trace: " . $e->getTraceAsString());
                return redirect()->back()->withErrors(['profile_photo' => 'Error saat memproses gambar: ' . $e->getMessage()]);
            }
        }
        // Prioritas kedua: Pilih avatar default
        elseif ($request->filled('default_avatar')) {
            if (in_array($request->default_avatar, $this->defaultAvatars)) {
                // Hapus foto lama jika ada dan BUKAN salah satu default
                if ($user->profile_photo_path &&
                    !in_array($user->profile_photo_path, $this->defaultAvatars) &&
                    Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Simpan path avatar default
                $user->profile_photo_path = $request->default_avatar;
                $user->save();

                return redirect()->back()->with('status', 'Foto profil berhasil diubah ke avatar default!');
            } else {
                throw ValidationException::withMessages(['default_avatar' => 'Avatar default yang dipilih tidak valid.']);
            }
        } else {
            return redirect()->back()->withErrors(['profile_photo' => 'Tidak ada foto yang dipilih atau diunggah.']);
        }
    }

    /**
     * Menghapus foto profil pengguna (mengembalikan ke default awal atau null).
     */
    public function deleteProfilePhoto()
    {
        $user = Auth::user();

        // Hapus foto dari storage jika ada dan BUKAN salah satu dari avatar default
        if ($user->profile_photo_path &&
            !in_array($user->profile_photo_path, $this->defaultAvatars) &&
            Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Set foto profil ke avatar default pertama sebagai fallback jika dihapus
        $user->profile_photo_path = $this->defaultAvatars[0]; // Mengembalikan ke 'profile_photos/avatar_1 (1).jpg'
        $user->save();

        return redirect()->back()->with('status', 'Foto profil berhasil dihapus dan kembali ke default.');
    }
}