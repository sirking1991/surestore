<?php

namespace App\Filament\Widgets;

use App\Models\Production;
use App\Models\ProductionProduct;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ManufacturingChart extends ChartWidget
{
    protected static ?string $heading = 'Production Output';
    
    public static function canView(): bool
    {
        return in_array(request()->route()->getName(), [
            'filament.admin.pages.manufacturing-dashboard',
        ]);
    }
    
    protected static ?string $pollingInterval = '60s';
    
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
        $days = 30;
        $period = now()->subDays($days)->daysUntil(now());
        
        $productionData = ProductionProduct::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(quantity) as total')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(fn ($record) => $record->total)
            ->toArray();
            
        $dates = [];
        $production = [];
        
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->format('M d');
            $production[] = $productionData[$formattedDate] ?? 0;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Daily Production Output',
                    'data' => $production,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(6, 182, 212, 0.2)',
                    'borderColor' => 'rgb(6, 182, 212)',
                    'tension' => 0.2,
                ],
            ],
            'labels' => $dates,
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return value + " units"; }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.parsed.y + " units"; }',
                    ],
                ],
            ],
        ];
    }
}
