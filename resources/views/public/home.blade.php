@extends('layouts.public')
@section('title', 'Beranda')
@section('content')
<section class="hero">
    <img class="hero-photo" src="{{ $community['profile']['hero_image_url'] ?? asset('images/community/hero.jpg') }}" alt="Kegiatan Komunitas Literasi Remaja Tambun Selatan">
    <div class="container hero-content">
        <h1>{{ $community['profile']['name'] ?? 'Komunitas Literasi Remaja Tambun Selatan' }}</h1>
        <p>{{ $community['profile']['home_intro'] ?? '' }}</p>
        <div class="hero-actions"><a class="btn btn-white" href="{{ route('about') }}">About Us</a><a class="btn btn-outline-light" href="{{ route('join') }}">Join Us</a></div>
    </div>
</section>
<section class="section impact-section">
    <div class="container">
        <div class="section-heading center"><p class="impact-overline">Jejak &amp; Capaian Nyata</p><h2>Data Komunitas Berdasarkan Fakta Lapangan</h2></div>
        <div class="impact-grid">
            @foreach($community['impact'] as $metric)
                <div><p class="impact-label">{{ $metric['label'] }}</p><strong class="impact-number" data-count="{{ $metric['value'] }}" data-prefix="{{ $metric['prefix'] }}" data-suffix="{{ $metric['suffix'] }}">{{ $metric['prefix'] }}{{ $metric['value'] }}{{ $metric['suffix'] }}</strong><p class="impact-unit">{{ $metric['unit'] }}</p></div>
            @endforeach
        </div>
    </div>
</section>
<section class="section partners-section">
    <div class="container">
        <div class="section-heading center"><h2>Media Partner</h2></div>
        @if(count($partners))
            <div class="partner-carousel" data-partner-carousel>
                <button class="partner-arrow partner-arrow-prev" type="button" data-partner-prev aria-label="Geser mitra ke kiri">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <div class="partner-viewport" tabindex="0">
                    <div class="partner-strip" data-partner-strip>
                        @foreach($partners as $partner)
                            @if(!empty($partner['url']))<a class="partner-logo" href="{{ $partner['url'] }}" target="_blank" rel="noopener noreferrer">@else<div class="partner-logo">@endif
                                @if(!empty($partner['image']))
                                    <img src="{{ $partner['image'] }}" alt="{{ $partner['name'] }}" data-image-fallback data-fallback-class="partner-image-fallback" data-fallback-text="{{ $partner['name'] }}">
                                @else
                                    <span>{{ $partner['name'] }}</span>
                                @endif
                            @if(!empty($partner['url']))</a>@else</div>@endif
                        @endforeach
                    </div>
                </div>
                <button class="partner-arrow partner-arrow-next" type="button" data-partner-next aria-label="Geser mitra ke kanan">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>
        @else
            <p class="empty-state partner-empty">Media partner akan ditampilkan setelah data tersedia.</p>
        @endif
    </div>
</section>
<section class="section section-news">
    <div class="container">
        <div class="section-heading center"><h2>Publikasi &amp; Kabar Kegiatan</h2><p>Dokumentasi perjalanan, artikel edukatif, dan liputan berita komunitas.</p></div>
        <div class="post-grid">@foreach(array_slice($posts, 0, 3) as $post)<x-post-card :post="$post"/>@endforeach</div>
        <div class="center-action"><a class="btn btn-primary icon-link" href="{{ route('publication.index') }}">Lihat Publikasi <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a></div>
    </div>
</section>
@endsection
