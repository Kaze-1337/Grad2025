@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Import Product</h3>
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
                    <a href="{{ route('admin.product-imports') }}">
                        <div class="text-tiny">Product Imports</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Import</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <form class="form-new-product form-style-1" action="{{ route('admin.product-imports.store') }}" method="POST">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Product <span class="tf-color-1">*</span></div>
                    <select name="product_id" class="flex-grow @error('product_id') is-invalid @enderror" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Size <span class="tf-color-1">*</span></div>
                    <select name="size_id" class="flex-grow @error('size_id') is-invalid @enderror" required>
                        <option value="">Select Size</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}" {{ old('size_id') == $size->id ? 'selected' : '' }}>
                                {{ $size->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('size_id') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Quantity <span class="tf-color-1">*</span></div>
                    <input type="number" class="flex-grow @error('quantity') is-invalid @enderror" 
                        name="quantity" value="{{ old('quantity') }}" required min="1">
                    @error('quantity') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Import Price <span class="tf-color-1">*</span></div>
                    <input type="number" class="flex-grow @error('import_price') is-invalid @enderror" 
                        name="import_price" value="{{ old('import_price') }}" required min="0" step="0.01">
                    @error('import_price') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Import Date <span class="tf-color-1">*</span></div>
                    <input type="date" class="flex-grow @error('import_date') is-invalid @enderror" 
                        name="import_date" value="{{ old('import_date', date('Y-m-d')) }}" required>
                    @error('import_date') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                </fieldset>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 