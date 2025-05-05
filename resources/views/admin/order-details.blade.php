@extends('layouts.admin')
@section('content')
    <style>
        .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;
        }

        .table-striped .image {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            flex-shrink: 0;
            border-radius: 10px;
            overflow: hidden;
            background: #f5f5f5;
        }

        .table-striped td.name-col, .table-striped th.name-col {
            min-width: 220px;
            max-width: 350px;
        }

        .table-striped td.price-col, .table-striped th.price-col {
            min-width: 80px;
            max-width: 110px;
            text-align: center;
        }

        .table-striped td.qty-col, .table-striped th.qty-col {
            min-width: 60px;
            max-width: 80px;
            text-align: center;
        }

        .table-striped td.sku-col, .table-striped th.sku-col {
            min-width: 90px;
            max-width: 120px;
            text-align: center;
        }

        .table-striped td.cat-col, .table-striped th.cat-col {
            min-width: 90px;
            max-width: 120px;
            text-align: center;
        }

        .table-striped td.brand-col, .table-striped th.brand-col {
            min-width: 90px;
            max-width: 120px;
            text-align: center;
        }

        .table-striped td.opt-col, .table-striped th.opt-col {
            min-width: 80px;
            max-width: 130px;
            text-align: center;
        }

        .table-striped td.ret-col, .table-striped th.ret-col {
            min-width: 70px;
            max-width: 90px;
            text-align: center;
        }

        .table-striped td.act-col, .table-striped th.act-col {
            min-width: 50px;
            max-width: 70px;
            text-align: center;
        }

        .pname {
            display: flex;
            gap: 13px;
            align-items: center;
        }

        .name {
            flex: 1;
        }

        .name a {
            color: #333;
            text-decoration: none;
        }
    </style>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Order Details</h3>
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
                        <div class="text-tiny">Order Details</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Items</h5>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Back</a>
                </div>
                <div class="table-responsive">
                    @if(Session::has('status'))
                        <p class="alert alert-success">{{ Session::get('status') }}</p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Order No</th>
                            <td>{{ $order->id }}</td>
                            <th>Mobile</th>
                            <td>{{ $order->phone }}</td>
                            <th>Zip Code</th>
                            <td>{{ $order->zip }}</td>
                        </tr>
                        <tr>
                            <th>Order Date</th>
                            <td>{{ $order->created_at }}</td>
                            <th>Deliverd Date</th>
                            <td>{{ $order->delivered_date }}</td>
                            <th>Canceled Date</th>
                            <td>{{ $order->canceled_date }}</td>
                        </tr>
                        <tr>
                            <th>Order Status</th>
                            <td colspan="5">
                                @if($order->status == 'delivered')
                                    <span class="badge bg-success">Delivered</span>
                                @elseif($order->status == 'canceled')
                                    <span class="badge bg-danger">Canceled</span>
                                @else
                                    <span class="badge bg-warning">Ordered</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Items</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="name-col">Name</th>
                                        <th class="text-center price-col">Price</th>
                                        <th class="text-center qty-col">Quantity</th>
                                        <th class="text-center sku-col">SKU</th>
                                        <th class="text-center cat-col">Category</th>
                                        <th class="text-center brand-col">Brand</th>
                                        <th class="text-center opt-col">Options</th>
                                        <th class="text-center ret-col">Return Status</th>
                                        <th class="text-center act-col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderItems as $item)
                                        <tr>
                                            <td class="name-col">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <img src="{{ asset('uploads/products/' . $item->product->image) }}" alt="" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                                                    <div>
                                                        <div>{{ $item->product->name }}</div>
                                                        <div style="font-size: 12px; color: #888;">{{ $item->product->short_description }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center price-col">${{ number_format($item->price, 2) }}</td>
                                            <td class="text-center qty-col">{{ $item->quantity }}</td>
                                            <td class="text-center sku-col">{{ $item->product->SKU }}</td>
                                            <td class="text-center cat-col">{{ $item->product->category->name }}</td>
                                            <td class="text-center brand-col">{{ $item->product->brand->name }}</td>
                                            <td class="text-center opt-col">
                                                @php
                                                    $options = json_decode($item->options, true);
                                                @endphp
                                                @if($options)
                                                    @foreach($options as $key => $value)
                                                        <div>{{ $key }}: {{ $value }}</div>
                                                    @endforeach
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="text-center ret-col">{{ $item->rstatus == 0 ? "No" : "Yes" }}</td>
                                            <td class="text-center act-col">
                                                <div class="list-icon-function view-icon">
                                                    <div class="item eye">
                                                        <i class="icon-eye"></i>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="divider"></div>
                        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

                        </div>
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                        {{ $orderItems->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                <div class="wg-box mt-5">
                    <h5>Shipping Address</h5>
                    <div class="my-account__address-item col-md-6">
                        <div class="my-account__address-item__detail">
                            <p>Name: {{ $order->name }}</p>
                            <p>Location: {{ $order->address }}</p>
                            <p>{{ $order->locality }}</p>
                            <p>{{ $order->city }}, {{ $order->country }} </p>
                            <p>{{ $order->landmark }}</p>
                            <p>ZIP CODE: {{ $order->zip }}</p>
                            <br>
                            <p>Mobile: {{ $order->phone }}</p>
                        </div>
                    </div>
                </div>

                <div class="wg-box mt-5">
                    <h5>Transactions</h5>
                    <table class="table table-striped table-bordered table-transaction">
                        <tbody>
                            <tr>
                                <th>Subtotal</th>
                                <td>${{$order->subtotal  }}</td>
                                <th>Tax</th>
                                <td>${{$order->tax  }}</td>
                                <th>Discount</th>
                                <td>${{$order->discount  }}</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td>{{$order->total  }}</td>
                                <th>Payment Mode</th>
                                <td>{{$order->mode  }}</td>
                                <th>Status</th>
                                <td>
                                    @if($transaction->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($transaction->status == 'declined')
                                        <span class="badge bg-danger">Declined</span>
                                    @elseif($transaction->status == 'refunded')
                                        <span class="badge bg-secondary">Refunded</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @if($transaction)
                        <p>Status: {{ $transaction->status }}</p>
                    @else
                        <p>No transaction found for this order.</p>
                    @endif
                </div>

                <div class="wg-box mt-5">
                    <h5>Update Order Status</h5>
                    <form action="{{ route('admin.order.status.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="select">
                                <select id="order_status" name="order_status">
                                    <option value="ordered"{{ $order->status =='order' ? "selected":"" }}>Ordered</option>
                                    <option value="delivered"{{ $order->status =='delivered' ? "selected":"" }}>Delivered</option>
                                    <option value="canceled"{{ $order->status =='canceled' ? "selected":"" }}>Canceled</option>
                                </select>
                            </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary tf-button w208">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection