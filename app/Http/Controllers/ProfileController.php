<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    protected $defaultAvatars = [
        'profile_photos/avatar_1 (1).jpg',
        'profile_photos/avatar_1 (2).jpg',
        'profile_photos/avatar_1 (3).jpg',
        'profile_photos/avatar_1 (4).jpg',
        'profile_photos/avatar_1 (5).jpg',
        'profile_photos/avatar_1 (6).jpg',
    ];

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', [
            'user' => $user,
            'defaultAvatars' => $this->defaultAvatars,
        ]);
    }

    public function updateProfilePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_photo' => 'nullable|image|max:2048', // File langsung
            'default_avatar' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path &&
                !in_array($user->profile_photo_path, $this->defaultAvatars) &&
                Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Simpan file yang diunggah ke folder 'avatars' di dalam storage/app/public
            // Laravel secara otomatis menghasilkan nama unik
            $path = $request->file('profile_photo')->store('avatars', 'public');

            if (!$path) {
                \Log::error("Failed to save uploaded profile photo for user ID: " . $user->id);
                return redirect()->back()->withErrors(['profile_photo' => 'Gagal menyimpan gambar profil.']);
            }

            $user->profile_photo_path = $path;
            $user->save();

            return redirect()->back()->with('status', 'Foto profil berhasil diunggah!');
        } elseif ($request->filled('default_avatar')) {
            // ... (logika pilih avatar default) ...
            if (in_array($request->default_avatar, $this->defaultAvatars)) {
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

    public function deleteProfilePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo_path &&
            !in_array($user->profile_photo_path, $this->defaultAvatars) &&
            Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = $this->defaultAvatars[0];
        $user->save();

        return redirect()->back()->with('status', 'Foto profil berhasil dihapus dan kembali ke default.');
    }
}