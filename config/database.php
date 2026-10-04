<?php

use Illuminate\Support\Str;

$mysqlSslCaOption = class_exists(\Pdo\Mysql::class)
    ? \Pdo\Mysql::ATTR_SSL_CA
    : PDO::MYSQL_ATTR_SSL_CA;

$mysqlOptions = static fn (array $options): array => array_filter(
    $options,
    static fn ($value): bool => $value !== null && $value !== ''
);

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'literasi_pad'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? $mysqlOptions([
                PDO::ATTR_TIMEOUT => env('DB_TIMEOUT', 5),
                $mysqlSslCaOption => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'mysql_aiven' => [
            'driver' => 'mysql',
            'url' => null,
            'host' => env('AIVEN_DB_HOST'),
            'port' => env('AIVEN_DB_PORT', '3306'),
            'database' => env('AIVEN_DB_DATABASE'),
            'username' => env('AIVEN_DB_USERNAME'),
            'password' => env('AIVEN_DB_PASSWORD'),
            'unix_socket' => '',
            'charset' => env('AIVEN_DB_CHARSET', 'utf8mb4'),
            'collation' => env('AIVEN_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? $mysqlOptions([
                PDO::ATTR_TIMEOUT => env('AIVEN_DB_TIMEOUT', 10),
                $mysqlSslCaOption => env('AIVEN_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
    ],
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
