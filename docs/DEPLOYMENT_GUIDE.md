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

## Render + Aiven MySQL + Cloudflare R2

`Dockerfile` membangun aset dengan `npm ci`, menggunakan `composer.lock`, memasang PDO MySQL dan S3 adapter. `render.yaml` menyediakan nama env vars; nilai sensitif ditandai `sync: false`.
1. Provision Aiven MySQL 8+ dengan TLS dan backup otomatis. Render blueprint tidak membuat MySQL sendiri; masukkan `AIVEN_DB_HOST`, `AIVEN_DB_PORT`, `AIVEN_DB_DATABASE`, `AIVEN_DB_USERNAME`, dan `AIVEN_DB_PASSWORD` dari Aiven.
2. Upload CA certificate Aiven sebagai Render Secret File dengan path `/etc/secrets/aiven-ca.pem`, lalu set `AIVEN_MYSQL_ATTR_SSL_CA=/etc/secrets/aiven-ca.pem`. Jangan gunakan path Windows seperti `C:/certificates/...` di Render.
3. Buat bucket R2 untuk media publik. Atur custom/public asset URL sebagai `R2_PUBLIC_URL`, S3 API endpoint sebagai `R2_ENDPOINT`, bucket sebagai `R2_BUCKET`, access key/secret, region `auto`, dan path-style endpoint. Jangan set ACL/visibility object untuk R2; bucket/custom domain yang mengatur public read.
4. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `APP_KEY` Laravel `base64:...`, `SESSION_DRIVER=database`, `CACHE_STORE=array`, `FILESYSTEM_DISK=r2`, `DB_CONNECTION=mysql_aiven`, dan tiga `INITIAL_ADMIN_*` melalui Render Environment settings.
5. Deploy image. Container startup hanya memvalidasi CA Aiven dan menjalankan Apache; tidak menjalankan migration atau seeder otomatis.
6. Dari komputer lokal, setelah target database disetujui, cek dahulu `php artisan aiven:deployment-preflight`, lalu jalankan `php artisan migrate --database=mysql_aiven --force`.
7. Setelah migrasi sukses dan env Super Admin diisi, jalankan `php artisan db:seed --database=mysql_aiven --class=InitialSuperAdminSeeder --force` dan `php artisan db:seed --database=mysql_aiven --class=SiteDefaultsSeeder --force`. Seeder idempotent dan tidak mengganti password akun existing.
8. Login di `/admin/login`, buat akun Admin/Sub-Admin, lalu masukkan konten resmi. Verifikasi `/up`, setiap public page, CSRF/session, permission, upload ke R2, URL public media, backup DB, dan restore.

## APP_KEY dan Keamanan

- Untuk app baru, buat key Laravel lokal dengan `php artisan key:generate --show`, lalu set output sebagai `APP_KEY` secret Render. Jangan pernah salin ke Git/chat/log.
- Gunakan password initial admin kuat minimal 14 karakter. Seeder Hash dan idempotent; rerun tidak mengganti password akun yang sudah ada.
- Jangan gunakan filesystem ephemeral Render sebagai lokasi permanen upload.
- Jangan deploy, push, atau mengubah data production tanpa persetujuan pemilik.

## Verifikasi Belum Tersedia

Environment credential MySQL/R2 dan Docker CLI tidak tersedia saat implementasi, sehingga build image dan smoke test staging/restore belum dapat dijalankan.
