<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Production;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDashboardWidgets extends Command
{
    protected $signature = 'dashboard:test';
    protected $description = 'Test dashboard widget data retrieval';

    public function handle()
    {
        $this->info('Testing Dashboard Widgets Data Retrieval');
        $this->info('======================================');
        
        // Test Sales Dashboard Data
        $this->info('SALES DASHBOARD');
        $this->info('--------------');
        
        $todayStart = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        // Today's sales
        $todaySales = Payment::whereDate('created_at', $todayStart)
            ->sum('amount');
        $this->info("Today's Sales: $" . number_format($todaySales, 2));

        // This month's sales
        $monthSales = Payment::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->sum('amount');
        $this->info("Monthly Sales: $" . number_format($monthSales, 2));

        // Year to date sales
        $yearSales = Payment::whereBetween('created_at', [
            $yearStart, 
            Carbon::now()
        ])->sum('amount');
        $this->info("Year to Date Sales: $" . number_format($yearSales, 2));

        // Order count this month
        $orderCount = Order::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->count();
        $this->info("Orders This Month: " . $orderCount);

        // Average order value
        $averageOrderValue = $orderCount > 0 
            ? $monthSales / $orderCount 
            : 0;
        $this->info("Average Order Value: $" . number_format($averageOrderValue, 2));
        
        // Test Purchases Dashboard Data
        $this->info("\nPURCHASES DASHBOARD");
        $this->info('------------------');
        
        // Today's purchases
        $todayPurchases = PurchaseInvoice::whereDate('created_at', $todayStart)
            ->sum('total');
        $this->info("Today's Purchases: $" . number_format($todayPurchases, 2));

        // This month's purchases
        $monthPurchases = PurchaseInvoice::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->sum('total');
        $this->info("Monthly Purchases: $" . number_format($monthPurchases, 2));

        // Year to date purchases
        $yearPurchases = PurchaseInvoice::whereBetween('created_at', [
            $yearStart, 
            Carbon::now()
        ])->sum('total');
        $this->info("Year to Date Purchases: $" . number_format($yearPurchases, 2));

        // Purchase order count this month
        $purchaseOrderCount = PurchaseOrder::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->count();
        $this->info("Purchase Orders This Month: " . $purchaseOrderCount);

        // Pending payments
        $pendingPayments = PurchaseInvoice::where('status', 'pending')
            ->sum('total');
        $this->info("Pending Payments: $" . number_format($pendingPayments, 2));
        
        // Test Manufacturing Dashboard Data
        $this->info("\nMANUFACTURING DASHBOARD");
        $this->info('----------------------');
        
        // Today's production
        $todayProduction = Production::whereDate('created_at', $todayStart)
            ->count();
        $this->info("Today's Production: " . $todayProduction);

        // This month's production
        $monthProduction = Production::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->count();
        $this->info("Monthly Production: " . $monthProduction);

        // Products manufactured this month
        $productsManufactured = DB::table('production_products')
            ->whereBetween('created_at', [
                $monthStart, 
                Carbon::now()
            ])
            ->sum('quantity');
        $this->info("Products Manufactured This Month: " . $productsManufactured);

        // Work order count this month
        $workOrderCount = WorkOrder::whereBetween('created_at', [
            $monthStart, 
            Carbon::now()
        ])->count();
        $this->info("Work Orders This Month: " . $workOrderCount);

        // Pending work orders
        $pendingWorkOrders = WorkOrder::where('status', 'pending')
            ->count();
        $this->info("Pending Work Orders: " . $pendingWorkOrders);
        
        return Command::SUCCESS;
    }
}
