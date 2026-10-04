<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\JoinCard;
use App\Models\MediaPartner;
use App\Models\Publication;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    public function home()
    {
        return view('public.home', [
            'community' => $this->community(),
            'partners' => MediaPartner::query()->where('is_active', true)->orderBy('position')->get()->map->toPublicArray()->all(),
            'posts' => Publication::query()->where('status', 'published')->orderByDesc('published_at')->limit(6)->get()->map->toPublicArray()->all(),
        ]);
    }

    public function about()
    {
        return view('public.about', ['community' => $this->community()]);
    }

    public function publicationIndex(Request $request)
    {
        $posts = Publication::query()
            ->where('status', 'published')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request): void {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where('title', 'like', $term)->orWhere('excerpt', 'like', $term);
            }))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->orderByDesc('published_at')
            ->get()
            ->map->toPublicArray()
            ->all();

        return view('public.publication', [
            'posts' => $posts,
            'categories' => Publication::query()->where('status', 'published')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function publicationDetail(string $slug)
    {
        $publication = Publication::query()->where('status', 'published')->where('slug', $slug)->firstOrFail();
        $posts = Publication::query()->where('status', 'published')->orderByDesc('published_at')->get()->map->toPublicArray()->all();

        return view('public.publication-detail', ['posts' => $posts, 'slug' => $publication->slug]);
    }

    public function libraryIndex(Request $request)
    {
        $books = Book::query()
            ->where('is_published', true)
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request): void {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where('title', 'like', $term)->orWhere('author', 'like', $term);
            }))
            ->when($request->filled('genre'), fn ($query) => $query->where('genre', $request->string('genre')))
            ->when($request->filled('publisher'), fn ($query) => $query->where('publisher', $request->string('publisher')))
            ->orderBy('title')
            ->get()
            ->map->toPublicArray()
            ->all();

        return view('public.library', [
            'books' => $books,
            'genres' => collect(['Semua'])->concat(Book::query()->where('is_published', true)->distinct()->orderBy('genre')->pluck('genre')->reject(fn ($genre) => $genre === 'Semua')),
            'publishers' => Book::query()->where('is_published', true)->distinct()->orderBy('publisher')->pluck('publisher'),
        ]);
    }

    public function libraryDetail(string $slug)
    {
        $book = Book::query()->where('is_published', true)->where('slug', $slug)->firstOrFail();

        return view('public.library-detail', ['books' => [$book->toPublicArray()], 'slug' => $slug]);
    }

    public function join()
    {
        return view('public.join', [
            'joinCards' => JoinCard::query()->orderBy('position')->get()->map->toPublicArray()->all(),
        ]);
    }

    private function community(): array
    {
        return SiteSetting::valueFor('community', [
            'impact' => [],
            'contact' => ['whatsapp' => '', 'email' => '', 'location' => ''],
            'social' => [],
            'social_visibility' => [],
            'profile' => ['name' => 'Komunitas Literasi Remaja Tambun Selatan', 'home_intro' => '', 'about' => '', 'mission' => ''],
        ]);
    }
}