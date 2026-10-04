<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - PAD Literasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    @php($adminUser = auth()->user())
    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="Dashboard admin">
                <img src="{{ asset('images/branding/logo.png') }}" alt="Logo Komunitas Literasi Remaja Tambun Selatan">
            </a>
            <nav class="admin-nav" aria-label="Navigasi panel admin">
                <div class="admin-nav-title">UMUM</div>
                <a @class(['selected' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}"><span>01</span>Dashboard</a>
                <div class="admin-nav-title">MANAJEMEN KONTEN</div>
                @if($adminUser->hasPermission('publications.manage'))
                    <a @class(['selected' => request()->routeIs('admin.publications.*')]) href="{{ route('admin.publications.index') }}"><span>02</span>Publication</a>
                @endif
                @if($adminUser->hasPermission('books.manage'))
                    <a @class(['selected' => request()->routeIs('admin.books.*')]) href="{{ route('admin.books.index') }}"><span>03</span>Digital Library</a>
                @endif
                @if($adminUser->hasPermission('join_cards.manage'))
                    <a @class(['selected' => request()->routeIs('admin.join-cards.*')]) href="{{ route('admin.join-cards.index') }}"><span>04</span>Join Us</a>
                @endif
                @if($adminUser->hasPermission('partners.manage'))
                    <a @class(['selected' => request()->routeIs('admin.partners.*')]) href="{{ route('admin.partners.index') }}"><span>05</span>Media Partner</a>
                @endif
                @if($adminUser->hasPermission('site_content.manage'))
                    <a @class(['selected' => request()->routeIs('admin.site-content.*')]) href="{{ route('admin.site-content.edit') }}"><span>06</span>Identitas &amp; Konten</a>
                @endif
                @if($adminUser->isSuperAdmin())
                    <div class="admin-nav-title">PENGATURAN AKSES</div>
                    <a @class(['selected' => request()->routeIs('admin.users.*')]) href="{{ route('admin.users.index') }}"><span>07</span>Kelola Admin &amp; Izin</a>
                @endif
            </nav>
            <div class="admin-sidebar-bottom">
                <a href="{{ route('home') }}">Lihat website publik</a>
            </div>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button id="adminMenuBtn" type="button" aria-label="Buka sidebar">&#9776;</button>
                    <span>Workspace / <b>@yield('title', 'Dashboard')</b></span>
                </div>
                <div class="admin-topbar-right">
                    <span class="admin-avatar">{{ mb_strtoupper(mb_substr($adminUser->name, 0, 1)) }}</span>
                    <span class="admin-identity"><b>{{ $adminUser->name }}</b><small>{{ str_replace('_', ' ', ucfirst($adminUser->role)) }}</small></span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-secondary" type="submit">Keluar</button>
                    </form>
                </div>
            </header>
            <main class="admin-content">
                @if(session('status'))<p class="form-status" role="status">{{ session('status') }}</p>@endif
                @yield('content')
            </main>
        </div>
    </div>
    <div class="toast" id="siteToast" role="status" aria-live="polite"></div>
</body>
</html>
