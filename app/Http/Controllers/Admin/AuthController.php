<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $identifier = mb_strtolower(trim($request->string('identifier')->toString()));
        $key = Str::transliterate(Str::lower($identifier).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['identifier' => 'Terlalu banyak percobaan. Coba kembali dalam satu menit.']);
        }

        $user = User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($identifier): void {
                $query->whereRaw('LOWER(email) = ?', [$identifier])
                    ->orWhereRaw('LOWER(username) = ?', [$identifier]);
            })
            ->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['identifier' => 'Username/email atau kata sandi tidak valid.']);
        }

        if (! in_array($user->role, ['super_admin', 'admin', 'sub_admin'], true)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['identifier' => 'Username/email atau kata sandi tidak valid.']);
        }

        Auth::login($user, $request->boolean('remember'));
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
