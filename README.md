<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Dokumentasi Proyek Absensi Magang</title>
  </head>
  <body>
    <h1>Proyek Absensi Magang</h1>
    <p>
      Aplikasi ini adalah sistem absensi online yang dibangun menggunakan framework Laravel. Sistem ini ditujukan untuk
      mengelola kehadiran peserta magang, dengan fitur yang mencakup multi-level otentikasi (Superadmin, Admin, User),
      manajemen data, dan pelaporan.
    </p>
    <h2>📋 Persyaratan Sistem</h2>
    <p>Pastikan lingkungan server atau komputer lokal Anda telah memenuhi persyaratan berikut sebelum melakukan instalasi:</p>
    <ul>
      <li>PHP: Versi 8.2 atau lebih tinggi.</li>
      <li>Composer: Versi 2.0 atau lebih tinggi.</li>
      <li>Database: MySQL atau MariaDB.</li>
      <li>Web Server: Apache, Nginx, atau server lain yang kompatibel dengan Laravel.</li>
    </ul>
    <h2>🚀 Panduan Instalasi</h2>
    <p>Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek ini di lingkungan lokal.</p>
    <h3>1. Unduh Proyek (Clone Repository)</h3>
    <p>Gunakan git untuk mengunduh repositori proyek ke direktori lokal Anda.</p>
    <pre><code>git clone https://github.com/Lynna-wh/absensi-magang.git
cd absensi-magang
</code></pre>
    <h3>2. Instal Dependensi PHP</h3>
    <p>Proyek ini menggunakan Composer untuk mengelola dependensi PHP. Jalankan perintah berikut di terminal Anda:</p>
    <pre><code>composer install</code></pre>
    <h3>3. Konfigurasi Lingkungan (.env)</h3>
    <p>Salin berkas konfigurasi contoh .env.example menjadi berkas baru bernama .env.</p>
    <pre><code>cp .env.example .env</code></pre>
    <p>Setelah itu, buka berkas .env dan sesuaikan variabel-variabel di bawah ini:</p>
    <h4>a. Konfigurasi Aplikasi &amp; Database</h4>
    <p>Pastikan Anda sudah membuat sebuah database kosong sebelum melanjutkan.</p>
    <pre><code># URL aplikasi Anda, untuk lokal biasanya seperti ini
APP_URL=http://127.0.0.1:8000

# Konfigurasi Koneksi Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_anda
</code></pre>
    <h4>b. Konfigurasi Email (untuk Fitur Lupa Password)</h4>
    <p>
      Fitur lupa password memerlukan koneksi ke server SMTP. Contoh ini menggunakan Gmail. Untuk mendapatkan
      MAIL_PASSWORD, Anda perlu membuat Sandi Aplikasi (App Password) di akun Google Anda.
    </p>
    <p>Cara Mendapatkan Sandi Aplikasi Gmail:</p>
    <ol>
      <li>Buka Akun Google Anda dan pergi ke Kelola Akun Google Anda.</li>
      <li>Pilih tab Keamanan.</li>
      <li>Pastikan Verifikasi 2 Langkah sudah Aktif. Jika belum, aktifkan terlebih dahulu.</li>
      <li>Di bawah bagian "Cara Anda login ke Google", klik Sandi aplikasi.</li>
      <li>Beri nama pada aplikasi Anda, misalnya Absensi Magang PLN, lalu klik Buat.</li>
      <li>
        Anda akan mendapatkan 16 karakter sandi. Salin sandi ini. Inilah yang akan Anda gunakan sebagai MAIL_PASSWORD.
      </li>
    </ol>
    <p>Setelah mendapatkan sandi tersebut, isi konfigurasi berikut di berkas .env:</p>
    <pre><code>MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME="email.anda@gmail.com"
MAIL_PASSWORD="sandi_16_karakter_yang_disalin"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="email.anda@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
</code></pre>
      <p><strong>Cara Mendapatkan Sandi Aplikasi Gmail:</strong></p>
      <ol>
        <li>Buka Akun Google Anda dan pergi ke <em>Kelola Akun Google Anda</em></li>
        <li>Pilih tab <strong>Keamanan</strong></li>
        <li>Pastikan <strong>Verifikasi 2 Langkah</strong> sudah aktif</li>
        <li>Klik <strong>Sandi aplikasi</strong></li>
        <li>Beri nama aplikasi (misal: Absensi Magang PLN), lalu klik <strong>Buat</strong></li>
        <li>Salin sandi 16 karakter yang muncul</li>
      </ol>
    </li>
    <li>
      <strong>Inisialisasi Aplikasi</strong>
      <pre><code>php artisan key:generate
php artisan migrate --seed</code></pre>
    </li>
    <li>
      <strong>Konfigurasi Tambahan</strong>
      <p><strong>a. Membuat Symbolic Link</strong></p>
      <pre><code>php artisan storage:link</code></pre>
      <p><strong>b. Mengganti Default Avatar</strong></p>
      <ol>
        <li>Letakkan file avatar.png di <code>public/profile_photos/</code></li>
        <li>Edit file <code>app/Models/User.php</code></li>
        <li>Cari method <code>getProfilePhotoUrlAttribute()</code> dan ubah menjadi:</li>
      </ol>
      <pre><code>return asset('profile_photos/avatar.png');</code></pre>
    </li>
    <li>
      <strong>Jalankan Server Pengembangan</strong>
      <pre><code>php artisan serve</code></pre>
      <p>Akses aplikasi di: <a href="http://127.0.0.1:8000" target="_blank">http://127.0.0.1:8000</a></p>
    </li>
  </ol>

  <h2>💾 Database Dump</h2>
  <p><strong>Cara Membuat Database Dump (Export)</strong></p>
  <p><em>Via phpMyAdmin:</em></p>
  <ol>
    <li>Buka phpMyAdmin dan pilih database proyek Anda</li>
    <li>Klik tab "Export"</li>
    <li>Pilih metode "Quick" dan format "SQL"</li>
    <li>Klik "Go" untuk mengunduh</li>
  </ol>

  <p><em>Via Terminal (mysqldump):</em></p>
  <pre><code>mysqldump -u [username] -p [nama_database] > dump_absensi.sql</code></pre>

  <p><strong>Cara Import Database Dump</strong></p>
  <p><em>Via phpMyAdmin:</em></p>
  <ol>
    <li>Buat database kosong</li>
    <li>Pilih database tersebut</li>
    <li>Klik tab "Import"</li>
    <li>Pilih file <code>.sql</code> lalu klik "Go"</li>
  </ol>

  <p><em>Via Terminal:</em></p>
  <pre><code>mysql -u [username] -p [nama_database_baru] < dump_absensi.sql</code></pre>

  <h2>📖 Petunjuk Penggunaan</h2>
  <p>Aplikasi ini memiliki tiga level pengguna: Superadmin, Admin, dan User.</p>

  <h3>Alur Kerja Aplikasi</h3>
  <ol>
    <li>Superadmin mendaftarkan Admin baru</li>
    <li>Admin mendaftarkan para User (peserta magang)</li>
    <li>User melakukan absensi harian</li>
  </ol>

  <h3>1. Superadmin</h3>
  <p>Tugas: Mengelola akun Admin dan data master Bidang.</p>

  <h3>2. Admin</h3>
  <p>Tugas: Mengelola akun User, menyetujui koreksi absensi, dan memantau laporan.</p>

  <h3>3. User (Peserta Magang)</h3>
  <p>Tugas: Melakukan absen masuk/pulang, melihat riwayat, dan mengajukan koreksi.</p>

  <h2>🧑‍💻 Akun Demo</h2>
  <p>Setelah menjalankan <code>php artisan migrate --seed</code>, gunakan akun-akun berikut:</p>
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
