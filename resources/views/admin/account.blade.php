@extends('layouts.app')

@section('content')
<main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Manajemen Akun</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan lihat informasi detail akun pengguna.</p>
        </div>
        @include('layouts.profile')
    </div>

    {{-- Container Utama dengan tema putih yang disempurnakan --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200">
        <div class="p-4">
            <form id="filter-form">
                <div class="flex flex-col sm:flex-row gap-4">
                    {{-- Input Pencarian --}}
                    <div class="relative flex-grow">
                        <input type="search" id="search-input" name="search" placeholder="Cari berdasarkan nama atau email..."
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    {{-- Dropdown Filter --}}
                    <select id="filter-role" name="filter_role" class="w-full sm:w-auto border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                        <option value="">Semua Status</option>
                        <option value="mahasiswa" {{ request('filter_role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="siswa" {{ request('filter_role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- Kontainer untuk Tabel --}}
        <div id="table-container" class="border-t border-gray-200">
            @include('admin._account-table', ['users' => $users])
        </div>
    </div>
</main>

<div id="custom-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg transform transition-all duration-300 ease-out scale-95 opacity-0" id="modal-content">
        <div class="flex justify-between items-center p-4 border-b">
            <h5 class="text-xl font-bold text-gray-800">Detail Informasi User</h5>
            <button id="modal-close-btn" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div class="p-6">
            <div class="text-center -mt-16 mb-4">
                <img id="modal-photo" src="" class="w-24 h-24 rounded-full mx-auto object-cover ring-4 ring-white shadow-lg">
            </div>
            <h3 id="modal-name" class="text-2xl font-bold text-center text-gray-800 mb-4"></h3>
            <div class="w-full max-w-md mx-auto text-sm text-gray-700">
                <table class="w-full">
                    <tbody>
                        <tr class="border-b"><td class="font-semibold text-gray-500 py-2 pr-2 align-top">Email</td><td id="modal-email" class="py-2 text-gray-800 font-medium"></td></tr>
                        <tr class="border-b"><td class="font-semibold text-gray-500 py-2 pr-2 align-top">No. HP</td><td id="modal-phone" class="py-2 text-gray-800 font-medium"></td></tr>
                        <tr class="border-b"><td class="font-semibold text-gray-500 py-2 pr-2 align-top">Status</td><td id="modal-role" class="py-2 text-gray-800 font-medium"></td></tr>
                        <tr class="border-b"><td class="font-semibold text-gray-500 py-2 pr-2 align-top">NIM</td><td id="modal-nim" class="py-2 text-gray-800 font-medium"></td></tr>
                        <tr><td class="font-semibold pt-2 pr-2 align-top">Asal Kampus</td><td id="modal-kampus" class="pt-2 text-gray-800 font-medium"></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

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
        
        fetch(`{{ route('admin.management.accounts.index') }}?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            tableContainer.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error:', error);
            tableContainer.style.opacity = '1';
        });
    }

    searchInput.addEventListener('keyup', () => {
        clearTimeout(debounceTimer);
        // REVISI: Delay diubah menjadi 200ms
        debounceTimer = setTimeout(fetchData, 200);
    });

    filterRole.addEventListener('change', fetchData);

    // Script untuk modal (sudah benar, tidak ada perubahan)
    const modal = document.getElementById('custom-modal');
    const modalContent = document.getElementById('modal-content');
    const closeBtn = document.getElementById('modal-close-btn');

    const openModal = (button) => {
        document.getElementById('modal-photo').src = button.dataset.photo;
        document.getElementById('modal-name').textContent = button.dataset.name;
        document.getElementById('modal-email').textContent = ': ' + button.dataset.email;
        document.getElementById('modal-phone').textContent = ': ' + button.dataset.phone;
        document.getElementById('modal-role').textContent = ': ' + button.dataset.role;
        document.getElementById('modal-nim').textContent = ': ' + button.dataset.nim;
        document.getElementById('modal-kampus').textContent = ': ' + button.dataset.kampus;
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('opacity-0', 'scale-95');
        }, 10);
    };
    
    const closeModal = () => {
        modalContent.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    };

    document.querySelector('body').addEventListener('click', function(event) {
        if (event.target.classList.contains('see-more-btn')) {
            openModal(event.target);
        }
        if (event.target === closeBtn || event.target === modal) {
             closeModal();
        }
    });
});
</script>
@endpush