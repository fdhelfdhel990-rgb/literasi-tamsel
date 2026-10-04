<?php

namespace Tests\Feature;

use App\Models\JoinCard;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\SiteDefaultsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class CommunityContentTest extends TestCase
{
    use CreatesAdminUsers;
    use RefreshDatabase;

    public function test_default_database_seeder_does_not_import_mock_publications_or_books(): void
    {
        Config::set('initial_admin.name', '');
        Config::set('initial_admin.email', '');
        Config::set('initial_admin.password', '');

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('site_settings', 1);
        $this->assertDatabaseCount('join_cards', 3);
        $this->assertDatabaseCount('publications', 0);
        $this->assertDatabaseCount('books', 0);
    }

    public function test_fresh_install_has_four_stat_defaults_and_three_closed_join_cards(): void
    {
        $this->seed(SiteDefaultsSeeder::class);

        $community = SiteSetting::valueFor('community');
        $this->assertCount(4, $community['impact']);
        $this->assertSame([0, 0, 0, 0], array_column($community['impact'], 'value'));
        $this->assertCount(3, JoinCard::query()->get());
        $this->assertSame(0, JoinCard::query()->where('is_open', true)->count());
        $this->get('/join-us')->assertOk()->assertSee('Ikut Volunteer')->assertSee('Pendaftaran belum dibuka.');
    }

    public function test_statistics_profile_contact_and_social_visibility_update_public_home_and_footer(): void
    {
        $this->seed(DemoContentSeeder::class);
        $admin = $this->createAdmin(User::ROLE_SUPER_ADMIN);
        $this->actingAs($admin);

        $this->put(route('admin.site-content.update'), [
            'impact' => [
                ['value' => 777], ['value' => 888], ['value' => 22], ['value' => 3],
            ],
            'profile' => [
                'name' => 'Komunitas Database Test',
                'home_intro' => 'Intro dari database test.',
                'about' => 'Profil komunitas dari database.',
                'mission' => 'Misi dari database.',
            ],
            'contact' => [
                'whatsapp' => '628123456789',
                'email' => 'hello@example.test',
                'location' => 'Tambun Selatan, Bekasi',
            ],
            'social' => [
                'youtube' => 'https://youtube.com/@literasi-test',
                'instagram' => 'https://instagram.com/literasi-test',
                'tiktok' => '',
                'facebook' => '',
            ],
            'social_visibility' => ['youtube' => '1', 'instagram' => '0', 'tiktok' => '0', 'facebook' => '0'],
        ])->assertRedirect(route('admin.site-content.edit'));

        $this->assertSame(777, SiteSetting::valueFor('community')['impact'][0]['value']);
        $updatedCommunity = SiteSetting::valueFor('community');
        $this->assertSame('https://youtube.com/@literasi-test', $updatedCommunity['social']['youtube']);
        $this->assertTrue($updatedCommunity['social_visibility']['youtube']);
        $home = $this->get('/')->assertOk()->assertSee('Komunitas Database Test')->assertSee('Intro dari database test.')->assertSee('data-count="777"', false);
        $home->assertSee('https://youtube.com/@literasi-test')->assertDontSee('href="https://instagram.com/literasi-test"', false)->assertDontSee('href="#i-instagram"', false);
        $this->get('/about')->assertOk()->assertSee('Profil komunitas dari database.')->assertSee('Misi dari database.');
        $this->get('/')->assertSee('628123456789')->assertSee('hello@example.test');
    }

    public function test_join_cards_update_cta_and_closed_description_on_public_page(): void
    {
        $this->seed(DemoContentSeeder::class);
        $admin = $this->createAdmin(User::ROLE_ADMIN);
        $this->actingAs($admin);

        $cards = [
            ['key' => 'volunteer', 'title' => 'Relawan DB', 'description' => 'Relawan description', 'form_url' => 'https://forms.google.com/volunteer', 'is_open' => '1', 'closed_description' => 'Relawan tutup', 'button_label' => 'Isi form relawan'],
            ['key' => 'volunteer_event', 'title' => 'Event DB', 'description' => 'Event description', 'form_url' => '', 'is_open' => '0', 'closed_description' => 'Pendaftaran event ditutup saat ini.', 'button_label' => 'Daftar event'],
            ['key' => 'collaboration', 'title' => 'Kolaborasi DB', 'description' => 'Collaboration description', 'form_url' => 'https://forms.google.com/collaboration', 'is_open' => '1', 'closed_description' => 'Kolaborasi tutup', 'button_label' => 'Ajukan kerja sama'],
        ];

        $this->put(route('admin.join-cards.update'), ['cards' => $cards])->assertRedirect(route('admin.join-cards.index'));
        $this->assertDatabaseHas('join_cards', ['key' => 'volunteer', 'title' => 'Relawan DB', 'form_url' => 'https://forms.google.com/volunteer']);
        $this->assertFalse(JoinCard::query()->where('key', 'volunteer_event')->firstOrFail()->is_open);
        $this->get('/join-us')->assertOk()->assertSee('Relawan DB')->assertSee('href="https://forms.google.com/volunteer"', false)->assertSee('Pendaftaran event ditutup saat ini.')->assertDontSee('href="https://forms.google.com/"', false);
    }
}
