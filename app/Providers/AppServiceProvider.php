<?php

namespace App\Providers;

use App\Models\Enterprise;
use App\Observers\EnterpriseObserver;
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
        Enterprise::observe(EnterpriseObserver::class);
    }
}
