<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Proyek Absensi Magang</title>
</head>
<body>

<h1>Proyek Absensi Magang</h1>
<p>Aplikasi ini adalah sistem absensi online yang dibangun menggunakan framework <strong>Laravel</strong>. Sistem ini ditujukan untuk mengelola kehadiran peserta magang, dengan fitur yang mencakup multi-level otentikasi (<em>Superadmin, Admin, User</em>), manajemen data, dan pelaporan.</p>

<h2>📋 Persyaratan Sistem</h2>
<p>Pastikan lingkungan server atau komputer lokal Anda telah memenuhi persyaratan berikut sebelum melakukan instalasi:</p>
<ul>
  <li><strong>PHP</strong>: Versi 8.2 atau lebih tinggi</li>
  <li><strong>Composer</strong>: Versi 2.0 atau lebih tinggi</li>
  <li><strong>Database</strong>: MySQL atau MariaDB</li>
  <li><strong>Web Server</strong>: Apache, Nginx, atau lainnya yang kompatibel dengan Laravel</li>
</ul>

<h2>🚀 Panduan Instalasi</h2>
<ol>
  <li><strong>Unduh Proyek (Clone Repository)</strong><br>
    Gunakan git untuk mengunduh repositori ke direktori lokal:
    <pre><code>git clone https://github.com/Lynna-wh/absensi-magang.git
cd absensi-magang</code></pre>
  </li>

  <li><strong>Instal Dependensi PHP</strong><br>
    Jalankan perintah berikut:
    <pre><code>composer install</code></pre>
  </li>

  <li><strong>Konfigurasi Lingkungan (.env)</strong><br>
    Salin dan sesuaikan konfigurasi:
    <pre><code>cp .env.example .env</code></pre>
    <p><strong>a. Konfigurasi Aplikasi & Database</strong></p>
    <pre><code>APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_anda</code></pre>

    <p><strong>b. Konfigurasi Email (untuk Lupa Password)</strong></p>
    <p>Gunakan Gmail dan sandi aplikasi:</p>
    <pre><code>MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME="email.anda@gmail.com"
MAIL_PASSWORD="sandi_16_karakter_yang_disalin"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="email.anda@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"</code></pre>
  </li>

  <li><strong>Inisialisasi Aplikasi</strong><br>
    <ul>
      <li>Generate Kunci Aplikasi:
        <pre><code>php artisan key:generate</code></pre>
      </li>
      <li>Jalankan Migrasi dan Seeder:
        <pre><code>php artisan migrate --seed</code></pre>
      </li>
    </ul>
  </li>

  <li><strong>Jalankan Server Pengembangan</strong><br>
    <pre><code>php artisan serve</code></pre>
    <p>Aplikasi sekarang dapat diakses melalui browser di: <a href="http://127.0.0.1:8000">http://127.0.0.1:8000</a></p>
  </li>
</ol>

<h2>🧑‍💻 Akun Demo</h2>
<p>Setelah menjalankan <code>migrate --seed</code>, Anda bisa login dengan akun-akun berikut:</p>
<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr>
      <th>Role</th>
      <th>Email</th>
      <th>Password</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Super Admin</td>
      <td>superadmin@example.com</td>
      <td>password</td>
    </tr>
    <tr>
      <td>Admin</td>
      <td>admin@example.com</td>
      <td>password</td>
    </tr>
    <tr>
      <td>User</td>
      <td>user@example.com</td>
      <td>password</td>
    </tr>
  </tbody>
</table>

</body>
</html>
