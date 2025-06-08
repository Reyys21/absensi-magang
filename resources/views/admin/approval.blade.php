@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Styling untuk tabel responsif (mode kartu di mobile) */
    @media (max-width: 1023px) {
        .responsive-table thead {
            display: none;
        }
        .responsive-table, .responsive-table tbody, .responsive-table tr, .responsive-table td {
            display: block;
            width: 100%;
        }
        .responsive-table tr {
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .responsive-table td {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            border-bottom: 1px solid #e5e7eb;
        }
        .responsive-table td:last-child {
            border-bottom: 0;
        }
        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            text-align: left;
            margin-right: 1rem;
            color: #4b5563;
        }
    }
    
    /* Styling untuk Lihat Selengkapnya */
    .expandable-content.collapsed {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Batasi hingga 2 baris */
        -webkit-box-orient: vertical;
        word-break: break-word;
    }
    .toggle-expand {
        color: #4f46e5;
        font-weight: 500;
        cursor: pointer;
        font-size: 0.875rem;
        display: none; /* Tampil via JS jika perlu */
        margin-top: 4px;
    }
    .toggle-expand:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col md:flex-row min-h-screen font-[Inter] bg-gray-50">
    <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Admin: Persetujuan Koreksi</h1>
            @include('layouts.profile')
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg shadow-sm" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg shadow-sm" role="alert">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Permintaan Tertunda</h2>
                <p class="text-sm text-gray-500 mt-1">Daftar permintaan yang memerlukan tindakan Anda.</p>
            </div>

            <!-- PERBAIKAN: class="overflow-x-auto" dihapus dari div ini -->
            <div>
                @if ($requests->isEmpty())
                    <div class="text-center py-16 px-6">
                        <i class="fa-solid fa-check-circle text-5xl text-green-400"></i>
                        <h3 class="mt-4 text-xl font-semibold text-gray-700">Semua Beres!</h3>
                        <p class="mt-2 text-gray-500">Tidak ada permintaan koreksi yang menunggu persetujuan.</p>
                    </div>
                @else
                    <!-- PERBAIKAN: class="min-w-full" dihapus dari tabel -->
                    <table class="w-full responsive-table">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr class="divide-x divide-gray-200">
                                <!-- PERBAIKAN: class="min-w-[]" dihapus dari semua th -->
                                <th class="px-6 py-3 font-medium">No</th>
                                <th class="px-6 py-3 font-medium">Pemohon</th>
                                <th class="px-6 py-3 font-medium">Tanggal</th>
                                <th class="px-6 py-3 font-medium">Check-In</th>
                                <th class="px-6 py-3 font-medium">Check-Out</th>
                                <th class="px-6 py-3 font-medium">Judul Aktivitas</th>
                                <th class="px-6 py-3 font-medium">Deskripsi</th>
                                <th class="px-6 py-3 font-medium">Alasan</th>
                                <th class="px-6 py-3 font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @foreach ($requests as $requestItem)
                                <tr class="divide-x divide-gray-200 hover:bg-gray-50 transition">
                                    <td data-label="No" class="px-6 py-4 text-gray-600 font-medium text-center align-top">{{ $requests->firstItem() + $loop->index }}</td>
                                    <td data-label="Pemohon" class="px-6 py-4 text-gray-900 font-medium align-top">{{ $requestItem->user->name ?? 'N/A' }}</td>
                                    <td data-label="Tanggal" class="px-6 py-4 text-gray-600 whitespace-nowrap align-top">{{ $requestItem->attendance_date->format('d M Y') }}</td>
                                    <td data-label="Check-In" class="px-6 py-4 text-gray-600 whitespace-nowrap align-top">{{ $requestItem->new_check_in ? $requestItem->new_check_in->format('H:i') : '--' }}</td>
                                    <td data-label="Check-Out" class="px-6 py-4 text-gray-600 whitespace-nowrap align-top">{{ $requestItem->new_check_out ? $requestItem->new_check_out->format('H:i') : '--' }}</td>
                                    <td data-label="Judul" class="px-6 py-4 text-gray-600 align-top">
                                        <div class="expandable-content collapsed">
                                            {!! nl2br(e($requestItem->new_activity_title ?: '--')) !!}
                                        </div>
                                        <a href="#" class="toggle-expand">Lihat Selengkapnya</a>
                                    </td>
                                    <td data-label="Deskripsi" class="px-6 py-4 text-gray-600 align-top">
                                        <div class="expandable-content collapsed">
                                           {!! nl2br(e($requestItem->new_activity_description ?: '--')) !!}
                                        </div>
                                        <a href="#" class="toggle-expand">Lihat Selengkapnya</a>
                                    </td>
                                    <td data-label="Alasan" class="px-6 py-4 text-gray-600 align-top">
                                        <div class="expandable-content collapsed">
                                            {!! nl2br(e($requestItem->reason ?: '--')) !!}
                                        </div>
                                        <a href="#" class="toggle-expand">Lihat Selengkapnya</a>
                                    </td>
                                    <td data-label="Aksi" class="px-6 py-4 align-top">
                                        <div class="flex items-center justify-end md:justify-center space-x-2">
                                            <form action="{{ route('admin.approval.approve', $requestItem->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin MENYETUJUI permintaan ini?');">
                                                @csrf
                                                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-xs px-3 py-1.5 text-center">
                                                    Terima
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal({{ $requestItem->id }}, '{{ $requestItem->user->name ?? 'User' }}', '{{ $requestItem->attendance_date->format('d M Y') }}')" class="text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-xs px-3 py-1.5 text-center">
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($requests->hasPages())
            <div class="p-4 sm:p-6 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
            @endif
        </div>
    </main>
</div>

<!-- Modal Penolakan -->
<div id="rejectModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4" style="display: none; z-index: 100;">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
        <!-- Konten diisi oleh JS -->
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // === Script untuk "Lihat Selengkapnya" (FIXED) ===
    document.querySelectorAll('.expandable-content').forEach(content => {
        const toggleButton = content.nextElementSibling;
        
        // Cek apakah konten perlu dipotong setelah dirender
        if (content.scrollHeight > content.clientHeight) {
            toggleButton.style.display = 'inline-block';
        }
        
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            content.classList.toggle('collapsed');
            this.textContent = content.classList.contains('collapsed') ? 'Lihat Selengkapnya' : 'Ringkas';
        });
    });

    // === Script untuk Modal Penolakan (Sama seperti sebelumnya) ===
    window.openRejectModal = function(requestId, userName, date) { 
        const rejectModal = document.getElementById('rejectModal');
        const url = `{{ url('admin/approval-requests') }}/${requestId}/reject`;
        const modalContent = `
            <div class="p-6">
                <div class="flex items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Permintaan</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Anda akan menolak koreksi dari <strong>${userName}</strong> untuk tanggal <strong>${date}</strong>.</p>
                        </div>
                    </div>
                </div>
                <form id="rejectForm" action="${url}" method="POST" class="mt-4">
                    @csrf
                    <div>
                        <label for="admin_notes" class="block text-sm font-medium text-gray-700">Alasan Penolakan (Wajib)</label>
                        <textarea id="admin_notes" name="admin_notes" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md" required></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                <button type="submit" form="rejectForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Kirim Penolakan
                </button>
                <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        `;
        rejectModal.querySelector('.relative').innerHTML = modalContent;
        rejectModal.style.display = 'flex';
    };
    window.closeRejectModal = function() { 
        document.getElementById('rejectModal').style.display = 'none';
    };
});
</script>
@endpush
