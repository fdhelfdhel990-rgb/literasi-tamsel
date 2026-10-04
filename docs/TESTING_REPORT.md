# Laporan Pengujian

Tanggal: 2026-10-04.

## Lulus

- `php artisan test`: 20 test lulus, 157 assertion (SQLite in-memory); termasuk auth/session/logout, initial Super Admin seeder + login, inactive users, policy per role, CRUD publication/book/partner, image upload/replacement/delete, pencarian, statistik/profil/footer/social visibility, Join Us, transfer Super Admin, dan guard isolasi test DB.
- Database development XAMPP MariaDB 10.4: seluruh tujuh migrations berhasil diterapkan. Seeder default + sample lokal menghasilkan 9 publication, 9 buku, dan 3 partner; migration/session table aktif.
- `php artisan route:list --except-vendor`: 45 route terdaftar. Guest `/admin` dialihkan ke `/admin/login` (302).
- `php artisan view:cache`: berhasil. `npm run build`: berhasil.
- Browser Playwright menguji delapan route pada lebar 1440, 1366, 768, 390 px (32 kombinasi): tidak ada horizontal overflow; body/kontrol Poppins. Login Super Admin memakai akun lokal sekali pakai, kemudian akun dibersihkan.
- Browser admin CRUD: publication create/edit/publish/public detail/delete; buku create/edit/delete dan preview cover lama; partner create/edit/delete serta tautan carousel. Record/akun E2E temporer dibersihkan setelah test.
- Join Us: tiga kartu berukuran dan bergaya identik di desktop/mobile. Publication detail: frame gambar 1.65:1, thumbnail 88×88 desktop/76×76 mobile, tombol share tinggi sama dan warna terverifikasi; URL share menunjuk artikel aktif; Clipboard API menampilkan feedback sukses.
- Fallback media diuji dengan memaksa URL gambar 404; frame artikel dan thumbnail menampilkan placeholder dengan ukuran tetap.
- Screenshot browser diambil untuk Publication Detail desktop 1440 dan mobile 390.
- `composer validate --no-check-publish`: valid. Render `render.yaml` berhasil diparse oleh `js-yaml` sementara.
- Secret audit: credential DB produksi/R2/initial admin tidak diisi; `.env` ignored; nilai password E2E tidak disimpan ke source atau DB.

## Batas Verifikasi

- Test suite memakai SQLite in-memory; migrasi dan seed development diuji terpisah pada MariaDB lokal.
- Belum ada nilai secret MySQL/R2 production, bucket public URL, atau layanan Render untuk uji end-to-end.
- Docker CLI tidak tersedia di mesin ini sehingga image container belum dapat dibangun lokal.
- Diagnostic CSS editor dapat menandai `@source`/`@theme` unknown; Vite Tailwind 4 build berhasil.
