<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return Auth::check() && Auth::user()?->is_active
            ? redirect()->route('admin.dashboard')
            : view('admin.login');
    }

    public function store(AdminLoginRequest $request)
    {
        $email = mb_strtolower(trim($request->string('email')->toString()));
        $key = Str::transliterate(Str::lower($email).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Terlalu banyak percobaan. Coba kembali dalam satu menit.']);
        }

        if (! Auth::attempt(['email' => $email, 'password' => $request->string('password')->toString(), 'is_active' => true], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak valid.']);
        }

        if (! in_array(Auth::user()->role, ['super_admin', 'admin', 'sub_admin'], true)) {
            Auth::logout();
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['email' => 'Akun tidak memiliki akses panel.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}