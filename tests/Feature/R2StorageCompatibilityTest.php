<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesAdminUsers;
use Tests\Concerns\CreatesTestImages;
use Tests\TestCase;

class R2StorageCompatibilityTest extends TestCase
{
    use CreatesAdminUsers;
    use CreatesTestImages;
    use RefreshDatabase;

    public function test_cms_upload_update_and_delete_uses_configured_r2_disk(): void
    {
        config([
            'filesystems.default' => 'r2',
            'filesystems.disks.r2.url' => 'https://media.example.test',
        ]);

        Storage::fake('r2');

        $this->actingAs($this->createAdmin(User::ROLE_ADMIN));

        $this->post(route('admin.publications.store'), [
            'title' => 'R2 Publication',
            'slug' => 'r2-publication',
            'category' => 'Kegiatan',
            'published_at' => '2026-10-04',
            'excerpt' => 'Ringkasan upload R2.',
            'content' => 'Konten upload R2.',
            'status' => 'published',
            'featured_image' => $this->fakeImage('r2-featured.png'),
        ])->assertRedirect(route('admin.publications.index'));

        $publication = Publication::query()->where('slug', 'r2-publication')->firstOrFail();
        $originalPath = $publication->featured_image_path;

        $this->assertStringStartsWith('publications/', $originalPath);
        Storage::disk('r2')->assertExists($originalPath);

        $this->put(route('admin.publications.update', $publication), [
            'title' => 'R2 Publication Updated',
            'slug' => 'r2-publication',
            'category' => 'Kegiatan',
            'published_at' => '2026-10-04',
            'excerpt' => 'Ringkasan upload R2 terbaru.',
            'content' => 'Konten upload R2 terbaru.',
            'status' => 'published',
            'featured_image' => $this->fakeImage('r2-replacement.webp'),
        ])->assertRedirect(route('admin.publications.index'));

        $publication->refresh();
        Storage::disk('r2')->assertMissing($originalPath);
        Storage::disk('r2')->assertExists($publication->featured_image_path);

        $replacementPath = $publication->featured_image_path;
        $this->delete(route('admin.publications.destroy', $publication))->assertRedirect(route('admin.publications.index'));

        Storage::disk('r2')->assertMissing($replacementPath);
    }

    public function test_render_health_route_is_available(): void
    {
        $this->get('/up')->assertOk()->assertSee('OK');
    }
}
