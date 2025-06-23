@extends('layouts.app')

@section('content')
    {{-- Library untuk Grafik & Kalender --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <header class="bg-white flex flex-row justify-between items-center py-2 px-4 sm:px-6 md:px-8 border-b border-gray-200">
        <div>
            <h1 class="text-lg sm:text-1xl lg:text-2xl font-bold text-[#2A2B2A]">Dashboard</h1>
        </div>
        @include('layouts.profile')
    </header>

    {{-- Konten utama dimulai di sini --}}
    <main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50/50">

        {{-- ▼▼▼ KODE BARU DITAMBAHKAN DI SINI ▼▼▼ --}}
        <div class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded-r-lg shadow-sm" role="alert">
            @if(Auth::user()->can('view all users'))
                <p class="font-bold">Anda Memiliki Hak Akses Global</p>
                <p class="text-sm mt-1">Anda dapat melihat data dari semua bidang. Gunakan filter pada setiap tabel atau grafik untuk melihat data bidang spesifik.</p>
            @else
                <p class="font-bold">Anda Mengelola Bidang: {{ Auth::user()->bidang->name ?? 'N/A' }}</p>
                <p class="text-sm mt-1">Data yang ditampilkan di bawah ini adalah data yang relevan untuk bidang Anda.</p>
            @endif
        </div>
        {{-- ▲▲▲ AKHIR KODE BARU ▲▲▲ --}}

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="flex flex-col gap-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-[#14BDEB] p-5 rounded-2xl shadow-sm border border-gray-100">
                        <div
                            class="flex items-center justify-center bg-indigo-100 text-indigo-500 w-10 h-10 rounded-lg mb-4">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <p class="text-3xl font-bold text-[#2A2B2A]">{{ $totalUsers }}</p>
                        <p class="text-sm font-medium text-[#2A2B2A]">Total Pengguna</p>
                    </div>
                    <div class="bg-[#FFD100] p-5 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-center bg-green-100 text-green-500 w-10 h-10 rounded-lg mb-4">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                        <p class="text-3xl font-bold text-[#2A2B2A]">{{ $activeInRange }}</p>
                        <div class="flex items-center text-sm font-medium text-[#2A2B2A]">
                            <span>Pengguna Aktif</span>
                            <span
                                class="ml-2 text-xs font-semibold {{ $activeTrend >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                ({{ number_format($activeTrend, 0) }}%)
                            </span>
                        </div>
                    </div>
                    <div class="bg-[#F7FFF7] p-5 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-center bg-sky-100 text-sky-500 w-10 h-10 rounded-lg mb-4"><i
                                class="fa-solid fa-user-check"></i></div>
                        <p class="text-3xl font-bold text-[#2A2B2A]">{{ $completedInRange }}</p>
                        <p class="text-sm font-medium text-[#2A2B2A]">Absensi Lengkap</p>
                    </div>
                    <div class="bg-[#F0386B] p-5 rounded-2xl shadow-sm border border-gray-100">
                        <div
                            class="flex items-center justify-center bg-orange-100 text-orange-500 w-10 h-10 rounded-lg mb-4">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                        <p class="text-3xl font-bold text-[#2A2B2A]">{{ $pendingCorrections }}</p>
                        <p class="text-sm font-medium text-[#2A2B2A]">Koreksi Pending</p>
                    </div>
                </div>

                <div class="bg-[#F7FFF7] p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-[#2A2B2A]">Permintaan Koreksi Menunggu</h3>
                        <a href="{{ route('admin.approval.requests') }}"
                            class="text-sm font-medium text-[#2A2B2A] hover:text-[#C1C4C1] ">Lihat Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-[##14BDEB] text-[#2A2B2A] uppercase text-xs">
                                <tr>
                                    <th class="p-3 text-left">Pengguna</th>
                                    <th class="p-3 text-left">Tanggal Koreksi</th>
                                    <th class="p-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($latestCorrections as $correction)
                                    <tr class="hover:bg-[#FFD100] ">
                                        <td class="p-3">
                                            <div class="flex items-center gap-3">
                                                @php
                                                    $defaultPhoto = 'profile_photos/avatar_1 (1).jpg';
                                                    $photoPath = !empty($correction->user->profile_photo_path)
                                                        ? $correction->user->profile_photo_path
                                                        : $defaultPhoto;
                                                    $finalPhotoUrl = Str::startsWith($photoPath, 'avatars/')
                                                        ? asset('storage/' . $photoPath)
                                                        : asset($photoPath);
                                                @endphp
                                                <img src="{{ $finalPhotoUrl }}" alt="Foto"
                                                    class="h-9 w-9 rounded-full object-cover">
                                                <div>
                                                    <p class="font-semibold text-[#2A2B2A]">{{ $correction->user->name }}</p>
                                                    <p class="text-xs text-[#2A2B2A]">{{ $correction->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-3 text-[#2A2B2A]">{{ $correction->attendance_date->format('d M Y') }}
                                        </td>
                                        <td class="p-3">
                                            <span
                                                class="px-3 py-1 text-xs font-semibold rounded-full bg-[#F7FFF7] text-[#2A2B2A] border-2 border-[#ffd100]">
                                                {{ Str::ucfirst($correction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center p-8 text-[#2A2B2A]"><i
                                                class="fa-solid fa-check-double fa-3x mb-2"></i>
                                            <p class="font-medium">Tidak ada permintaan koreksi.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-6">

                <div class="bg-[#F7FFF7] p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-[#2A2B2A]">Performa Magang</h3>
                        <form id="filter-form" method="GET">
                            <select name="filter" id="date-filter"
                                class="bg-[#F7FFF7] border-gray-200  rounded-sm text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="last_7_days" {{ $currentFilter == 'last_7_days' ? 'selected' : '' }}>7 Hari
                                    Terakhir</option>
                                <option value="this_month" {{ $currentFilter == 'this_month' ? 'selected' : '' }}>Bulan Ini
                                </option>
                                <option value="custom" {{ $currentFilter == 'custom' ? 'selected' : '' }}>Pilih Tanggal
                                </option>
                            </select>
                            <input type="text" id="custom-range-input" name="custom_range" class="hidden">
                        </form>
                    </div>
                    <div id="performance-chart"></div>
                </div>

                <div class="bg-[#F7FFF7] p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-[#2A2B2A] mb-4">Komposisi Pengguna</h3>
                    <div id="composition-chart" class="flex justify-center"></div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendData = @json($trendData);
            const compositionData = @json($compositionData);
            const totalUsers = {{ $totalUsers }};

            // Opsi Grafik Batang (Bar Chart)
            var performanceChartOptions = {
                series: [{
                    name: 'Mahasiswa',
                    data: trendData.mahasiswa
                }, {
                    name: 'Siswa',
                    data: trendData.siswa
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    stacked: false,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '40%',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: trendData.labels,
                    labels: {
                        style: {
                            colors: '#2A2B2A'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: ['#2A2B2A', '#14BDEB'],
                            colors: '#'
                        },
                        offsetX: -10
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetY: -5,
                    markers: {
                        radius: 12
                    }
                },
                colors: ['#2A2B2A', '#14BDEB'],
                grid: {
                    show: true,
                    borderColor: '#EDF2F7',
                    strokeDashArray: 4,
                    padding: {
                        left: 0,
                        right: 0
                    }
                },
                tooltip: {
                    y: {
                        formatter: (val) => val + " orang"
                    }
                },
            };
            var performanceChart = new ApexCharts(document.querySelector("#performance-chart"),
                performanceChartOptions);
            performanceChart.render();

            // Opsi Grafik Donat (Donut Chart)
            var compositionChartOptions = {
                series: compositionData.series,
                chart: {
                    type: 'donut',
                    height: 250
                },
                labels: compositionData.labels,
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: {
                                    show: false
                                },
                                value: {
                                    offsetY: 8,
                                    fontSize: '28px',
                                    fontWeight: 'bold'
                                },
                                total: {
                                    show: true,
                                    label: 'Total Pengguna',
                                    color: '#6B7280',
                                    fontSize: '14px',
                                    formatter: () => totalUsers
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                colors: ['  #FFD100', '#F0386B'],
                legend: {
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    itemMargin: {
                        horizontal: 10
                    },
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 12
                    }
                }
            };
            var compositionChart = new ApexCharts(document.querySelector("#composition-chart"),
                compositionChartOptions);
            compositionChart.render();

            // Logika untuk Filter Tanggal
            const filterSelect = document.getElementById('date-filter');
            const fp = flatpickr("#custom-range-input", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                onClose: function(selectedDates) {
                    if (selectedDates.length === 2) {
                        const form = document.getElementById('filter-form');
                        let startInput = form.querySelector('input[name="start_date"]') || document
                            .createElement('input');
                        startInput.type = 'hidden';
                        startInput.name = 'start_date';
                        startInput.value = this.formatDate(selectedDates[0], "Y-m-d");
                        form.appendChild(startInput);

                        let endInput = form.querySelector('input[name="end_date"]') || document
                            .createElement('input');
                        endInput.type = 'hidden';
                        endInput.name = 'end_date';
                        endInput.value = this.formatDate(selectedDates[1], "Y-m-d");
                        form.appendChild(endInput);

                        form.submit();
                    }
                }
            });
            filterSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    fp.open();
                } else {
                    document.getElementById('filter-form').submit();
                }
            });
        });
    </script>
@endpush