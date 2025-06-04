{{-- resources/views/fold_AttendanceApproval/Attendance Approval.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

        <main id="main-content" class="flex-1 p-4 md:p-6 transition-all duration-300 ease-in-out">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-0">Approval Requests</h1>
                @include('layouts.profile')
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Permintaan Koreksi Absensi Anda</h2>

                @if ($requests->isEmpty())
                    <p class="text-gray-600 text-center py-8">Tidak ada permintaan koreksi absensi yang Anda ajukan.</p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No.
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Check-In (Req)
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Check-Out (Req)
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Judul Aktivitas (Req)
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Deskripsi Aktivitas (Req)
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Alasan
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($requests as $index => $requestItem)
                                    {{-- Ubah nama variabel untuk menghindari konflik --}}
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $requests->firstItem() + $index }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $requestItem->attendance_date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $requestItem->new_check_in ? $requestItem->new_check_in->format('H:i') : '--.--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $requestItem->new_check_out ? $requestItem->new_check_out->format('H:i') : '--.--' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                            {{ $requestItem->new_activity_title ?: '--' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                            {{ $requestItem->new_activity_description ?: '--' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate"> {{-- Tambahkan class truncated untuk Reason --}}
                                            {{ $requestItem->reason ?: '--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($requestItem->status === 'pending')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Proses
                                                </span>
                                            @elseif($requestItem->status === 'approved')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Diterima
                                                </span>
                                            @elseif($requestItem->status === 'rejected')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Ditolak
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($requestItem->status) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection
