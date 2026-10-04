<?php

namespace Database\Seeders;

use App\Models\JoinCard;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->firstOrCreate(['key' => 'community'], [
            'value' => [
                'impact' => [
                    ['label' => 'Berdampak Positif ke', 'value' => 0, 'prefix' => '>', 'suffix' => '', 'unit' => 'Anak'],
                    ['label' => 'Koleksi Bacaan', 'value' => 0, 'prefix' => '±', 'suffix' => '', 'unit' => 'Buku'],
                    ['label' => 'Sukarelawan Aktif', 'value' => 0, 'prefix' => '', 'suffix' => '', 'unit' => 'Remaja'],
                    ['label' => 'Perpustakaan Desa', 'value' => 0, 'prefix' => '', 'suffix' => '', 'unit' => 'Lokasi Binaan'],
                ],
                'contact' => [
                    'whatsapp' => '',
                    'email' => '',
                    'location' => 'Tambun Selatan, Kabupaten Bekasi, Jawa Barat',
                ],
                'social' => ['youtube' => '', 'instagram' => '', 'tiktok' => '', 'facebook' => ''],
                'social_visibility' => ['youtube' => true, 'instagram' => true, 'tiktok' => true, 'facebook' => true],
                'profile' => [
                    'name' => 'Komunitas Literasi Remaja Tambun Selatan',
                    'home_intro' => 'Memperluas akses bacaan dan ruang belajar bagi generasi muda di Tambun Selatan.',
                    'about' => 'Komunitas Literasi Remaja Tambun Selatan mengajak generasi muda memperluas akses bacaan melalui kegiatan membaca dan perpustakaan keliling.',
                    'mission' => 'Mendekatkan buku dan ruang belajar kepada anak-anak di Tambun Selatan.',
                ],
            ],
        ]);

        $cards = [
            ['key' => 'volunteer', 'title' => 'Ikut Volunteer', 'description' => 'Bergabung sebagai pendamping membaca dan penggerak kegiatan komunitas.', 'button_label' => 'Daftar sebagai volunteer'],
            ['key' => 'volunteer_event', 'title' => 'Ikut Volunteer Event', 'description' => 'Terlibat pada agenda literasi tertentu sesuai waktu dan kemampuan Anda.', 'button_label' => 'Daftar sebagai volunteer'],
            ['key' => 'collaboration', 'title' => 'Ajukan Kerja Sama Kolaborasi', 'description' => 'Buka peluang kemitraan program, publikasi, atau dukungan operasional.', 'button_label' => 'Ajukan Kolaborasi'],
        ];

        foreach ($cards as $position => $card) {
            JoinCard::query()->firstOrCreate(['key' => $card['key']], $card + [
                'form_url' => null,
                'is_open' => false,
                'closed_description' => 'Pendaftaran belum dibuka. Silakan cek kembali informasi komunitas.',
                'position' => $position,
            ]);
        }
    }
}