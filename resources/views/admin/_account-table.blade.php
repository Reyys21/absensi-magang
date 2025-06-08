<div class="overflow-x-auto">
    <table class="w-full min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="p-4 text-left font-semibold">Pengguna</th>
                <th class="p-4 text-left font-semibold">Status</th>
                <th class="p-4 text-left font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            @php
                                $defaultPhoto = 'profile_photos/avatar_1 (1).jpg';
                                $photoPath = !empty($user->profile_photo_path) ? $user->profile_photo_path : $defaultPhoto;
                                $finalPhotoUrl = Str::startsWith($photoPath, 'avatars/') ? asset('storage/' . $photoPath) : asset($photoPath);
                            @endphp
                            <img src="{{ $finalPhotoUrl }}" alt="Foto" class="h-10 w-10 rounded-full object-cover">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 whitespace-nowrap"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->role == 'mahasiswa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">{{ Str::ucfirst($user->role) }}</span></td>
                    <td class="p-4 whitespace-nowrap">
                        <button type="button" class="see-more-btn font-medium text-blue-600 hover:text-blue-800"
                            data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-phone="{{ $user->phone ?? 'N/A' }}"
                            data-role="{{ Str::ucfirst($user->role) }}" data-nim="{{ $user->nim ?? 'N/A' }}" data-kampus="{{ $user->asal_kampus }}"
                            data-photo="{{ $finalPhotoUrl }}">
                            Lihat Detail
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                     <td colspan="3" class="text-center p-10 text-gray-500">
                        <i class="fa-solid fa-folder-open fa-3x mb-3"></i>
                        <p class="font-medium">Data tidak ditemukan.</p>
                        <p class="text-xs mt-1">Coba ubah kata kunci pencarian atau filter Anda.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
    <div class="p-4 border-t border-gray-200">
        {{ $users->links('pagination::tailwind') }}
    </div>
@endif