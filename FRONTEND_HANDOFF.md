# Project handoff

- Hi-Fi visual references available in `docs/frontend/references/`: Home, Publication, and Digital Library. Public layout remains reference-led; the DMT Print image is not present in this repository, so the login uses only PAD branding and its blue palette.
- Public pages: Home, About Us, Publication listing/detail, Digital Library listing/detail, and Join Us. Contact information stays in the footer; navbar remains five items.
- Database: MySQL/MariaDB migrations and Eloquent models are implemented. The demo JSON may be imported manually for local sample content; it is not read by runtime public routes and is not run by the production seeder.
- Admin: session login at `/admin/login`; no public registration. Policies enforce Admin/Sub-Admin/Super Admin access on server requests. The first Super Admin comes from environment variables through `InitialSuperAdminSeeder`.
- Media: local uploads use the public Storage disk; production can use the S3-compatible disk configured for Cloudflare R2.
- Books are catalog-only. Borrowing, inventory, reservation, and application submissions are outside the current agreed data model.
- Actual web route/form contract and assumptions: `docs/API_CONTRACT.md`. ERD: `docs/ERD.md`. Deployment guide: `docs/DEPLOYMENT_GUIDE.md`.
