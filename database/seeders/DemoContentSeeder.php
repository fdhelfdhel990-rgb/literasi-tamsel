<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\JoinCard;
use App\Models\MediaPartner;
use App\Models\Publication;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(File::get(resource_path('data/demo.json')), true, flags: JSON_THROW_ON_ERROR);

        foreach ($data['books'] as $book) {
            Book::firstOrCreate(['slug' => $book['slug']], [
                'title' => $book['title'],
                'author' => $book['author'],
                'publisher' => $book['publisher'],
                'genre' => $book['genre'],
                'description' => $book['description'],
                'cover_path' => $book['cover'] ? 'legacy:images/'.$book['cover'] : null,
                'is_published' => true,
            ]);
        }

        foreach ($data['posts'] as $post) {
            Publication::firstOrCreate(['slug' => $post['slug']], [
                'title' => $post['title'],
                'category' => $post['type'],
                'published_at' => $this->parseIndonesianDate($post['date']),
                'excerpt' => $post['excerpt'],
                'content' => $post['content'] ?? $post['excerpt'],
                'featured_image_path' => $post['image'] ? 'legacy:images/'.$post['image'] : null,
                'status' => 'published',
            ]);
        }

        foreach ($data['partners'] as $position => $partner) {
            MediaPartner::firstOrCreate(['name' => $partner['name']], [
                'image_path' => $partner['image'] ? 'legacy:images/'.$partner['image'] : null,
                'position' => $position,
                'is_active' => true,
            ]);
        }

        $community = $data['community'];
        $community['profile'] = [
            'name' => 'Komunitas Literasi Remaja Tambun Selatan',
            'home_intro' => 'Fokus komunitas adalah menyebarluaskan akses buku bacaan anak secara gratis untuk membantu meningkatkan literasi dan kepedulian generasi muda di sekitar kita.',
            'about' => 'Komunitas Literasi Remaja Tambun Selatan mengajak generasi muda memperluas akses bacaan melalui kegiatan membaca dan perpustakaan keliling.',
            'mission' => 'Mendekatkan buku dan ruang belajar kepada anak-anak di Tambun Selatan.',
        ];
        $setting = SiteSetting::firstOrCreate(['key' => 'community'], ['value' => $community]);
        $current = $setting->value ?? [];
        $current['profile'] = array_merge($community['profile'], $current['profile'] ?? []);
        $setting->update(['value' => $current]);

        $keys = ['volunteer', 'volunteer_event', 'collaboration'];
        foreach ($community['join'] as $position => $card) {
            JoinCard::firstOrCreate(['key' => $keys[$position]], [
                'title' => $card['title'],
                'description' => $card['description'],
                'form_url' => $card['url'] ?: null,
                'is_open' => (bool) $card['open'],
                'closed_description' => 'Pendaftaran sedang ditutup.',
                'button_label' => $card['button'],
                'position' => $position,
            ]);
        }
    }

    private function parseIndonesianDate(string $date): string
    {
        $months = [
            'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
            'April' => 'April', 'Mei' => 'May', 'Juni' => 'June', 'Juli' => 'July',
            'Agustus' => 'August', 'September' => 'September', 'Oktober' => 'October',
            'November' => 'November', 'Desember' => 'December',
        ];

        return Carbon::parse(strtr($date, $months))->toDateString();
    }
}