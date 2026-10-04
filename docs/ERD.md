# Entity Relationship Diagram

Schema implementasi MySQL/MariaDB. Tidak ada ERD lama di repo; diagram ini mendokumentasikan rancangan yang dibuat dari kebutuhan master task.

```mermaid
erDiagram
    USERS ||--o{ PUBLICATIONS : creates
    USERS ||--o{ BOOKS : creates
    USERS ||--o{ MEDIA_PARTNERS : creates
    USERS ||--o{ SITE_SETTINGS : updates

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role
        json permissions
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
    PUBLICATIONS {
        bigint id PK
        string title
        string slug UK
        string category
        date published_at
        text excerpt
        longtext content
        string featured_image_path
        string status
        bigint created_by FK
    }
    BOOKS {
        bigint id PK
        string title
        string slug UK
        string author
        string publisher
        string genre
        text description
        string isbn UK
        string cover_path
        boolean is_published
        bigint created_by FK
    }
    MEDIA_PARTNERS {
        bigint id PK
        string name
        string image_path
        string url
        int position
        boolean is_active
        bigint created_by FK
    }
    SITE_SETTINGS {
        bigint id PK
        string key UK
        json value
        bigint updated_by FK
    }
    JOIN_CARDS {
        bigint id PK
        string key UK
        string title
        text description
        string form_url
        boolean is_open
        text closed_description
        string button_label
        int position
    }
    SESSIONS {
        string id PK
        bigint user_id
        text payload
        int last_activity
    }
```

## Assumptions

- `users.role` memiliki nilai `super_admin`, `admin`, atau `sub_admin`. Permission Sub-Admin disimpan sebagai JSON karena modul/aksi yang dapat diberikan sudah terbatas dan tidak ada requirement role editor khusus; policy tetap memeriksa setiap request di server.
- Admin dapat mengelola seluruh konten, tetapi tidak akun/role. Sub-Admin hanya menjalankan izin yang diberikan. Super Admin mengelola semua modul dan akun.
- `site_settings.value` menyimpan satu dokumen JSON community yang berisi statistik, profil, kontak, dan tautan sosial; `join_cards` berupa tiga record tetap.
- Buku hanya memiliki metadata katalog dan `is_published`; tidak ada stok, peminjaman, reservasi, atau transaksi buku.
- `created_by`/`updated_by` boleh null untuk konten awal yang diimpor atau sesudah akun pengelola dihapus. Gambar lama ditandai prefix `legacy:`; upload baru disimpan di Laravel Storage.
- Tidak dibuat tabel pengajuan Join Us karena kebutuhan saat ini hanya mengelola kartu dan URL Google Form, bukan menyimpan data pendaftar.
