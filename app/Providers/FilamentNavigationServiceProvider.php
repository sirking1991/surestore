<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;

class FilamentNavigationServiceProvider extends ServiceProvider
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
        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Inventories')
                    ->icon('heroicon-o-archive-box'),
                NavigationGroup::make()
                    ->label('Analytics')
                    ->icon('heroicon-o-chart-bar'),
                NavigationGroup::make()
                    ->label('Reports')
                    ->icon('heroicon-o-document-chart-bar'),
                NavigationGroup::make()
                    ->label('Manufacturing')
                    ->icon('heroicon-o-cog-6-tooth'),
                NavigationGroup::make()
                    ->label('Purchases')
                    ->icon('heroicon-o-shopping-cart'),
                NavigationGroup::make()
                    ->label('Sales')
                    ->icon('heroicon-o-currency-dollar'),
                NavigationGroup::make()
                    ->label('Master Files')
                    ->icon('heroicon-o-folder'),
                NavigationGroup::make()
                    ->label('Administration')
                    ->icon('heroicon-o-user-group'),
            ]);
        });
    }
}
