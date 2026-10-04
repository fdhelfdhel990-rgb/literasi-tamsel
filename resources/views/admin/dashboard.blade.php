@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="admin-heading">
    <div><span class="eyebrow">RINGKASAN KOMUNITAS</span><h1>Selamat datang di workspace.</h1><p>Konten yang dikelola melalui database komunitas.</p></div>
    <span class="admin-date">{{ now()->locale('id')->translatedFormat('d F Y') }}</span>
</div>
<div class="admin-stats">
    <article><span>BK</span><small>Koleksi Buku</small><strong>{{ $bookCount }}</strong>@if(auth()->user()->hasPermission('books.manage'))<a href="{{ route('admin.books.index') }}">Kelola katalog</a>@endif</article>
    <article><span>PB</span><small>Publication</small><strong>{{ $publicationCount }}</strong>@if(auth()->user()->hasPermission('publications.manage'))<a href="{{ route('admin.publications.index') }}">Kelola publikasi</a>@endif</article>
    <article><span>MP</span><small>Media Partner aktif</small><strong>{{ $partnerCount }}</strong>@if(auth()->user()->hasPermission('partners.manage'))<a href="{{ route('admin.partners.index') }}">Kelola mitra</a>@endif</article>
    <article><span>AD</span><small>Akun admin aktif</small><strong>{{ $adminCount }}</strong>@if(auth()->user()->isSuperAdmin())<a href="{{ route('admin.users.index') }}">Kelola akses</a>@else<span>Workspace</span>@endif</article>
</div>
<div class="admin-dash-grid">
    <section class="admin-panel">
        <div class="panel-heading"><div><h2>Manajemen Konten</h2><p>Pilih modul untuk memperbarui situs publik.</p></div></div>
        <div class="quick-action-list">
            @if(auth()->user()->hasPermission('publications.manage'))<a href="{{ route('admin.publications.index') }}">Publication <span>Kelola artikel dan status tayang</span></a>@endif
            @if(auth()->user()->hasPermission('books.manage'))<a href="{{ route('admin.books.index') }}">Digital Library <span>Kelola metadata dan sampul buku</span></a>@endif
            @if(auth()->user()->hasPermission('join_cards.manage'))<a href="{{ route('admin.join-cards.index') }}">Join Us <span>Kelola kartu dan status pendaftaran</span></a>@endif
            @if(auth()->user()->hasPermission('site_content.manage'))<a href="{{ route('admin.site-content.edit') }}">Identitas &amp; Konten <span>Kelola statistik dan informasi footer</span></a>@endif
        </div>
    </section>
    <section class="admin-panel">
        <div class="panel-heading"><div><h2>Statistik Homepage</h2><p>Nilai ini tampil pada section statistik beranda.</p></div></div>
        <div class="quick-action-list">
            @foreach($impact as $metric)
                <span>{{ $metric['label'] ?? 'Statistik' }} <b>{{ ($metric['prefix'] ?? '').($metric['value'] ?? 0).($metric['suffix'] ?? '') }}</b> {{ $metric['unit'] ?? '' }}</span>
            @endforeach
        </div>
        @if(auth()->user()->hasPermission('site_content.manage'))
            <a class="btn btn-secondary" href="{{ route('admin.site-content.edit') }}">Edit statistik</a>
        @endif
    </section>
</div>
@endsection
