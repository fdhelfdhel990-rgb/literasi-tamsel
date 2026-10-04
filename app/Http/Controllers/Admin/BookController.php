<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Throwable;

class BookController extends Controller
{
    use StoresImages;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Book::class);
        $items = Book::query()
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request): void {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where('title', 'like', $term)->orWhere('author', 'like', $term);
            }))
            ->when($request->filled('genre'), fn ($query) => $query->where('genre', $request->string('genre')))
            ->when($request->filled('publisher'), fn ($query) => $query->where('publisher', $request->string('publisher')))
            ->orderByDesc('updated_at')->paginate(15)->withQueryString();

        return view('admin.content.index', [
            'items' => $items,
            'module' => 'books',
            'title' => 'Digital Library',
            'createLabel' => 'Tambah buku',
            'columns' => ['title' => 'Judul', 'author' => 'Penulis', 'publisher' => 'Penerbit', 'genre' => 'Genre', 'is_published' => 'Status'],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Book::class);

        return view('admin.content.form', ['module' => 'books', 'title' => 'Tambah buku', 'item' => new Book(), 'imageUrl' => null]);
    }

    public function store(BookRequest $request)
    {
        $data = $request->validated();
        $cover = $data['cover'] ?? null;
        unset($data['cover']);
        $data['created_by'] = $request->user()->id;
        $data['cover_path'] = $cover ? $this->storeImage($cover, 'books') : null;

        try {
            Book::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredImage($data['cover_path']);
            throw $exception;
        }

        return redirect()->route('admin.books.index')->with('status', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        return view('admin.content.form', ['module' => 'books', 'title' => 'Edit buku', 'item' => $book, 'imageUrl' => $book->cover_url]);
    }

    public function update(BookRequest $request, Book $book)
    {
        $data = $request->validated();
        $cover = $data['cover'] ?? null;
        unset($data['cover']);
        $oldPath = $book->cover_path;
        $newPath = $cover ? $this->storeImage($cover, 'books') : null;

        if ($newPath) {
            $data['cover_path'] = $newPath;
        }

        try {
            $book->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredImage($newPath);
            throw $exception;
        }

        if ($newPath) {
            $this->deleteStoredImage($oldPath);
        }

        return redirect()->route('admin.books.index')->with('status', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $path = $book->cover_path;
        $book->delete();
        $this->deleteStoredImage($path);

        return redirect()->route('admin.books.index')->with('status', 'Buku berhasil dihapus.');
    }
}