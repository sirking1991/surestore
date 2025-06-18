<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
    
    /**
     * Get sales performance data by region and product category
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $salesRepId (ignored - kept for backward compatibility)
     * @param int|null $regionId
     * @param int|null $categoryId
     * @return array
     */
    public function getSalesPerformance(
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?int $salesRepId = null,
        ?int $regionId = null,
        ?int $categoryId = null
    ): array {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
        
        $query = Invoice::query()
            ->select(
                'invoices.id',
                'invoices.invoice_number',
                'invoices.invoice_date',
                'invoices.total',
                'invoices.amount_paid',
                'invoices.amount_due',
                'invoices.status',
                'invoices.customer_id',
                'customers.name as customer_name',
                'customers.region_id',
                'customer_regions.name as region_name'
            )
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoin('customer_regions', 'customers.region_id', '=', 'customer_regions.id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->where('invoices.status', '!=', 'cancelled');
            
        // Note: salesRepId parameter is ignored as Invoice doesn't have user_id field
        
        if ($regionId) {
            $query->where('customers.region_id', $regionId);
        }
        
        // Get the base invoice data
        $invoices = $query->get();
        
        // If category filter is applied, we need to filter by invoice items with products in that category
        if ($categoryId) {
            // Get all invoice IDs that have items with products in the specified category
            $invoiceIdsWithCategory = InvoiceItem::query()
                ->select('invoice_id')
                ->whereIn('invoice_id', $invoices->pluck('id'))
                ->whereHas('product', function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->distinct()
                ->pluck('invoice_id')
                ->toArray();
            
            // Filter invoices to only those with products in the specified category
            $invoices = $invoices->filter(function ($invoice) use ($invoiceIdsWithCategory) {
                return in_array($invoice->id, $invoiceIdsWithCategory);
            });
        }
        
        // Group by region
        $regionPerformance = $invoices->groupBy('region_id')->map(function ($invoices, $regionId) {
            $regionName = $invoices->first()->region_name ?? 'Unknown';
            
            return [
                'id' => $regionId,
                'name' => $regionName,
                'total_invoices' => $invoices->count(),
                'total_sales' => $invoices->sum('total'),
                'total_paid' => $invoices->sum('amount_paid'),
                'total_due' => $invoices->sum('amount_due'),
            ];
        })->values();
        
        // Calculate totals
        $totalSales = $invoices->sum('total');
        $totalPaid = $invoices->sum('amount_paid');
        $totalDue = $invoices->sum('amount_due');
        $totalInvoices = $invoices->count();
        
        return [
            'regions' => $regionPerformance,
            'totals' => [
                'total_sales' => $totalSales,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
                'total_invoices' => $totalInvoices,
            ],
            'date_range' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
        ];
    }
    
    /**
     * Get seasonal trend data with year-over-year comparisons
     *
     * @param string $periodType 'month' or 'quarter'
     * @param int $year Current year for analysis
     * @param int|null $compareYear Previous year for comparison (null for no comparison)
     * @param int|null $categoryId Filter by product category
     * @param int|null $regionId Filter by customer region
     * @return array
     */
    public function getSeasonalTrends(
        string $periodType = 'month',
        ?int $year = null,
        ?int $compareYear = null,
        ?int $categoryId = null,
        ?int $regionId = null
    ): array {
        $year = $year ?? Carbon::now()->year;
        $compareYear = $compareYear ?? ($year - 1);
        
        // Format for grouping by month or quarter
        $periodFormat = $periodType === 'quarter' ? "'Q'q" : "'M'n";
        $labelFormat = $periodType === 'quarter' ? "'Q'q Y" : "F Y";
        
        // Get current year data
        $currentYearData = $this->getPeriodSalesData($year, $periodType, $categoryId, $regionId);
        
        // Get comparison year data if requested
        $compareYearData = $compareYear ? 
            $this->getPeriodSalesData($compareYear, $periodType, $categoryId, $regionId) : 
            [];
        
        // Prepare the result structure
        $result = [
            'current_year' => $year,
            'compare_year' => $compareYear,
            'period_type' => $periodType,
            'periods' => [],
            'totals' => [
                'current_year' => [
                    'sales' => 0,
                    'invoices' => 0,
                ],
                'compare_year' => [
                    'sales' => 0,
                    'invoices' => 0,
                ],
            ],
        ];
        
        // Determine all periods to show
        $periods = [];
        if ($periodType === 'quarter') {
            $periods = [1, 2, 3, 4]; // Q1, Q2, Q3, Q4
        } else {
            $periods = range(1, 12); // Months 1-12
        }
        
        // Build the periods data
        foreach ($periods as $periodNumber) {
            $periodKey = $periodType === 'quarter' ? "Q{$periodNumber}" : $periodNumber;
            
            // Get data for current year period
            $currentPeriodData = $currentYearData[$periodKey] ?? [
                'sales' => 0,
                'invoices' => 0,
            ];
            
            // Get data for comparison year period
            $comparePeriodData = $compareYear ? ($compareYearData[$periodKey] ?? [
                'sales' => 0,
                'invoices' => 0,
            ]) : null;
            
            // Calculate growth percentages
            $salesGrowth = $comparePeriodData && $comparePeriodData['sales'] > 0 ?
                (($currentPeriodData['sales'] - $comparePeriodData['sales']) / $comparePeriodData['sales'] * 100) :
                null;
                
            $invoiceGrowth = $comparePeriodData && $comparePeriodData['invoices'] > 0 ?
                (($currentPeriodData['invoices'] - $comparePeriodData['invoices']) / $comparePeriodData['invoices'] * 100) :
                null;
            
            // Format period label
            $periodDate = $periodType === 'quarter' ?
                Carbon::createFromDate($year, $periodNumber * 3, 1) :
                Carbon::createFromDate($year, $periodNumber, 1);
            $periodLabel = $periodDate->format($labelFormat);
            
            // Add to result
            $result['periods'][] = [
                'period_number' => $periodNumber,
                'period_key' => $periodKey,
                'label' => $periodLabel,
                'current_year' => $currentPeriodData,
                'compare_year' => $comparePeriodData,
                'growth' => [
                    'sales' => $salesGrowth,
                    'invoices' => $invoiceGrowth,
                ],
            ];
            
            // Add to totals
            $result['totals']['current_year']['sales'] += $currentPeriodData['sales'];
            $result['totals']['current_year']['invoices'] += $currentPeriodData['invoices'];
            
            if ($compareYear) {
                $result['totals']['compare_year']['sales'] += $comparePeriodData['sales'];
                $result['totals']['compare_year']['invoices'] += $comparePeriodData['invoices'];
            }
        }
        
        // Calculate overall growth
        if ($compareYear && $result['totals']['compare_year']['sales'] > 0) {
            $result['totals']['growth'] = [
                'sales' => ($result['totals']['current_year']['sales'] - $result['totals']['compare_year']['sales']) / 
                    $result['totals']['compare_year']['sales'] * 100,
                'invoices' => $result['totals']['compare_year']['invoices'] > 0 ?
                    ($result['totals']['current_year']['invoices'] - $result['totals']['compare_year']['invoices']) / 
                    $result['totals']['compare_year']['invoices'] * 100 : null,
            ];
        }
        
        return $result;
    }
    
    /**
     * Get sales data grouped by period (month or quarter) for a specific year
     *
     * @param int $year
     * @param string $periodType 'month' or 'quarter'
     * @param int|null $categoryId
     * @param int|null $regionId
     * @return array
     */
    protected function getPeriodSalesData(
        int $year,
        string $periodType = 'month',
        ?int $categoryId = null,
        ?int $regionId = null
    ): array {
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
        
        $periodFormat = $periodType === 'quarter' ? "'Q'q" : "m";
        $periodColumn = $periodType === 'quarter' ? "CONCAT('Q', QUARTER(invoices.invoice_date))" : "MONTH(invoices.invoice_date)";
        
        $query = Invoice::query()
            ->select(
                DB::raw($periodColumn . ' as period'),
                DB::raw('SUM(invoices.total) as total_sales'),
                DB::raw('COUNT(invoices.id) as invoice_count')
            )
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->where('invoices.status', '!=', 'cancelled')
            ->groupBy(DB::raw($periodColumn));
        
        if ($regionId) {
            $query->join('customers', 'invoices.customer_id', '=', 'customers.id')
                  ->where('customers.region_id', $regionId);
        }
        
        // If category filter is applied, we need to join with invoice_items and products
        if ($categoryId) {
            $query->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                  ->join('products', 'invoice_items.product_id', '=', 'products.id')
                  ->where('products.category_id', $categoryId)
                  ->distinct();
        }
        
        $data = $query->get();
        
        // Convert to associative array by period
        $result = [];
        foreach ($data as $row) {
            $result[$row->period] = [
                'sales' => (float) $row->total_sales,
                'invoices' => (int) $row->invoice_count,
            ];
        }
        
        return $result;
    }
    
    /**
     * Analyze product bundles to find frequently bought-together products
     *
     * @param int $minOccurrences Minimum number of times products must appear together
     * @param int|null $categoryId Filter by product category
     * @param string|null $startDate Start date for analysis
     * @param string|null $endDate End date for analysis
     * @param int $limit Maximum number of bundles to return
     * @return array
     */
    public function getProductBundles(
        int $minOccurrences = 2,
        ?int $categoryId = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $limit = 20
    ): array {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonths(6);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
        
        // Get all invoice IDs within the date range
        $invoiceQuery = Invoice::query()
            ->select('id')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');
            
        $invoiceIds = $invoiceQuery->pluck('id')->toArray();
        
        if (empty($invoiceIds)) {
            return [
                'bundles' => [],
                'total_analyzed' => 0,
            ];
        }
        
        // Get all invoice items for these invoices
        $invoiceItemsQuery = InvoiceItem::query()
            ->select('invoice_id', 'product_id')
            ->whereIn('invoice_id', $invoiceIds);
            
        // Apply category filter if specified
        if ($categoryId) {
            $invoiceItemsQuery->whereHas('product', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }
        
        $invoiceItems = $invoiceItemsQuery->get();
        
        // Group items by invoice to find products that appear together
        $invoiceProducts = $invoiceItems->groupBy('invoice_id');
        
        // Count occurrences of product pairs
        $productPairs = [];
        $productOccurrences = [];
        
        foreach ($invoiceProducts as $invoiceId => $items) {
            // Skip invoices with only one product
            if ($items->count() < 2) {
                continue;
            }
            
            // Get all product IDs in this invoice
            $productIds = $items->pluck('product_id')->toArray();
            
            // Count individual product occurrences
            foreach ($productIds as $productId) {
                if (!isset($productOccurrences[$productId])) {
                    $productOccurrences[$productId] = 0;
                }
                $productOccurrences[$productId]++;
            }
            
            // Generate all possible pairs
            for ($i = 0; $i < count($productIds); $i++) {
                for ($j = $i + 1; $j < count($productIds); $j++) {
                    // Sort product IDs to ensure consistent pairing
                    $pair = [$productIds[$i], $productIds[$j]];
                    sort($pair);
                    $pairKey = implode('-', $pair);
                    
                    if (!isset($productPairs[$pairKey])) {
                        $productPairs[$pairKey] = [
                            'product_ids' => $pair,
                            'count' => 0,
                            'invoices' => [],
                        ];
                    }
                    
                    $productPairs[$pairKey]['count']++;
                    $productPairs[$pairKey]['invoices'][] = $invoiceId;
                }
            }
        }
        
        // Filter pairs by minimum occurrences
        $productPairs = array_filter($productPairs, function ($pair) use ($minOccurrences) {
            return $pair['count'] >= $minOccurrences;
        });
        
        // Sort pairs by occurrence count (descending)
        uasort($productPairs, function ($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        // Limit the number of results
        $productPairs = array_slice($productPairs, 0, $limit, true);
        
        // Get product details for each pair
        $bundles = [];
        foreach ($productPairs as $pairKey => $pair) {
            $products = Product::whereIn('id', $pair['product_ids'])->get();
            
            $bundleProducts = $products->map(function ($product) use ($productOccurrences) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'individual_occurrences' => $productOccurrences[$product->id] ?? 0,
                ];
            });
            
            $bundles[] = [
                'products' => $bundleProducts,
                'bought_together_count' => $pair['count'],
                'total_value' => $products->sum('price'),
                'affinity_score' => $this->calculateAffinityScore($pair['count'], $productOccurrences, $pair['product_ids']),
            ];
        }
        
        return [
            'bundles' => $bundles,
            'total_analyzed' => count($invoiceIds),
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
        ];
    }
    
    /**
     * Calculate affinity score for product bundle
     * This is a simple implementation of lift analysis for association rules
     *
     * @param int $pairCount Number of times products appear together
     * @param array $productOccurrences Individual product occurrences
     * @param array $productIds Product IDs in the pair
     * @return float
     */
    protected function calculateAffinityScore(int $pairCount, array $productOccurrences, array $productIds): float
    {
        if (count($productIds) !== 2 || !isset($productOccurrences[$productIds[0]]) || !isset($productOccurrences[$productIds[1]])) {
            return 0;
        }
        
        $product1Occurrences = $productOccurrences[$productIds[0]];
        $product2Occurrences = $productOccurrences[$productIds[1]];
        
        // Simple lift calculation: P(A,B) / (P(A) * P(B))
        // Higher values indicate stronger association
        $totalInvoices = max(array_sum(array_map(function($count) { return min($count, 1); }, $productOccurrences)), 1);
        
        $probPair = $pairCount / $totalInvoices;
        $probProduct1 = $product1Occurrences / $totalInvoices;
        $probProduct2 = $product2Occurrences / $totalInvoices;
        
        $lift = $probPair / ($probProduct1 * $probProduct2);
        
        return round($lift, 2);
    }
}
