@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">

        {{-- sidebar --}}


 <div class="flex-1 flex flex-col">

            <header class="bg-white flex flex-row justify-between items-center py-2 px-4 sm:px-6 md:px-8 border-b border-gray-200 sticky top-0 z-10">
                <div>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Kehadiran Saya</h1>
                </div>
                @include('layouts.profile')
            </header>

            {{-- Konten utama dimulai di sini --}}
            <main id="main-content" class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50/50">

            <div class="flex flex-wrap gap-2 mb-6 items-center">
                <div class="relative">
                    <button onclick="toggleDropdown('exportDropdown')"
                        class="bg-[#A74FDE] text-white px-4 py-2 rounded hover:bg-[#c98ef2] text-sm border-2 border-black ">
                        Ekspor <i class="fa-solid fa-chevron-down ml-2"></i>
                    </button>
                    <div id="exportDropdown"
                        class="dropdown absolute mt-2 bg-[#A74FDE] text-white shadow-lg z-10 w-48 text-left px-4 py-2 rounded text-sm border-2 border-black transition-all duration-300 ease-out transform opacity-0 scale-95 pointer-events-none">

                        <a href="#" onclick="exportToExcel()"
                            class="block px-4 py-2 text-white hover:bg-[#debaf8] hover:text-black hover:rounded">
                            Excel
                        </a>
                        <a href="#" onclick="exportToCSV()"
                            class="block px-4 py-2 text-white hover:bg-[#debaf8] hover:text-black hover:rounded">
                            CSV
                        </a>
                        <a href="#" onclick="exportToPDF()"
                            class="block px-4 py-2 text-white hover:bg-[#debaf8] hover:text-black hover:rounded">
                            PDF
                        </a>
                        <a href="#" onclick="printTable('attendanceTable')"
                            class="block px-4 py-2 text-white hover:bg-[#debaf8] hover:text-black hover:rounded">
                            Cetak
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <button onclick="toggleDropdown('filterDropdown')"
                        class="bg-[#3E25FF] text-white px-4 py-2 rounded hover:bg-[#aeb1ff] text-sm border-2 border-black">
                        Filter <i class="fa-solid fa-chevron-down ml-2"></i>
                    </button>

                    <form id="filterDropdown" action="{{ route('attendance.my') }}" method="GET"
                        class="hidden absolute mt-2 bg-[#3E25FF] text-white px-4 py-2 rounded text-sm border-2 border-black rounded p-4 w-64 z-10 space-y-3 transition-all duration-300 opacity-0 scale-95">

                        <button type="submit" name="sort" value="desc"
                            class="w-full text-left text-sm text-white hover:bg-[#aeb1ff] hover:text-black px-2 py-1 rounded hover:rounded">Terbaru</button>

                        <button type="submit" name="sort" value="asc"
                            class="w-full text-left text-sm text-white hover:bg-[#aeb1ff] hover:text-black px-2 py-1 rounded hover:rounded">Terlama</button>

                        <div class="flex flex-col">
                            <label class="text-sm text-white mb-1">Pilih Tanggal</label>
                            <input type="date" name="date"
                                class="border text-black border-white rounded px-2 py-1 text-sm"
                                onchange="this.form.submit()" value="{{ request('date') }}" />
                        </div>

                        <a href="{{ route('attendance.my') }}"
                            class="block text-center mt-2 bg-red-500 hover:bg-[#aeb1ff] hover:text-black text-white rounded px-2 py-1 text-sm cursor-pointer hover:rounded">
                            Hapus Filter
                        </a>
                    </form>

                </div>
            </div>

            {{-- Table container with overflow-x-auto for responsiveness --}}
            <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200" id="tableContainer">
                <table id="attendanceTable" class="min-w-full text-sm text-left table-auto">
                    <thead class="bg-white text-black uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-4 whitespace-nowrap">No</th>
                            <th class="py-3 px-4 whitespace-nowrap">Tanggal</th>
                            <th class="py-3 px-4 whitespace-nowrap">Check-In</th>
                            <th class="py-3 px-4 whitespace-nowrap">Check-Out</th>
                            {{-- Removed whitespace-nowrap to allow text wrapping for Activity Title --}}
                            <th class="py-3 px-4">Judul Aktivitas</th>
                            {{-- Removed whitespace-nowrap to allow text wrapping for Activity Description and adjusted width --}}
                            <th class="py-3 px-4 w-full md:w-[35%]">Deskripsi Aktivitas</th>
                            <th class="py-3 px-4 whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y divide-gray-200">
                        @forelse($attendances as $index => $item)
                            <tr class="align-top border hover:bg-gray-50 transition-all duration-200">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="px-4 py-2 border border-gray-300 text-center">
                                    {{ $item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('H.i') : '--.--' }}
                                </td>
                                <td class="px-4 py-2 border border-gray-300 text-center">
                                    {{ $item->check_out ? \Carbon\Carbon::parse($item->check_out)->format('H.i') : '--.--' }}
                                </td>
                                <td class="py-2 px-4 text-justify activity-cell-title">
                                    @if ($item->activity_title)
                                        <span class="font-semibold text-gray-800">{{ $item->activity_title }}</span>
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-justify activity-cell-description">
                                    @if ($item->activity_description)
                                        @php
                                            $limit = 150;
                                            $shortDescription = Str::limit($item->activity_description, $limit);
                                            $isLongDescription = strlen($item->activity_description) > $limit;
                                        @endphp

                                        <span class="activity-content activity-short-description-{{ $item->id }}"
                                            data-full-text="{!! nl2br(e($item->activity_description)) !!}"
                                            data-short-text="{!! nl2br(e($shortDescription)) !!}">
                                            {!! nl2br(e($shortDescription)) !!}
                                        </span>

                                        @if ($isLongDescription)
                                            <a href="#" class="text-[#8180ff] hover:underline see-more-btn"
                                                data-id="{{ $item->id }}">Lihat Selengkapnya</a>
                                            <a href="#" class="text-blue-500 hover:underline see-less-btn hidden"
                                                data-id="{{ $item->id }}">Ringkasan</a>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    @php
                                        $attendanceStatus = $item->attendance_status;
                                        $customColor = '';
                                        switch ($attendanceStatus) {
                                            case 'Lengkap':
                                                $customColor = '#28CB6E';
                                                break;

                                            case 'Tidak Hadir (Belum Lengkap)':
                                                $customColor = '#f86917';
                                                break;
                                            default:
                                                $customColor = '#A0AEC0'; // warna default (abu-abu)
                                                break;
                                        }
                                    @endphp
                                    <span class="text-white px-2 py-1 rounded-full text-xs font-medium"
                                        style="background-color: {{ $customColor }};">
                                        {{ $attendanceStatus }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500 italic">Tidak ada catatan kehadiran
                                    ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
  
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

            <script>
                // Fungsi untuk mengaktifkan/menonaktifkan visibilitas dropdown
                function toggleDropdown(id) {
                    const dropdown = document.getElementById(id);
                    const allDropdowns = document.querySelectorAll('.dropdown');

                    // Tutup dropdown lain yang terbuka
                    allDropdowns.forEach(d => {
                        if (d.id !== id && !d.classList.contains('hidden')) {
                            d.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                            d.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                            setTimeout(() => d.classList.add('hidden'), 300);
                        }
                    });

                    // Alihkan dropdown yang diklik
                    if (dropdown.classList.contains('hidden')) {
                        dropdown.classList.remove('hidden', 'pointer-events-none');
                        setTimeout(() => {
                            dropdown.classList.remove('opacity-0', 'scale-95');
                            dropdown.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
                        }, 10);
                    } else {
                        dropdown.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                        setTimeout(() => {
                            dropdown.classList.add('hidden');
                        }, 300);
                    }
                }

                // Tutup dropdown saat mengklik di luar area
                document.addEventListener('click', function(e) {
                    const isDropdownButton = e.target.closest('button[onclick^="toggleDropdown"]');
                    const isDropdownContent = e.target.closest('.dropdown');

                    if (!isDropdownButton && !isDropdownContent) {
                        document.querySelectorAll('.dropdown').forEach(dropdown => {
                            if (!dropdown.classList.contains('hidden')) {
                                dropdown.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                                dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                                setTimeout(() => dropdown.classList.add('hidden'), 300);
                            }
                        });
                    }
                });


                // Fungsionalitas Lihat Selengkapnya / Ringkasan untuk deskripsi aktivitas
                document.querySelectorAll('.see-more-btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const itemId = this.dataset.id;
                        const shortDescriptionSpan = document.querySelector(
                            `.activity-short-description-${itemId}`);
                        const fullText = shortDescriptionSpan.dataset.fullText;
                        const seeLessBtn = document.querySelector(`.see-less-btn[data-id="${itemId}"]`);

                        shortDescriptionSpan.innerHTML = fullText;
                        this.classList.add('hidden');
                        if (seeLessBtn) {
                            seeLessBtn.classList.remove('hidden');
                        }
                    });
                });

                document.querySelectorAll('.see-less-btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const itemId = this.dataset.id;
                        const shortDescriptionSpan = document.querySelector(
                            `.activity-short-description-${itemId}`);
                        // Ambil teks pendek asli dari atribut data
                        const originalShortText = shortDescriptionSpan.dataset.shortText;
                        const seeMoreBtn = document.querySelector(`.see-more-btn[data-id="${itemId}"]`);

                        shortDescriptionSpan.innerHTML = originalShortText;
                        this.classList.add('hidden');
                        if (seeMoreBtn) {
                            seeMoreBtn.classList.remove('hidden');
                        }
                    });
                });

                // --- Fungsi Ekspor ---

                function exportToExcel() {
                    const table = document.getElementById('attendanceTable');
                    // Buat workbook dan sheet baru
                    const wb = XLSX.utils.book_new();
                    const ws_data = [];

                    // Tambahkan baris header secara manual, hilangkan efek whitespace-nowrap
                    const headerNames = ["No", "Tanggal", "Check-In", "Check-Out", "Judul Aktivitas",
                        "Deskripsi Aktivitas", "Status"
                    ];
                    ws_data.push(headerNames);

                    // Iterasi baris tabel untuk mendapatkan teks lengkap Deskripsi Aktivitas
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const rowData = [];
                        // Dapatkan data untuk kolom tetap
                        rowData.push(row.cells[0].innerText.trim()); // No
                        rowData.push(row.cells[1].innerText.trim()); // Tanggal
                        rowData.push(row.cells[2].innerText.trim()); // Check-In
                        rowData.push(row.cells[3].innerText.trim()); // Check-Out

                        // Dapatkan teks lengkap untuk Judul dan Deskripsi Aktivitas
                        const activityTitleElement = row.querySelector('.activity-cell-title span');
                        const activityDescriptionElement = row.querySelector(
                        '.activity-cell-description .activity-content');
                        const statusElement = row.querySelector('td:last-child span');

                        const activityTitle = activityTitleElement ? activityTitleElement.innerText.trim() : '';
                        let activityDescription = activityDescriptionElement ? activityDescriptionElement.dataset
                            .fullText || activityDescriptionElement.innerText.trim() : '';
                        activityDescription = activityDescription.replace(/(\r\n|\n|\r)/gm, " ").replace(/\s\s+/g,
                        " "); // Bersihkan baris baru untuk Excel

                        const status = statusElement ? statusElement.innerText.trim() : '';

                        rowData.push(activityTitle);
                        rowData.push(activityDescription);
                        rowData.push(status);
                        ws_data.push(rowData);
                    });

                    const ws = XLSX.utils.aoa_to_sheet(ws_data);
                    XLSX.utils.book_append_sheet(wb, ws, "Kehadiran");
                    XLSX.writeFile(wb, "kehadiran_saya.xlsx");
                }


                function exportToCSV() {
                    const table = document.getElementById('attendanceTable');
                    let csv = [];

                    const headerNames = ["No", "Tanggal", "Check-In", "Check-Out", "Judul Aktivitas",
                        "Deskripsi Aktivitas", "Status"
                    ];
                    csv.push(headerNames.map(h => cleanTextForCSV(h)).join(','));

                    table.querySelectorAll('tbody tr').forEach(row => {
                        let rowData = [];
                        rowData.push(cleanTextForCSV(row.cells[0].innerText)); // No
                        rowData.push(cleanTextForCSV(row.cells[1].innerText)); // Tanggal
                        rowData.push(cleanTextForCSV(row.cells[2].innerText)); // Check-In
                        rowData.push(cleanTextForCSV(row.cells[3].innerText)); // Check-Out

                        const activityTitleCell = row.querySelector('.activity-cell-title span');
                        const activityDescriptionSpan = row.querySelector(
                            '.activity-cell-description .activity-content');
                        const statusSpan = row.querySelector('td:last-child span');


                        const activityTitle = activityTitleCell ? activityTitleCell.innerText.trim() : '';
                        let activityDescription = activityDescriptionSpan ? activityDescriptionSpan.dataset
                            .fullText || activityDescriptionSpan.innerText.trim() : '';
                        activityDescription = activityDescription.replace(/(\r\n|\n|\r)/gm, " ").replace(
                            /\s\s+/g, " ");

                        const status = statusSpan ? statusSpan.innerText.trim() : '';

                        rowData.push(cleanTextForCSV(activityTitle));
                        rowData.push(cleanTextForCSV(activityDescription));
                        rowData.push(cleanTextForCSV(status));

                        csv.push(rowData.join(','));
                    });

                    const csvFile = new Blob([csv.join('\n')], {
                        type: 'text/csv'
                    });
                    const downloadLink = document.createElement('a');
                    downloadLink.download = "kehadiran_saya.csv";
                    downloadLink.href = window.URL.createObjectURL(csvFile);
                    downloadLink.style.display = 'none';
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }

                function cleanTextForCSV(text) {
                    let cleanedText = text.replace(/(\r\n|\n|\r)/gm, " ").replace(/\s\s+/g, " ").trim();
                    if (cleanedText.includes(',') || cleanedText.includes('"')) {
                        cleanedText = '"' + cleanedText.replace(/"/g, '""') + '"';
                    }
                    return cleanedText;
                }

                async function exportToPDF() {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF('l', 'pt', 'a4'); // 'l' untuk lanskap, 'pt' untuk poin, 'a4' untuk ukuran A4
                    const table = document.getElementById('attendanceTable');

                    // 1. Simpan status awal dan perluas semua konten "Aktivitas"
                    const initialStates = [];
                    document.querySelectorAll('.activity-cell-description').forEach(cell => {
                        const shortDescriptionSpan = cell.querySelector('.activity-content');
                        const seeMoreBtn = cell.querySelector('.see-more-btn');
                        const seeLessBtn = cell.querySelector('.see-less-btn');

                        if (shortDescriptionSpan && shortDescriptionSpan.dataset.fullText) {
                            initialStates.push({
                                shortDescriptionSpan: shortDescriptionSpan,
                                originalContent: shortDescriptionSpan.innerHTML,
                                seeMoreBtn: seeMoreBtn,
                                seeLessBtn: seeLessBtn,
                                seeMoreHidden: seeMoreBtn ? seeMoreBtn.classList.contains('hidden') : true,
                                seeLessHidden: seeLessBtn ? seeLessBtn.classList.contains('hidden') : true
                            });

                            shortDescriptionSpan.innerHTML = shortDescriptionSpan.dataset.fullText;
                            if (seeMoreBtn) seeMoreBtn.classList.add('hidden');
                            if (seeLessBtn) seeLessBtn.classList.add('hidden');
                        }
                    });

                    // Sesuaikan sementara lebar tabel atau overflow kontainer untuk memastikan semua konten dirender untuk html2canvas
                    const tableContainer = document.getElementById('tableContainer');
                    const originalTableContainerStyle = tableContainer.style.cssText;
                    tableContainer.style.overflowX = 'visible';
                    tableContainer.style.width = 'fit-content'; // Penting untuk ekspor PDF

                    const originalTableStyle = table.style.cssText;
                    table.style.width = 'fit-content';
                    table.style.tableLayout = 'auto'; // Izinkan kolom untuk menyesuaikan ukuran berdasarkan konten

                    // Tunggu DOM untuk merender perubahan
                    await new Promise(resolve => setTimeout(resolve, 300));

                    doc.html(table, {
                        callback: function(doc) {
                            doc.save('Kehadiran_Saya.pdf');

                            // Kembalikan konten "Aktivitas" ke status asli
                            initialStates.forEach(state => {
                                state.shortDescriptionSpan.innerHTML = state.originalContent;
                                if (state.seeMoreBtn && !state.seeMoreHidden) state.seeMoreBtn.classList
                                    .remove('hidden');
                                if (state.seeLessBtn && !state.seeLessHidden) state.seeLessBtn.classList
                                    .remove('hidden');
                            });

                            // Kembalikan gaya kontainer tabel
                            tableContainer.style.cssText = originalTableContainerStyle;
                            table.style.cssText = originalTableStyle;
                        },
                        x: 10,
                        y: 10,
                        html2canvas: {
                            scale: 0.6, // Skala disesuaikan agar lebih banyak konten muat di halaman
                            logging: true,
                            allowTaint: true,
                            useCORS: true,
                        }
                    });
                }


                async function printTable(tableID) {
                    const table = document.getElementById(tableID);
                    const originalBodyHtml = document.body.innerHTML;

                    // Simpan status awal dan perluas semua konten "Aktivitas" untuk dicetak
                    const initialStates = [];
                    document.querySelectorAll('.activity-cell-description').forEach(cell => {
                        const shortDescriptionSpan = cell.querySelector('.activity-content');
                        const seeMoreBtn = cell.querySelector('.see-more-btn');
                        const seeLessBtn = cell.querySelector('.see-less-btn');

                        if (shortDescriptionSpan && shortDescriptionSpan.dataset.fullText) {
                            initialStates.push({
                                shortDescriptionSpan: shortDescriptionSpan,
                                originalContent: shortDescriptionSpan.innerHTML,
                                seeMoreBtn: seeMoreBtn,
                                seeLessBtn: seeLessBtn,
                                seeMoreHidden: seeMoreBtn ? seeMoreBtn.classList.contains('hidden') : true,
                                seeLessHidden: seeLessBtn ? seeLessBtn.classList.contains('hidden') : true
                            });

                            shortDescriptionSpan.innerHTML = shortDescriptionSpan.dataset.fullText;
                            if (seeMoreBtn) seeMoreBtn.classList.add('hidden');
                            if (seeLessBtn) seeLessBtn.classList.add('hidden');
                        }
                    });

                    // Buat elemen sementara untuk menampung tabel untuk dicetak
                    const printWindow = window.open('', '', 'height=600,width=800');
                    printWindow.document.write('<html><head><title>Cetak</title>');
                    // Tautkan ke CSS aplikasi utama untuk gaya yang konsisten
                    printWindow.document.write('<link href="{{ asset('build/assets/app.css') }}" rel="stylesheet">');
                    printWindow.document.write('<style>');
                    printWindow.document.write('body { font-family: sans-serif; margin: 20px; }');
                    printWindow.document.write(
                        'table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }'
                    );
                    printWindow.document.write(
                        'th, td { border: 1px solid #ccc; padding: 5px; text-align: left; vertical-align: top;}'
                    );
                    printWindow.document.write('thead { background-color: #f2f2f2; }');
                    // Sembunyikan tombol "Lihat Selengkapnya/Ringkasan" saat dicetak
                    printWindow.document.write('.see-more-btn, .see-less-btn { display: none !important; }');
                    // Pastikan teks melengkung di sel deskripsi saat dicetak
                    printWindow.document.write('td.activity-cell-description { white-space: normal; }');
                    // Gaya khusus cetak untuk tata letak tabel dan pembungkus kata
                    printWindow.document.write(
                        '@media print { body { -webkit-print-color-adjust: exact; } table { table-layout: fixed; width: 100%; } td { word-wrap: break-word; } }'
                        );
                    printWindow.document.write('</style>');
                    printWindow.document.write('</head><body>');
                    printWindow.document.write('<h1>Catatan Kehadiran Saya</h1>');
                    printWindow.document.write(table.outerHTML);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();

                    printWindow.onload = function() {
                        printWindow.focus();
                        printWindow.print();
                        printWindow.close();

                        // Kembalikan konten "Aktivitas" ke status asli setelah dicetak
                        initialStates.forEach(state => {
                            state.shortDescriptionSpan.innerHTML = state.originalContent;
                            if (state.seeMoreBtn && !state.seeMoreHidden) state.seeMoreBtn.classList.remove(
                                'hidden');
                            if (state.seeLessBtn && !state.seeLessHidden) state.seeLessBtn.classList.remove(
                                'hidden');
                        });
                    };
                }
            </script>
        </main>
    </div>
@endsection
