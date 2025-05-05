@extends('layouts.admin')
@section('content')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var options = {
        series: [{
            name: 'Total',
            data: [{{ $AmountM }}]
        }, {
            name: 'Pending',
            data: [{{ $OrderedAmountM }}]
        }, {
            name: 'Delivered',
            data: [{{ $DeliveredAmountM }}]
        }, {
            name: 'Canceled',
            data: [{{ $CancelededAmountM }}]
        }, {
            name: 'Import',
            data: [{{ $ImportAmountM }}]
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
        dataLabels: {
            enabled: false
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'left',
            fontSize: '14px',
            markers: {
                width: 12,
                height: 12,
                radius: 12
            }
        },
        colors: ['#2377FC', '#FFA500', '#078407', '#FF0000', '#800080'],
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            labels: {
                style: {
                    fontSize: '12px',
                    colors: '#212529'
                }
            }
        },
        yaxis: {
            show: true,
            labels: {
                formatter: function(val) {
                    return "$" + val.toFixed(2);
                },
                style: {
                    fontSize: '12px'
                }
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return "$ " + val.toFixed(2)
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#line-chart-8"), options);
    chart.render();
});
</script>

<style>
.dashboard-layout {
    display: flex;
    gap: 30px;
    padding: 20px;
}

.stats-column {
    flex: 0 0 30%;
}

.chart-column {
    flex: 0 0 70%;
    min-height: 500px;
}

.chart-box {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.chart-container {
    margin-top: 30px;
    height: 350px;
}

.revenue-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    margin-bottom: 30px;
}

.grid-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
}

.stat-item {
    text-align: center;
    padding: 10px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.stat-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 14px;
    color: #666;
}

.stat-value {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.bg-primary { background-color: #2377FC; }
.bg-warning { background-color: #FFA500; }
.bg-success { background-color: #078407; }
.bg-danger { background-color: #FF0000; }
.bg-purple { background-color: #800080; }

.wg-chart-default {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.stats-header {
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

#line-chart-8 {
    width: 100%;
    height: 100%;
}

@media (max-width: 1200px) {
    .grid-stats {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-layout {
        flex-direction: column;
    }
    
    .stats-column,
    .chart-column {
        flex: 0 0 100%;
    }
    
    .grid-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .chart-container {
        height: 300px;
    }
}
</style>
@endpush

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="dashboard-layout">
            <!-- Left Column - Statistics -->
            <div class="stats-column">
                <div class="stats-header">Orders & Import Statistics</div>
                <!-- Orders Statistics -->
                <div class="wg-chart-default">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-shopping-bag"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Total Orders</div>
                                <h4>{{ $dashboardDatas[0]->Total }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-dollar-sign"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Total Amount</div>
                                <h4>${{ number_format($dashboardDatas[0]->TotalAmount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-shopping-bag"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Pending Orders</div>
                                <h4>{{ $dashboardDatas[0]->TotalOrdered }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-dollar-sign"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Pending Orders Amount</div>
                                <h4>${{ number_format($dashboardDatas[0]->TotalOrderedAmount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-shopping-bag"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Delivered Orders</div>
                                <h4>{{ $dashboardDatas[0]->TotalDelivered }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-dollar-sign"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Delivered Orders Amount</div>
                                <h4>${{ number_format($dashboardDatas[0]->TotalDeliveredAmount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-shopping-bag"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Canceled Orders</div>
                                <h4>{{ $dashboardDatas[0]->TotalCanceled }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-dollar-sign"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Canceled Orders Amount</div>
                                <h4>${{ number_format($dashboardDatas[0]->TotalCanceledAmount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Import Statistics -->
                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-package"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Total Import Value</div>
                                <h4>${{ number_format($importStats[0]->TotalImportAmount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wg-chart-default mb-20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap14">
                            <div class="image ic-bg">
                                <i class="icon-box"></i>
                            </div>
                            <div>
                                <div class="body-text mb-2">Total Imported Items</div>
                                <h4>{{ number_format($importStats[0]->TotalQuantity) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Chart -->
            <div class="chart-column">
                <div class="chart-box">
                    <div class="flex items-center justify-between">
                        <h5 class="stats-header">Monthly Revenue</h5>
                    </div>
                    <!-- Revenue Summary -->
                    <div class="revenue-summary">
                        <div class="grid-stats">
                            <div class="stat-item">
                                <div class="stat-label">
                                    <span class="dot bg-primary"></span>
                                    <span>Total</span>
                                </div>
                                <div class="stat-value">${{ number_format($TotalAmount, 2) }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">
                                    <span class="dot bg-warning"></span>
                                    <span>Pending</span>
                                </div>
                                <div class="stat-value">${{ number_format($TotalOrderedAmount, 2) }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">
                                    <span class="dot bg-success"></span>
                                    <span>Delivered</span>
                                </div>
                                <div class="stat-value">${{ number_format($TotalDeliveredAmount, 2) }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">
                                    <span class="dot bg-danger"></span>
                                    <span>Canceled</span>
                                </div>
                                <div class="stat-value">${{ number_format($TotalCanceledAmount, 2) }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">
                                    <span class="dot bg-purple"></span>
                                    <span>Import</span>
                                </div>
                                <div class="stat-value">${{ number_format($TotalImportAmount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="line-chart-8"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="tf-section mb-30">
            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Recent orders</h5>
                    <div class="dropdown default">
                        <a class="btn btn-secondary dropdown-toggle" href="{{ route('admin.orders') }}">
                            <span class="view-all">View all</span>
                        </a>
                    </div>
                </div>
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:70px">OrderNo</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Tax</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Total Items</th>
                                    <th class="text-center">Delivered On</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                
                                
                                <tr>
                                    <td class="text-center">{{ $order->id }}</td>
                                    <td class="text-center">{{ $order->name }}</td>
                                    <td class="text-center">{{ $order->phone }}</td>
                                    <td class="text-center">${{ number_format($order->subtotal, 2) }}</td>
                                    <td class="text-center">${{ number_format($order->tax, 2) }}</td>
                                    <td class="text-center">${{ number_format($order->total, 2) }}</td>
                                    <td class="text-center">
                                        @if($order->status == 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($order->status == 'canceled')
                                            <span class="badge bg-danger">Canceled</span>
                                        @else
                                            <span class="badge bg-warning">Ordered</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $order->created_at }}</td>
                                    <td class="text-center">{{ $order->orderItems->count() }}</td>
                                    <td class="text-center">{{ $order->delivered_date }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.order.details',['order_id'=>$order->id]) }}">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection