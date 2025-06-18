<?php

namespace App\Filament\Pages;

use App\Models\ProductCategory;
use App\Services\ReportService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class ProductBundleAnalysisReport extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = 'Product Bundles';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 30;

    protected static string $view = 'filament.pages.product-bundle-analysis-report';
    
    public ?array $data = [];
    
    public int $minOccurrences = 2;
    public ?int $categoryId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $limit = 20;
    
    public array $productBundles = [];
    
    public function mount(): void
    {
        $this->startDate = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->form->fill();
        $this->loadProductBundles();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('minOccurrences')
                    ->label('Minimum Occurrences')
                    ->helperText('Minimum number of times products must appear together')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->default(2)
                    ->required(),
                Select::make('categoryId')
                    ->label('Product Category')
                    ->options(ProductCategory::pluck('name', 'id'))
                    ->placeholder('All Categories')
                    ->searchable(),
                DatePicker::make('startDate')
                    ->label('Start Date')
                    ->default(Carbon::now()->subMonths(6))
                    ->required(),
                DatePicker::make('endDate')
                    ->label('End Date')
                    ->default(Carbon::now())
                    ->required(),
                TextInput::make('limit')
                    ->label('Max Results')
                    ->helperText('Maximum number of bundles to show')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->default(20)
                    ->required(),
            ])
            ->statePath('data');
    }
    
    public function loadProductBundles(): void
    {
        $reportService = app(ReportService::class);
        $this->productBundles = $reportService->getProductBundles(
            $this->minOccurrences,
            $this->categoryId,
            $this->startDate,
            $this->endDate,
            $this->limit
        );
    }
    
    public function filter(): void
    {
        $this->validate();
        $this->minOccurrences = (int) $this->data['minOccurrences'] ?? 2;
        $this->categoryId = $this->data['categoryId'] ?? null;
        $this->startDate = $this->data['startDate'] ?? null;
        $this->endDate = $this->data['endDate'] ?? null;
        $this->limit = (int) $this->data['limit'] ?? 20;
        
        $this->loadProductBundles();
    }
}
