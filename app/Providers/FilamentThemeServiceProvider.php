<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FilamentThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Using Filament's built-in collapsible sidebar feature
        // Configuration is in AdminPanelProvider
    }
}
