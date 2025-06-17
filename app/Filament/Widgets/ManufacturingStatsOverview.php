<?php

namespace App\Filament\Widgets;

use App\Models\Production;
use App\Models\WorkOrder;
use App\Models\ProductionProduct;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ManufacturingStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    
    protected static ?int $sort = 1;
    
    protected function getHeading(): string
    {
        return 'Manufacturing Overview';
    }
    
    public static function canView(): bool
    {
        return in_array(request()->route()->getName(), [
            'filament.admin.pages.manufacturing-dashboard',
        ]);
    }

    protected function getStats(): array
    {
        $todayStart = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        // Today's production
        $todayProduction = Production::whereDate('created_at', $todayStart)
            ->count();

        // This month's production
        $monthProduction = Production::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->count();

        // Products manufactured this month
        $productsManufactured = ProductionProduct::whereHas('production', function ($query) use ($monthStart) {
            $query->whereBetween('created_at', [
                $monthStart, 
                Carbon::now()
            ]);
        })->sum('quantity');

        // Work orders this month
        $workOrderCount = WorkOrder::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->count();

        // Pending work orders
        $pendingWorkOrders = WorkOrder::where('status', 'pending')
            ->count();

        return [
            Stat::make('Today\'s Production', number_format($todayProduction))
                ->description('Production runs today')
                ->descriptionIcon('heroicon-m-cog')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make('Monthly Production', number_format($monthProduction))
                ->description('Production runs this month')
                ->descriptionIcon('heroicon-m-cog')
                ->chart([15, 30, 20, 45, 35, 40, 50])
                ->color('info'),

            Stat::make('Products Manufactured', number_format($productsManufactured))
                ->description('This month')
                ->descriptionIcon('heroicon-m-cube')
                ->chart([30, 60, 40, 50, 80, 70, 90])
                ->color('info'),

            Stat::make('Work Orders', number_format($workOrderCount))
                ->description('This month')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success'),

            Stat::make('Pending Work Orders', number_format($pendingWorkOrders))
                ->description('Awaiting completion')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
