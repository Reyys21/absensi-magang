@extends('layouts.app')

@push('styles')
{{-- CSS untuk animasi dan style baris yang bisa di-klik --}}
<style>
    .clickable-row {
        cursor: pointer;
        /* Transisi yang lebih halus untuk semua properti */
        transition: background-color 0.25s ease-in-out, box-shadow 0.25s ease-in-out;
    }

    .clickable-row:hover {
        background-color: #f8fafc; /* Warna hover yang lebih bersih (Tailwind's slate-50) */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07); /* Efek bayangan yang lebih lembut */
    }

    /* Menargetkan ikon panah di dalam baris */
    .clickable-row .row-arrow-icon {
        color: #9ca3af; /* Warna abu-abu (gray-400) untuk ikon */
        transition: transform 0.25s ease-in-out, opacity 0.25s ease-in-out;
        opacity: 0; /* Ikon disembunyikan secara default */
        transform: translateX(-5px); /* Posisi awal sedikit ke kiri */
    }

    /* Saat baris di-hover, ikon akan muncul dan bergerak */
    .clickable-row:hover .row-arrow-icon {
        opacity: 1; /* Muncul perlahan (fade-in) */
        transform: translateX(0); /* Bergerak ke posisi normal */
    }
</style>
@endpush

@section('content')
<div class="flex-1 flex flex-col">
    <header class="bg-white flex flex-row justify-between items-center py-2 px-4 sm:px-6 md:px-8 border-b border-gray-200 sticky top-0 z-10">
        <div>
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Manajemen Bidang</h1>
        </div>
        @include('layouts.profile')
    </header>

    <main class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50/50">
        <div class="flex justify-end items-center mb-6">
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">No.</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Bidang</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        {{-- Header kosong untuk kolom ikon panah --}}
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Lihat Detail</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($bidangs as $index => $bidang)
                    <tr class="clickable-row" data-href="{{ route('superadmin.bidang.show', $bidang) }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bidangs->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bidang->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $bidang->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <a href="{{ route('superadmin.bidang.edit', $bidang) }}" class="text-indigo-600 hover:text-indigo-900 px-2 py-1 rounded-md hover:bg-indigo-50">Edit</a>
                            <form action="{{ route('superadmin.bidang.destroy', $bidang) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded-md hover:bg-red-50">Hapus</button>
                            </form>
                        </td>
                        {{-- Kolom baru untuk ikon panah kanan --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <i class="fa-solid fa-arrow-right row-arrow-icon"></i>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
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
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk membuat baris bisa di-klik
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function (event) {
            // Hentikan navigasi jika yang diklik adalah link, tombol, atau form di dalam baris
            if (event.target.closest('a, button, form')) {
                return;
            }

            const href = this.dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
    });

    // Fungsi untuk search (tidak berubah)
    const searchInput = document.getElementById('search-input');
    let debounceTimer;

    searchInput.addEventListener('keyup', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }, 300);
    });
});
</script>
@endpush