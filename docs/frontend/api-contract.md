# Kontrak route dan form

Kontrak yang aktif saat ini adalah web SSR Laravel dengan Blade, session cookie, dan form biasa. Tidak ada endpoint JSON `/api/*`; Axios client yang lama hanya placeholder dan tidak dipanggil. Karena interaksi tersedia lewat navigasi/form server-rendered, tidak ada kebutuhan request asynchronous yang memerlukan Axios.

Route publik: `GET /`, `/about`, `/publication?q=&category=`, `/publication/{slug}`, `/digital-library?q=&genre=&publisher=`, `/digital-library/{slug}`, `/join-us`.

Route admin berada di bawah middleware session `auth` dan `admin.active`, dilindungi Form Request serta policy:

| Modul | Operasi | Route dasar |
|---|---|---|
| Login | GET/POST, logout POST | `/admin/login`, `/admin/logout` |
| Publication | GET/POST/PUT/DELETE | `/admin/publications` |
| Digital Library | GET/POST/PUT/DELETE | `/admin/books` |
| Media Partner | GET/POST/PUT/DELETE | `/admin/partners` |
| Identitas/kontak | GET/PUT | `/admin/site-content` |
| Join Us cards | GET/PUT | `/admin/join-cards` |
| Admin/Sub-Admin | GET/POST/PUT/DELETE nonaktif | `/admin/users` |
| Transfer Super Admin | POST + current password + frasa konfirmasi | `/admin/users/transfer-super-admin` |

Semua mutasi form memakai CSRF. Filter listing menerima query string; validasi input dan upload dilakukan server-side. Bentuk JSON API baru harus dibuat dan disepakati terpisah bila kelak diperlukan.

Lihat [kontrak lengkap dan matriks permission](../API_CONTRACT.md) serta [ERD](../ERD.md).
