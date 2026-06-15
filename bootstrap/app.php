<?php

use App\Http\Middleware\setLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin') // All routes will start with /admin/
                ->name('admin.')  // Route names will be admin.branches.index
                ->group(base_path('routes/admin.php'));
        },
    )
    // ->withMiddleware(function (Middleware $middleware) {
    //     //
    // })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('member/dashboard') || $request->is('member/dashboard/*')) {
                return route('frontend.member.login');
            }

            return route('login');
        });
        $middleware->web(append: [
            setLocale::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'payment/*',
        ]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'setLocale' => \App\Http\Middleware\setLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
