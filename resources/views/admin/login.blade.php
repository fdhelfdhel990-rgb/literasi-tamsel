<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Masuk Admin — Literasi Tamsel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="demo-login-body">
    <main class="demo-login">
        <section class="login-aside">
            <img src="{{ asset('images/branding/logo.png') }}" alt="Logo Komunitas Literasi Remaja Tambun Selatan">
            <span>ADMIN WORKSPACE</span>
            <h1>Kelola cerita, buku, dan kegiatan komunitas.</h1>
            <p>Panel pengelolaan Komunitas Literasi Remaja Tambun Selatan.</p>
            <small class="login-copyright">© {{ date('Y') }} Komunitas Literasi Remaja Tambun Selatan</small>
        </section>
        <section class="login-main">
            <div class="login-form">
                <span class="eyebrow">SELAMAT DATANG KEMBALI</span>
                <h2>Masuk ke Admin</h2>
                <p>Gunakan akun administrator yang diberikan oleh Super Admin.</p>
                @if(session('status'))
                    <p class="login-status" role="status">{{ session('status') }}</p>
                @endif
                @if($errors->any())
                    <p class="login-error" role="alert">{{ $errors->first() }}</p>
                @endif
                <form action="{{ route('admin.login.store') }}" method="POST">
                    @csrf
                    <label for="email">Email
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                    </label>
                    <label for="password">Kata sandi
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                    </label>
                    <label class="remember-row" for="remember">
                        <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                        <span>Ingat saya</span>
                    </label>
                    <button class="btn btn-primary" type="submit">Masuk</button>
                </form>
            </div>
            <a class="login-return" href="{{ route('home') }}">← Kembali ke website publik</a>
        </section>
    </main>
</body>
</html>