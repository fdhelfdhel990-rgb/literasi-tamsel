<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureAdminActive;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        then: function (): void {
            Route::get('/up', fn () => response('OK', 200, ['Content-Type' => 'text/plain']))->name('health');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Render terminates HTTPS at its reverse proxy; honor forwarded scheme.
        $middleware->trustProxies(at: '*');
        $middleware->alias(['admin.active' => EnsureAdminActive::class]);
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {})->create();
