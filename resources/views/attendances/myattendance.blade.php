@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen font-[Inter]">


        {{-- sidebar --}}
        @include('layouts.sidebar')

        <main class="flex-1 p-4 md:p-6 bg-gray-100">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold">My Attendance</h1>
                {{-- Ini dia! Sertakan komponen profil di sini --}}
                @include('layouts.profile')
            </div>

            <div class="flex flex-wrap gap-2 mb-6 items-center">
                <div class="relative">
                    <button onclick="toggleDropdown('exportDropdown')"
                        class="bg-[#A74FDE] text-white px-4 py-2 rounded hover:bg-[#c98ef2] text-sm border-2 border-black ">
                        Export <i class="fa-solid fa-chevron-down ml-2"></i>
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
                            Print
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <button onclick="toggleDropdown('filterDropdown')"
                        class="bg-[#3E25FF] text-white px-4 py-2 rounded hover:bg-[#aeb1ff] text-sm border-2 border-black">
                        Filter <i class="fa-solid fa-chevron-down ml-2"></i>
                    </button>

                    <form id="filterDropdown" action="{{ route('attendance.my') }}" method="GET"
                        class="hidden absolute mt-2 bg-[#3E25FF] text-white px-4 py-2 rounded text-sm border-2 border-black rounded p-4 w-64 z-10 space-y-3  transition-all duration-300 opacity-0 scale-95">

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
                            Clear Filter
                        </a>
                    </form>

                </div>
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200" id="tableContainer">
                <table id="attendanceTable" class="min-w-full text-sm text-left table-auto">
                    <thead class="bg-white text-black uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-4 whitespace-nowrap">No</th>
                            <th class="py-3 px-4 whitespace-nowrap">Date</th>
                            <th class="py-3 px-4 whitespace-nowrap">Check-In</th>
                            <th class="py-3 px-4 whitespace-nowrap">Check-Out</th>
                            <th class="py-3 px-4 whitespace-nowrap">Activity Title</th>
                            <th class="py-3 px-4 whitespace-nowrap w-[40%]">Activity Description</th>
                            <th class="py-3 px-4 whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y divide-gray-200">
                        @forelse($attendances as $index => $item)
                            <tr class=" align-top border hover:bg-gray-50 transition-all duration-200">
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
                                            data-full-text="{!! nl2br(e($item->activity_description)) !!}">
                                            {!! nl2br(e($shortDescription)) !!}
                                        </span>

                                        @if ($isLongDescription)
                                            <a href="#" class="text-[#8180ff] hover:underline see-more-btn"
                                                data-id="{{ $item->id }}">See More</a>
                                            <a href="#" class="text-blue-500 hover:underline see-less-btn hidden"
                                                data-id="{{ $item->id }}">Summary</a>
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
                                            case 'Complete':
                                                $customColor = '#28CB6E';
                                                break;
                                            case 'Not Checked In':
                                                $customColor = '#E7E015';
                                                break;
                                            case 'Not Checked Out':
                                                $customColor = '#f86917';
                                                break;
                                            case 'Absent':
                                                $customColor = '#E61126';
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
                                <td colspan="7" class="text-center py-6 text-gray-500 italic">No attendance records
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

            <script>
                function toggleDropdown(id) {
                    const dropdown = document.getElementById(id);
                    const allDropdowns = document.querySelectorAll('.dropdown');

                    allDropdowns.forEach(d => {
                        if (d.id !== id && !d.classList.contains('hidden')) {
                            d.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                            d.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                            setTimeout(() => d.classList.add('hidden'), 300);
                        }
                    });

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


                // See More / See Less functionality
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
                        const shortText = `{!! nl2br(e(Str::limit($item->activity_description ?? '', 150))) !!}`; // This needs to be correctly injected from Laravel
                        const seeMoreBtn = document.querySelector(`.see-more-btn[data-id="${itemId}"]`);

                        // To correctly get the original short text, you might need to store it in a data attribute
                        // or re-calculate it. For simplicity, we'll use a placeholder or re-limit here.
                        // A more robust solution might pass the original short text as a data attribute on the span.
                        const originalShortText = shortDescriptionSpan.dataset.shortText || `{!! nl2br(e(Str::limit($item->activity_description ?? '', 150))) !!}`;
                        shortDescriptionSpan.innerHTML = originalShortText;


                        this.classList.add('hidden');
                        if (seeMoreBtn) {
                            seeMoreBtn.classList.remove('hidden');
                        }
                    });
                });

                // --- Export Functions ---

                function exportToExcel() {
                    const table = document.getElementById('attendanceTable');
                    const ws = XLSX.utils.table_to_sheet(table, {
                        raw: true
                    }); // Use raw: true to get raw cell values first

                    // Define the actual header names
                    const headerNames = ["No", "Date", "Check-In", "Check-Out", "Activity Title",
                        "Activity Description", "Status"
                    ];
                    XLSX.utils.sheet_add_aoa(ws, [headerNames], {
                        origin: "A1"
                    });


                    // Process each row to correctly populate Activity Title and Activity Description
                    // This ensures the full text is captured, even if "See More" was active.
                    const rows = document.querySelectorAll('#attendanceTable tbody tr');
                    rows.forEach((row, rowIndex) => {
                        const activityTitleCell = row.querySelector('.activity-cell-title span');
                        const activityDescriptionSpan = row.querySelector(
                            '.activity-cell-description .activity-content');
                        const statusSpan = row.querySelector('td:last-child span');

                        const activityTitle = activityTitleCell ? activityTitleCell.innerText.trim() : '';
                        let activityDescription = activityDescriptionSpan ? activityDescriptionSpan.dataset
                            .fullText || activityDescriptionSpan.innerText.trim() : '';

                        // Clean newlines for Excel
                        activityDescription = activityDescription.replace(/(\r\n|\n|\r)/gm, " ")
                            .replace(/\s\s+/g, " ");

                        const status = statusSpan ? statusSpan.innerText.trim() : '';

                        // Update the cell values in the worksheet
                        // Row index starts from 1 for header + current row index
                        // Column indexes: No (0), Date (1), Check-In (2), Check-Out (3), Activity Title (4), Activity Description (5), Status (6)
                        XLSX.utils.sheet_add_aoa(ws, [
                            [
                                row.cells[0].innerText, // No
                                row.cells[1].innerText, // Date
                                row.cells[2].innerText, // Check-In
                                row.cells[3].innerText, // Check-Out
                                activityTitle, // Full Activity Title
                                activityDescription, // Full Activity Description
                                status // Status
                            ]
                        ], {
                            origin: -1
                        }); // -1 appends to the next row
                    });

                    // Remove the original table_to_sheet data and then add the structured data
                    // This is a workaround because table_to_sheet doesn't directly handle the collapsed text properly.
                    // We generate a new sheet with the corrected data.
                    const newWs = XLSX.utils.aoa_to_sheet([headerNames]);
                    rows.forEach((row, rowIndex) => {
                        const activityTitleCell = row.querySelector('.activity-cell-title span');
                        const activityDescriptionSpan = row.querySelector(
                            '.activity-cell-description .activity-content');
                        const statusSpan = row.querySelector('td:last-child span');

                        const activityTitle = activityTitleCell ? activityTitleCell.innerText.trim() : '';
                        let activityDescription = activityDescriptionSpan ? activityDescriptionSpan.dataset
                            .fullText || activityDescriptionSpan.innerText.trim() : '';

                        activityDescription = activityDescription.replace(/(\r\n|\n|\r)/gm, " ")
                            .replace(/\s\s+/g, " ");

                        const status = statusSpan ? statusSpan.innerText.trim() : '';

                        XLSX.utils.sheet_add_aoa(newWs, [
                            [
                                row.cells[0].innerText,
                                row.cells[1].innerText,
                                row.cells[2].innerText,
                                row.cells[3].innerText,
                                activityTitle,
                                activityDescription,
                                status
                            ]
                        ], {
                            origin: -1
                        });
                    });


                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, newWs, "Attendance"); // Use newWs
                    XLSX.writeFile(wb, "my_attendance.xlsx");
                }


                function exportToCSV() {
                    const table = document.getElementById('attendanceTable');
                    let csv = [];

                    const headerNames = ["No", "Date", "Check-In", "Check-Out", "Activity Title",
                        "Activity Description", "Status"
                    ];
                    csv.push(headerNames.map(h => cleanTextForCSV(h)).join(','));

                    table.querySelectorAll('tbody tr').forEach(row => {
                        let rowData = [];
                        rowData.push(cleanTextForCSV(row.cells[0].innerText)); // No
                        rowData.push(cleanTextForCSV(row.cells[1].innerText)); // Date
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
                    downloadLink.download = "my_attendance.csv";
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
                    const doc = new jsPDF('l', 'pt', 'a4');
                    const table = document.getElementById('attendanceTable');

                    // 1. Store initial states and expand all "Activity" content
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

                    // Temporarily adjust table width or container overflow to ensure all content is rendered for html2canvas
                    const tableContainer = document.getElementById('tableContainer');
                    const originalTableContainerStyle = tableContainer.style.cssText;
                    tableContainer.style.overflowX = 'visible';
                    tableContainer.style.width = 'fit-content'; // Crucial for PDF export

                    const originalTableStyle = table.style.cssText;
                    table.style.width = 'fit-content';
                    table.style.tableLayout = 'auto'; // Allow columns to size based on content

                    // Wait for DOM to render changes
                    await new Promise(resolve => setTimeout(resolve, 300));

                    doc.html(table, {
                        callback: function(doc) {
                            doc.save('My_Attendance.pdf');

                            // Revert "Activity" content to original state
                            initialStates.forEach(state => {
                                state.shortDescriptionSpan.innerHTML = state.originalContent;
                                if (state.seeMoreBtn && !state.seeMoreHidden) state.seeMoreBtn.classList
                                    .remove('hidden');
                                if (state.seeLessBtn && !state.seeLessHidden) state.seeLessBtn.classList
                                    .remove('hidden');
                            });

                            // Revert table container styles
                            tableContainer.style.cssText = originalTableContainerStyle;
                            table.style.cssText = originalTableStyle;
                        },
                        x: 10,
                        y: 10,
                        html2canvas: {
                            scale: 0.6, // Adjusted scale
                            logging: true,
                            allowTaint: true,
                            useCORS: true,
                            // Adjusted width/height for html2canvas might not be strictly needed if table.style.width is set to 'fit-content'
                        }
                    });
                }


                async function printTable(tableID) {
                    const table = document.getElementById(tableID);
                    const originalBodyHtml = document.body.innerHTML;

                    // Store initial states and expand all "Activity" content for printing
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

                    // Create a temporary element to hold the table for printing
                    const printWindow = window.open('', '', 'height=600,width=800');
                    printWindow.document.write('<html><head><title>Print</title>');
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
                    printWindow.document.write('.see-more-btn, .see-less-btn { display: none !important; }');
                    printWindow.document.write('td.activity-cell-description { white-space: normal; }');
                    // Add print-specific styles to ensure table fits page, text wraps
                    printWindow.document.write('@media print { body { -webkit-print-color-adjust: exact; } table { table-layout: fixed; width: 100%; } td { word-wrap: break-word; } }');
                    printWindow.document.write('</style>');
                    printWindow.document.write('</head><body>');
                    printWindow.document.write('<h1>My Attendance Records</h1>');
                    printWindow.document.write(table.outerHTML);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();

                    printWindow.onload = function() {
                        printWindow.focus();
                        printWindow.print();
                        printWindow.close();

                        // Revert "Activity" content to original state after printing
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