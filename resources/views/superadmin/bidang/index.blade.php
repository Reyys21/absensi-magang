@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Bidang</h1>
        <a href="{{ route('superadmin.bidang.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fa-solid fa-plus mr-2"></i>
            Tambah Bidang Baru
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r-lg" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        {{-- Tambah Form Pencarian --}}
        <div class="p-4 border-b border-gray-200">
            <div class="relative">
                <input type="search" id="search-input" name="search" placeholder="Cari nama bidang..."
                       value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    {{-- Tambah kolom No. --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">No.</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Bidang</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($bidangs as $index => $bidang)
                <tr>
                    {{-- Tampilkan nomor urut --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bidangs->firstItem() + $index }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bidang->id }}</td>
                    {{-- Ubah Nama Bidang menjadi link ke halaman show --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <a href="{{ route('superadmin.bidang.show', $bidang) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $bidang->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                        <a href="{{ route('superadmin.bidang.edit', $bidang) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                        <form action="{{ route('superadmin.bidang.destroy', $bidang) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    {{-- Ubah colspan menjadi 4 --}}
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Belum ada bidang yang dibuat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $bidangs->links() }}
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    let debounceTimer;

    searchInput.addEventListener('keyup', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('page', 1); // Reset halaman ke 1 setiap kali filter berubah
            window.location.href = url.toString();
        }, 300); // Debounce untuk 300ms
    });
});
</script>
@endpush