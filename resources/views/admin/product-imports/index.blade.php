@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Product Imports</h3>
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
                    <div class="text-tiny">Product Imports</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name"
                                tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{ route('admin.product-imports.create') }}">
                    <i class="icon-plus"></i>Add new
                </a>
            </div>

            <div class="table-responsive">
                @if(Session::has('status'))
                    <p class="alert alert-success">{{ Session::get('status') }}</p>
                @endif
                @if(Session::has('error'))
                    <p class="alert alert-danger">{{ Session::get('error') }}</p>
                @endif

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Import Price</th>
                            <th>Import Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($imports as $import)
                            <tr>
                                <td>{{ $import->id }}</td>
                                <td class="pname">
                                    <div class="product-item">
                                        <div class="image">
                                            <img src="{{ asset('uploads/products/'.$import->product->image) }}" alt="{{ $import->product->name }}">
                                        </div>
                                        <div class="name">
                                            <div class="body-text">{{ $import->product->name }}</div>
                                            <div class="text-tiny text-muted">{{ $import->product->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $import->size->name }}</td>
                                <td>{{ $import->quantity }}</td>
                                <td>{{ number_format($import->import_price) }}</td>
                                <td>{{ \Carbon\Carbon::parse($import->import_date)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $imports->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection 