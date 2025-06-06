@extends('layouts.app')
@section('content')
<main>
   

    <div class="mb-4 mb-xl-5 pt-7">
        <div class="container mt-5">
      <div class="row">
                <div class="col-12 col-md-6 col-lg-7">
                    <!-- Product Images -->
                    <div class="product-images">
                        <!-- Main Image -->
                        <div class="main-image mb-3">
                            <img src="{{ asset('uploads/products/'.$product->image) }}" 
                                 alt="{{ $product->name }}" class="img-fluid">
                        </div>
                        
                        <!-- Thumbnail Images -->
                        <div class="thumbnail-images d-flex gap-2">
                            <div class="thumb active">
                                <img src="{{ asset('uploads/products/'.$product->image) }}" 
                                     alt="{{ $product->name }}" class="img-fluid">
                  </div>
                            @if($product->images)
                                @foreach(json_decode($product->images) as $image)
                                    @if(!empty(trim($image)))
                                    <div class="thumb">
                                        <img src="{{ asset('uploads/products/'.$image) }}" 
                                             alt="{{ $product->name }}" class="img-fluid">
                  </div>
                                    @endif
                  @endforeach
                            @endif
              </div>
            </div>
                </div>

                <div class="col-12 col-md-6 col-lg-5">
                    <div class="product-summary">
                        <h2 class="product-title mb-3 fs-4">{{ $product->name }}</h2>
                        <div class="product-price mb-3">
                            @if($product->sale_price)
                                <s class="old-price">${{ $product->regular_price }}</s>
                                <span class="current-price">${{ $product->sale_price }}</span>
                            @else
                                <span class="current-price">${{ $product->regular_price }}</span>
                            @endif
        </div>

                        <div class="product-actions">
                            <form method="POST" action="{{ route('cart.add') }}" class="d-flex flex-column gap-3 add-to-cart-form">
                                @csrf
                                <div class="product-sizes mb-4">
                                    <h5 class="mb-3">Available Sizes:</h5>
                                    <div class="size-options">
                                        @foreach($product->sizes as $size)
                                            @if($size->pivot->quantity > 0)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="sizes[]" value="{{ $size->id }}" id="size{{ $size->id }}">
                                                <label class="form-check-label" for="size{{ $size->id }}">
                                                    {{ $size->display_name }} ({{ $size->pivot->quantity }} in stock)
                                                </label>
          </div>
                                            @endif
                                        @endforeach
            </div>
                                    @if($product->sizes->where('pivot.quantity', '>', 0)->count() == 0)
                                        <div class="alert alert-warning">
                                            No sizes available in stock at the moment.
          </div>
                @endif
          </div>

                                <div class="product-quantity mb-4">
                                    <h5 class="mb-3">Quantity:</h5>
                                    <div class="quantity-selector d-flex align-items-center">
                                        <button type="button" class="quantity-button minus">-</button>
                                        <input type="number" class="quantity-input" value="1" min="1" max="99">
                                        <button type="button" class="quantity-button plus">+</button>
          </div>
            </div>

                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="name" value="{{ $product->name }}">
                <input type="hidden" name="price" value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">
                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
              </form>
                  </div>

                        <div class="product-description mt-4">
                            <h5 class="mb-3">Product Description:</h5>
                            <p>{{ $product->description }}</p>
                </div>
                    </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <h4 class="mb-3 fw-bold">Shoe Size Chart & Measurement Guide</h4>
      <div class="mb-4">
        <p class="mb-2">The StockX shoe size chart is converted based on European sizing and applies to all collections.</p>
        <strong>How to measure your shoe size</strong>
        <ul class="mb-2">
          <li>Choose a flat surface and place your heel against a wall.</li>
          <li>Measure the distance from point A (heel) to point B (the longest toe). The distance between A and B is your foot length.</li>
        </ul>
      </div>
      <h5 class="fw-semibold mb-3">How to choose the right shoe size for you</h5>
      <div class="table-responsive">
  <table class="table align-middle text-center bg-white shadow-sm" style="border-collapse: separate; border-spacing: 0; border: 1px solid #000;">
    <thead class="table-dark">
      <tr>
        <th style="border: 1px solid #000;">ASIA</th>
        <th style="border: 1px solid #000;">EU</th>
        <th style="border: 1px solid #000;">US</th>
        <th style="border: 1px solid #000;">UK</th>
        <th style="border: 1px solid #000;">JP</th>
        <th style="border: 1px solid #000;">KR</th>
        <th style="border: 1px solid #000;">CN</th>
        <th style="border: 1px solid #000;">Foot Length (CM)</th>
      </tr>
    </thead>
    <tbody>
      <tr><td style="border: 1px solid #000;">39</td><td style="border: 1px solid #000;">39</td><td style="border: 1px solid #000;">6</td><td style="border: 1px solid #000;">5</td><td style="border: 1px solid #000;">24.5</td><td style="border: 1px solid #000;">245</td><td style="border: 1px solid #000;">245</td><td style="border: 1px solid #000;">24.1</td></tr>
      <tr><td style="border: 1px solid #000;">40</td><td style="border: 1px solid #000;">40</td><td style="border: 1px solid #000;">7</td><td style="border: 1px solid #000;">6</td><td style="border: 1px solid #000;">25</td><td style="border: 1px solid #000;">250</td><td style="border: 1px solid #000;">250</td><td style="border: 1px solid #000;">24.8</td></tr>
      <tr><td style="border: 1px solid #000;">41</td><td style="border: 1px solid #000;">41</td><td style="border: 1px solid #000;">8</td><td style="border: 1px solid #000;">7</td><td style="border: 1px solid #000;">25.5</td><td style="border: 1px solid #000;">255</td><td style="border: 1px solid #000;">255</td><td style="border: 1px solid #000;">25.5</td></tr>
      <tr><td style="border: 1px solid #000;">42</td><td style="border: 1px solid #000;">42</td><td style="border: 1px solid #000;">9</td><td style="border: 1px solid #000;">8</td><td style="border: 1px solid #000;">26</td><td style="border: 1px solid #000;">260</td><td style="border: 1px solid #000;">260</td><td style="border: 1px solid #000;">26.2</td></tr>
      <tr><td style="border: 1px solid #000;">43</td><td style="border: 1px solid #000;">43</td><td style="border: 1px solid #000;">10</td><td style="border: 1px solid #000;">9</td><td style="border: 1px solid #000;">27</td><td style="border: 1px solid #000;">270</td><td style="border: 1px solid #000;">270</td><td style="border: 1px solid #000;">26.9</td></tr>
      <tr><td style="border: 1px solid #000;">44</td><td style="border: 1px solid #000;">44</td><td style="border: 1px solid #000;">11</td><td style="border: 1px solid #000;">10</td><td style="border: 1px solid #000;">27.5</td><td style="border: 1px solid #000;">275</td><td style="border: 1px solid #000;">275</td><td style="border: 1px solid #000;">27.6</td></tr>
      <tr><td style="border: 1px solid #000;">45</td><td style="border: 1px solid #000;">45</td><td style="border: 1px solid #000;">12</td><td style="border: 1px solid #000;">11</td><td style="border: 1px solid #000;">28</td><td style="border: 1px solid #000;">280</td><td style="border: 1px solid #000;">280</td><td style="border: 1px solid #000;">28.3</td></tr>
      <tr><td style="border: 1px solid #000;">46</td><td style="border: 1px solid #000;">46</td><td style="border: 1px solid #000;">13</td><td style="border: 1px solid #000;">12</td><td style="border: 1px solid #000;">28.5</td><td style="border: 1px solid #000;">285</td><td style="border: 1px solid #000;">285</td><td style="border: 1px solid #000;">28.8</td></tr>
    </tbody>
  </table>
</div>
    </div>
  </div>
</div>
</main>

<style>
.pt-7 {
    padding-top: 7rem !important;
}

.product-images {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.main-image {
    width: 100%;
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #f8f9fa;
    border-radius: 4px;
}

.main-image img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.thumbnail-images {
    overflow-x: auto;
    padding: 10px 0;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}

.thumbnail-images::-webkit-scrollbar {
    height: 6px;
}

.thumbnail-images::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.thumbnail-images::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.thumb {
    min-width: 100px;
    height: 100px;
    border: 2px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 5px;
    transition: border-color 0.2s ease;
}

.thumb:hover {
    border-color: #666;
}

.thumb.active {
    border-color: #000;
}

.thumb img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.product-price .old-price {
    color: #999;
    margin-right: 10px;
    text-decoration: line-through;
}

.product-price .current-price {
    color: #000;
    font-weight: bold;
    font-size: 1.2em;
}

.quantity-selector {
    border: 1px solid #ddd;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    background: #fff;
    width: 120px;
    height: 40px;
    position: relative;
}

.quantity-button {
    width: 30px;
    height: 100%;
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #333;
    position: absolute;
    top: 0;
}

.quantity-button.minus {
    left: 0;
    border-right: 1px solid #ddd;
}

.quantity-button.plus {
    right: 0;
    border-left: 1px solid #ddd;
}

.quantity-input {
    width: 100%;
    height: 100%;
    text-align: center;
    border: none;
    background: none;
    padding: 0 30px;
    -moz-appearance: textfield;
    font-size: 14px;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.size-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.form-check-inline {
    margin-right: 1rem;
}

.product-title {
    font-size: 1.5rem !important;
    line-height: 1.2;
    max-width: 80%;
}

@media (max-width: 768px) {
    .pt-7 {
        padding-top: 5rem !important;
    }
    .main-image {
        height: 350px;
    }
    
    .thumb {
        min-width: 80px;
        height: 80px;
    }

    .product-title {
        font-size: 1.25rem !important;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const mainImage = document.querySelector('.main-image img');
    const thumbnails = document.querySelectorAll('.thumb');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            
            mainImage.src = this.querySelector('img').src;
            
            
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    
    const quantityInput = document.querySelector('.quantity-input');
    const hiddenQuantityInput = document.querySelector('input[name="quantity"]');

    document.querySelector('.quantity-button.plus').addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
        hiddenQuantityInput.value = quantityInput.value;
    });

    document.querySelector('.quantity-button.minus').addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            hiddenQuantityInput.value = quantityInput.value;
        }
    });

    quantityInput.addEventListener('change', function() {
        if (this.value < 1) this.value = 1;
        hiddenQuantityInput.value = this.value;
    });

    
    const addToCartForm = document.querySelector('.add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            @if(!Auth::check())
                e.preventDefault();
                window.location.href = "{{ route('login') }}";
                return;
            @endif
            if (document.querySelectorAll('input[name="sizes[]"]:checked').length === 0) {
                e.preventDefault();
                alert('Please select at least one size');
            }
        });
    }
});
</script>
@endpush
@endsection