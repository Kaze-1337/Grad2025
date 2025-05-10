@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Product Inventory Statistics</h3>
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
                    <div class="text-tiny">Product Statistics</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <ul>
                <li>Total products: <strong>{{ $totalProducts }}</strong></li>
                <li>Total quantity in stock: <strong>{{ $totalQuantity }}</strong></li>
                <li>Total sold: <strong>{{ $soldQuantity }}</strong></li>
                <li>Products in stock: <strong>{{ $inStock }}</strong></li>
                <li>Out of stock products: <strong>{{ $outOfStock }}</strong></li>
            </ul>

            <h4 class="mt-4 mb-3">Stock details by size</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Size</th>
                            <th>Quantity in stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            @foreach($product->sizes as $size)
                                @if($size->pivot->quantity > 0)
                                    <tr>
                                        <td>
                                            <div class="product-item d-flex align-items-center gap10">
                                                <div class="image" style="width:40px">
                                                    <img src="{{ asset('uploads/products/'.$product->image) }}" alt="{{ $product->name }}" style="width:40px; height:40px; object-fit:cover;">
                                                </div>
                                                <div class="name">
                                                    <div class="body-text">{{ $product->name }}</div>
                                                    <div class="text-tiny text-muted">{{ $product->slug }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $product->SKU }}</td>
                                        <td>{{ $size->name }}</td>
                                        <td>{{ $size->pivot->quantity }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection