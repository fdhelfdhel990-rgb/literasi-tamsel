# Manifest aset gambar (Hi-Fi)

Seluruh path berada relatif terhadap `public/images/`. Untuk mengganti gambar, **pertahankan nama file yang sudah dipakai** atau ubah path di Blade/`resources/data/demo.json`. Jangan commit foto atau logo tanpa izin pemilik.

| Aset | Path | Pemakaian |
|---|---|---|
| Logo horizontal resmi | `branding/logo.png` | Navbar, footer, login, admin |
| Foto hero | `community/hero.jpg` | Home |
| Dokumentasi kegiatan | `community/activity.jpg` | Publikasi |
| Relawan | `community/volunteers.jpg` | About/Publikasi |
| Anak-anak | `community/children.jpg` | Publikasi |
| Gerobak Angkasa | `community/gerobak.jpg` | About |
| Mobil perpustakaan | `community/van.jpg` | About |
| Sampul buku | `books/*.jpg` | Katalog; nama file dipetakan dalam demo.json |
| Logo mitra | `partners/*.jpg` | Home; nama file dipetakan dalam demo.json |

Untuk gambar yang belum ada, kosongkan field `image`/`cover` dalam `resources/data/demo.json` dan gunakan placeholder netral, bukan gambar acak. Ganti teks, statistik, identitas mitra, dan informasi kontak dengan data resmi client sebelum publikasi.
