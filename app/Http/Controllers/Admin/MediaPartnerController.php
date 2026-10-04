<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaPartnerRequest;
use App\Models\MediaPartner;
use Illuminate\Http\Request;
use Throwable;

class MediaPartnerController extends Controller
{
    use StoresImages;

    public function index(Request $request)
    {
        $this->authorize('viewAny', MediaPartner::class);

        return view('admin.content.index', [
            'items' => MediaPartner::query()
                ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q')->trim().'%'))
                ->when($request->has('active'), fn ($query) => $query->where('is_active', $request->boolean('active')))
                ->orderBy('position')->paginate(20)->withQueryString(),
            'module' => 'partners',
            'title' => 'Media Partner',
            'createLabel' => 'Tambah partner',
            'columns' => ['name' => 'Nama', 'position' => 'Urutan', 'is_active' => 'Status'],
        ]);
    }

    public function create()
    {
        $this->authorize('create', MediaPartner::class);

        return view('admin.content.form', ['module' => 'partners', 'title' => 'Tambah partner', 'item' => new MediaPartner(), 'imageUrl' => null]);
    }

    public function store(MediaPartnerRequest $request)
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);
        $data['created_by'] = $request->user()->id;
        $data['image_path'] = $image ? $this->storeImage($image, 'partners') : null;

        try {
            MediaPartner::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredImage($data['image_path']);
            throw $exception;
        }

        return redirect()->route('admin.partners.index')->with('status', 'Partner berhasil ditambahkan.');
    }

    public function edit(MediaPartner $partner)
    {
        $this->authorize('update', $partner);

        return view('admin.content.form', ['module' => 'partners', 'title' => 'Edit partner', 'item' => $partner, 'imageUrl' => $partner->image_url]);
    }

    public function update(MediaPartnerRequest $request, MediaPartner $partner)
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);
        $oldPath = $partner->image_path;
        $newPath = $image ? $this->storeImage($image, 'partners') : null;
        if ($newPath) {
            $data['image_path'] = $newPath;
        }

        try {
            $partner->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredImage($newPath);
            throw $exception;
        }

        if ($newPath) {
            $this->deleteStoredImage($oldPath);
        }

        return redirect()->route('admin.partners.index')->with('status', 'Partner berhasil diperbarui.');
    }

    public function destroy(MediaPartner $partner)
    {
        $this->authorize('delete', $partner);
        $path = $partner->image_path;
        $partner->delete();
        $this->deleteStoredImage($path);

        return redirect()->route('admin.partners.index')->with('status', 'Partner berhasil dihapus.');
    }
}