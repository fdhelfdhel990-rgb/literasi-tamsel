# Pekerjaan Tersisa

Implementasi dan QA lokal Phase 1-6 selesai. Persiapan konfigurasi Phase 7 tersedia. Belum ada production deployment.

## Memerlukan Environment/Approval Pemilik

- [ ] Buat database production pada penyedia MySQL/MariaDB eksternal, lalu masukkan host, user, password, database, port, dan CA TLS ke Render secret settings.
- [ ] Buat bucket/domain R2, access key/secret, endpoint, public asset URL, dan kebijakan akses publik sesuai kebijakan organisasi.
- [ ] Tentukan `APP_URL`, buat `APP_KEY` production baru, dan masukkan `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL`, `INITIAL_ADMIN_PASSWORD` lewat secret settings. Jangan kirim secret lewat chat.
- [ ] Tinjau isi demo dan ganti semua record sample dengan data/foto resmi yang berizin sebelum website publik.
- [ ] Review asumsi model/permission pada [ERD](ERD.md) dan [API contract](API_CONTRACT.md) dengan pemilik/backend.
- [ ] Docker CLI tidak tersedia lokal; jalankan build image di CI atau mesin yang memiliki Docker sebelum deploy.
- [ ] Lakukan deploy staging, verifikasi migration, login, CRUD, R2, backup/restore, dan rollback. Production deploy menunggu persetujuan eksplisit.

## Catatan

- Playwright telah memverifikasi public pages, login screen, responsive viewport dan publication detail; auth/CRUD server-side ditutup oleh 20 feature test.
- Automated tests memakai SQLite in-memory; migration development telah dijalankan terhadap MariaDB lokal. Belum ada layanan test MySQL terpisah.
- Diagnostic CSS editor dapat berbeda dari pipeline Tailwind 4; `npm run build` berhasil.
