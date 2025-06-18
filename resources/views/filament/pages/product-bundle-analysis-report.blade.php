<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 bg-white rounded-lg shadow">
            <h2 class="text-lg font-medium mb-4">Product Bundle Analysis</h2>
            
            <form wire:submit="filter" class="space-y-4">
                {{ $this->form }}
                
                <div class="flex justify-end">
                    <x-filament::button type="submit">
                        Analyze Bundles
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        <div class="p-4 bg-white rounded-lg shadow">
            <h2 class="text-lg font-medium mb-4">
                Frequently Bought Together Products
                <span class="text-sm font-normal text-gray-500 ml-2">
                    {{ $productBundles['date_range']['start'] ?? '' }} to {{ $productBundles['date_range']['end'] ?? '' }}
                </span>
            </h2>
            
            <div class="text-sm text-gray-500 mb-4">
                Analyzed {{ $productBundles['total_analyzed'] ?? 0 }} invoices.
                Showing products that appeared together at least {{ $minOccurrences }} times.
            </div>
            
            @if(empty($productBundles['bundles']))
                <div class="p-4 text-center text-gray-500">
                    No product bundles found matching the criteria. Try adjusting your filters or adding more sales data.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($productBundles['bundles'] as $bundle)
                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-medium">Bundle #{{ $loop->iteration }}</h3>
                                    <div class="text-sm text-gray-500">
                                        Bought together {{ $bundle['bought_together_count'] }} times
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">{{ number_format($bundle['total_value'], 2) }}</div>
                                    <div class="text-xs text-gray-500">Total Value</div>
                                </div>
                            </div>
                            
                            <div class="mt-2 space-y-2">
                                @foreach($bundle['products'] as $product)
                                    <div class="flex justify-between items-center p-2 {{ $loop->odd ? 'bg-gray-50' : '' }} rounded">
                                        <div>
                                            <div class="font-medium">{{ $product['name'] }}</div>
                                            <div class="text-xs text-gray-500">SKU: {{ $product['sku'] }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div>${{ number_format($product['price'], 2) }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $product['individual_occurrences'] }} individual sales
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-3 pt-2 border-t flex justify-between items-center">
                                <div class="text-sm">
                                    <span class="font-medium">Affinity Score:</span> 
                                    <span class="{{ $bundle['affinity_score'] >= 2 ? 'text-green-600' : '' }} font-medium">
                                        {{ $bundle['affinity_score'] }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($bundle['affinity_score'] >= 3)
                                        Strong association
                                    @elseif($bundle['affinity_score'] >= 2)
                                        Good association
                                    @elseif($bundle['affinity_score'] >= 1)
                                        Neutral association
                                    @else
                                        Weak association
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    <h3 class="text-md font-medium mb-2">Recommendations:</h3>
                    <ul class="list-disc pl-5 space-y-1">
                        @php
                            $topBundle = collect($productBundles['bundles'])->sortByDesc('affinity_score')->first();
                            $highValueBundle = collect($productBundles['bundles'])->sortByDesc('total_value')->first();
                        @endphp
                        
                        @if(!empty($topBundle))
                            <li>
                                Consider creating a product bundle for 
                                @foreach($topBundle['products'] as $product)
                                    <span class="font-medium">{{ $product['name'] }}</span>{{ !$loop->last ? ' and ' : '' }}
                                @endforeach
                                with an affinity score of {{ $topBundle['affinity_score'] }}.
                            </li>
                        @endif
                        
                        @if(!empty($highValueBundle) && $highValueBundle !== $topBundle)
                            <li>
                                The highest value bundle is 
                                @foreach($highValueBundle['products'] as $product)
                                    <span class="font-medium">{{ $product['name'] }}</span>{{ !$loop->last ? ' and ' : '' }}
                                @endforeach
                                at ${{ number_format($highValueBundle['total_value'], 2) }}.
                            </li>
                        @endif
                        
                        <li>
                            Consider cross-selling or promotional offers for frequently bought-together products.
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
