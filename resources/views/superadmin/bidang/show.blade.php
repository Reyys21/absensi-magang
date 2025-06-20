@extends('layouts.app')

@push('styles')
<style>
    .tab-button.active {
        border-b-2 border-indigo-500 text-indigo-600 font-semibold;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Detail Bidang</h1>
        <a href="{{ route('superadmin.bidang.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-300">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Kembali ke Manajemen Bidang
        </a>
    </div>

    {{-- Notifikasi --}}
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

    {{-- Detail Organisasi (Bidang) --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-800 text-2xl font-bold">
                {{ strtoupper(substr($bidang->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $bidang->name }}</h2>
                <p class="text-gray-500 text-sm">ID Bidang: {{ $bidang->id }}</p>
            </div>
        </div>
    </div>

    {{-- Tab Navigasi --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button type="button" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 active" data-tab="users">
                    Users
                </button>
                <button type="button" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="corrections">
                    Permintaan Koreksi
                </button>
                <button type="button" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="roles">
                    Roles (Admin)
                </button>
            </nav>
        </div>

        {{-- Konten Tab --}}
        <div id="tab-content-users" class="tab-content pt-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Users di Bidang {{ $bidang->name }}</h3>
            {{-- Search Bar untuk Users --}}
            <div class="p-4 border-b border-gray-200 mb-4">
                <div class="relative">
                    <input type="search" id="user-search-input" name="user_search" placeholder="Cari user (nama atau email)..."
                           value="{{ request('user_search') }}"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $index => $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $users->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role == 'mahasiswa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ Str::ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.monitoring.users.show', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada user di bidang ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>

        <div id="tab-content-corrections" class="tab-content pt-6 hidden">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Permintaan Koreksi di Bidang {{ $bidang->name }}</h3>
            {{-- Search Bar untuk Permintaan Koreksi --}}
            <div class="p-4 border-b border-gray-200 mb-4">
                <div class="relative">
                    <input type="search" id="correction-search-input" name="correction_search" placeholder="Cari permintaan koreksi (nama user atau tanggal)..."
                           value="{{ request('correction_search') }}"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Absensi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($corrections as $index => $correction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $corrections->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $correction->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $correction->attendance_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ Str::ucfirst($correction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                {{-- Link untuk lihat detail permintaan koreksi, jika ada halaman detail untuk ini --}}
                                <a href="{{ route('admin.approval.requests', ['search' => $correction->user->name]) }}" class="text-indigo-600 hover:text-indigo-900">Lihat di Approval</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada permintaan koreksi pending untuk bidang ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $corrections->links() }}</div>
        </div>

        <div id="tab-content-roles" class="tab-content pt-6 hidden">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Manajemen Admin di Bidang {{ $bidang->name }}</h3>
             <div class="flex justify-end items-center mb-4">
                <a href="{{ route('superadmin.admins.create', ['bidang_id' => $bidang->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-indigo-700">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Admin di Bidang Ini
                </a>
            </div>
            {{-- Search Bar untuk Admin --}}
            <div class="p-4 border-b border-gray-200 mb-4">
                <div class="relative">
                    <input type="search" id="admin-search-input" name="admin_search" placeholder="Cari admin (nama atau email)..."
                           value="{{ request('admin_search') }}"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($admins as $index => $admin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $admins->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $admin->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $admin->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <a href="{{ route('superadmin.admins.edit', $admin) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                <form action="{{ route('superadmin.admins.destroy', $admin) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus admin ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada admin yang ditugaskan ke bidang ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $admins->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    const currentTabParam = new URLSearchParams(window.location.search).get('tab') || 'users';

    // Fungsi untuk menampilkan tab yang benar
    function showTab(tabName) {
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        tabButtons.forEach(button => {
            button.classList.remove('active', 'border-indigo-500', 'text-indigo-600', 'font-semibold');
            button.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        document.getElementById('tab-content-' + tabName).classList.remove('hidden');
        const activeTabButton = document.querySelector(`.tab-button[data-tab="${tabName}"]`);
        activeTabButton.classList.add('active', 'border-indigo-500', 'text-indigo-600', 'font-semibold');
        activeTabButton.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    }

    // Set tab aktif berdasarkan URL parameter saat memuat halaman
    showTab(currentTabParam);

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabName = button.dataset.tab;
            showTab(tabName);
            // Update URL tanpa reload halaman
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            // Hapus parameter search spesifik tab lain saat berganti tab
            if (tabName !== 'users') url.searchParams.delete('user_search');
            if (tabName !== 'corrections') url.searchParams.delete('correction_search');
            if (tabName !== 'roles') url.searchParams.delete('admin_search');
            window.history.pushState({ path: url.href }, '', url.href);
        });
    });


    // Fungsionalitas Search untuk masing-masing tab
    const userSearchInput = document.getElementById('user-search-input');
    const correctionSearchInput = document.getElementById('correction-search-input');
    const adminSearchInput = document.getElementById('admin-search-input');

    function applySearchFilter(inputElement, searchParamName) {
        let debounceTimer;
        inputElement.addEventListener('keyup', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set(searchParamName, inputElement.value);
                url.searchParams.set('page', 1); // Reset halaman ke 1
                window.location.href = url.toString(); // Reload halaman untuk filter
            }, 300);
        });
    }

    applySearchFilter(userSearchInput, 'user_search');
    applySearchFilter(correctionSearchInput, 'correction_search');
    applySearchFilter(adminSearchInput, 'admin_search');
});
</script>
@endpush