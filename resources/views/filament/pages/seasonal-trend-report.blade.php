<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 bg-white rounded-lg shadow">
            <h2 class="text-lg font-medium mb-4">Seasonal Trend Analysis</h2>
            
            <form wire:submit="filter" class="space-y-4">
                {{ $this->form }}
                
                <div class="flex justify-end">
                    <x-filament::button type="submit">
                        Apply Filters
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        <div class="p-4 bg-white rounded-lg shadow">
            <h2 class="text-lg font-medium mb-4">
                {{ $periodType === 'month' ? 'Monthly' : 'Quarterly' }} Sales Trends 
                ({{ $year }}{{ $compareYear ? ' vs ' . $compareYear : '' }})
            </h2>
            
            @if(empty($seasonalTrends['periods']))
                <div class="p-4 text-center text-gray-500">
                    No sales data found for the selected filters.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Period</th>
                                <th scope="col" class="px-6 py-3">{{ $year }} Sales</th>
                                @if($compareYear)
                                    <th scope="col" class="px-6 py-3">{{ $compareYear }} Sales</th>
                                    <th scope="col" class="px-6 py-3">Growth %</th>
                                @endif
                                <th scope="col" class="px-6 py-3">{{ $year }} Invoices</th>
                                @if($compareYear)
                                    <th scope="col" class="px-6 py-3">{{ $compareYear }} Invoices</th>
                                    <th scope="col" class="px-6 py-3">Growth %</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seasonalTrends['periods'] as $period)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $period['label'] }}
                                    </td>
                                    <td class="px-6 py-4">{{ number_format($period['current_year']['sales'], 2) }}</td>
                                    @if($compareYear)
                                        <td class="px-6 py-4">{{ number_format($period['compare_year']['sales'], 2) }}</td>
                                        <td class="px-6 py-4 {{ $period['growth']['sales'] > 0 ? 'text-green-600' : ($period['growth']['sales'] < 0 ? 'text-red-600' : '') }}">
                                            @if($period['growth']['sales'] !== null)
                                                {{ number_format($period['growth']['sales'], 1) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-4">{{ $period['current_year']['invoices'] }}</td>
                                    @if($compareYear)
                                        <td class="px-6 py-4">{{ $period['compare_year']['invoices'] }}</td>
                                        <td class="px-6 py-4 {{ $period['growth']['invoices'] > 0 ? 'text-green-600' : ($period['growth']['invoices'] < 0 ? 'text-red-600' : '') }}">
                                            @if($period['growth']['invoices'] !== null)
                                                {{ number_format($period['growth']['invoices'], 1) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            
                            <!-- Totals row -->
                            <tr class="bg-gray-100 font-medium">
                                <td class="px-6 py-4">Total</td>
                                <td class="px-6 py-4">{{ number_format($seasonalTrends['totals']['current_year']['sales'], 2) }}</td>
                                @if($compareYear)
                                    <td class="px-6 py-4">{{ number_format($seasonalTrends['totals']['compare_year']['sales'], 2) }}</td>
                                    <td class="px-6 py-4 {{ isset($seasonalTrends['totals']['growth']['sales']) && $seasonalTrends['totals']['growth']['sales'] > 0 ? 'text-green-600' : (isset($seasonalTrends['totals']['growth']['sales']) && $seasonalTrends['totals']['growth']['sales'] < 0 ? 'text-red-600' : '') }}">
                                        @if(isset($seasonalTrends['totals']['growth']['sales']))
                                            {{ number_format($seasonalTrends['totals']['growth']['sales'], 1) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                                <td class="px-6 py-4">{{ $seasonalTrends['totals']['current_year']['invoices'] }}</td>
                                @if($compareYear)
                                    <td class="px-6 py-4">{{ $seasonalTrends['totals']['compare_year']['invoices'] }}</td>
                                    <td class="px-6 py-4 {{ isset($seasonalTrends['totals']['growth']['invoices']) && $seasonalTrends['totals']['growth']['invoices'] > 0 ? 'text-green-600' : (isset($seasonalTrends['totals']['growth']['invoices']) && $seasonalTrends['totals']['growth']['invoices'] < 0 ? 'text-red-600' : '') }}">
                                        @if(isset($seasonalTrends['totals']['growth']['invoices']))
                                            {{ number_format($seasonalTrends['totals']['growth']['invoices'], 1) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    <h3 class="text-md font-medium mb-2">Key Insights:</h3>
                    <ul class="list-disc pl-5 space-y-1">
                        @if(isset($seasonalTrends['totals']['growth']['sales']))
                            <li>
                                Overall sales {{ $seasonalTrends['totals']['growth']['sales'] > 0 ? 'increased' : 'decreased' }} by 
                                <span class="{{ $seasonalTrends['totals']['growth']['sales'] > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    {{ number_format(abs($seasonalTrends['totals']['growth']['sales']), 1) }}%
                                </span> 
                                compared to {{ $compareYear }}.
                            </li>
                        @endif
                        
                        @php
                            $bestPeriod = collect($seasonalTrends['periods'])->sortByDesc('current_year.sales')->first();
                            $worstPeriod = collect($seasonalTrends['periods'])->sortBy('current_year.sales')->first();
                            
                            if ($compareYear) {
                                $bestGrowth = collect($seasonalTrends['periods'])
                                    ->filter(function($period) {
                                        return $period['growth']['sales'] !== null;
                                    })
                                    ->sortByDesc('growth.sales')
                                    ->first();
                            }
                        @endphp
                        
                        <li>
                            Best performing period: <span class="font-medium">{{ $bestPeriod['label'] }}</span> with 
                            ${{ number_format($bestPeriod['current_year']['sales'], 2) }} in sales.
                        </li>
                        
                        @if(isset($bestGrowth) && $bestGrowth['growth']['sales'] > 0)
                            <li>
                                Highest growth: <span class="font-medium">{{ $bestGrowth['label'] }}</span> with 
                                <span class="text-green-600 font-medium">{{ number_format($bestGrowth['growth']['sales'], 1) }}%</span> increase.
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
