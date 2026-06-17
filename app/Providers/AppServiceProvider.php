<?php

namespace App\Providers;

use App\Support\SeoSettings;
use Illuminate\Support\Facades\Gate;
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
    }
}
