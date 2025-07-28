<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Proyek Absensi Magang</title>
</head>
<body>

  <h1>Proyek Absensi Magang</h1>
  <p>
    Aplikasi ini adalah sistem absensi online yang dibangun menggunakan framework <strong>Laravel</strong>.
    Sistem ini ditujukan untuk mengelola kehadiran peserta magang, dengan fitur yang mencakup multi-level otentikasi
    (<em>Superadmin, Admin, User</em>), manajemen data, dan pelaporan.
  </p>

  <hr>

  <h2>📋 Persyaratan Sistem</h2>
  <p>Pastikan lingkungan server atau komputer lokal Anda telah memenuhi persyaratan berikut sebelum melakukan instalasi:</p>
  <ul>
    <li><strong>PHP</strong>: Versi 8.2 atau lebih tinggi</li>
    <li><strong>Composer</strong>: Versi 2.0 atau lebih tinggi</li>
    <li><strong>Database</strong>: MySQL atau MariaDB</li>
    <li><strong>Web Server</strong>: Apache, Nginx, atau server lain yang kompatibel dengan Laravel</li>
  </ul>

  <hr>

  <h2>🚀 Panduan Instalasi</h2>
  <ol>
    <li>
      <strong>Unduh Proyek (Clone Repository)</strong>
      <pre><code>git clone https://github.com/Lynna-wh/absensi-magang.git
cd absensi-magang</code></pre>
    </li>
    <li>
      <strong>Instal Dependensi PHP</strong>
      <pre><code>composer install</code></pre>
    </li>
    <li>
      <strong>Konfigurasi Lingkungan (.env)</strong><br>
      Salin berkas konfigurasi:
      <pre><code>cp .env.example .env</code></pre>

      <p><strong>a. Konfigurasi Aplikasi & Database</strong></p>
      <pre><code>APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_anda</code></pre>

      <p><strong>b. Konfigurasi Email (untuk Fitur Lupa Password)</strong></p>
      <p>Contoh konfigurasi SMTP menggunakan Gmail:</p>
      <pre><code>MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email.anda@gmail.com
MAIL_PASSWORD=sandi_16_karakter_yang_disalin
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email.anda@gmail.com
MAIL_FROM_NAME=${APP_NAME}</code></pre>

      <p><strong>Cara mendapatkan Sandi Aplikasi Gmail:</strong></p>
      <ol>
        <li>Buka Akun Google Anda dan pilih <em>Kelola Akun Google Anda</em></li>
        <li>Pilih tab <strong>Keamanan</strong></li>
        <li>Aktifkan <strong>Verifikasi 2 Langkah</strong></li>
        <li>Klik <strong>Sandi Aplikasi</strong></li>
        <li>Beri nama aplikasi (misalnya: Absensi Magang PLN), lalu klik <strong>Buat</strong></li>
        <li>Salin 16 karakter sandi yang muncul untuk digunakan di <code>.env</code></li>
      </ol>
    </li>
    <li>
      <strong>Inisialisasi Aplikasi</strong>
      <p>Jalankan perintah-perintah berikut:</p>
      <pre><code>php artisan key:generate
php artisan migrate --seed</code></pre>
    </li>
    <li>
      <strong>Jalankan Server Pengembangan</strong>
      <pre><code>php artisan serve</code></pre>
      <p>Akses aplikasi melalui browser di: <a href="http://127.0.0.1:8000" target="_blank">http://127.0.0.1:8000</a></p>
    </li>
  </ol>

  <hr>

  <h2>🧑‍💻 Akun Demo</h2>
  <p>Setelah menjalankan <code>migrate --seed</code>, Anda bisa login menggunakan akun-akun berikut:</p>

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
