<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class SalesDashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Sales Dashboard';
    
    protected static ?string $title = 'Sales Dashboard';
    
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?int $navigationSort = 1;
    
    protected static string $routePath = 'sales-dashboard';
    
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            SalesChart::class,
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
