<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesAdminUsers;
use Tests\Concerns\CreatesTestImages;
use Tests\TestCase;

class BookCrudTest extends TestCase
{
    use CreatesAdminUsers;
    use CreatesTestImages;
    use RefreshDatabase;

    public function test_book_can_be_created_edited_filtered_and_cover_replaced(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin(User::ROLE_ADMIN);
        $this->actingAs($admin);

        $this->post(route('admin.books.store'), [
            'title' => 'Testing Library Book',
            'slug' => 'testing-library-book',
            'author' => 'Test Author',
            'publisher' => 'Test Press',
            'genre' => 'Novel',
            'description' => 'Book summary for testing.',
            'isbn' => 'TEST-9780000001',
            'is_published' => '1',
            'cover' => $this->fakeImage('cover.png'),
        ])->assertRedirect(route('admin.books.index'));

        $book = Book::query()->where('slug', 'testing-library-book')->firstOrFail();
        $originalPath = $book->cover_path;
        Storage::disk('public')->assertExists($originalPath);
        $this->get(route('admin.books.edit', $book))->assertOk()->assertSee(Storage::disk('public')->url($originalPath));
        $this->get(route('admin.books.index', ['q' => 'Test Author']))->assertOk()->assertSee('Testing Library Book');
        $this->get(route('library.show', $book->slug))->assertOk()->assertSee(Storage::disk('public')->url($originalPath))->assertSee('Test Author');

        $this->put(route('admin.books.update', $book), [
            'title' => 'Testing Library Book',
            'slug' => 'testing-library-book',
            'author' => 'Test Author Updated',
            'publisher' => 'Test Press',
            'genre' => 'Novel',
            'description' => 'Updated description.',
            'isbn' => 'TEST-9780000001',
            'is_published' => '1',
            'cover' => $this->fakeImage('replacement.webp'),
        ])->assertRedirect(route('admin.books.index'));

        $book->refresh();
        $this->assertSame('Test Author Updated', $book->author);
        Storage::disk('public')->assertMissing($originalPath);
        Storage::disk('public')->assertExists($book->cover_path);
        $this->get('/digital-library?q=Testing')->assertOk()->assertSee('Testing Library Book');
    }

    public function test_unpublished_book_is_hidden_from_public_catalog(): void
    {
        Book::query()->create([
            'title' => 'Hidden Book', 'slug' => 'hidden-book', 'author' => 'Author',
            'publisher' => 'Press', 'genre' => 'Novel', 'is_published' => false,
        ]);

        $this->get('/digital-library')->assertOk()->assertDontSee('Hidden Book');
        $this->get('/digital-library/hidden-book')->assertNotFound();
    }
}