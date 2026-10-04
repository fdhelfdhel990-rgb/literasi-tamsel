# Menjalankan PAD Literasi dengan XAMPP

Proyek menggunakan Laravel 12, Blade/Tailwind 4, Vite, MySQL/MariaDB, session authentication, dan Laravel Storage. PHP minimum 8.2.

## 1. Siapkan XAMPP

1. Jalankan Apache dan MySQL dari XAMPP Control Panel.
2. Buka `http://localhost/phpmyadmin`.
3. Pilih **Databases**, buat database `literasi_pad` dengan collation `utf8mb4_unicode_ci`.
4. Jangan anggap service selalu hidup; pastikan MySQL berjalan sebelum migrasi.

## 2. Siapkan `.env`

Dari PowerShell di folder proyek, jangan menimpa `.env` yang sudah ada:

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
$env:Path = 'D:\xampp\php;' + $env:Path
```

Pastikan konfigurasi DB di `.env` mengarah ke database yang dibuat lewat phpMyAdmin. Contoh XAMPP default:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=literasi_pad
DB_USERNAME=root
DB_PASSWORD=
```

Jika memakai password MySQL sendiri, isi hanya di `.env` lokal. Pertahankan `APP_KEY` yang sudah ada. Untuk `.env` baru yang masih kosong, jalankan `php artisan key:generate` satu kali.

## 3. Install, Migrasi, dan Jalankan

```powershell
composer install
npm ci
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Saat mengembangkan CSS/JS, jalankan `npm run dev` di terminal kedua. Hentikan server dengan `Ctrl+C`.

Jika Composer di XAMPP gagal karena ekstensi ZIP belum aktif:

```powershell
& 'D:\xampp\php\php.exe' -d extension=zip 'C:\ProgramData\ComposerSetup\bin\composer.phar' install
```

Untuk data contoh lokal saja, jalankan `php artisan db:seed --class=DemoContentSeeder`. Seed ini mengimpor data demonstrasi lama ke database dan **tidak** dijalankan oleh seeder production.

## 4. Akun Super Admin Pertama

Atur nama/email/password lokal di `.env` sebelum provisioning. Password minimal 14 karakter; gunakan nilai rahasia milik Anda dan jangan commit/share file `.env`. `php artisan db:seed` menjalankan SiteDefaultsSeeder dan InitialSuperAdminSeeder.

```dotenv
INITIAL_ADMIN_NAME="Nama Super Admin"
INITIAL_ADMIN_EMAIL=admin@example.com
INITIAL_ADMIN_PASSWORD=
```

Isi password kuat secara lokal, lalu jalankan:

```powershell
php artisan db:seed
```

Seeder meng-hash password dengan Laravel Hash, membuat satu akun Super Admin, tidak membuat akun yang sudah ada kembali, dan tidak pernah mereset password saat dijalankan ulang. Masuk di `http://127.0.0.1:8000/admin/login`. Admin/Sub-Admin ditambahkan dari panel Super Admin; tidak ada registrasi publik.

## 5. Pengujian

```powershell
php artisan route:list
php artisan view:cache
php artisan test
npm run build
```

Testing otomatis memakai SQLite in-memory; development memakai MySQL/MariaDB yang dikonfigurasi di `.env`.

## Catatan Konten

- Halaman publik: `/`, `/about`, `/publication`, `/publication/{slug}`, `/digital-library`, `/digital-library/{slug}`, `/join-us`.
- Buku adalah katalog saja. Tidak ada peminjaman, stok, atau reservasi.
- Kontak sosial berada di footer, navbar tetap lima item.
- Upload lokal menggunakan disk `public`; production perlu S3-compatible storage/R2.
- Detail deployment: [docs/DEPLOYMENT_GUIDE.md](docs/DEPLOYMENT_GUIDE.md).
