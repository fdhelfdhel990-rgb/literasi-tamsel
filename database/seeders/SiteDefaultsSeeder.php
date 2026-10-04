<?php

namespace Database\Seeders;

use App\Models\JoinCard;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultImpact = [
            ['label' => 'Berdampak Positif ke', 'value' => 400, 'prefix' => '>', 'suffix' => '', 'unit' => 'Anak'],
            ['label' => 'Koleksi Bacaan', 'value' => 500, 'prefix' => '±', 'suffix' => '', 'unit' => 'Buku'],
            ['label' => 'Sukarelawan Aktif', 'value' => 20, 'prefix' => '', 'suffix' => '', 'unit' => 'Remaja'],
            ['label' => 'Perpustakaan Desa', 'value' => 2, 'prefix' => '', 'suffix' => '', 'unit' => 'Lokasi Binaan'],
        ];

        $setting = SiteSetting::query()->firstOrCreate(['key' => 'community'], [
            'value' => [
                'impact' => $defaultImpact,
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

        $community = $setting->value ?? [];
        $impact = $community['impact'] ?? [];
        if (count($impact) === 4 && collect($impact)->every(fn ($metric) => (int) ($metric['value'] ?? 0) === 0)) {
            $community['impact'] = $defaultImpact;
            $setting->update(['value' => $community]);
        }

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
