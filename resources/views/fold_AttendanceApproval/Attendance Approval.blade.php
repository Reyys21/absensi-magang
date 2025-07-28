@extends('layouts.app')

@push('styles')
    <style>
        /* Sembunyikan header tabel di layar kecil */
        @media (max-width: 767px) {
            .responsive-table thead {
                display: none;
            }

            .responsive-table,
            .responsive-table tbody,
            .responsive-table tr,
            .responsive-table td {
                display: block;
                width: 100%;
            }

            .responsive-table tr {
                margin-bottom: 1.5rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                overflow: hidden;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }

            .responsive-table td {
                padding-left: 50%;
                position: relative;
                text-align: right;
                border: none;
                border-bottom: 1px solid #e2e8f0;
            }

            .responsive-table td:last-child {
                border-bottom: none;
            }

            .responsive-table td:before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 45%;
                padding-left: 1rem;
                font-weight: 600;
                text-align: left;
                white-space: nowrap;
            }
        }

        .read-more-link {
            color: #4f46e5;
            /* indigo-600 */
            cursor: pointer;
            font-weight: 500;
            margin-left: 5px;
        }

        .read-more-link:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    <div class="flex-1 flex flex-col">

        <header
            class="bg-white flex flex-row justify-between items-center py-2 px-4 sm:px-6 md:px-8 border-b border-gray-200 sticky top-0 z-10">
            <div>
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Permintaan Koreksi</h1>
            </div>
            @include('layouts.profile')
        </header>

        {{-- Konten utama dimulai di sini --}}
        <main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50/50">

            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Permintaan Koreksi Absensi Anda</h2>

                @if ($requests->isEmpty())
                    <p class="text-gray-600 text-center py-8">Tidak ada permintaan koreksi absensi yang Anda ajukan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full responsive-table">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No.</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Check-In (Req)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Check-Out (Req)</th>

                                    {{-- ▼▼▼ KOLOM BARU DITAMBAHKAN DI SINI ▼▼▼ --}}
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Judul Aktivitas</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Deskripsi</th>
                                    {{-- ▲▲▲ AKHIR KOLOM BARU ▲▲▲ --}}

                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Alasan Anda</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Catatan Admin</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 md:divide-y-0">
                                @foreach ($requests as $index => $requestItem)
                                    <tr>
                                        <td data-label="No." class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $requests->firstItem() + $index }}</td>
                                        <td data-label="Tanggal" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $requestItem->attendance_date->format('d M Y') }}</td>
                                        <td data-label="Check-In" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $requestItem->new_check_in ? $requestItem->new_check_in->format('H:i') : '--.--' }}
                                        </td>
                                        <td data-label="Check-Out"
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $requestItem->new_check_out ? $requestItem->new_check_out->format('H:i') : '--.--' }}
                                        </td>

                                        {{-- ▼▼▼ DATA BARU DITAMPILKAN DI SINI ▼▼▼ --}}
                                        <td data-label="Judul Aktivitas" class="px-6 py-4 text-sm text-gray-900">
                                            <span class="expandable-text"
                                                data-original-text="{{ $requestItem->new_activity_title ?: '--' }}"></span>
                                        </td>
                                        <td data-label="Deskripsi" class="px-6 py-4 text-sm text-gray-900">
                                            <span class="expandable-text"
                                                data-original-text="{{ $requestItem->new_activity_description ?: '--' }}"></span>
                                        </td>
                                        {{-- ▲▲▲ AKHIR DATA BARU ▲▲▲ --}}

                                        <td data-label="Alasan Anda" class="px-6 py-4 text-sm text-gray-900">
                                            <span class="expandable-text"
                                                data-original-text="{{ $requestItem->reason ?: '--' }}"></span>
                                        </td>
                                        <td data-label="Status" class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($requestItem->status === 'pending')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Proses</span>
                                            @elseif($requestItem->status === 'approved')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Diterima</span>
                                            @elseif($requestItem->status === 'rejected')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($requestItem->status) }}</span>
                                            @endif
                                        </td>
                                        <td data-label="Catatan Admin" class="px-6 py-4 text-sm text-gray-900">
                                            <span class="expandable-text"
                                                data-original-text="{{ $requestItem->admin_notes ?: '--' }}"></span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const charLimit = 50; // Batas karakter sebelum dipotong

            document.querySelectorAll('.expandable-text').forEach(span => {
                const originalText = span.dataset.originalText;

                if (originalText.length > charLimit) {
                    const truncatedText = originalText.substring(0, charLimit) + '...';
                    span.innerHTML = `${truncatedText} <a class="read-more-link">Lihat selengkapnya</a>`;

                    const link = span.querySelector('.read-more-link');
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (link.textContent === 'Lihat selengkapnya') {
                            span.innerHTML =
                                `${originalText} <a class="read-more-link">Ringkas</a>`;
                            // Re-add event listener to the new link
                            span.querySelector('.read-more-link').addEventListener('click',
                                arguments.callee);
                        } else {
                            span.innerHTML =
                                `${truncatedText} <a class="read-more-link">Lihat selengkapnya</a>`;
                            // Re-add event listener to the new link
                            span.querySelector('.read-more-link').addEventListener('click',
                                arguments.callee);
                        }
                    });
                } else {
                    span.textContent = originalText;
                }
            });
        });
    </script>
@endpush
