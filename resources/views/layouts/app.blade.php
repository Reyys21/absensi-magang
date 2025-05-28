<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Absensi Magang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script> {{-- Menggunakan Tailwind dari CDN --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    {{-- Cropper.js CSS harus di <head> --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">

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
    @stack('styles') {{-- Tempatkan ini untuk CSS tambahan dari view lain --}}
</head>

<body class="bg-gray-100 text-gray-900">

    {{-- INI ADALAH DIV UTAMA YANG MENGATUR TATA LETAK APLIKASI --}}
    <div class="flex h-screen"> {{-- Tambahkan kelas flex dan h-screen untuk layout utama --}}
        
        {{-- Sidebar (akan di-include di sini) --}}
        @include('layouts.sidebar') 

        {{-- Main Content Area --}}
        <main id="main-content" class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out">
            {{-- Konten utama dari view turunan akan di-yield di sini --}}
            @yield('content')
        </main>
    </div>

    {{-- Font Awesome JS (lebih baik di akhir body) --}}
    <script src="https://kit.fontawesome.com/2d9ecd9e37.js" crossorigin="anonymous"></script>
    {{-- Cropper.js JavaScript (lebih baik di akhir body, setelah elemen-elemennya) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    {{-- Pastikan @stack('scripts') ini ada di bagian bawah body, setelah semua JS lainnya --}}
    @stack('scripts') 
</body>

</html>