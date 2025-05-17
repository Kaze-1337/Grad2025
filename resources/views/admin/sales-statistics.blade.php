@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Sales Statistics</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Sales Statistics</div>
                </li>
            </ul>
        </div>        <!-- Period Selection -->
        <div class="wg-box mb-4">
            <form action="{{ route('admin.sales.statistics') }}" method="GET" class="d-flex align-items-center gap-3">
                <div class="form-group mb-0">
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>
            </form>
        </div>        
        
        <div class="charts-row">
            <div class="chart-box">
                <div class="flex items-center justify-between">
                    <h5 class="stats-header">Actual Revenue (Delivered Orders, Last 30 Days)</h5>
                </div>
                <div class="chart-container">
                    <div id="daily-actual-sales-chart"></div>
                </div>
            </div>
            <div class="chart-box">
                <div class="flex items-center justify-between">
                    <h5 class="stats-header">Expected Revenue (Ordered, Last 30 Days)</h5>
                </div>
                <div class="chart-container">
                    <div id="daily-expected-sales-chart"></div>
                </div>
            </div>
        </div>

        <!-- Best Sellers -->
        <div class="wg-box mb-4">
            <h5 class="mb-3">Best Selling Products</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Total Quantity Sold</th>
                            <th class="text-center">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bestSellers as $item)
                            <tr>
                                <td>
                                    <div class="product-item d-flex align-items-center gap-2">
                                        <div class="image" style="width:40px">
                                            <img src="{{ asset('uploads/products/'.$item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 style="width:40px; height:40px; object-fit:cover;">
                                        </div>
                                        <div class="name">
                                            <div class="body-text">{{ $item->product->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->product->SKU }}</td>
                                <td class="text-center">{{ $item->total_quantity }}</td>
                                <td class="text-center">${{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Current Period Sales -->
        <div class="wg-box">
            <h5 class="mb-3">Sales for {{ ucfirst($period) }}</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Quantity Sold</th>
                            <th class="text-center">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $item)
                            <tr>
                                <td>
                                    <div class="product-item d-flex align-items-center gap-2">
                                        <div class="image" style="width:40px">
                                            <img src="{{ asset('uploads/products/'.$item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 style="width:40px; height:40px; object-fit:cover;">
                                        </div>
                                        <div class="name">
                                            <div class="body-text">{{ $item->product->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->product->SKU }}</td>
                                <td class="text-center">{{ $item->total_quantity }}</td>
                                <td class="text-center">${{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expected Sales Table -->
        <div class="wg-box">
            <h5 class="mb-3">Expected Revenue for {{ ucfirst($period) }} (Ordered)</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Expected Quantity</th>
                            <th class="text-center">Expected Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expectedSales as $item)
                            <tr>
                                <td>
                                    <div class="product-item d-flex align-items-center gap-2">
                                        <div class="image" style="width:40px">
                                            <img src="{{ asset('uploads/products/'.$item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 style="width:40px; height:40px; object-fit:cover;">
                                        </div>
                                        <div class="name">
                                            <div class="body-text">{{ $item->product->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->product->SKU }}</td>
                                <td class="text-center">{{ $item->total_quantity }}</td>
                                <td class="text-center">${{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.charts-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.chart-box {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.chart-container {
    margin-top: 30px;
    height: 300px;
}

.stats-header {
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
   
    var actualOptions = {
        series: [{
            name: 'Actual Revenue',
            data: {!! json_encode($dailySales->pluck('total_amount')) !!}
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: { show: false }
        },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#2377FC'],
        xaxis: {
            categories: {!! json_encode($dailySales->pluck('date')) !!},
            labels: { style: { fontSize: '12px', colors: '#212529' } }
        },
        yaxis: {
            labels: { formatter: function(value) { return '$' + value.toFixed(2); } }
        },
        tooltip: {
            y: { formatter: function(value) { return '$' + value.toFixed(2); } }
        }
    };
    var actualChart = new ApexCharts(document.querySelector("#daily-actual-sales-chart"), actualOptions);
    actualChart.render();

    // Expected Revenue Chart
    var expectedOptions = {
        series: [{
            name: 'Expected Revenue',
            data: {!! json_encode($dailyExpectedSales->pluck('total_amount')) !!}
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: { show: false }
        },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#FCA237'],
        xaxis: {
            categories: {!! json_encode($dailyExpectedSales->pluck('date')) !!},
            labels: { style: { fontSize: '12px', colors: '#212529' } }
        },
        yaxis: {
            labels: { formatter: function(value) { return '$' + value.toFixed(2); } }
        },
        tooltip: {
            y: { formatter: function(value) { return '$' + value.toFixed(2); } }
        }
    };
    var expectedChart = new ApexCharts(document.querySelector("#daily-expected-sales-chart"), expectedOptions);
    expectedChart.render();

    // Daily Sales Chart
    var dailyOptions = {
        series: [{
            name: 'Sales Amount',
            data: {!! json_encode($dailySales->pluck('total_amount')) !!}
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        colors: ['#2377FC'],
        xaxis: {
            categories: {!! json_encode($dailySales->pluck('date')) !!},
            labels: {
                style: {
                    fontSize: '12px',
                    colors: '#212529'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return '$' + value.toFixed(2);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return '$' + value.toFixed(2);
                }
            }
        }
    };
    var dailyChart = new ApexCharts(document.querySelector("#daily-sales-chart"), dailyOptions);
    dailyChart.render();

    // Monthly Sales Chart
    var monthlyOptions = {
        series: [{
            name: 'Sales Amount',
            data: {!! json_encode($monthlySales->pluck('total_amount')) !!}
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
                borderRadius: 4
            },
        },
        colors: ['#2377FC'],
        xaxis: {
            categories: {!! json_encode($monthlySales->map(function($item) {
                return date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year));
            })) !!},
            labels: {
                style: {
                    fontSize: '12px',
                    colors: '#212529'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return '$' + value.toFixed(2);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return '$' + value.toFixed(2);
                }
            }
        }
    };
    var monthlyChart = new ApexCharts(document.querySelector("#monthly-sales-chart"), monthlyOptions);
    monthlyChart.render();
});
</script>
@endpush
@endsection
