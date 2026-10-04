<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Komunitas Literasi Remaja Tambun Selatan - Gerobak Angkasa dan akses bacaan anak.">
    <title>@yield('title', 'Beranda') - Komunitas Literasi Remaja Tambun Selatan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="site-body">
    <a class="skip-link" href="#main">Langsung ke konten</a>
    <x-site-header/>
    <main id="main">@yield('content')</main>
    <x-site-footer :community="$community ?? []"/>
    <div class="toast" id="siteToast" role="status" aria-live="polite"></div>
</body>
</html>
