<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ManufacturingChart;
use App\Filament\Widgets\ManufacturingStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class ManufacturingDashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Manufacturing Dashboard';
    
    protected static ?string $title = 'Manufacturing Dashboard';
    
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    
    protected static ?int $navigationSort = 3;
    
    protected static string $routePath = 'manufacturing-dashboard';
    
    public function getWidgets(): array
    {
        return [
            ManufacturingStatsOverview::class,
            ManufacturingChart::class,
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
