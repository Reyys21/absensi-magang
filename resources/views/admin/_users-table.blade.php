<div class="overflow-x-auto">
    <table class="w-full min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                {{-- Kolom No untuk urutan per halaman --}}
                <th class="p-4 text-left font-semibold w-16">No</th>
                
                {{-- ▼▼▼ KOLOM BARU DITAMBAHKAN ▼▼▼ --}}
                <th class="p-4 text-left font-semibold">ID</th>
                
                <th class="p-4 text-left font-semibold">
                    @php
                        $direction = (request('sort_by') == 'name' && request('sort_direction') == 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a href="{{ route('admin.monitoring.users.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_direction' => $direction])) }}" class="flex items-center gap-2 hover:text-blue-600">
                        Nama
                        @if(request('sort_by') == 'name')
                            <i class="fa-solid {{ request('sort_direction') == 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                        @else
                            <i class="fa-solid fa-sort text-gray-300"></i>
                        @endif
                    </a>
                </th>
                <th class="p-4 text-left font-semibold">
                     @php
                        $direction = (request('sort_by') == 'email' && request('sort_direction') == 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a href="{{ route('admin.monitoring.users.index', array_merge(request()->query(), ['sort_by' => 'email', 'sort_direction' => $direction])) }}" class="flex items-center gap-2 hover:text-blue-600">
                        Email
                        @if(request('sort_by') == 'email')
                            <i class="fa-solid {{ request('sort_direction') == 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                        @else
                            <i class="fa-solid fa-sort text-gray-300"></i>
                        @endif
                    </a>
                </th>
                <th class="p-4 text-left font-semibold">Status</th>
                <th class="p-4 text-left font-semibold">Bidang</th>
                <th class="p-4 text-left font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($users as $key => $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    {{-- Penomoran ini sudah benar untuk pagination, jangan diubah --}}
                    <td class="p-4 whitespace-nowrap text-center">{{ $users->firstItem() + $key }}</td>
                    
                    {{-- ▼▼▼ DATA BARU DITAMPILKAN ▼▼▼ --}}
                    <td class="p-4 whitespace-nowrap font-medium text-gray-500">{{ $user->id }}</td>

                    <td class="p-4 whitespace-nowrap">{{ $user->name }}</td>
                    <td class="p-4 whitespace-nowrap">{{ $user->email }}</td>
                    <td class="p-4 whitespace-nowrap"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->role == 'mahasiswa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">{{ Str::ucfirst($user->role) }}</span></td>
                    <td class="p-4 whitespace-nowrap">{{ $user->bidang->name ?? 'N/A' }}</td>
                    <td class="p-4 whitespace-nowrap">
                        <a href="{{ route('admin.monitoring.users.show', $user->id) }}" class="font-medium text-blue-600 hover:text-blue-800">
                            Lihat Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    {{-- Sesuaikan colspan menjadi 7 karena ada penambahan 1 kolom --}}
                    <td colspan="7" class="text-center p-10 text-gray-500">
                        <i class="fa-solid fa-folder-open fa-3x mb-3"></i>
                        <p class="font-medium">Data tidak ditemukan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($users->hasPages())
    <div class="p-4 border-t border-gray-200">
        {{ $users->links('pagination::tailwind') }}
    </div>
@endif