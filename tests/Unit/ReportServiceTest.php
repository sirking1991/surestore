<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\CustomerRegion;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected ReportService $reportService;
    protected User $salesRep;
    protected CustomerRegion $region;
    protected ProductCategory $category;
    protected Customer $customer;
    protected Product $product1;
    protected Product $product2;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->reportService = new ReportService();
        
        // Create test data
        // Create the salesperson role if it doesn't exist
        if (!Role::where('name', 'salesperson')->exists()) {
            Role::create(['name' => 'salesperson']);
        }
        
        $this->salesRep = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Sales Rep'
        ]);
        
        // Assign salesperson role
        $this->salesRep->assignRole('salesperson');
        
        $this->region = CustomerRegion::create([
            'name' => 'Test Region'
        ]);
        
        $this->category = ProductCategory::create([
            'name' => 'Test Category'
        ]);
        
        $this->customer = Customer::factory()->create([
            'region_id' => $this->region->id
        ]);
        
        $this->product1 = Product::factory()->create([
            'name' => 'Test Product 1',
            'category_id' => $this->category->id,
            'purchase_price' => 80,
            'selling_price' => 100
        ]);
        
        $this->product2 = Product::factory()->create([
            'name' => 'Test Product 2',
            'category_id' => $this->category->id,
            'purchase_price' => 160,
            'selling_price' => 200
        ]);
        
        // Create invoices and items
        $this->createTestInvoices();
    }
    
    protected function createTestInvoices(): void
    {
        // Current year invoice
        $currentYearInvoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'invoice_date' => Carbon::now()->subMonth(),
            'status' => 'paid',
            'total' => 300
        ]);
        
        InvoiceItem::factory()->create([
            'invoice_id' => $currentYearInvoice->id,
            'product_id' => $this->product1->id,
            'quantity' => 1,
            'unit' => 'pcs',
            'unit_price' => 100,
            'subtotal' => 100,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount_rate' => 0,
            'discount_amount' => 0,
            'total' => 100
        ]);
        
        InvoiceItem::factory()->create([
            'invoice_id' => $currentYearInvoice->id,
            'product_id' => $this->product2->id,
            'quantity' => 1,
            'unit' => 'pcs',
            'unit_price' => 200,
            'subtotal' => 200,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount_rate' => 0,
            'discount_amount' => 0,
            'total' => 200
        ]);
        
        // Previous year invoice
        $previousYearInvoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'invoice_date' => Carbon::now()->subYear()->subMonth(),
            'status' => 'paid',
            'total' => 100
        ]);
        
        InvoiceItem::factory()->create([
            'invoice_id' => $previousYearInvoice->id,
            'product_id' => $this->product1->id,
            'quantity' => 1,
            'unit' => 'pcs',
            'unit_price' => 100,
            'subtotal' => 100,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount_rate' => 0,
            'discount_amount' => 0,
            'total' => 100
        ]);
    }
    
    /** @test */
    public function it_can_get_sales_performance_data()
    {
        $startDate = Carbon::now()->subMonths(3)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
        
        // Test with filters
        $result = $this->reportService->getSalesPerformance(
            $startDate,
            $endDate,
            null, // No sales rep filter since Invoice doesn't have user_id
            $this->region->id,
            $this->category->id
        );
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('regions', $result);
        $this->assertArrayHasKey('totals', $result);
        
        // Test that our region is in the results
        $foundRegion = false;
        foreach ($result['regions'] as $region) {
            if ($region['id'] == $this->region->id) {
                $foundRegion = true;
                break;
            }
        }
        $this->assertTrue($foundRegion, 'Region not found in results');
    }
    
    /** @test */
    public function it_can_get_seasonal_trends_data()
    {
        $currentYear = Carbon::now()->year;
        $compareYear = $currentYear - 1;
        
        $result = $this->reportService->getSeasonalTrends(
            'monthly',
            $currentYear,
            $compareYear,
            $this->category->id,
            $this->region->id
        );
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('periods', $result);
        $this->assertArrayHasKey('totals', $result);
        
        // Test with quarterly period
        $result = $this->reportService->getSeasonalTrends(
            'quarterly',
            $currentYear,
            $compareYear,
            $this->category->id,
            $this->region->id
        );
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('periods', $result);
        $this->assertArrayHasKey('totals', $result);
    }
    
    /** @test */
    public function it_can_get_product_bundles_data(): void
    {
        // Create another invoice with both products to create a bundle
        $bundleInvoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'invoice_date' => Carbon::now()->subDays(15),
            'status' => 'paid',
            'total' => 300
        ]);
        
        // Add both products to this invoice to create a bundle
        InvoiceItem::factory()->create([
            'invoice_id' => $bundleInvoice->id,
            'product_id' => $this->product1->id,
            'quantity' => 1,
            'unit' => 'pcs',
            'unit_price' => 100,
            'subtotal' => 100,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount_rate' => 0,
            'discount_amount' => 0,
            'total' => 100
        ]);
        
        InvoiceItem::factory()->create([
            'invoice_id' => $bundleInvoice->id,
            'product_id' => $this->product2->id,
            'quantity' => 1,
            'unit' => 'pcs',
            'unit_price' => 200,
            'subtotal' => 200,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount_rate' => 0,
            'discount_amount' => 0,
            'total' => 200
        ]);
        
        $minOccurrences = 1; // Lower this for our test
        $result = $this->reportService->getProductBundles(
            $minOccurrences,
            $this->category->id,
            Carbon::now()->subYear()->format('Y-m-d'),
            Carbon::now()->format('Y-m-d'),
            10 // limit
        );
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('bundles', $result);
    }
}
