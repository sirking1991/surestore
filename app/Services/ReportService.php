<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get sales summary by date range
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $customerId
     * @return array
     */
    public function getSalesSummary(?string $startDate = null, ?string $endDate = null, ?int $customerId = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        $invoiceQuery = Invoice::query()
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');
            
        if ($customerId) {
            $invoiceQuery->where('customer_id', $customerId);
        }
        
        $totalSales = $invoiceQuery->sum('total');
        $totalPaid = $invoiceQuery->sum('amount_paid');
        $totalUnpaid = $invoiceQuery->sum('amount_due');
        $invoiceCount = $invoiceQuery->count();
        
        // Get monthly sales for the chart
        $monthlySales = Invoice::query()
            ->select(
                DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                DB::raw('SUM(total) as total')
            )
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->when($customerId, function ($query) use ($customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->groupBy(DB::raw('DATE_FORMAT(invoice_date, "%Y-%m")'))
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                    'total' => (float) $item->total
                ];
            });
            
        return [
            'total_sales' => $totalSales,
            'total_paid' => $totalPaid,
            'total_unpaid' => $totalUnpaid,
            'invoice_count' => $invoiceCount,
            'monthly_sales' => $monthlySales,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }
    
    /**
     * Get top selling products by date range
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int $limit
     * @return Collection
     */
    public function getTopProducts(?string $startDate = null, ?string $endDate = null, int $limit = 10): Collection
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
        
        return InvoiceItem::query()
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_amount')
            )
            ->whereHas('invoice', function (Builder $query) use ($startDate, $endDate) {
                $query->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', '!=', 'cancelled');
            })
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->with('product:id,name,code')
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Unknown Product',
                    'product_code' => $item->product->code ?? 'N/A',
                    'total_quantity' => $item->total_quantity,
                    'total_amount' => $item->total_amount,
                ];
            });
    }
    
    /**
     * Get sales by customer
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int $limit
     * @return Collection
     */
    public function getSalesByCustomer(?string $startDate = null, ?string $endDate = null, int $limit = 10): Collection
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
        
        return Invoice::query()
            ->select(
                'customer_id',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total) as total_amount'),
                DB::raw('SUM(amount_paid) as total_paid'),
                DB::raw('SUM(amount_due) as total_due')
            )
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->groupBy('customer_id')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->with('customer:id,name,code')
            ->get()
            ->map(function ($item) {
                return [
                    'customer_id' => $item->customer_id,
                    'customer_name' => $item->customer->name ?? 'Unknown Customer',
                    'customer_code' => $item->customer->code ?? 'N/A',
                    'invoice_count' => $item->invoice_count,
                    'total_amount' => $item->total_amount,
                    'total_paid' => $item->total_paid,
                    'total_due' => $item->total_due,
                ];
            });
    }
    
    /**
     * Get detailed sales data for export
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $customerId
     * @param int|null $productId
     * @return Collection
     */
    public function getSalesDetailedReport(
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?int $customerId = null,
        ?int $productId = null
    ): Collection {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
        
        $query = InvoiceItem::query()
            ->with(['invoice', 'invoice.customer', 'product'])
            ->whereHas('invoice', function (Builder $query) use ($startDate, $endDate, $customerId) {
                $query->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', '!=', 'cancelled');
                    
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                }
            });
            
        if ($productId) {
            $query->where('product_id', $productId);
        }
        
        return $query->get()
            ->map(function ($item) {
                return [
                    'invoice_number' => $item->invoice->invoice_number ?? 'N/A',
                    'invoice_date' => optional($item->invoice)->invoice_date?->format('Y-m-d') ?? 'N/A',
                    'customer_name' => optional($item->invoice->customer)->name ?? 'Unknown Customer',
                    'product_code' => optional($item->product)->code ?? 'N/A',
                    'product_name' => optional($item->product)->name ?? 'Unknown Product',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'tax' => $item->tax,
                    'total' => $item->total,
                ];
            });
    }
}
