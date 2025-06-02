<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Daftar avatar default yang tersedia
    protected $defaultAvatars = [
        'profile_photos/avatar_1 (1).jpg',
        'profile_photos/avatar_1 (2).jpg',
        'profile_photos/avatar_1 (3).jpg',
        'profile_photos/avatar_1 (4).jpg',
        'profile_photos/avatar_1 (5).jpg',
        'profile_photos/avatar_1 (6).jpg',
    ];

    /**
     * Menampilkan formulir edit profil untuk informasi umum.
     * Mengirimkan data user yang sedang login dan daftar avatar default.
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
     * Menampilkan formulir untuk mengubah password.
     * Mengirimkan data user yang sedang login dan daftar avatar default (untuk konsistensi tampilan sidebar).
     */
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        return view('profile.change-password', [
            'user' => $user,
            'defaultAvatars' => $this->defaultAvatars,
        ]);
    }

    /**
     * Mengupdate informasi dasar profil pengguna.
     */
    public function updateProfileInformation(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:mahasiswa,siswa'], // Sesuaikan dengan role yang valid
            'asal_kampus' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nim' => ['nullable', 'string', 'max:20', 'unique:users,nim,' . $user->id], // NIM opsional dan unik
        ]);

        // Menggunakan forceFill untuk memastikan semua kolom bisa diisi
        // Pastikan kolom-kolom ini ada di tabel 'users' Anda
        $user->forceFill([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'asal_kampus' => $request->asal_kampus,
            'phone' => $request->phone,
            'nim' => $request->nim,
        ])->save();

        return redirect()->back()->with('status', 'Informasi profil berhasil diperbarui.');
    }

    /**
     * Mengupdate password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'], // Validasi password saat ini
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'], // Password baru
            'password_confirmation' => ['required'], // Konfirmasi password baru
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('status', 'Password berhasil diperbarui.');
    }

    /**
     * Mengupdate foto profil pengguna (upload baru atau pilih default).
     */
    public function updateProfilePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_photo' => 'nullable|image|max:2048', // Maksimal 2MB
            'default_avatar' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_photo')) {
            // Hapus foto profil sebelumnya jika ada dan bukan avatar default
            if ($user->profile_photo_path &&
                !in_array($user->profile_photo_path, $this->defaultAvatars) &&
                Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Simpan foto baru
            $path = $request->file('profile_photo')->store('avatars', 'public');

            if (!$path) {
                \Log::error("Gagal menyimpan foto profil yang diunggah untuk user ID: " . $user->id);
                return redirect()->back()->withErrors(['profile_photo' => 'Gagal menyimpan gambar profil. Mohon coba lagi.']);
            }

            $user->profile_photo_path = $path;
            $user->save();

            return redirect()->back()->with('status', 'Foto profil berhasil diunggah!');
        } elseif ($request->filled('default_avatar')) {
            // Jika memilih avatar default
            if (in_array($request->default_avatar, $this->defaultAvatars)) {
                // Hapus foto profil sebelumnya jika ada dan bukan avatar default
                if ($user->profile_photo_path &&
                    !in_array($user->profile_photo_path, $this->defaultAvatars) &&
                    Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

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
     * Menghapus foto profil pengguna dan mengembalikan ke avatar default pertama.
     */
    public function deleteProfilePhoto()
    {
        $user = Auth::user();

        // Hapus foto profil saat ini jika ada dan bukan avatar default
        if ($user->profile_photo_path &&
            !in_array($user->profile_photo_path, $this->defaultAvatars) &&
            Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Setel ke avatar default pertama
        $user->profile_photo_path = $this->defaultAvatars[0];
        $user->save();

        return redirect()->back()->with('status', 'Foto profil berhasil dihapus dan kembali ke default.');
    }
}