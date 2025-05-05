@extends('layouts.admin')
@section('content')

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug data
    console.log('Monthly Data:', {
        total: @json($amountPerMonth),
        ordered: @json($orderedAmountPerMonth),
        delivered: @json($deliveredAmountPerMonth),
        canceled: @json($canceledAmountPerMonth)
    });

    var options = {
        series: [{
            name: 'Total',
            data: @json($amountPerMonth)
        }, {
            name: 'Pending',
            data: @json($orderedAmountPerMonth)
        }, {
            name: 'Delivered',
            data: @json($deliveredAmountPerMonth)
        }, {
            name: 'Canceled',
            data: @json($canceledAmountPerMonth)
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
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
        colors: ['#2377FC', '#FFA500', '#078407', '#FF0000'],
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
            title: {
                text: 'Amount ($)'
            },
            labels: {
                formatter: function(value) {
                    return '$' + value.toFixed(2);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "$" + val.toFixed(2)
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 10
            }
        }
    };
    var chart = new ApexCharts(document.querySelector("#user-order-chart"), options);
    chart.render();
});
</script>
@endpush

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>User Profile: {{ $user->name }}</h3>
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
                    <div class="text-tiny">User Profile</div>
                </li>
            </ul>
        </div>
        <div class="dashboard-layout" style="display: flex; gap: 30px; padding: 20px;">
            <!-- Left: Stats (1/3 width) -->
            <div class="stats-column" style="flex: 0 0 33.3333%; max-width: 33.3333%;">
                <div class="stats-header">Order Statistics</div>
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Total Orders</div>
                    <h4>{{ $totalOrders }}</h4>
                </div>
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Total Amount</div>
                    <h4>${{ number_format($totalAmount, 2) }}</h4>
                </div>
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Delivered Orders</div>
                    <h4>{{ $deliveredOrders }}</h4>
                </div>
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Canceled Orders</div>
                    <h4>{{ $canceledOrders }}</h4>
                </div>
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Account Type</div>
                    <h4>{{ $user->utype === 'ADM' ? 'Administrator' : 'Customer' }}</h4>
                </div>
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Email</div>
                    <h4>{{ $user->email }}</h4>
                </div>
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Created At</div>
                    <h4>{{ $user->created_at }}</h4>
                </div>
            </div>
            <!-- Right: Chart (2/3 width) -->
            <div class="chart-column" style="flex: 0 0 66.6667%; max-width: 66.6667%;">
                <div class="wg-box">
                    <div class="flex items-center justify-between mb-20">
                        <h5 class="stats-header">Monthly Orders & Amount</h5>
                    </div>
                    <div class="chart-container" style="padding: 20px;">
                        <div id="user-order-chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Order History Table (full width) -->
        <div class="tf-section mb-30">
            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Order History</h5>
                </div>
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:70px">OrderNo</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td class="text-center">{{ $order->id }}</td>
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
                                    <td class="text-center">
                                        <a href="{{ route('admin.order.details', ['order_id' => $order->id]) }}">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
