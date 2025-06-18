<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 bg-white rounded-lg shadow">
            <h2 class="text-lg font-medium mb-4">Filter Sales Performance</h2>
            
            <form wire:submit="filter" class="space-y-4">
                {{ $this->form }}
                
                <div class="flex justify-end">
                    <x-filament::button type="submit">
                        Filter Results
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        <div class="p-4 bg-white rounded-lg shadow">
            <h2 class="text-lg font-medium mb-4">Sales Performance Summary</h2>
            
            @if($salesPerformance->isEmpty())
                <div class="p-4 text-center text-gray-500">
                    No sales data found for the selected filters.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Sales Rep</th>
                                <th scope="col" class="px-6 py-3">Invoices</th>
                                <th scope="col" class="px-6 py-3">Total Amount</th>
                                <th scope="col" class="px-6 py-3">Amount Paid</th>
                                <th scope="col" class="px-6 py-3">Amount Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesPerformance as $salesRep)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $salesRep['sales_rep_name'] ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4">{{ $salesRep['invoice_count'] }}</td>
                                    <td class="px-6 py-4">{{ number_format($salesRep['total_amount'], 2) }}</td>
                                    <td class="px-6 py-4">{{ number_format($salesRep['total_paid'], 2) }}</td>
                                    <td class="px-6 py-4">{{ number_format($salesRep['total_due'], 2) }}</td>
                                </tr>
                                
                                @if(!empty($salesRep['regions']))
                                    @foreach($salesRep['regions'] as $region)
                                        <tr class="bg-gray-50 text-xs">
                                            <td class="px-6 py-2 pl-10">
                                                <span class="text-gray-500">Region:</span> {{ $region['region_name'] ?? 'Unknown' }}
                                            </td>
                                            <td class="px-6 py-2">{{ $region['invoice_count'] }}</td>
                                            <td class="px-6 py-2">{{ number_format($region['total_amount'], 2) }}</td>
                                            <td class="px-6 py-2">{{ number_format($region['total_paid'], 2) }}</td>
                                            <td class="px-6 py-2">{{ number_format($region['total_due'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                            
                            <tr class="bg-gray-100 font-medium">
                                <td class="px-6 py-4">Total</td>
                                <td class="px-6 py-4">{{ $salesPerformance->sum('invoice_count') }}</td>
                                <td class="px-6 py-4">{{ number_format($salesPerformance->sum('total_amount'), 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($salesPerformance->sum('total_paid'), 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($salesPerformance->sum('total_due'), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
