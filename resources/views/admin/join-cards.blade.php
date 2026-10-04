@extends('layouts.admin')
@section('title', 'Join Us')
@section('content')
<div class="admin-heading"><div><span class="eyebrow">KONTEN WEBSITE</span><h1>Join Us</h1><p>Atur informasi, Google Form, dan status untuk ketiga kartu pendaftaran.</p></div></div>
<form class="admin-panel admin-form site-editor" action="{{ route('admin.join-cards.update') }}" method="POST">
    @csrf
    @method('PUT')
    @foreach($cards as $index => $card)
        <fieldset class="join-card-editor">
            <legend>{{ $card->title }}</legend>
            <input type="hidden" name="cards[{{ $index }}][key]" value="{{ $card->key }}">
            <div class="editor-grid">
                <label>Judul<input name="cards[{{ $index }}][title]" value="{{ old('cards.'.$index.'.title', $card->title) }}" required maxlength="255"></label>
                <label>Label tombol<input name="cards[{{ $index }}][button_label]" value="{{ old('cards.'.$index.'.button_label', $card->button_label) }}" required maxlength="100"></label>
                <label class="editor-wide">Deskripsi<textarea name="cards[{{ $index }}][description]" rows="3" required>{{ old('cards.'.$index.'.description', $card->description) }}</textarea></label>
                <label class="editor-wide">URL Google Form<input name="cards[{{ $index }}][form_url]" type="url" value="{{ old('cards.'.$index.'.form_url', $card->form_url) }}" placeholder="https://forms.google.com/..."></label>
                <label class="editor-wide">Pesan saat pendaftaran ditutup<textarea name="cards[{{ $index }}][closed_description]" rows="2" required>{{ old('cards.'.$index.'.closed_description', $card->closed_description) }}</textarea></label>
                <input type="hidden" name="cards[{{ $index }}][is_open]" value="0">
                <label class="check-label editor-wide"><input type="checkbox" name="cards[{{ $index }}][is_open]" value="1" @checked((bool) old('cards.'.$index.'.is_open', $card->is_open))> Pendaftaran dibuka</label>
            </div>
        </fieldset>
    @endforeach
    <div class="editor-actions"><button class="btn btn-primary" type="submit">Simpan kartu Join Us</button></div>
</form>
@endsection