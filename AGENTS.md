# AGENTS.md — PAD Literasi Tambun Selatan

## Project Rules

This repository contains the Website Komunitas Literasi Remaja Tambun Selatan.

Tech stack:
- Laravel 12
- Blade
- Tailwind CSS
- Vanilla JavaScript
- Axios
- PostgreSQL
- Vite

## Design Rules

1. The provided High-Fidelity screenshots are the primary visual source of truth.
2. Do not redesign public pages.
3. Use Poppins globally.
4. Use the official horizontal logo from public/images/branding/logo.png.
5. Keep typography, spacing, component dimensions, and colors consistent.
6. Do not add new public sections without approval.
7. Contact information belongs in the footer; Contact Us must not appear in the primary navbar.
8. The copyright line must be horizontally centered.
9. Use restrained animations and respect prefers-reduced-motion.
10. Admin styling must follow the public website's design language.

## Development Rules

- Do not delete existing functionality to fix unrelated errors.
- Do not overwrite working code without reviewing it.
- Never expose environment variables or credentials.
- Use Laravel validation, authorization, and CSRF protection.
- Store dynamic content in PostgreSQL.
- Store uploaded media using Laravel Storage.
- Do not use localStorage as the production database.
- Keep API contracts documented.
- Do not push or deploy without explicit approval.

## Testing Rules

After every implementation phase:
- Run relevant automated tests.
- Run npm run build.
- Check route:list.
- Verify public and admin functionality.
- Test responsive layouts.
- Document changed files and remaining issues.

Do not report a feature as complete unless it has been tested successfully.