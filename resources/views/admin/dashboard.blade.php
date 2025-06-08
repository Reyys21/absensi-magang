@extends('layouts.app')

@section('content')
{{-- Library untuk Grafik & Kalender --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    /* Style untuk transisi animasi on-load */
    .dashboard-card {
        transition: opacity 0.5s ease-out, transform 0.5s ease-out;
    }
    .loading-state {
        opacity: 0;
        transform: translateY(20px);
    }
</style>

<main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            {{-- REVISI: Ukuran font responsif --}}
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan aktivitas dan data pengguna.</p>
        </div>
        @include('layouts.profile')
    </div>

    {{-- REVISI: Penambahan class untuk animasi on-load --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 dashboard-card loading-state">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Selamat Datang Kembali, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-600 mt-1">Saat ini ada <span class="font-bold text-orange-500">{{ $pendingCorrections }} permintaan koreksi</span> yang menunggu persetujuan Anda.</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex items-center">
             <form id="filter-form" method="GET" class="w-full">
                <label for="date-filter" class="block text-sm font-medium text-gray-700 mb-1">Tampilkan Data Untuk</label>
                <select name="filter" id="date-filter" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                    <option value="today" {{ $currentFilter == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="last_7_days" {{ $currentFilter == 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="this_month" {{ $currentFilter == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="custom" {{ $currentFilter == 'custom' ? 'selected' : '' }}>Pilih Tanggal</option>
                </select>
                <input type="text" id="custom-range-input" name="custom_range" class="hidden">
             </form>
        </div>
    </div>

    {{-- Grid Layout untuk KPI Cards --}}
    {{-- REVISI: Penambahan class untuk animasi on-load dengan delay berbeda --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="dashboard-card loading-state bg-white p-6 rounded-xl shadow-md border border-gray-200 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center gap-4"><div class="bg-blue-100 text-blue-500 p-3 rounded-full"><i class="fa-solid fa-users"></i></div><p class="text-sm font-medium text-gray-500">Total Pengguna</p></div>
            <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $totalUsers }}</p>
        </div>
        <div class="dashboard-card loading-state bg-white p-6 rounded-xl shadow-md border border-gray-200 transition-all hover:shadow-xl hover:-translate-y-1" style="transition-delay: 100ms;">
            <div class="flex items-center gap-4"><div class="bg-green-100 text-green-500 p-3 rounded-full"><i class="fa-solid fa-user-clock"></i></div><p class="text-sm font-medium text-gray-500">Pengguna Aktif</p></div>
            <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $activeInRange }}</p>
            <p class="text-xs mt-1 font-semibold {{ $activeTrend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                <i class="fa-solid {{ $activeTrend >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ number_format(abs($activeTrend), 1) }}% vs periode sebelumnya
            </p>
        </div>
        <div class="dashboard-card loading-state bg-white p-6 rounded-xl shadow-md border border-gray-200 transition-all hover:shadow-xl hover:-translate-y-1" style="transition-delay: 200ms;">
            <div class="flex items-center gap-4"><div class="bg-teal-100 text-teal-500 p-3 rounded-full"><i class="fa-solid fa-user-check"></i></div><p class="text-sm font-medium text-gray-500">Absensi Lengkap</p></div>
            <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $completedInRange }}</p>
        </div>
        <div class="dashboard-card loading-state bg-white p-6 rounded-xl shadow-md border border-gray-200 transition-all hover:shadow-xl hover:-translate-y-1" style="transition-delay: 300ms;">
            <div class="flex items-center gap-4"><div class="bg-orange-100 text-orange-500 p-3 rounded-full"><i class="fa-solid fa-circle-exclamation"></i></div><p class="text-sm font-medium text-gray-500">Koreksi Pending</p></div>
            <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $pendingCorrections }}</p>
        </div>

        {{-- Grid untuk Chart dan Tabel --}}
        <div class="col-span-1 sm:col-span-2 lg:col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border border-gray-200 dashboard-card loading-state" style="transition-delay: 400ms;">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Aktivitas Absensi Pengguna</h3>
                <div id="attendance-chart" style="min-height: 365px;"></div>
            </div>
            <div class="lg:col-span-1 flex flex-col gap-6">
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 dashboard-card loading-state" style="transition-delay: 500ms;">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Komposisi Pengguna</h3>
                    <div id="composition-chart" class="flex justify-center"></div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 dashboard-card loading-state" style="transition-delay: 600ms;">
                    <div class="flex justify-between items-center mb-2"><h3 class="text-lg font-bold text-gray-700">Aksi Cepat</h3><a href="{{ route('admin.approval.requests') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Semua</a></div>
                    <div class="space-y-3">
                        @forelse ($latestCorrections as $correction)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                            @php
                                $defaultPhoto = 'profile_photos/avatar_1 (1).jpg';
                                $photoPath = !empty($correction->user->profile_photo_path) ? $correction->user->profile_photo_path : $defaultPhoto;
                                $finalPhotoUrl = Str::startsWith($photoPath, 'avatars/') ? asset('storage/' . $photoPath) : asset($photoPath);
                            @endphp
                            <img src="{{ $finalPhotoUrl }}" alt="Foto" class="h-9 w-9 rounded-full object-cover">
                            <div><p class="text-sm font-semibold text-gray-800">{{ $correction->user->name }}</p><p class="text-xs text-gray-500">Koreksi untuk {{ $correction->attendance_date->format('d M') }}</p></div>
                        </div>
                        @empty
                        <div class="text-center p-4 text-gray-400"><i class="fa-solid fa-check-double fa-2x mb-2"></i><p class="text-sm">Tidak ada aksi.</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // REVISI: Script untuk memicu animasi on-load
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach((card, index) => {
        // Hapus class loading untuk memicu transisi
        setTimeout(() => {
            card.classList.remove('loading-state');
        }, 100 * index); // Efek muncul satu per satu
    });

    // Data dari Controller
    const trendData = @json($trendData);
    const compositionData = @json($compositionData);
    const totalUsers = {{ $totalUsers }};

    // Opsi Grafik Batang
    var attendanceChartOptions = {
        series: [{ name: 'Mahasiswa', data: trendData.mahasiswa }, { name: 'Siswa', data: trendData.siswa }],
        chart: { type: 'bar', height: 350, stacked: true, toolbar: { show: false }, parentHeightOffset: 0 },
        plotOptions: { bar: { horizontal: false, columnWidth: '60%', borderRadius: 6 } },
        dataLabels: { enabled: false },
        xaxis: { categories: trendData.labels, labels: { style: { colors: '#6B7280', fontSize: '12px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#6B7280', fontSize: '12px' } } },
        legend: { position: 'top', horizontalAlign: 'right', offsetY: -5, markers: { radius: 12 } },
        colors: ['#3B82F6', '#10B981'],
        grid: { show: true, borderColor: '#EDF2F7', strokeDashArray: 4, padding: { left: -5, right: 0 } },
        tooltip: { y: { formatter: (val) => val + " orang" } },
    };
    var attendanceChart = new ApexCharts(document.querySelector("#attendance-chart"), attendanceChartOptions);
    attendanceChart.render();

    // Opsi Grafik Donat
    var compositionChartOptions = {
        series: compositionData.series,
        chart: { type: 'donut', height: 200 },
        labels: compositionData.labels,
        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, name: { show: false }, value: { offsetY: 8, fontSize: '22px', fontWeight: 'bold' }, total: { show: true, label: 'Total', color: '#6B7280', fontSize: '14px', formatter: () => totalUsers } } } } },
        dataLabels: { enabled: false },
        colors: ['#3B82F6', '#10B981'],
        legend: { show: true, position: 'bottom', horizontalAlign: 'center', itemMargin: { horizontal: 10 } }
    };
    var compositionChart = new ApexCharts(document.querySelector("#composition-chart"), compositionChartOptions);
    compositionChart.render();

    // Logika untuk Filter Tanggal
    const filterSelect = document.getElementById('date-filter');
    const customRangeInput = document.getElementById('custom-range-input');
    const fp = flatpickr(customRangeInput, {
        mode: "range", dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y",
        onClose: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                const form = document.getElementById('filter-form');
                let startInput = form.querySelector('input[name="start_date"]');
                if (!startInput) {
                    startInput = document.createElement('input');
                    startInput.type = 'hidden'; startInput.name = 'start_date'; form.appendChild(startInput);
                }
                startInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                let endInput = form.querySelector('input[name="end_date"]');
                if (!endInput) {
                    endInput = document.createElement('input');
                    endInput.type = 'hidden'; endInput.name = 'end_date'; form.appendChild(endInput);
                }
                endInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
                form.submit();
            }
        }
    });
    filterSelect.addEventListener('change', function() { if (this.value === 'custom') { fp.open(); } else { document.getElementById('filter-form').submit(); } });
});
</script>
@endpush