<?php

namespace App\Filament\Pages;

use App\Models\CustomerRegion;
use App\Models\ProductCategory;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class SalesPerformanceReport extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Sales Performance';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.sales-performance-report';
    
    public ?array $data = [];
    
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $salesRepId = null;
    public ?int $regionId = null;
    public ?int $categoryId = null;
    
    public Collection $salesPerformance;
    
    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->form->fill();
        $this->loadSalesPerformance();
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
                Select::make('salesRepId')
                    ->label('Sales Rep')
                    ->options(function() {
                        return User::role('salesperson')->get()->mapWithKeys(function ($user) {
                            return [$user->id => $user->first_name . ' ' . $user->last_name];
                        })->toArray();
                    })
                    ->placeholder('All Sales Reps')
                    ->searchable(),
                Select::make('regionId')
                    ->label('Customer Region')
                    ->options(CustomerRegion::pluck('name', 'id'))
                    ->placeholder('All Regions')
                    ->searchable(),
                Select::make('categoryId')
                    ->label('Product Category')
                    ->options(ProductCategory::pluck('name', 'id'))
                    ->placeholder('All Categories')
                    ->searchable(),
            ])
            ->statePath('data');
    }
    
    public function loadSalesPerformance(): void
    {
        $reportService = app(ReportService::class);
        $reportData = $reportService->getSalesPerformance(
            $this->startDate,
            $this->endDate,
            $this->salesRepId,
            $this->regionId,
            $this->categoryId
        );
        
        // Transform the data to match the expected format in the Blade template
        $this->salesPerformance = collect([
            [
                'sales_rep_name' => 'All Regions',
                'invoice_count' => $reportData['totals']['total_invoices'],
                'total_amount' => $reportData['totals']['total_sales'],
                'total_paid' => $reportData['totals']['total_paid'],
                'total_due' => $reportData['totals']['total_due'],
                'regions' => collect($reportData['regions'])->map(function($region) {
                    return [
                        'region_name' => $region['name'],
                        'invoice_count' => $region['total_invoices'],
                        'total_amount' => $region['total_sales'],
                        'total_paid' => $region['total_paid'],
                        'total_due' => $region['total_due']
                    ];
                })->toArray()
            ]
        ]);
    }
    
    public function filter(): void
    {
        $this->validate();
        $this->startDate = $this->data['startDate'] ?? null;
        $this->endDate = $this->data['endDate'] ?? null;
        $this->salesRepId = $this->data['salesRepId'] ?? null;
        $this->regionId = $this->data['regionId'] ?? null;
        $this->categoryId = $this->data['categoryId'] ?? null;
        
        $this->loadSalesPerformance();
    }
    
    protected function getFormActions(): array
    {
        return [
            
        ];
    }
    
    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
