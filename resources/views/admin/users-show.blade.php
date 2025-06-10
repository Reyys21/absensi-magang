@extends('layouts.app')

@section('content')
    {{-- Menggunakan struktur layout yang konsisten dengan halaman lain --}}
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]" style="background-color: #F7F7F7;"> {{-- Applied F7F7F7 to main background --}}
        <div class="flex-1 flex flex-col">

            {{-- 1. HEADER KONSISTEN: Menggunakan header sticky yang sama seperti halaman lain. --}}
            <header
                class="bg-white flex flex-row justify-between items-center py-2 px-4 sm:px-6 md:px-8 border-b border-gray-200 sticky top-0 z-10">
                <div>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold" style="color: #2A2B2A;">Detail Pengguna</h1>
                    {{-- Applied 2A2B2A to header text --}}
                </div>
                @include('layouts.profile')
            </header>

            <main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8" style="background-color: #F7F7F7;"> {{-- Applied F7F7F7 to main content background --}}

                {{-- Pindah link "Kembali ke Daftar User" ke dalam main --}}
                <a href="{{ route('admin.monitoring.users.index') }}"
                    class="text-sm font-medium hover:text-blue-800 transition-colors text-lg ">
                    <i class="fa-solid fa-arrow-left text-[#14BDEB]"></i>
                    <span class="text-[#2A2B2A]">Kembali</span>
                </a>

                {{-- BLOK HEADER LAMA DI DALAM MAIN TELAH DIHAPUS --}}

                {{-- 2. INFO PENGGUNA DIPERBAIKI: Ukuran foto profil diperbaiki dan perataan disempurnakan. --}}
                <div class="mb-6 p-4 border rounded-xl bg-white shadow-sm flex flex-col sm:flex-row items-center gap-5">
                    @php
                        $defaultPhoto = 'profile_photos/avatar_1 (1).jpg';
                        $photoPath = !empty($user->profile_photo_path) ? $user->profile_photo_path : $defaultPhoto;
                        $finalPhotoUrl = Str::startsWith($photoPath, 'avatars/')
                            ? asset('storage/' . $photoPath)
                            : asset($photoPath);
                    @endphp
                    {{-- Ukuran gambar dibalik agar lebih besar di layar sm ke atas --}}
                    <img src="{{ $finalPhotoUrl }}" alt="Foto"
                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover flex-shrink-0">
                    <div class="text-center sm:text-left">
                        <h2 class="text-xl sm:text-2xl font-bold" style="color: #2A2B2A;">{{ $user->name }}</h2>
                        {{-- Applied 2A2B2A to user name --}}
                        <p class="text-sm text-gray-500">{{ $user->email }} | HP: {{ $user->phone ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Container untuk Riwayat --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- 3. KARTU SEIMBANG: Diberi tinggi tetap dan flex-col agar sama tinggi dan bisa di-scroll --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 flex flex-col h-[600px]">
                        {{-- 4. HEADER KARTU RESPONSIF: Header kartu akan stack di mobile --}}
                        <div
                            class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-calendar-check" style="color: #14BDEB;"></i> {{-- Applied 14BDEB to icon --}}
                                <h3 class="text-lg font-semibold" style="color: #2A2B2A;">Riwayat Absensi</h3>
                                {{-- Applied 2A2B2A to heading --}}
                            </div>
                            <form id="form-absensi" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                                <input type="date" name="filter_tanggal_absensi" id="tanggal-absensi"
                                    value="{{ request('filter_tanggal_absensi') }}"
                                    class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 hidden flex-grow">
                                <select name="filter_type_absensi" id="filter-type-absensi"
                                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                    <option value="terbaru"
                                        {{ request('filter_type_absensi', 'terbaru') == 'terbaru' ? 'selected' : '' }}>
                                        Terbaru</option>
                                    <option value="terlama"
                                        {{ request('filter_type_absensi') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                                    <option value="tanggal"
                                        {{ request('filter_type_absensi') == 'tanggal' ? 'selected' : '' }}>Pilih Tanggal
                                    </option>
                                    <option value="clear">Hapus Filter</option>
                                </select>
                            </form>
                        </div>
                        {{-- Konten yang bisa di-scroll --}}
                        <div class="overflow-y-auto flex-grow">
                            @forelse($attendances as $attendance)
                                <div
                                    class="p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold" style="color: #2A2B2A;">
                                            {{ $attendance->date->translatedFormat('l, d M Y') }}</p> {{-- Applied 2A2B2A to attendance date --}}
                                        <p class="text-xs text-gray-500">Check-in:
                                            {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }} |
                                            Check-out:
                                            {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $attendance->attendance_status == 'Lengkap' ? 'text-green-800' : 'text-red-800' }}"
                                        style="background-color: {{ $attendance->attendance_status == 'Lengkap' ? '#CCEECC' : '#FFCCCC' }};">
                                        {{-- Added custom background colors --}}
                                        {{ $attendance->attendance_status }}</span>
                                </div>
                            @empty
                                <div
                                    class="text-center p-10 text-gray-400 flex flex-col items-center justify-center h-full">
                                    <i class="fa-solid fa-ghost fa-2x mb-2"></i>
                                    <p>Tidak ada riwayat absensi.</p>
                                </div>
                            @endforelse
                        </div>
                        @if ($attendances->hasPages())
                            <div class="p-4 border-t border-gray-200 rounded-b-xl" style="background-color: #F7F7F7;">
                                {{ $attendances->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Kartu Riwayat Koreksi (diterapkan perbaikan yang sama) --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 flex flex-col h-[600px]">
                        <div
                            class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-user-pen" style="color: #F0386B;"></i> {{-- Applied F0386B to icon --}}
                                <h3 class="text-lg font-semibold" style="color: #2A2B2A;">Riwayat Pengajuan Koreksi</h3>
                                {{-- Applied 2A2B2A to heading --}}
                            </div>
                            <form id="form-koreksi" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                                <input type="date" name="filter_tanggal_koreksi" id="tanggal-koreksi"
                                    value="{{ request('filter_tanggal_koreksi') }}"
                                    class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 hidden flex-grow">
                                <select name="filter_type_koreksi" id="filter-type-koreksi"
                                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                    <option value="terbaru"
                                        {{ request('filter_type_koreksi', 'terbaru') == 'terbaru' ? 'selected' : '' }}>
                                        Terbaru</option>
                                    <option value="terlama"
                                        {{ request('filter_type_koreksi') == 'terlama' ? 'selected' : '' }}>Terlama
                                    </option>
                                    <option value="tanggal"
                                        {{ request('filter_type_koreksi') == 'tanggal' ? 'selected' : '' }}>Pilih Tanggal
                                    </option>
                                    <option value="clear">Hapus Filter</option>
                                </select>
                            </form>
                        </div>
                        <div class="overflow-y-auto flex-grow">
                            @forelse($correctionRequests as $request)
                                <div
                                    class="p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold" style="color: #2A2B2A;">Koreksi untuk
                                            {{ $request->attendance_date->format('d M Y') }}</p> {{-- Applied 2A2B2A to correction date --}}
                                        <p class="text-xs text-gray-500">Diajukan pada
                                            {{ $request->created_at->format('d M Y') }}</p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full @if ($request->status == 'approved') text-green-800 @elseif($request->status == 'rejected') text-red-800 @else text-yellow-800 @endif"
                                        style="background-color: @if ($request->status == 'approved') #CCEECC @elseif($request->status == 'rejected') #FFCCCC @else #FFF3CC @endif;">
                                        {{-- Added custom background colors --}}
                                        {{ Str::ucfirst($request->status) }}</span>
                                </div>
                            @empty
                                <div
                                    class="text-center p-10 text-gray-400 flex flex-col items-center justify-center h-full">
                                    <i class="fa-solid fa-ghost fa-2x mb-2"></i>
                                    <p>Tidak ada riwayat pengajuan koreksi.</p>
                                </div>
                            @endforelse
                        </div>
                        @if ($correctionRequests->hasPages())
                            <div class="p-4 border-t border-gray-200 rounded-b-xl" style="background-color: #F7F7F7;">
                                {{ $correctionRequests->links() }}</div>
                        @endif
                    </div>

                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi generik untuk menangani logika filter
            function handleFilter(filterTypeEl, dateInputEl, formEl) {
                // Tampilkan/sembunyikan input tanggal saat halaman dimuat
                if (filterTypeEl.value === 'tanggal') {
                    dateInputEl.classList.remove('hidden');
                }

                // Saat dropdown utama berubah
                filterTypeEl.addEventListener('change', function() {
                    const selectedValue = this.value;
                    if (selectedValue === 'tanggal') {
                        dateInputEl.classList.remove('hidden');
                    } else if (selectedValue === 'clear') {
                        // Hapus semua parameter query string dan redirect
                        window.location.href = '{{ route('admin.monitoring.users.show', $user->id) }}';
                    } else {
                        dateInputEl.classList.add('hidden');
                        formEl.submit(); // Langsung submit untuk 'Terbaru' atau 'Terlama'
                    }
                });

                // Saat tanggal dipilih
                dateInputEl.addEventListener('change', function() {
                    if (this.value) { // Hanya submit jika ada tanggal yang dipilih
                        formEl.submit();
                    }
                });
            }

            // Terapkan fungsi ke filter Absensi
            handleFilter(
                document.getElementById('filter-type-absensi'),
                document.getElementById('tanggal-absensi'),
                document.getElementById('form-absensi')
            );

            // Terapkan fungsi ke filter Koreksi
            handleFilter(
                document.getElementById('filter-type-koreksi'),
                document.getElementById('tanggal-koreksi'),
                document.getElementById('form-koreksi')
            );
        });
    </script>
@endpush
