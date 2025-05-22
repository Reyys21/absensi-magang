<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Absensi Magang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/2d9ecd9e37.js" crossorigin="anonymous"></script>
   @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif; /* Menggunakan Font Inter yang sudah Anda load */
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">
    {{-- Konten utama dari view yang extend layout ini --}}
    @yield('content')

    @yield('Script') {{-- Pastikan @yield('Script') ini ada di bagian bawah body --}}

    
</body>

</html>