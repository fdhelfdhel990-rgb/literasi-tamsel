<?php
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', fn () => $this->comment('Terus bergerak bersama literasi.'));

Artisan::command('aiven:test-connection', function (): int {
    $connection = 'mysql_aiven';
    $config = config("database.connections.{$connection}");
    $mask = static function (string $value): string {
        if (mb_strlen($value) <= 8) {
            return str_repeat('*', max(1, mb_strlen($value)));
        }

        return mb_substr($value, 0, 4).'...'.mb_substr($value, -4);
    };

    if (! extension_loaded('pdo_mysql')) {
        $this->error('Ekstensi pdo_mysql belum aktif.');

        return 1;
    }

    foreach (['host', 'port', 'database', 'username', 'password'] as $key) {
        if (blank($config[$key] ?? null)) {
            $this->error("Konfigurasi Aiven belum lengkap: AIVEN_DB_".strtoupper($key).'.');

            return 1;
        }
    }

    if (blank(env('AIVEN_MYSQL_ATTR_SSL_CA'))) {
        $this->error('Konfigurasi Aiven belum lengkap: AIVEN_MYSQL_ATTR_SSL_CA.');

        return 1;
    }

    if (! is_file((string) env('AIVEN_MYSQL_ATTR_SSL_CA'))) {
        $this->error('File CA certificate Aiven tidak ditemukan.');

        return 1;
    }

    if (filled(env('DATABASE_URL'))) {
        $this->warn('DATABASE_URL sedang terisi dan dapat menimpa koneksi mysql lokal. Koneksi mysql_aiven tidak memakai DATABASE_URL.');
    }

    if (filled(env('DB_URL'))) {
        $this->warn('DB_URL terdeteksi, tetapi config Laravel proyek ini memakai DATABASE_URL, bukan DB_URL.');
    }

    try {
        DB::purge($connection);

        $version = DB::connection($connection)->selectOne('SELECT VERSION() AS version');
        $database = DB::connection($connection)->selectOne('SELECT DATABASE() AS database_name');

        $this->info('Koneksi Aiven MySQL TLS berhasil.');
        $this->line('Connection: '.$connection);
        $this->line('Host: '.$mask((string) $config['host']));
        $this->line('Port: '.$config['port']);
        $this->line('Database: '.($database->database_name ?? '[tidak terpilih]'));
        $this->line('MySQL version: '.($version->version ?? '[tidak terbaca]'));

        return 0;
    } catch (Throwable $exception) {
        $this->error('Koneksi Aiven MySQL TLS gagal.');
        $this->line($exception->getMessage());

        return 1;
    }
});

Artisan::command('admin:reset-credentials {identifier : Email atau username akun admin}', function (string $identifier): int {
    $normalized = mb_strtolower(trim($identifier));
    $user = User::query()
        ->where(function ($query) use ($normalized): void {
            $query->whereRaw('LOWER(email) = ?', [$normalized])
                ->orWhereRaw('LOWER(username) = ?', [$normalized]);
        })
        ->first();

    if (! $user || ! in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_SUB_ADMIN], true)) {
        $this->error('Akun admin tidak ditemukan.');

        return 1;
    }

    if (! $this->confirm("Reset password untuk {$user->email}?", false)) {
        $this->warn('Reset dibatalkan.');

        return 1;
    }

    $password = (string) $this->secret('Masukkan password baru (minimal 12 karakter)');
    $confirmation = (string) $this->secret('Ulangi password baru');

    if ($password !== $confirmation || mb_strlen($password) < 12) {
        $this->error('Password tidak cocok atau kurang dari 12 karakter.');

        return 1;
    }

    $user->forceFill(['password' => Hash::make($password), 'is_active' => true])->save();
    $this->info('Kredensial admin berhasil diperbarui.');

    return 0;
});
