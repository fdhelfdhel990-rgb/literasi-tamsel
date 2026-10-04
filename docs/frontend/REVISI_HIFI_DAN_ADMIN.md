# Revisi Hi-Fi dan editor preview

Acuan: screenshot Home, Join Us, dan Publication Detail yang diberikan pada 3 Oktober 2026. Desain publik mengikuti referensi, sedangkan panel admin tetap pengembangan tersendiri dengan warna dan tipografi yang sama.

## Perubahan
- Navbar tetap lima menu tanpa Contact Us, memakai logo horizontal resmi `public/images/branding/logo.png`, Poppins, dan underline halus. Tinggi desktop disesuaikan agar seimbang dengan Hi-Fi.
- Home: hero terpusat, empat statistik, penghitung angka dari nol saat terlihat, media partner bergerak mendatar (jeda saat hover/focus; panah manual), kartu berita dan footer tiga kolom.
- Footer: Jelajahi, ikon sosial YouTube/Instagram/TikTok/Facebook, WhatsApp, lokasi, email; copyright terpusat. URL kosong tidak menjadi tautan palsu.
- Publication Detail: search di atas berita terbaru, share di bawah foto dan sebelum judul/deskripsi, warna tombol sesuai Hi-Fi. Instagram hanya membuka Instagram (platform tidak mendukung share URL web langsung); Copy Link disediakan.
- Join Us: tiga card dan ikon SVG, Google Form langsung pada tombol jika URL disediakan, teks dan status pendaftaran bisa diubah melalui editor preview.

## Admin CMS
- Login session berada di `/admin/login`; registrasi publik tidak tersedia.
- CRUD konten, statistik, footer, kartu Join Us, akun dan izin menggunakan MySQL serta Laravel Storage.
- Upload divalidasi server-side; preview file sementara memakai object URL browser dan tidak menyimpan data CMS ke `localStorage`.
- Super Admin awal diprovision dari env. Policy/middleware menegakkan izin pada tiap mutasi.

## Handoff backend
Rute web SSR saat ini memakai session, CSRF, Form Requests, policy, model, dan migrations MySQL. API JSON belum diperlukan oleh interaksi saat ini; kontrak route aktif dijelaskan di `docs/API_CONTRACT.md`.

## Penggantian aset
- Logo: `public/images/branding/logo.png` (logo horizontal resmi).
- Hero: `public/images/community/hero.jpg`.
- Berita: perbarui `resources/data/demo.json` bagian `posts[].image`.
- Partner awal: `resources/data/demo.json` bagian `partners[]`; nama file relatif terhadap `public/images/`.

## Cara mencoba lokal
```powershell
$env:Path = 'D:\xampp\php;' + $env:Path
composer install
npm install
npm run build
php artisan serve
```
Admin CMS: `http://127.0.0.1:8000/admin/login` setelah database dan Super Admin lokal diprovision.
