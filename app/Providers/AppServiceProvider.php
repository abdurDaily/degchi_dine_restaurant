<?php

namespace App\Providers;

use App\Support\SeoSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SeoSettings::class, fn () => new SeoSettings());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability)
        {
            return $user->hasRole('Super Admin') ? true : null;
        });

        View::composer('frontend.layout', function ($view) {
            $view->with('seoSettings', app(SeoSettings::class));
        });

        $this->configureRateLimiting();
    }

    /**
     * Named limiters for the public write endpoints. Login throttling stays
     * with Laravel's authentication throttling so attempts are not counted
     * twice and are cleared after a successful login.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $message = 'Too many registration attempts. Please wait a minute and try again.';

                    if ($request->expectsJson()) {
                        return response()->json(['message' => $message], 429, $headers);
                    }

                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['registration' => $message])
                        ->withHeaders($headers);
                });
        });

        RateLimiter::for('membership-application', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $message = 'Too many membership applications. Please wait a minute and try again.';

                    if ($request->expectsJson()) {
                        return response()->json(['message' => $message], 429, $headers);
                    }

                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['membership' => $message])
                        ->withHeaders($headers);
                });
        });
    }
}
