<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Absensi Magang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- Keyframes Animasi (Sangat Sederhana) --- */
        @keyframes fadeInSimple { from { opacity: 0; } to { opacity: 1; } }
        @keyframes drawLineMinimal { from { width: 0; } to { width: 40px; } }

        /* --- Gaya Dasar dengan Palet Tema Terang --- */
        :root {
            --bg-light-main: #F5F5F5;
            --bg-light-card: #FFFFFF;
            --text-dark-primary: #212529;
            --text-dark-secondary: #5C5C5C;
            --text-light-gray: #E8E8E8; /* Tambahkan ini jika belum ada atau sesuaikan namanya */
            
            --accent-teal: #1E8A92;
            --accent-yellow: #FDEE00;
            --accent-red: #F44336;

            --border-color-lighttheme: #DCDCDC;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            color: var(--text-dark-primary);
            background-color: var(--bg-light-main);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            width: 90%;
            max-width: 1080px;
            margin: 0 auto;
            padding: 20px 0;
        }

        /* --- Header --- */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 5%;
            background-color: var(--bg-light-card);
            border-bottom: 1px solid var(--border-color-lighttheme);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-container { display: flex; align-items: center; text-decoration: none; }
        .header-logo {
            height: 35px; width: auto;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        .logo-container:hover .header-logo { transform: scale(1.05); }
        .logo-text {
            font-size: 1.3em; font-weight: 600;
            color: var(--text-dark-primary);
        }

        nav ul { list-style: none; margin: 0; padding: 0; display: flex; align-items: center; }
        nav li { margin-left: 25px; }
        nav a {
            text-decoration: none; color: var(--text-dark-secondary);
            font-size: 0.9em; font-weight: 500;
            position: relative; padding: 5px 0;
            transition: color 0.25s ease;
        }
        nav a::before {
            content: ''; position: absolute;
            width: 0; height: 1.5px; bottom: -2px; left: 0;
            background-color: var(--accent-teal);
            transition: width 0.25s ease-out;
        }
        nav a:hover { color: var(--accent-teal); }
        nav a:hover::before { width: 100%; }

        .nav-login-btn {
            background-color: var(--accent-teal);
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.25s ease, transform 0.2s ease;
        }
        .nav-login-btn::before { display: none; }
        .nav-login-btn:hover {
            background-color: #186A70;
            transform: translateY(-1px);
        }

        /* --- Hero Section --- */
        .hero {
            text-align: center; padding: 80px 20px 100px;
            background-color: var(--bg-light-card);
            color: var(--text-dark-primary);
            overflow: hidden; position: relative;
            border-bottom: 1px solid var(--border-color-lighttheme);
        }
        .hero .container { position: relative; z-index: 2; animation: fadeInSimple 0.7s ease-out; }
        .hero h1 {
            font-size: 2.8em; font-weight: 700;
            margin-bottom: 20px; line-height: 1.2;
        }
        .hero h1 .highlight { color: var(--accent-teal); }
        .hero p {
            font-size: 1.1em; color: var(--text-dark-secondary);
            margin-bottom: 35px; max-width: 650px;
            margin-left: auto; margin-right: auto;
        }
        .hero .btn-get-started {
            padding: 14px 35px; text-decoration: none;
            border-radius: 8px; font-size: 1em; font-weight: 600;
            transition: background-color 0.25s ease, transform 0.2s ease, box-shadow 0.25s ease;
            border: none; letter-spacing: 0.5px;
            background-color: var(--accent-yellow);
            color: var(--text-dark-primary);
            box-shadow: 0 3px 8px rgba(253, 238, 0, 0.35);
        }
        .hero .btn-get-started:hover {
            background-color: #E9D700;
            transform: scale(1.03);
            box-shadow: 0 5px 12px rgba(253, 238, 0, 0.45);
        }

        /* --- Bagian Info (Di Bawah Hero) --- */
        .info-section-wrapper {
            background-color: var(--bg-light-main);
            padding: 60px 0 40px;
        }
        .info-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 25px;
        }
        .info-box {
            background-color: var(--bg-light-card);
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid var(--border-color-lighttheme);
            box-shadow: 0 3px 6px rgba(0,0,0,0.05);
            transition: border-color 0.3s ease, transform 0.3s ease;
            animation: fadeInSimple 0.6s ease-out;
        }
        .info-box:hover {
            transform: translateY(-4px);
            border-color: var(--accent-teal);
        }
        .info-box .info-icon {
            font-size: 2.2em; margin-bottom: 15px;
            color: var(--accent-teal);
            line-height: 1;
        }
        .info-box h3 {
            font-size: 1.3em; font-weight: 600;
            margin-top: 0; margin-bottom: 10px;
            color: var(--accent-teal);
        }
        .info-box p {
            font-size: 0.88em; color: var(--text-dark-secondary);
            line-height: 1.55;
        }

        /* --- Bagian "Tentang Sistem Kami" & Fitur --- */
        .content-section {
            padding: 70px 5%; text-align: center;
            animation: fadeInSimple 0.7s ease-out;
        }
        .content-section.lighter-bg { background-color: var(--bg-light-main); }
        .content-section.card-bg-section {
            background-color: var(--bg-light-card);
            border-top: 1px solid var(--border-color-lighttheme);
            border-bottom: 1px solid var(--border-color-lighttheme);
        }

        .content-section h2 {
            font-size: 2em; font-weight: 700;
            margin-bottom: 15px; color: var(--text-dark-primary);
            position: relative; display: inline-block;
        }
        .content-section h2::after {
            content: ''; display: block; width: 0; height: 2px;
            margin: 10px auto 0; border-radius: 2px;
        }
        .about-system h2::after { background-color: var(--accent-teal); }
        .features h2::after { background-color: var(--accent-yellow); }

        .content-section > p {
            font-size: 1em; color: var(--text-dark-secondary);
            line-height: 1.65; max-width: 650px;
            margin: 0 auto 35px auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }
        .feature-item {
            background-color: var(--bg-light-main);
            padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.06);
            transition: border-color 0.3s ease, transform 0.3s ease;
            border: 1px solid var(--border-color-lighttheme);
            text-align: left;
            animation: fadeInSimple 0.6s ease-out;
        }
        .feature-item:hover {
            transform: translateY(-3px);
            border-color: var(--accent-yellow);
        }
        .feature-item .icon {
            width: 45px; height: 45px;
            background-color: var(--accent-yellow);
            border-radius: 10px;
            margin-bottom: 15px; display: flex;
            justify-content: center; align-items: center;
        }
        .feature-item .icon img {
            max-width: 45%; max-height: 45%;
            filter: brightness(0) invert(15%) sepia(20%) saturate(1000%) hue-rotate(320deg);
        }
        .feature-item h4 {
            font-size: 1.2em; font-weight: 600;
            margin-bottom: 8px; color: var(--accent-teal);
        }
        .feature-item p.desc {
            font-size: 0.85em; color: var(--text-dark-secondary);
            line-height: 1.5;
        }

        /* --- Footer --- */
        footer {
            background-color: var(--text-dark-primary); /* Footer gelap */
            color: var(--text-light-gray); /* KOREKSI DI SINI: Teks terang di footer */
            padding: 40px 5% 25px; text-align: center;
            margin-top: 40px;
            border-top: 2px solid var(--accent-yellow);
        }
        .footer-content {
            display: flex; flex-direction: column;
            align-items: center; gap: 12px;
        }
        .footer-content p { margin: 0; font-size: 0.8em; }
        /* Hapus .footer-links jika tidak digunakan */
        /* .footer-links a {
            text-decoration: none;
            color: var(--text-light-gray);
            margin: 0 10px;
            font-size: 0.8em;
            font-weight: 500;
            transition: color 0.25s ease;
        }
        .footer-links a:hover {
            color: var(--accent-yellow);
        } */

        /* --- JavaScript untuk Animasi Garis Judul (jika masih dipertahankan) --- */
        .animate-on-scroll.draw-line.is-visible::after {
            animation: drawLineMinimal 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }

        /* --- Penyesuaian Responsif (Disederhanakan) --- */
        @media (max-width: 768px) {
            nav ul { display: none; }
            header { flex-direction: row; justify-content: space-between; }
            .hero h1 { font-size: 2.2em; }
            .content-section h2 { font-size: 1.8em; }
            .info-boxes, .features-grid { grid-template-columns: 1fr; }
        }
         @media (max-width: 480px) {
            body { font-size: 14px; }
            .hero h1 { font-size: 1.8em; }
            .content-section h2 { font-size: 1.6em; }
            .info-box h3, .feature-item h4 { font-size: 1.15em; }
            .logo-text { font-size: 1.2em; }
        }
    </style>
</head>
<body>

    <header>
        <a href="{{ route('home') }}" class="logo-container">
            <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="header-logo">
            <span class="logo-text">Absensi Magang</span>
        </a>
        <nav>
            <ul>
                <li><a href="{{ route('home') }}">Beranda</a></li>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#features">Fitur</a></li>
                <li><a href="{{ route('login') }}" class="nav-login-btn">Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Selamat Datang Di Web <span class="highlight">Absensi Magang</span></h1>
                <p>Lacak absensi magang Anda dengan mudah, tetap terinformasi detail penting secara instan.</p>
                <div class="buttons">
                    <a href="{{ route('login') }}" class="btn btn-get-started">Mulai Sekarang</a>
                </div>
            </div>
        </section>

        <section class="info-section-wrapper">
            <div class="container">
                <div class="info-boxes">
                    <div class="info-box">
                        <div class="info-icon">🚀</div>
                        <h3>Absensi Cepat</h3>
                        <p>Catat absensi Anda dalam hitungan detik. Efisien dan tanpa repot.</p>
                    </div>
                    <div class="info-box">
                        <div class="info-icon">🔔</div>
                        <h3>Update Real-time</h3>
                        <p>Terima notifikasi instan mengenai komitmen dan pembaruan magang.</p>
                    </div>
                    <div class="info-box">
                        <div class="info-icon">🖥️</div>
                        <h3>Antarmuka Jelas</h3>
                        <p>Desain bersih dan ramah pengguna, membuat pengalaman Anda efisien.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="content-section lighter-bg">
            <div class="container">
                <h2 class="animate-on-scroll draw-line">Tentang Sistem Kami</h2>
                <p>Platform kami menyediakan antarmuka yang mudah digunakan untuk melacak absensi magang Anda secara lancar, memastikan semua tercatat dengan baik dan transparan.</p>
            </div>
        </section>

        <section id="features" class="content-section card-bg-section">
            <div class="container">
                <h2 class="animate-on-scroll draw-line">Fitur Utama</h2>
                <p>Jelajahi fitur inti yang dirancang untuk memaksimalkan dan menyederhanakan pengalaman magang Anda.</p>
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="icon">
                            <img src="https://icongr.am/feather/calendar.svg?size=32&color=212529" alt="Ikon Kalender">
                        </div>
                        <h4>Pelacakan Kehadiran</h4>
                        <p class="desc">Monitor dan catat kehadiran Anda secara akurat, mudah diakses kapan pun.</p>
                    </div>
                    <div class="feature-item">
                        <div class="icon">
                            <img src="https://icongr.am/feather/globe.svg?size=32&color=212529" alt="Ikon Globe">
                        </div>
                        <h4>Akses Mudah</h4>
                        <p class="desc">Akses data absensi Anda dari mana saja melalui platform online kami.</p>
                    </div>
                    <div class="feature-item">
                        <div class="icon">
                            <img src="https://icongr.am/feather/bell.svg?size=32&color=212529" alt="Ikon Lonceng">
                        </div>
                        <h4>Notifikasi Penting</h4>
                        <p class="desc">Dapatkan pemberitahuan terkait jadwal dan status kehadiran Anda.</p>
                    </div>
                    <div class="feature-item">
                        <div class="icon">
                            <img src="https://icongr.am/feather/bar-chart-2.svg?size=32&color=212529" alt="Ikon Grafik">
                        </div>
                        <h4>Ringkasan Analitik</h4>
                        <p class="desc">Lihat ringkasan data kehadiran Anda secara periodik dengan jelas.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-content" >
            <p>&copy; 2025 Absensi Magang. Hak Cipta Dilindungi Oleh Rizky mwhehe.</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const lineObserverOptions = { root: null, rootMargin: "0px", threshold: 0.5 };
            const lineObserver = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, lineObserverOptions);

            document.querySelectorAll('.content-section h2.animate-on-scroll.draw-line').forEach(h2 => {
                lineObserver.observe(h2);
            });

            document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    if(targetElement){
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        });
    </script>

</body>
</html>