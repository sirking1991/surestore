<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Product;
use App\Services\ReportService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;

class SalesReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.sales-report';
    
    protected static ?string $navigationGroup = 'Reports';
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $title = 'Sales Report';
    
    public ?array $data = [];
    
    public array $topProducts = [];
    
    public array $topCustomers = [];
    
    public array $monthlySales = [];
    
    public ?string $startDate = null;
    
    public ?string $endDate = null;
    
    public ?int $customerId = null;
    
    public ?int $productId = null;
    
    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->generateReport();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('startDate')
                    ->label('Start Date')
                    ->required(),
                DatePicker::make('endDate')
                    ->label('End Date')
                    ->required(),
                Select::make('customerId')
                    ->label('Customer')
                    ->options(Customer::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('All Customers'),
                Select::make('productId')
                    ->label('Product')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('All Products'),
            ])
            ->columns(4);
    }
    
    public function generateReport(): void
    {
        $reportService = app(ReportService::class);
        
        try {
            $this->data = $reportService->getSalesSummary(
                $this->startDate,
                $this->endDate,
                $this->customerId
            );
            
            $this->topProducts = $reportService->getTopProducts(
                $this->startDate,
                $this->endDate
            )->toArray();
            
            $this->topCustomers = $reportService->getSalesByCustomer(
                $this->startDate,
                $this->endDate
            )->toArray();
            
            $this->monthlySales = $this->data['monthly_sales']->toArray();
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error generating report')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function exportCsv()
    {
        $reportService = app(ReportService::class);
        
        try {
            $data = $reportService->getSalesDetailedReport(
                $this->startDate,
                $this->endDate,
                $this->customerId,
                $this->productId
            );
            
            if ($data->isEmpty()) {
                Notification::make()
                    ->title('No data to export')
                    ->warning()
                    ->send();
                    
                return;
            }
            
            $csv = Writer::createFromString();
            
            // Add the header row
            $csv->insertOne(array_keys($data->first()));
            
            // Add the data rows
            $csv->insertAll($data->toArray());
            
            $filename = 'sales_report_' . Carbon::now()->format('Y-m-d_His') . '.csv';
            
            Notification::make()
                ->title('Export successful')
                ->success()
                ->send();
                
            return response()->streamDownload(function () use ($csv) {
                echo $csv->toString();
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error exporting report')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
