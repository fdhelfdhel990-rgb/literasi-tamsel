<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\MediaPartner;
use App\Models\Publication;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'bookCount' => Book::query()->count(),
            'publicationCount' => Publication::query()->count(),
            'partnerCount' => MediaPartner::query()->count(),
            'adminCount' => User::query()->where('is_active', true)->count(),
        ]);
    }
}