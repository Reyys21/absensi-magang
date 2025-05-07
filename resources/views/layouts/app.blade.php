<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Absensi Magang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tambahkan ini untuk Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- font awesome -->
    <script src="https://kit.fontawesome.com/2d9ecd9e37.js" crossorigin="anonymous">
    </script>

    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
    }
    </style>
</head>



<body class="bg-gray-100 text-gray-900">

    @yield('content')
    @yield('Script')

</body>

</html>