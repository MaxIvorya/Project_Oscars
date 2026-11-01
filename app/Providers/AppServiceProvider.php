<?php

namespace App\Providers;

use App\Models\MuaGiai;
use App\Observers\MuaGiaiObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        MuaGiai::observe(MuaGiaiObserver::class);
    }
}
