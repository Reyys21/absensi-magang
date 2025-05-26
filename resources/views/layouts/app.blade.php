<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Absensi Magang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script> {{-- Menggunakan Tailwind dari CDN --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/2d9ecd9e37.js" crossorigin="anonymous"></script>

    {{-- Hapus @vite ini. Semua CSS/JS akan dihandle langsung atau via CDN/include. --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Style scrollbar untuk body */
        body::-webkit-scrollbar {
            width: 8px;
        }

        body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        body::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
            border: 2px solid #f1f1f1;
        }

        body::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">

    {{-- INI ADALAH DIV UTAMA YANG MENGATUR TATA LETAK FLEX UNTUK SELURUH APLIKASI --}}

    {{-- Konten utama dari view turunan akan di-yield di sini --}}
    @yield('content')

    {{-- Pastikan @yield('Script') ini ada di bagian bawah body, setelah semua konten --}}
    @yield('Script')
</body>

</html>
