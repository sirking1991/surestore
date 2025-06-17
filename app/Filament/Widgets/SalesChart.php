<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Over Time';
    
    public static function canView(): bool
    {
        return in_array(request()->route()->getName(), [
            'filament.admin.pages.sales-dashboard',
        ]);
    }
    
    protected static ?string $pollingInterval = '60s';
    
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
        $days = 30;
        $period = now()->subDays($days)->daysUntil(now());
        
        $salesData = Payment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(fn ($record) => $record->total)
            ->toArray();
            
        $dates = [];
        $sales = [];
        
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->format('M d');
            $sales[] = $salesData[$formattedDate] ?? 0;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales',
                    'data' => $sales,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return "$" + context.parsed.y.toFixed(2); }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value; }',
                    ],
                ],
            ],
        ];
    }
}
