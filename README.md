# Website Komunitas Literasi Remaja Tambun Selatan

Laravel 12, Blade, Tailwind CSS 4, Poppins, JavaScript, Axios, MySQL/MariaDB, dan Laravel Storage. Public pages membaca data dari database; admin menggunakan session authentication, policies, dan CSRF.

- Petunjuk XAMPP/Windows dan phpMyAdmin: [README_FINAL.md](README_FINAL.md)
- ERD dan asumsi skema: [docs/ERD.md](docs/ERD.md)
- API dan route contract: [docs/API_CONTRACT.md](docs/API_CONTRACT.md)
- Render, MySQL eksternal, dan R2: [docs/DEPLOYMENT_GUIDE.md](docs/DEPLOYMENT_GUIDE.md)
- Laporan implementasi/pengujian: `docs/DEVELOPMENT_REPORT.md` dan `docs/TESTING_REPORT.md`

Admin masuk melalui `/admin/login`. Akun pertama dibuat dari env dengan `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL`, dan `INITIAL_ADMIN_PASSWORD`; tidak ada registrasi admin publik. Kontak tetap berada di footer, tanpa item Contact Us di navbar.
