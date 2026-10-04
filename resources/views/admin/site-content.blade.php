@extends('layouts.admin')
@section('title', 'Identitas & Konten')
@section('content')
<div class="admin-heading"><div><span class="eyebrow">KONTEN WEBSITE</span><h1>Identitas &amp; Konten</h1><p>Kelola statistik beranda, profil komunitas, kontak, dan visibilitas tautan sosial.</p></div><a class="btn btn-secondary" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat website ↗</a></div>
<form class="admin-panel admin-form site-editor" action="{{ route('admin.site-content.update') }}" method="POST">
    @csrf
    @method('PUT')
    <section>
        <h2>Statistik Komunitas</h2>
        <div class="editor-grid">
            @foreach($community['impact'] as $index => $metric)
                <label>{{ $metric['label'] }}<input name="impact[{{ $index }}][value]" type="number" min="0" max="10000000" value="{{ old('impact.'.$index.'.value', $metric['value']) }}" required></label>
            @endforeach
        </div>
    </section>
    <section>
        <h2>Profil Komunitas</h2>
        <div class="editor-grid">
            <label class="editor-wide">Nama komunitas<input name="profile[name]" value="{{ old('profile.name', $community['profile']['name'] ?? '') }}" required maxlength="255"></label>
            <label class="editor-wide">Pengantar beranda<textarea name="profile[home_intro]" rows="3" required>{{ old('profile.home_intro', $community['profile']['home_intro'] ?? '') }}</textarea></label>
            <label class="editor-wide">Tentang komunitas<textarea name="profile[about]" rows="4" required>{{ old('profile.about', $community['profile']['about'] ?? '') }}</textarea></label>
            <label class="editor-wide">Misi<textarea name="profile[mission]" rows="3" required>{{ old('profile.mission', $community['profile']['mission'] ?? '') }}</textarea></label>
        </div>
    </section>
    <section>
        <h2>Kontak Footer</h2>
        <div class="editor-grid">
            <label>WhatsApp<input name="contact[whatsapp]" type="tel" value="{{ old('contact.whatsapp', $community['contact']['whatsapp'] ?? '') }}" placeholder="62812..."></label>
            <label>Email<input name="contact[email]" type="email" value="{{ old('contact.email', $community['contact']['email'] ?? '') }}"></label>
            <label class="editor-wide">Lokasi<textarea name="contact[location]" rows="2" required>{{ old('contact.location', $community['contact']['location'] ?? '') }}</textarea></label>
        </div>
    </section>
    <section>
        <h2>Tautan Media Sosial</h2>
        <div class="editor-grid">
            @foreach(['youtube' => 'YouTube', 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'facebook' => 'Facebook'] as $key => $label)
                <fieldset class="social-setting">
                    <legend>{{ $label }}</legend>
                    <label>URL<input name="social[{{ $key }}]" type="url" value="{{ old('social.'.$key, $community['social'][$key] ?? '') }}" placeholder="https://"></label>
                    <input type="hidden" name="social_visibility[{{ $key }}]" value="0">
                    <label class="check-label"><input type="checkbox" name="social_visibility[{{ $key }}]" value="1" @checked((bool) old('social_visibility.'.$key, $community['social_visibility'][$key] ?? true))> Tampilkan ikon</label>
                </fieldset>
            @endforeach
        </div>
    </section>
    <div class="editor-actions"><button class="btn btn-primary" type="submit">Simpan perubahan</button></div>
</form>
@endsection