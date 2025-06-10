@extends('layouts.app')

@section('content')
    <header class="bg-white flex flex-row justify-between items-center py-2 px-4 sm:px-6 md:px-8 border-b border-gray-200">
        <div>
            {{-- Ukuran font disesuaikan untuk mobile, tablet, dan desktop --}}
            <h1 class="text-lg sm:text-1xl lg:text-2xl font-bold text-gray-800">Monitoring User</h1>
        </div>
        @include('layouts.profile')
    </header>

    {{-- Konten utama dimulai di sini --}}
    <main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50/50">

    <div class="bg-white rounded-xl shadow-md border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <form id="filter-form">
                {{-- REVISI: Dibuat responsif --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="search" id="search-input" name="search" placeholder="Cari nama atau email..."
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <select id="filter-role" name="filter_role" class="w-full sm:w-auto border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                        <option value="">Semua Status</option>
                        <option value="mahasiswa" {{ request('filter_role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="siswa" {{ request('filter_role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- REVISI: Kontainer tabel dibuat responsif dengan overflow --}}
        <div id="table-container" class="overflow-x-auto">
            @include('admin._users-table', ['users' => $users])
        </div>
    </div>
</main>
@endsection

{{-- Script untuk live search/filter tidak berubah --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filter-form');
    const searchInput = document.getElementById('search-input');
    const filterRole = document.getElementById('filter-role');
    const tableContainer = document.getElementById('table-container');
    let debounceTimer;

    function fetchData() {
        tableContainer.style.opacity = '0.5';
        const params = new URLSearchParams(new FormData(form)).toString();
        fetch(`{{ route('admin.monitoring.users.index') }}?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            tableContainer.style.opacity = '1';
        })
        .catch(error => console.error('Error:', error));
    }
    searchInput.addEventListener('keyup', () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(fetchData, 200); });
    filterRole.addEventListener('change', fetchData);
});
</script>
@endpush