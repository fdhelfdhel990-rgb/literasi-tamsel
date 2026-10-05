<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JoinCardController;
use App\Http\Controllers\Admin\MediaPartnerController;
use App\Http\Controllers\Admin\PublicationController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\PublicMediaController;
use Illuminate\Support\Facades\Route;

Route::get('/media/{directory}/{filename}', [PublicMediaController::class, 'show'])
    ->where(['directory' => 'books|publications|partners|community', 'filename' => '[A-Za-z0-9_-]+\\.(?:jpe?g|png|webp)'])
    ->name('media.show');

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/about', [PublicSiteController::class, 'about'])->name('about');
Route::get('/publication', [PublicSiteController::class, 'publicationIndex'])->name('publication.index');
Route::get('/publication/{slug}', [PublicSiteController::class, 'publicationDetail'])->name('publication.show');
Route::get('/digital-library', [PublicSiteController::class, 'libraryIndex'])->name('library.index');
Route::get('/digital-library/{slug}', [PublicSiteController::class, 'libraryDetail'])->name('library.show');
Route::get('/join-us', [PublicSiteController::class, 'join'])->name('join');

Route::middleware('guest')->group(function (): void {
    Route::get('/admin/login', [AuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'store'])->name('admin.login.store');
});

Route::middleware(['auth', 'admin.active'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('publications', PublicationController::class)->except(['show']);
    Route::resource('books', BookController::class)->except(['show']);
    Route::resource('partners', MediaPartnerController::class)->except(['show']);
    Route::get('/site-content', [SiteContentController::class, 'edit'])->name('site-content.edit');
    Route::put('/site-content', [SiteContentController::class, 'update'])->name('site-content.update');
    Route::get('/join-cards', [JoinCardController::class, 'index'])->name('join-cards.index');
    Route::put('/join-cards', [JoinCardController::class, 'update'])->name('join-cards.update');
    Route::post('/users/transfer-super-admin', [UserController::class, 'transfer'])->name('users.transfer');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('/privileges', fn () => redirect()->route('admin.users.index'))->name('privileges.index');
    Route::get('/settings', fn () => redirect()->route('admin.site-content.edit'))->name('settings.index');
    Route::get('/pages', fn () => redirect()->route('admin.site-content.edit'))->name('pages.index');
});

Route::get('/admin-demo', fn () => redirect()->route('admin.dashboard'));
Route::get('/admin-demo/{path}', fn () => redirect()->route('admin.dashboard'))->where('path', '.*');
