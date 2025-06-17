<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseOrder;
use App\Models\PurchaseInvoice;
use App\Models\Disbursement;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PurchaseStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    
    protected static ?int $sort = 1;
    
    protected function getHeading(): string
    {
        return 'Purchase Overview';
    }
    
    public static function canView(): bool
    {
        return in_array(request()->route()->getName(), [
            'filament.admin.pages.purchases-dashboard',
        ]);
    }

    protected function getStats(): array
    {
        $todayStart = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        // Today's purchases
        $todayPurchases = PurchaseInvoice::whereDate('created_at', $todayStart)
            ->sum('total');

        // This month's purchases
        $monthPurchases = PurchaseInvoice::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->sum('total');

        // Year to date purchases
        $yearPurchases = PurchaseInvoice::whereBetween('created_at', [
            $yearStart, 
            Carbon::now()
        ])->sum('total');

        // Purchase order count this month
        $purchaseOrderCount = PurchaseOrder::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->count();

        // Pending payments
        $pendingPayments = PurchaseInvoice::where('payment_status', 'pending')
            ->sum('total');

        return [
            Stat::make('Today\'s Purchases', number_format($todayPurchases, 2))
                ->description('Total purchases today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),

            Stat::make('Monthly Purchases', number_format($monthPurchases, 2))
                ->description('Total purchases this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([15, 30, 20, 45, 35, 40, 50])
                ->color('warning'),

            Stat::make('Year to Date', number_format($yearPurchases, 2))
                ->description('Total purchases this year')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([30, 60, 40, 50, 80, 70, 90])
                ->color('warning'),

            Stat::make('Purchase Orders', $purchaseOrderCount)
                ->description('This month')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Pending Payments', number_format($pendingPayments, 2))
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),
        ];
    }
}
