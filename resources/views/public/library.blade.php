@extends('layouts.public')
@section('title', 'Digital Library')
@section('content')
<section class="page-intro library-intro">
    <div class="container">
        <span class="eyebrow">KOLEKSI BUKU</span>
        <h1>Digital Library</h1>
        <p>Jelajahi katalog koleksi bacaan komunitas.</p>
    </div>
</section>
<section class="section library-section" data-library>
    <div class="container library-layout">
        <aside class="library-filters">
            <div class="filters-heading">
                <h2>Filter Buku</h2>
                <button id="resetFilters" type="button">Reset</button>
            </div>
            <label class="input-label" for="bookSearch">Pencarian</label>
            <input id="bookSearch" placeholder="Cari buku atau penulis ..." type="search">
            <fieldset>
                <legend>Ragam / Genre</legend>
                @foreach($genres as $genre)
                    <label class="radio-line"><input type="radio" name="genre" value="{{ $genre }}" @checked($genre === 'Semua')><span>{{ $genre }}</span></label>
                @endforeach
            </fieldset>
            <fieldset>
                <legend>Penerbit</legend>
                <select id="publisherFilter">
                    <option value="">Semua penerbit</option>
                    @foreach($publishers as $publisher)
                        <option>{{ $publisher }}</option>
                    @endforeach
                </select>
            </fieldset>
        </aside>
        <div class="books-area">
            <div class="library-toolbar">
                <span><strong id="bookCount">{{ count($books) }}</strong> koleksi ditemukan</span>
                <label>Urutkan <select id="bookSort"><option value="default">Pilihan katalog</option><option value="az">Judul A–Z</option><option value="za">Judul Z–A</option></select></label>
            </div>
            <div class="books-grid" id="bookGrid">
                @foreach($books as $book)
                    <div class="filter-book" data-title="{{ mb_strtolower($book['title'].' '.$book['author']) }}" data-genre="{{ $book['genre'] }}" data-publisher="{{ $book['publisher'] }}"><x-book-card :book="$book"/></div>
                @endforeach
            </div>
            <p class="empty-state" id="bookEmpty" hidden>Belum ada buku yang sesuai dengan filter.</p>
            <div class="pagination" id="bookPagination" aria-label="Navigasi halaman"></div>
        </div>
    </div>
</section>
@endsection