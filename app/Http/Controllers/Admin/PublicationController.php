<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublicationRequest;
use App\Models\Publication;
use Illuminate\Http\Request;
use Throwable;

class PublicationController extends Controller
{
    use StoresImages;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Publication::class);
        $items = Publication::query()
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q')->trim().'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('updated_at')->paginate(15)->withQueryString();

        return view('admin.content.index', [
            'items' => $items,
            'module' => 'publications',
            'title' => 'Publication',
            'createLabel' => 'Tambah publikasi',
            'columns' => ['title' => 'Judul', 'category' => 'Kategori', 'published_at' => 'Tanggal', 'status' => 'Status'],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Publication::class);

        return view('admin.content.form', ['module' => 'publications', 'title' => 'Tambah publikasi', 'item' => new Publication()]);
    }

    public function store(PublicationRequest $request)
    {
        $data = $request->validated();
        $image = $data['featured_image'] ?? null;
        unset($data['featured_image']);
        $data['created_by'] = $request->user()->id;
        $data['published_at'] = $data['published_at'] ?: ($data['status'] === 'published' ? now()->toDateString() : null);
        $data['featured_image_path'] = $image ? $this->storeImage($image, 'publications') : null;

        try {
            Publication::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredImage($data['featured_image_path']);
            throw $exception;
        }

        return redirect()->route('admin.publications.index')->with('status', 'Publikasi berhasil ditambahkan.');
    }

    public function edit(Publication $publication)
    {
        $this->authorize('update', $publication);

        return view('admin.content.form', [
            'module' => 'publications',
            'title' => 'Edit publikasi',
            'item' => $publication,
            'imageUrl' => $publication->featured_image_url,
        ]);
    }

    public function update(PublicationRequest $request, Publication $publication)
    {
        $data = $request->validated();
        $image = $data['featured_image'] ?? null;
        unset($data['featured_image']);
        $data['published_at'] = $data['published_at'] ?: ($data['status'] === 'published' ? now()->toDateString() : null);
        $oldPath = $publication->featured_image_path;
        $newPath = $image ? $this->storeImage($image, 'publications') : null;

        if ($newPath) {
            $data['featured_image_path'] = $newPath;
        }

        try {
            $publication->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredImage($newPath);
            throw $exception;
        }

        if ($newPath) {
            $this->deleteStoredImage($oldPath);
        }

        return redirect()->route('admin.publications.index')->with('status', 'Publikasi berhasil diperbarui.');
    }

    public function destroy(Publication $publication)
    {
        $this->authorize('delete', $publication);
        $path = $publication->featured_image_path;
        $publication->delete();
        $this->deleteStoredImage($path);

        return redirect()->route('admin.publications.index')->with('status', 'Publikasi berhasil dihapus.');
    }
}