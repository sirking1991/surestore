<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="generateReport" class="space-y-6">
            {{ $this->form }}
            
            <div class="flex justify-end">
                <x-filament::button type="submit">
                    Generate Report
                </x-filament::button>
                
                <x-filament::button 
                    type="button" 
                    color="success" 
                    class="ml-2"
                    wire:click="exportCsv"
                >
                    Export CSV
                </x-filament::button>
            </div>
        </form>
        
        @if(!empty($data))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-filament::card>
                    <div class="text-sm font-medium text-gray-500">Total Sales</div>
                    <div class="text-3xl font-bold">{{ number_format($data['total_sales'] ?? 0, 2) }}</div>
                </x-filament::card>
                
                <x-filament::card>
                    <div class="text-sm font-medium text-gray-500">Total Paid</div>
                    <div class="text-3xl font-bold text-success-500">{{ number_format($data['total_paid'] ?? 0, 2) }}</div>
                </x-filament::card>
                
                <x-filament::card>
                    <div class="text-sm font-medium text-gray-500">Total Unpaid</div>
                    <div class="text-3xl font-bold text-danger-500">{{ number_format($data['total_unpaid'] ?? 0, 2) }}</div>
                </x-filament::card>
                
                <x-filament::card>
                    <div class="text-sm font-medium text-gray-500">Invoice Count</div>
                    <div class="text-3xl font-bold">{{ number_format($data['invoice_count'] ?? 0) }}</div>
                </x-filament::card>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-filament::card>
                    <h2 class="text-lg font-medium mb-4">Top Products</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Product</th>
                                    <th scope="col" class="px-6 py-3">Code</th>
                                    <th scope="col" class="px-6 py-3">Quantity</th>
                                    <th scope="col" class="px-6 py-3">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">{{ $product['product_name'] }}</td>
                                        <td class="px-6 py-4">{{ $product['product_code'] }}</td>
                                        <td class="px-6 py-4">{{ number_format($product['total_quantity']) }}</td>
                                        <td class="px-6 py-4">{{ number_format($product['total_amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                                
                                @if(empty($topProducts))
                                    <tr class="bg-white border-b">
                                        <td colspan="4" class="px-6 py-4 text-center">No data available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </x-filament::card>
                
                <x-filament::card>
                    <h2 class="text-lg font-medium mb-4">Top Customers</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Customer</th>
                                    <th scope="col" class="px-6 py-3">Invoices</th>
                                    <th scope="col" class="px-6 py-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $customer)
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">{{ $customer['customer_name'] }}</td>
                                        <td class="px-6 py-4">{{ number_format($customer['invoice_count']) }}</td>
                                        <td class="px-6 py-4">{{ number_format($customer['total_amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                                
                                @if(empty($topCustomers))
                                    <tr class="bg-white border-b">
                                        <td colspan="3" class="px-6 py-4 text-center">No data available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </x-filament::card>
            </div>
            
            <x-filament::card>
                <h2 class="text-lg font-medium mb-4">Monthly Sales Trend</h2>
                <div id="sales-chart" style="height: 300px;"></div>
            </x-filament::card>
        @endif
    </div>
    
    @pushOnce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', function () {
            function initChart() {
                const chartElement = document.getElementById('sales-chart');
                if (!chartElement) return;
                
                // Safely parse the sales data
                let salesData = [];
                try {
                    salesData = JSON.parse('{!! addslashes(json_encode($monthlySales)) !!}');
                } catch (e) {
                    console.error('Error parsing sales data:', e);
                    return;
                }
                
                if (!salesData || !salesData.length) return;
                
                // Destroy existing chart if it exists
                if (window.salesChart) {
                    window.salesChart.destroy();
                }
                
                // Create chart options
                const options = {
                    series: [{
                        name: 'Sales',
                        data: salesData.map(function(item) { return item.total; })
                    }],
                    chart: {
                        type: 'area',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    xaxis: {
                        type: 'category',
                        categories: salesData.map(function(item) { return item.month; })
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return new Intl.NumberFormat('en-US', { 
                                    style: 'currency', 
                                    currency: 'PHP',
                                    maximumFractionDigits: 0
                                }).format(val);
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                if (val >= 1000000) {
                                    return (val / 1000000).toFixed(1) + 'M';
                                } else if (val >= 1000) {
                                    return (val / 1000).toFixed(1) + 'K';
                                } else {
                                    return val.toFixed(0);
                                }
                            }
                        }
                    }
                };
                
                // Create and render the chart
                window.salesChart = new ApexCharts(chartElement, options);
                window.salesChart.render();
            }
            
            // Initialize chart on page load
            initChart();
            
            // Re-initialize chart when data changes
            if (typeof Livewire !== 'undefined') {
                Livewire.on('reportGenerated', function() {
                    setTimeout(initChart, 100);
                });
            }
        });
    </script>
    @endPushOnce
</x-filament-panels::page>
