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
                        class="bg-[#A74FDE] text-white px-4 py-2 rounded hover:bg-[#c98ef2] text-sm border-2 border-black">
                        Export <i class="fa-solid fa-chevron-down ml-2"></i>
                    </button>
                    <div id="exportDropdown"
                        class="hidden absolute mt-2 bg-[#A74FDE] text-white shadow-lg z-10 w-48 text-left px-4 py-2 rounded  text-sm border-2 border-black">
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
                        class="hidden absolute mt-2 bg-[#3E25FF] text-white px-4 py-2 rounded text-sm border-2 border-black rounded p-4 w-64 z-10 space-y-3">

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

                                        <span class="activity-content activity-short-description-{{ $item->id }}">
                                            {!! nl2br(e($shortDescription)) !!}
                                        </span>

                                        @if ($isLongDescription)
                                            <a href="#" class="text-[#8180ff] hover:underline see-more-btn"
                                                data-id="{{ $item->id }}" data-full-text="{!! nl2br(e($item->activity_description)) !!}">See
                                                More</a>
                                            <a href="#" class="text-blue-500 hover:underline see-less-btn hidden"
                                                data-id="{{ $item->id }}"
                                                data-short-text="{!! nl2br(e($shortDescription)) !!}">Summary</a>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    @if ($item->status === 'on_time')
                                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">On
                                            Time</span>
                                    @elseif($item->status === 'late')
                                        <span
                                            class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">Late</span>
                                    @else
                                        <span
                                            class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">Absent</span>
                                    @endif
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
                    dropdown.classList.toggle('hidden');
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    const dropdowns = ['filterDropdown', 'exportDropdown', 'attendanceDropdown', 'approvalDropdown'];
                    dropdowns.forEach(id => {
                        const dropdown = document.getElementById(id);
                        if (dropdown) {
                            const button = dropdown.previousElementSibling;
                            if (button && !button.contains(e.target) && !dropdown.contains(e.target)) {
                                dropdown.classList.add('hidden');
                            }
                        }
                    });
                });

                document.querySelectorAll('.see-more-btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const itemId = this.dataset.id;
                        const fullText = this.dataset.fullText;
                        const shortDescriptionSpan = document.querySelector(
                            `.activity-short-description-${itemId}`);
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
                        const shortText = this.dataset.shortText;
                        const shortDescriptionSpan = document.querySelector(
                            `.activity-short-description-${itemId}`);
                        const seeMoreBtn = document.querySelector(`.see-more-btn[data-id="${itemId}"]`);

                        shortDescriptionSpan.innerHTML = shortText;
                        this.classList.add('hidden');
                        if (seeMoreBtn) {
                            seeMoreBtn.classList.remove('hidden');
                        }
                    });
                });

                // --- Export Functions ---

                function exportToExcel() {
                    const table = document.getElementById('attendanceTable');
                    const ws = XLSX.utils.table_to_sheet(table);

                    // Reconstruct the header row for Excel
                    const headerNames = ["No", "Date", "Check-In", "Check-Out", "Activity Title", "Activity Description", "Status"];
                    XLSX.utils.sheet_add_aoa(ws, [headerNames], {
                        origin: "A1"
                    });


                    // Process each row to populate Activity Title and Activity Description
                    const rows = document.querySelectorAll('#attendanceTable tbody tr');
                    rows.forEach((row, rowIndex) => {
                        const activityTitleCell = row.querySelector('.activity-cell-title span');
                        const activityDescriptionCell = row.querySelector('.activity-cell-description .activity-content');

                        const activityTitle = activityTitleCell ? activityTitleCell.innerText.trim() : '';
                        let activityDescription = activityDescriptionCell ? activityDescriptionCell.getAttribute(
                            'data-full-text') || activityDescriptionCell.innerText.trim() : '';
                        activityDescription = activityDescription.replace(/(\r\n|\n|\r)/gm,
                            " "); // Remove newlines for single-cell display

                        // Update the cell values in the worksheet
                        // Row index starts from 1 for header + current row index
                        const titleCellAddress = XLSX.utils.encode_cell({
                            r: rowIndex + 1,
                            c: 4
                        }); // Column F (0-indexed 5 -> but we put it in Activity Title)
                        const descCellAddress = XLSX.utils.encode_cell({
                            r: rowIndex + 1,
                            c: 5
                        }); // Column G (0-indexed 6 -> but we put it in Activity Description)

                        if (ws[titleCellAddress]) {
                            ws[titleCellAddress].v = activityTitle;
                        } else {
                            ws[titleCellAddress] = {
                                v: activityTitle,
                                t: 's'
                            };
                        }

                        if (ws[descCellAddress]) {
                            ws[descCellAddress].v = activityDescription;
                        } else {
                            ws[descCellAddress] = {
                                v: activityDescription,
                                t: 's'
                            };
                        }
                    });

                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Attendance");
                    XLSX.writeFile(wb, "my_attendance.xlsx");
                }


                function exportToCSV() {
                    const table = document.getElementById('attendanceTable');
                    let csv = [];

                    // Get headers
                    const headerNames = ["No", "Date", "Check-In", "Check-Out", "Activity Title", "Activity Description", "Status"];
                    csv.push(headerNames.map(h => cleanTextForCSV(h)).join(','));

                    // Get data from tbody
                    table.querySelectorAll('tbody tr').forEach(row => {
                        let rowData = [];
                        rowData.push(cleanTextForCSV(row.cells[0].innerText)); // No
                        rowData.push(cleanTextForCSV(row.cells[1].innerText)); // Date
                        rowData.push(cleanTextForCSV(row.cells[2].innerText)); // Check-In
                        rowData.push(cleanTextForCSV(row.cells[3].innerText)); // Check-Out

                        const activityTitleCell = row.querySelector('.activity-cell-title span');
                        const activityDescriptionCell = row.querySelector('.activity-cell-description .activity-content');

                        const activityTitle = activityTitleCell ? activityTitleCell.innerText.trim() : '';
                        let activityDescription = activityDescriptionCell ? activityDescriptionCell.getAttribute(
                            'data-full-text') || activityDescriptionCell.innerText.trim() : '';
                        activityDescription = activityDescription.replace(/(\r\n|\n|\r)/gm,
                            " "); // Remove newlines for single-cell display

                        rowData.push(cleanTextForCSV(activityTitle));
                        rowData.push(cleanTextForCSV(activityDescription));

                        rowData.push(cleanTextForCSV(row.cells[row.cells.length - 1].innerText)); // Status (last column)

                        csv.push(rowData.join(','));
                    });

                    // Download CSV file
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
                    const doc = new jsPDF('l', 'pt', 'a4'); // 'l' for landscape mode
                    const table = document.getElementById('attendanceTable');

                    // 1. Temporarily expand all "Activity" content
                    const initialStates = [];
                    document.querySelectorAll('.see-more-btn').forEach(button => {
                        const itemId = button.dataset.id;
                        const fullText = button.dataset.fullText;
                        const shortDescriptionSpan = document.querySelector(
                            `.activity-short-description-${itemId}`);
                        const seeLessBtn = document.querySelector(`.see-less-btn[data-id="${itemId}"]`);

                        initialStates.push({
                            itemId: itemId,
                            shortText: shortDescriptionSpan.innerHTML,
                            seeMoreHidden: button.classList.contains('hidden'),
                            seeLessHidden: seeLessBtn ? seeLessBtn.classList.contains('hidden') : true
                        });

                        if (shortDescriptionSpan) {
                            shortDescriptionSpan.innerHTML = fullText;
                        }
                        button.classList.add('hidden');
                        if (seeLessBtn) {
                            seeLessBtn.classList.add('hidden');
                        }
                    });

                    // Temporarily adjust table width or container overflow to ensure all content is rendered for html2canvas
                    const tableContainer = document.getElementById('tableContainer');
                    const originalTableContainerStyle = tableContainer.style.cssText;
                    tableContainer.style.overflowX = 'visible'; // Ensure content is not clipped for rendering

                    const originalTableWidth = table.style.width;
                    table.style.width = 'fit-content'; // Allow table to expand to fit content, not container width


                    // Wait for DOM to render changes
                    await new Promise(resolve => setTimeout(resolve, 300)); // Increased delay for rendering

                    doc.html(table, {
                        callback: function(doc) {
                            doc.save('My_Attendance.pdf');

                            // Revert "Activity" content to original state
                            initialStates.forEach(state => {
                                const shortDescriptionSpan = document.querySelector(
                                    `.activity-short-description-${state.itemId}`);
                                const seeMoreBtn = document.querySelector(
                                    `.see-more-btn[data-id="${state.itemId}"]`);
                                const seeLessBtn = document.querySelector(
                                    `.see-less-btn[data-id="${state.itemId}"]`);

                                if (shortDescriptionSpan) {
                                    shortDescriptionSpan.innerHTML = state.shortText;
                                }
                                if (seeMoreBtn) {
                                    if (!state.seeMoreHidden) {
                                        seeMoreBtn.classList.remove('hidden');
                                    } else {
                                        seeMoreBtn.classList.add('hidden');
                                    }
                                }
                                if (seeLessBtn) {
                                    if (!state.seeLessHidden) {
                                        seeLessBtn.classList.remove('hidden');
                                    } else {
                                        seeLessBtn.classList.add('hidden');
                                    }
                                }
                            });

                            // Revert table container styles
                            tableContainer.style.cssText = originalTableContainerStyle;
                            table.style.width = originalTableWidth;
                        },
                        x: 10,
                        y: 10,
                        html2canvas: {
                            scale: 0.6, // Adjusted scale: make it smaller to fit more content. You might need to fine-tune this.
                            logging: true, // Enable logging for debugging html2canvas issues
                            allowTaint: true, // Allow images from other origins (if any)
                            useCORS: true, // Enable CORS for images (if any)
                            width: table.offsetWidth, // Explicitly set width to capture full table
                            height: table.offsetHeight // Explicitly set height
                        }
                    });
                }


                async function printTable(tableID) {
                    const table = document.getElementById(tableID);
                    const originalBodyHtml = document.body.innerHTML;

                    // 1. Temporarily expand all "Activity" content for printing
                    const initialStates = [];
                    document.querySelectorAll('.see-more-btn').forEach(button => {
                        const itemId = button.dataset.id;
                        const fullText = button.dataset.fullText;
                        const shortDescriptionSpan = document.querySelector(
                            `.activity-short-description-${itemId}`);
                        const seeLessBtn = document.querySelector(`.see-less-btn[data-id="${itemId}"]`);

                        initialStates.push({
                            itemId: itemId,
                            shortText: shortDescriptionSpan.innerHTML,
                            seeMoreHidden: button.classList.contains('hidden'),
                            seeLessHidden: seeLessBtn ? seeLessBtn.classList.contains('hidden') : true
                        });

                        if (shortDescriptionSpan) {
                            shortDescriptionSpan.innerHTML = fullText;
                        }
                        button.classList.add('hidden');
                        if (seeLessBtn) {
                            seeLessBtn.classList.add('hidden');
                        }
                    });

                    // Create a temporary element to hold the table for printing
                    const printWindow = window.open('', '', 'height=600,width=800');
                    printWindow.document.write('<html><head><title>Print</title>');
                    // IMPORTANT: Update this path to your actual compiled CSS
                    printWindow.document.write('<link href="{{ asset('build/assets/app.css') }}" rel="stylesheet">');
                    printWindow.document.write('<style>');
                    printWindow.document.write('body { font-family: sans-serif; margin: 20px; }');
                    printWindow.document.write(
                        'table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }'
                    ); // Smaller font-size for print
                    printWindow.document.write(
                        'th, td { border: 1px solid #ccc; padding: 5px; text-align: left; vertical-align: top;}'
                    ); // Smaller padding, top align
                    printWindow.document.write('thead { background-color: #f2f2f2; }');
                    printWindow.document.write('.see-more-btn, .see-less-btn { display: none !important; }');
                    printWindow.document.write('td.activity-cell-description { white-space: normal; }'); // Ensure text wraps
                    printWindow.document.write('</style>');
                    printWindow.document.write('</head><body>');
                    printWindow.document.write('<h1>My Attendance Records</h1>');
                    printWindow.document.write(table.outerHTML); // Use outerHTML directly for the table
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.focus();
                    printWindow.print();
                    printWindow.close();

                    // 3. Revert "Activity" content to original state after printing
                    initialStates.forEach(state => {
                        const shortDescriptionSpan = document.querySelector(
                            `.activity-short-description-${state.itemId}`);
                        const seeMoreBtn = document.querySelector(
                            `.see-more-btn[data-id="${state.itemId}"]`);
                        const seeLessBtn = document.querySelector(
                            `.see-less-btn[data-id="${state.itemId}"]`);

                        if (shortDescriptionSpan) {
                            shortDescriptionSpan.innerHTML = state.shortText;
                        }
                        if (seeMoreBtn) {
                            if (!state.seeMoreHidden) {
                                seeMoreBtn.classList.remove('hidden');
                            } else {
                                seeMoreBtn.classList.add('hidden');
                            }
                        }
                        if (seeLessBtn) {
                            if (!state.seeLessHidden) {
                                seeLessBtn.classList.remove('hidden');
                            } else {
                                seeLessBtn.classList.add('hidden');
                            }
                        }
                    });
                }
            </script>
        @endsection
