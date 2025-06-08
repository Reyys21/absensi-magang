@extends('layouts.app')

@section('content')
<main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Detail Riwayat Pengguna</h1>
            <a href="{{ route('admin.monitoring.users.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                &larr; Kembali ke Daftar Monitoring
            </a>
        </div>
        @include('layouts.profile')
    </div>

    {{-- Kartu Info Pengguna --}}
    <div class="mb-6 p-4 border rounded-xl bg-white shadow-sm flex items-center gap-4">
        @php
            $defaultPhoto = 'profile_photos/avatar_1 (1).jpg';
            $photoPath = !empty($user->profile_photo_path) ? $user->profile_photo_path : $defaultPhoto;
            $finalPhotoUrl = Str::startsWith($photoPath, 'avatars/') ? asset('storage/' . $photoPath) : asset($photoPath);
        @endphp
        <img src="{{ $finalPhotoUrl }}" alt="Foto" class="w-16 h-16 rounded-full object-cover">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
            <p class="text-gray-500">{{ $user->email }} | HP: {{ $user->phone ?? 'N/A' }}</p>
        </div>
    </div>

    {{-- Container untuk Riwayat --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl shadow-md border border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-calendar-check text-blue-500"></i>
                    <h3 class="text-lg font-semibold text-gray-700">Riwayat Absensi</h3>
                </div>
                <form id="form-absensi" method="GET" class="flex items-center gap-2">
                    <input type="date" name="filter_tanggal_absensi" id="tanggal-absensi" value="{{ request('filter_tanggal_absensi') }}" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 hidden">
                    <select name="filter_type_absensi" id="filter-type-absensi" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                        <option value="terbaru" {{ request('filter_type_absensi', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlama" {{ request('filter_type_absensi') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                        <option value="tanggal" {{ request('filter_type_absensi') == 'tanggal' ? 'selected' : '' }}>Pilih Tanggal</option>
                        <option value="clear">Hapus Filter</option>
                    </select>
                </form>
            </div>
            <div class="overflow-y-auto flex-grow">
                @forelse($attendances as $attendance)
                <div class="p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $attendance->date->translatedFormat('l, d M Y') }}</p>
                        <p class="text-xs text-gray-500">Check-in: {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }} | Check-out: {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $attendance->attendance_status == 'Lengkap' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $attendance->attendance_status }}</span>
                </div>
                @empty
                <div class="text-center p-10 text-gray-400"><i class="fa-solid fa-ghost fa-2x mb-2"></i><p>Tidak ada riwayat absensi.</p></div>
                @endforelse
            </div>
            @if($attendances->hasPages())<div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">{{ $attendances->links() }}</div>@endif
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 flex flex-col">
             <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-pen text-orange-500"></i>
                    <h3 class="text-lg font-semibold text-gray-700">Riwayat Pengajuan Koreksi</h3>
                </div>
                <form id="form-koreksi" method="GET" class="flex items-center gap-2">
                    <input type="date" name="filter_tanggal_koreksi" id="tanggal-koreksi" value="{{ request('filter_tanggal_koreksi') }}" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 hidden">
                    <select name="filter_type_koreksi" id="filter-type-koreksi" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                        <option value="terbaru" {{ request('filter_type_koreksi', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlama" {{ request('filter_type_koreksi') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                        <option value="tanggal" {{ request('filter_type_koreksi') == 'tanggal' ? 'selected' : '' }}>Pilih Tanggal</option>
                        <option value="clear">Hapus Filter</option>
                    </select>
                </form>
            </div>
            <div class="overflow-y-auto flex-grow">
                @forelse($correctionRequests as $request)
                <div class="p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800">Koreksi untuk {{ $request->attendance_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">Diajukan pada {{ $request->created_at->format('d M Y') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full @if($request->status == 'approved') bg-green-100 text-green-800 @elseif($request->status == 'rejected') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">{{ Str::ucfirst($request->status) }}</span>
                </div>
                @empty
                <div class="text-center p-10 text-gray-400"><i class="fa-solid fa-ghost fa-2x mb-2"></i><p>Tidak ada riwayat pengajuan koreksi.</p></div>
                @endforelse
            </div>
            @if($correctionRequests->hasPages())<div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">{{ $correctionRequests->links() }}</div>@endif
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
            if(this.value) { // Hanya submit jika ada tanggal yang dipilih
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