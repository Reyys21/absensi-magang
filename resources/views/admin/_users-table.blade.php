<div class="overflow-x-auto">
    <table class="w-full min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="p-4 text-left font-semibold">Nama</th>
                <th class="p-4 text-left font-semibold">Email</th>
                <th class="p-4 text-left font-semibold">Nomor HP</th>
                <th class="p-4 text-left font-semibold">Status</th>
                <th class="p-4 text-left font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 whitespace-nowrap">{{ $user->name }}</td>
                    <td class="p-4 whitespace-nowrap">{{ $user->email }}</td>
                    <td class="p-4 whitespace-nowrap">{{ $user->phone ?? '-' }}</td>
                    <td class="p-4 whitespace-nowrap"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->role == 'mahasiswa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">{{ Str::ucfirst($user->role) }}</span></td>
                    <td class="p-4 whitespace-nowrap">
                        <a href="{{ route('admin.monitoring.users.show', $user->id) }}" class="font-medium text-[#4282aa] hover:text-[#212842]">
                            Lihat Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center p-10 text-gray-500">
                        <i class="fa-solid fa-folder-open fa-3x mb-3"></i>
                        <p class="font-medium">Data tidak ditemukan.</p>
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