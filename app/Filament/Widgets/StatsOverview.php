<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    
    protected static ?int $sort = 1;
    
    protected function getHeading(): string
    {
        return 'Sales Overview';
    }
    
    public static function canView(): bool
    {
        return in_array(request()->route()->getName(), [
            'filament.admin.pages.sales-dashboard',
        ]);
    }

    protected function getStats(): array
    {
        $todayStart = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        // Today's sales
        $todaySales = Payment::whereDate('created_at', $todayStart)
            ->sum('amount');

        // This month's sales
        $monthSales = Payment::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->sum('amount');

        // Year to date sales
        $yearSales = Payment::whereBetween('created_at', [
            $yearStart, 
            Carbon::now()
        ])->sum('amount');

        // Order count this month
        $orderCount = Order::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->count();

        // Average order value
        $averageOrderValue = $orderCount > 0 
            ? $monthSales / $orderCount 
            : 0;

        return [
            Stat::make('Today\'s Sales', '$' . number_format($todaySales, 2))
                ->description('Total sales today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

            Stat::make('Monthly Sales', '$' . number_format($monthSales, 2))
                ->description('Total sales this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([15, 30, 20, 45, 35, 40, 50])
                ->color('primary'),

            Stat::make('Year to Date', '$' . number_format($yearSales, 2))
                ->description('Total sales this year')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([30, 60, 40, 50, 80, 70, 90])
                ->color('primary'),

            Stat::make('Orders This Month', $orderCount)
                ->description('Number of orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Average Order Value', '$' . number_format($averageOrderValue, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
