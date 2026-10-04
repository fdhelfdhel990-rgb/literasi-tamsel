<?php

namespace Tests\Feature;

use App\Models\MediaPartner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesAdminUsers;
use Tests\Concerns\CreatesTestImages;
use Tests\TestCase;

class MediaPartnerCrudTest extends TestCase
{
    use CreatesAdminUsers;
    use CreatesTestImages;
    use RefreshDatabase;

    public function test_partner_can_be_created_reordered_hidden_replaced_and_deleted(): void
    {
        Storage::fake('public');
        $this->actingAs($this->createAdmin(User::ROLE_ADMIN));

        $this->post(route('admin.partners.store'), [
            'name' => 'Test Media Partner',
            'url' => 'https://partner.example.test',
            'position' => 5,
            'is_active' => '1',
            'image' => $this->fakeImage('partner.png'),
        ])->assertRedirect(route('admin.partners.index'));

        $partner = MediaPartner::query()->where('name', 'Test Media Partner')->firstOrFail();
        $originalPath = $partner->image_path;
        Storage::disk('public')->assertExists($originalPath);
        $this->get(route('admin.partners.edit', $partner))->assertOk()->assertSee(Storage::disk('public')->url($originalPath));
        $this->get('/')->assertOk()->assertSee('Test Media Partner')->assertSee('https://partner.example.test');

        $this->put(route('admin.partners.update', $partner), [
            'name' => 'Test Media Partner',
            'url' => 'https://updated.example.test',
            'position' => 1,
            'is_active' => '0',
            'image' => $this->fakeImage('partner-new.png'),
        ])->assertRedirect(route('admin.partners.index'));

        $partner->refresh();
        $this->assertSame(1, $partner->position);
        $this->assertFalse($partner->is_active);
        Storage::disk('public')->assertMissing($originalPath);
        Storage::disk('public')->assertExists($partner->image_path);
        $this->get('/')->assertDontSee('Test Media Partner');

        $path = $partner->image_path;
        $this->delete(route('admin.partners.destroy', $partner))->assertRedirect(route('admin.partners.index'));
        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('media_partners', ['id' => $partner->id]);
    }
}