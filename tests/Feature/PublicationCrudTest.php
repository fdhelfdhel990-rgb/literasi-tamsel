<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesAdminUsers;
use Tests\Concerns\CreatesTestImages;
use Tests\TestCase;

class PublicationCrudTest extends TestCase
{
    use CreatesAdminUsers;
    use CreatesTestImages;
    use RefreshDatabase;

    public function test_publication_can_be_created_edited_searched_published_and_deleted_with_images(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin(User::ROLE_ADMIN);
        $this->actingAs($admin);

        $this->post(route('admin.publications.store'), [
            'title' => 'Test Publication',
            'slug' => 'test-publication',
            'category' => 'Kegiatan',
            'published_at' => '2026-10-03',
            'excerpt' => 'Ringkasan artikel uji.',
            'content' => 'Isi artikel tersimpan dari database.',
            'status' => 'published',
            'featured_image' => $this->fakeImage('featured.png'),
        ])->assertRedirect(route('admin.publications.index'));

        $publication = Publication::query()->where('slug', 'test-publication')->firstOrFail();
        $originalPath = $publication->featured_image_path;
        Storage::disk('public')->assertExists($originalPath);
        $this->get(route('admin.publications.edit', $publication))->assertOk()->assertSee(Storage::disk('public')->url($originalPath));
        $this->get(route('admin.publications.index', ['q' => 'Test Publication']))->assertOk()->assertSee('Test Publication');
        $this->get(route('publication.show', $publication->slug))->assertOk()->assertSee('Isi artikel tersimpan dari database.')->assertSee(Storage::disk('public')->url($originalPath));

        $this->put(route('admin.publications.update', $publication), [
            'title' => 'Updated Publication',
            'slug' => 'test-publication',
            'category' => 'Liputan Media',
            'published_at' => '2026-10-03',
            'excerpt' => 'Ringkasan baru.',
            'content' => 'Artikel diperbarui.',
            'status' => 'published',
            'featured_image' => $this->fakeImage('replacement.png'),
        ])->assertRedirect(route('admin.publications.index'));

        $publication->refresh();
        $this->assertSame('Updated Publication', $publication->title);
        Storage::disk('public')->assertMissing($originalPath);
        Storage::disk('public')->assertExists($publication->featured_image_path);
        $this->get('/publication?q=Updated')->assertOk()->assertSee('Updated Publication');

        $replacementPath = $publication->featured_image_path;
        $this->delete(route('admin.publications.destroy', $publication))->assertRedirect(route('admin.publications.index'));
        $this->assertDatabaseMissing('publications', ['id' => $publication->id]);
        Storage::disk('public')->assertMissing($replacementPath);
    }

    public function test_draft_publication_is_not_available_on_public_routes(): void
    {
        $publication = Publication::query()->create([
            'title' => 'Private Draft', 'slug' => 'private-draft', 'category' => 'Kegiatan',
            'excerpt' => 'Draft excerpt', 'content' => 'Draft content', 'status' => 'draft',
        ]);

        $this->get(route('publication.show', $publication->slug))->assertNotFound();
    }
}