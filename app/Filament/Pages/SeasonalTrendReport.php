<?php

namespace App\Filament\Pages;

use App\Models\CustomerRegion;
use App\Models\ProductCategory;
use App\Services\ReportService;
use Carbon\Carbon;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class SeasonalTrendReport extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Seasonal Trends';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 20;

    protected static string $view = 'filament.pages.seasonal-trend-report';
    
    public ?array $data = [];
    
    public string $periodType = 'month';
    public ?int $year = null;
    public ?int $compareYear = null;
    public ?int $regionId = null;
    public ?int $categoryId = null;
    
    public array $seasonalTrends = [];
    
    public function mount(): void
    {
        $this->year = Carbon::now()->year;
        $this->compareYear = $this->year - 1;
        $this->form->fill();
        $this->loadSeasonalTrends();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('periodType')
                    ->label('Period Type')
                    ->options([
                        'month' => 'Monthly',
                        'quarter' => 'Quarterly',
                    ])
                    ->default('month')
                    ->inline()
                    ->required(),
                Select::make('year')
                    ->label('Year')
                    ->options($this->getYearOptions())
                    ->default(Carbon::now()->year)
                    ->required(),
                Select::make('compareYear')
                    ->label('Compare With Year')
                    ->options($this->getYearOptions())
                    ->default(Carbon::now()->year - 1)
                    ->placeholder('No Comparison'),
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
    
    protected function getYearOptions(): array
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 5, $currentYear + 1);
        return array_combine($years, $years);
    }
    
    public function loadSeasonalTrends(): void
    {
        $reportService = app(ReportService::class);
        $this->seasonalTrends = $reportService->getSeasonalTrends(
            $this->periodType,
            $this->year,
            $this->compareYear,
            $this->categoryId,
            $this->regionId
        );
    }
    
    public function filter(): void
    {
        $this->validate();
        $this->periodType = $this->data['periodType'] ?? 'month';
        $this->year = $this->data['year'] ?? Carbon::now()->year;
        $this->compareYear = $this->data['compareYear'] ?? null;
        $this->regionId = $this->data['regionId'] ?? null;
        $this->categoryId = $this->data['categoryId'] ?? null;
        
        $this->loadSeasonalTrends();
    }
}
