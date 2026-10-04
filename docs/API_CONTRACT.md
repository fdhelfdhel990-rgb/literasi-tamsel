# Kontrak Aplikasi

Status: diimplementasikan sebagai web SSR Laravel/Blade. Saat ini tidak ada endpoint JSON `/api/*`; Axios tidak dipakai karena workflow berjalan lewat halaman, query-string, dan form session/CSRF. Endpoint JSON dapat ditambahkan kelak tanpa mengubah model database.
## Route Publik

| Route | Fungsi | Query/filter |
|---|---|---|
| `GET /` | Home, statistik, partner aktif, publikasi terkini | data bersumber dari MySQL |
| `GET /about` | Profil/visi/misi | konten komunitas dari settings |
| `GET /publication` | Listing publication published | `q`, `category` |
| `GET /publication/{slug}` | Artikel published | latest sidebar memakai publication published |
| `GET /digital-library` | Katalog buku published | `q`, `genre`, `publisher` |
| `GET /digital-library/{slug}` | Detail buku published | katalog saja; tak ada pinjam/stok/reservasi |
| `GET /join-us` | Tiga kartu rekrutmen | form CTA hanya aktif bila status open dan URL terisi |

| Publikasi | judul, slug, kategori/jenis, tanggal, ringkasan, isi artikel, gambar unggulan, status draft/published | baca publik; cari/filter; CRUD admin |
| Statistik komunitas | label, nilai, prefix, suffix, unit, urutan | baca publik; edit admin berizin |
| Media partner | nama, logo, URL, urutan, status aktif | baca publik; CRUD admin berizin |
| Identitas dan kontak | WhatsApp, email, lokasi, URL sosial dan visibilitas | baca publik; edit admin berizin |
| Join Us | jenis, judul, deskripsi, URL formulir, status open/closed, pesan closed, urutan | baca publik; edit admin berizin |
| Admin dan privilege | nama, email, status, role/izin | kelola hanya oleh aktor berizin; serah-terima Super Admin aman |

Path, metode HTTP, pagination, bentuk error, aturan slug, dan matriks izin harus disetujui sebelum endpoint dibangun. Draft API frontend sebelumnya ada di `frontend/api-contract.md`.

## Persyaratan Keamanan

- Route admin memakai session authentication, middleware, CSRF, Form Request validation, Policy/authorization, dan pembatasan upload.
- Pemeriksaan izin harus di backend, bukan dengan menyembunyikan tombol UI.
- Validasi URL eksternal dan MIME/ukuran image dilakukan di server.
- Jangan mengirim kredensial, APP_KEY, atau data sensitif ke API publik.

## Hal yang Perlu Disepakati

1. Lampirkan ERD yang berlaku atau setujui ERD baru sebelum migrations dibuat.
2. Tetapkan operasi yang diizinkan untuk Admin, Sub-Admin, dan Super Admin per resource.
3. Tetapkan format tanggal, kategori, status publikasi, field buku, dan aturan penghapusan media.
4. Tetapkan proses provisioning Super Admin pertama dan konfirmasi transfer kepemilikan.
