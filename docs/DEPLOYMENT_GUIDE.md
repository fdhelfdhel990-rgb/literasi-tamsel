# Panduan Deployment

Status: konfigurasi siap ditinjau; belum dideploy. Render memerlukan endpoint MySQL/MariaDB eksternal dan bucket S3-compatible (contoh Cloudflare R2). Nilai host/user/password, APP_KEY, URL bucket, dan initial admin sengaja tidak tersedia di repo.
## Lokal: XAMPP + phpMyAdmin

Lihat langkah lengkap di [README_FINAL.md](../README_FINAL.md). Ringkasannya:
1. Jalankan Apache/MySQL di XAMPP.
2. Di phpMyAdmin buat `literasi_pad` dengan collation `utf8mb4_unicode_ci`.
3. Pertahankan `.env` yang sudah ada; atur `DB_CONNECTION=mysql`, host `127.0.0.1`, port `3306`, database `literasi_pad`, username `root`, dan password yang sesuai XAMPP Anda.
4. Jalankan `php artisan migrate`, isi `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL`, `INITIAL_ADMIN_PASSWORD` lokal, lalu `php artisan db:seed` untuk settings awal dan Super Admin.
5. Opsional untuk sample development saja: `php artisan db:seed --class=DemoContentSeeder`. Jangan jalankan seeder sample tersebut di production.
6. Jalankan `php artisan storage:link`, `npm run build`, lalu `php artisan serve`.

## Render + MySQL + R2

`Dockerfile` membangun aset dengan `npm ci`, menggunakan `composer.lock`, memasang PDO MySQL dan S3 adapter. `render.yaml` menyediakan nama env vars; nilai sensitif ditandai `sync: false`.
1. Provision layanan MySQL 8+/MariaDB eksternal dengan TLS dan backup otomatis. Render blueprint tidak membuat MySQL sendiri; masukkan DB host/port/database/username/password dari penyedia yang dipilih.
2. Buat bucket R2 untuk media publik. Atur custom/public asset URL sebagai `AWS_URL`, S3 endpoint sebagai `AWS_ENDPOINT`, bucket, access key, secret, region `auto`, `AWS_VISIBILITY=public`, dan path-style endpoint. Pastikan bucket/custom domain mengizinkan read untuk aset publik. Simpan CA database bila diwajibkan pada secret file dan set `MYSQL_ATTR_SSL_CA` ke path file itu.
3. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `APP_KEY` Laravel `base64:...`, `SESSION_DRIVER=database`, `CACHE_STORE=array`, `FILESYSTEM_DISK=s3`, `DB_CONNECTION=mysql`, dan tiga `INITIAL_ADMIN_*` melalui Render Environment settings.
4. Deploy image, kemudian dari Render Shell atau job deploy yang disetujui jalankan `php artisan migrate --force`.
5. Setelah migrasi sukses dan env Super Admin diisi, jalankan `php artisan db:seed --force`. Default seeder hanya membuat settings scaffold, tiga kartu Join Us, dan Super Admin; tidak mengimpor publikasi/buku contoh.
6. Login di `/admin/login`, buat akun Admin/Sub-Admin, lalu masukkan konten resmi. Verifikasi `/up`, setiap public page, CSRF/session, permission, upload ke R2, URL public media, backup DB, dan restore.

## APP_KEY dan Keamanan

- Untuk app baru, buat key Laravel lokal dengan `php artisan key:generate --show`, lalu set output sebagai `APP_KEY` secret Render. Jangan pernah salin ke Git/chat/log.
- Gunakan password initial admin kuat minimal 14 karakter. Seeder Hash dan idempotent; rerun tidak mengganti password akun yang sudah ada.
- Jangan gunakan filesystem ephemeral Render sebagai lokasi permanen upload.
- Jangan deploy, push, atau mengubah data production tanpa persetujuan pemilik.

## Verifikasi Belum Tersedia

Environment credential MySQL/R2 dan Docker CLI tidak tersedia saat implementasi, sehingga build image dan smoke test staging/restore belum dapat dijalankan.
