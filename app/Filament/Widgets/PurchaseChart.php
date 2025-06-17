<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseInvoice;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PurchaseChart extends ChartWidget
{
    protected static ?string $heading = 'Purchase Trends';
    
    public static function canView(): bool
    {
        return in_array(request()->route()->getName(), [
            'filament.admin.pages.purchases-dashboard',
        ]);
    }
    
    protected static ?string $pollingInterval = '60s';
    
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
        $days = 30;
        $period = now()->subDays($days)->daysUntil(now());
        
        $purchaseData = PurchaseInvoice::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(fn ($record) => $record->total)
            ->toArray();
            
        $dates = [];
        $purchases = [];
        
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->format('M d');
            $purchases[] = $purchaseData[$formattedDate] ?? 0;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Daily Purchases',
                    'data' => $purchases,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(234, 179, 8, 0.2)',
                    'borderColor' => 'rgb(234, 179, 8)',
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
