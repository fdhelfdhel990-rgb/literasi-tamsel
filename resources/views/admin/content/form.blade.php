@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="admin-heading"><div><span class="eyebrow">MANAJEMEN WEBSITE</span><h1>{{ $title }}</h1><p>Perubahan akan disimpan ke database.</p></div></div>
<form class="admin-panel admin-form" method="POST" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.'.$module.'.update', $item) : route('admin.'.$module.'.store') }}">
    @csrf
    @if($item->exists) @method('PUT') @endif
    <div class="editor-grid">
        @if($module === 'publications')
            <label>Judul<input name="title" value="{{ old('title', $item->title) }}" required maxlength="255">@error('title')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Slug<input name="slug" value="{{ old('slug', $item->slug) }}" required maxlength="255">@error('slug')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Kategori<input name="category" value="{{ old('category', $item->category) }}" required maxlength="80"></label>
            <label>Tanggal publikasi<input name="published_at" type="date" value="{{ old('published_at', $item->published_at?->format('Y-m-d')) }}"></label>
            <label>Status<select name="status"><option value="draft" @selected(old('status', $item->status ?: 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $item->status) === 'published')>Published</option></select></label>
            <label class="editor-wide">Ringkasan<textarea name="excerpt" rows="3" required>{{ old('excerpt', $item->excerpt) }}</textarea></label>
            <label class="editor-wide">Isi artikel<textarea name="content" rows="12" required>{{ old('content', $item->content) }}</textarea></label>
            <label class="editor-wide">Gambar unggulan<input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp" data-image-input>@error('featured_image')<small class="field-error">{{ $message }}</small>@enderror</label>
        @elseif($module === 'books')
            <label>Judul<input name="title" value="{{ old('title', $item->title) }}" required maxlength="255"></label>
            <label>Slug<input name="slug" value="{{ old('slug', $item->slug) }}" required maxlength="255"></label>
            <label>Penulis<input name="author" value="{{ old('author', $item->author) }}" required maxlength="255"></label>
            <label>Penerbit<input name="publisher" value="{{ old('publisher', $item->publisher) }}" required maxlength="255"></label>
            <label>Genre<input name="genre" value="{{ old('genre', $item->genre) }}" required maxlength="100"></label>
            <label>ISBN (opsional)<input name="isbn" value="{{ old('isbn', $item->isbn) }}" maxlength="32"></label>
            <label class="editor-wide">Deskripsi<textarea name="description" rows="5">{{ old('description', $item->description) }}</textarea></label>
            <input type="hidden" name="is_published" value="0">
            <label class="check-label editor-wide"><input type="checkbox" name="is_published" value="1" @checked((bool) old('is_published', $item->is_published ?? true))> Tampilkan pada katalog publik</label>
            <label class="editor-wide">Sampul buku<input type="file" name="cover" accept="image/jpeg,image/png,image/webp" data-image-input>@error('cover')<small class="field-error">{{ $message }}</small>@enderror</label>
        @else
            <label>Nama partner<input name="name" value="{{ old('name', $item->name) }}" required maxlength="255"></label>
            <label>Tautan partner<input name="url" type="url" value="{{ old('url', $item->url) }}" placeholder="https://"></label>
            <label>Urutan tampil<input name="position" type="number" min="0" value="{{ old('position', $item->position ?? 0) }}" required></label>
            <input type="hidden" name="is_active" value="0">
            <label class="check-label"><input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $item->is_active ?? true))> Tampilkan di website</label>
            <label class="editor-wide">Logo partner<input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-image-input>@error('image')<small class="field-error">{{ $message }}</small>@enderror</label>
        @endif
        <div class="editor-wide image-preview-wrap" @if(empty($imageUrl)) hidden @endif>
            <span class="input-label">Preview gambar saat ini / pilihan</span>
            <img class="admin-image-preview" data-image-preview src="{{ $imageUrl ?? '' }}" alt="Preview gambar">
        </div>
    </div>
    <div class="editor-actions"><button class="btn btn-primary" type="submit">Simpan</button><a class="btn btn-secondary" href="{{ route('admin.'.$module.'.index') }}">Batal</a></div>
</form>
@endsection