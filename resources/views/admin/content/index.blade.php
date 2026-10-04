@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="admin-heading">
    <div><span class="eyebrow">MANAJEMEN WEBSITE</span><h1>{{ $title }}</h1><p>Data disimpan di database dan ditampilkan sesuai status publikasi.</p></div>
    <a class="btn btn-primary" href="{{ route('admin.'.$module.'.create') }}">+ {{ $createLabel }}</a>
</div>
<form class="admin-panel admin-module-tools" method="GET" action="{{ route('admin.'.$module.'.index') }}">
    <label><span class="sr-only">Cari</span><input name="q" type="search" value="{{ request('q') }}" placeholder="Cari data ..."></label>
    @if($module === 'publications')
        <label><span class="sr-only">Kategori</span><input name="category" value="{{ request('category') }}" placeholder="Kategori"></label>
        <label><span class="sr-only">Status</span><select name="status"><option value="">Semua status</option><option value="draft" @selected(request('status') === 'draft')>Draft</option><option value="published" @selected(request('status') === 'published')>Published</option></select></label>
    @elseif($module === 'books')
        <label><span class="sr-only">Genre</span><input name="genre" value="{{ request('genre') }}" placeholder="Genre"></label>
        <label><span class="sr-only">Penerbit</span><input name="publisher" value="{{ request('publisher') }}" placeholder="Penerbit"></label>
    @elseif($module === 'partners')
        <label><span class="sr-only">Visibilitas</span><select name="active"><option value="">Semua partner</option><option value="1" @selected(request('active') === '1')>Aktif</option><option value="0" @selected(request('active') === '0')>Nonaktif</option></select></label>
    @endif
    <button class="btn btn-secondary" type="submit">Cari</button>
</form>
<section class="admin-panel">
    <div class="table-scroll">
        <table class="data-table">
            <thead><tr>@foreach($columns as $key => $label)<th>{{ $label }}</th>@endforeach<th>Aksi</th></tr></thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        @foreach($columns as $key => $label)
                            <td>
                                @if(in_array($key, ['status', 'is_active', 'is_published'], true))
                                    @php($active = $key === 'status' ? $item->status === 'published' : (bool) $item->{$key})
                                    <span class="pill {{ $active ? 'published' : 'pending' }}">{{ $key === 'status' ? ucfirst($item->status) : ($active ? 'Aktif' : 'Nonaktif') }}</span>
                                @elseif($key === 'published_at')
                                    {{ $item->published_at?->locale('id')->translatedFormat('d F Y') ?? '—' }}
                                @elseif($key === 'title' || $key === 'name')
                                    <strong class="table-title">{{ $item->{$key} }}</strong>
                                @else
                                    {{ $item->{$key} ?: '—' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="table-actions">
                            @if($module === 'publications' && $item->status === 'published')<a href="{{ route('publication.show', $item->slug) }}" target="_blank" rel="noopener">Lihat</a>@endif
                            @if($module === 'books' && $item->is_published)<a href="{{ route('library.show', $item->slug) }}" target="_blank" rel="noopener">Lihat</a>@endif
                            <a href="{{ route('admin.'.$module.'.edit', $item) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.'.$module.'.destroy', $item) }}" onsubmit="return confirm('Hapus data ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button class="table-delete" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="table-empty" colspan="{{ count($columns) + 1 }}">Belum ada data yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</section>
@endsection