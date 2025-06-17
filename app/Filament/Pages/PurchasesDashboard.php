<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PurchaseChart;
use App\Filament\Widgets\PurchaseStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class PurchasesDashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Purchases Dashboard';
    
    protected static ?string $title = 'Purchases Dashboard';
    
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?int $navigationSort = 2;
    
    protected static string $routePath = 'purchases-dashboard';
    
    public function getWidgets(): array
    {
        return [
            PurchaseStatsOverview::class,
            PurchaseChart::class,
        ];
    }
    
    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 3,
        ];
    }
}
