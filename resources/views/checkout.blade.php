@extends('layouts.app')
@section('content')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Shipping and Checkout</h2>
      <div class="checkout-steps">
        <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Shopping Bag</span>
            <em>Manage Your Items List</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Shipping and Checkout</span>
            <em>Checkout Your Items List</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Confirmation</span>
            <em>Review And Submit Your Order</em>
          </span>
        </a>
      </div>
      <form  name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST">
        @csrf
        <input type="hidden" name="mode" value="cod">
        <div class="checkout-form">
          <div class="billing-info__wrapper">
            <div class="row">
              <div class="col-6">
                <h4>SHIPPING DETAILS</h4>
              </div>
              <div class="col-6">
              </div>
            </div>
            <div class="row mt-5">
              <div class="col-md-6">
                  <div class="form-floating my-3">
                      <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                      <label for="name">Full Name *</label>
                      @error('name')<span class="text-danger">{{ $message }}</span> @enderror
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-floating my-3">
                      <input type="text" class="form-control" name="phone" required value="{{ old('phone') }}">
                      <label for="phone">Phone Number *</label>
                      @error('phone')<span class="text-danger">{{ $message }}</span> @enderror
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-floating my-3">
                      <input type="text" class="form-control" name="zip" required value="{{ old('zip') }}">
                      <label for="zip">Pincode *</label>
                      @error('zip')<span class="text-danger">{{ $message }}</span> @enderror
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-floating mt-3 mb-3">
                      <input type="text" class="form-control" name="state" required value="{{ old('state') }}">
                      <label for="state">State *</label>
                      @error('state')<span class="text-danger">{{ $message }}</span> @enderror
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-floating my-3">
                      <input type="text" class="form-control" name="city" required value="{{ old('city') }}">
                      <label for="city">Town / City *</label>
                      @error('city')<span class="text-danger">{{ $message }}</span> @enderror
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-floating my-3">
                      <input type="text" class="form-control" name="address" required value="{{ old('address') }}">
                      <label for="address">House no, Building Name *</label>
                      @error('address')<span class="text-danger">{{ $message }}</span> @enderror
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-floating my-3">
                      <input type="text" class="form-control" name="locality" required value="{{ old('locality') }}">
                      <label for="locality">Road Name, Area, Colony *</label>
                      @error('locality')<span class="text-danger">{{ $message }}</span> @enderror
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-floating my-3">
                      <input type="text" class="form-control" name="landmark" required value="{{ old('landmark') }}">
                      <label for="landmark">Note *</label>
                      @error('landmark')<span class="text-danger">{{ $message }}</span> @enderror
                  </div>
              </div>
          </div>
          </div>
          <div class="checkout__totals-wrapper">
            <div class="sticky-content">
              <div class="checkout__totals">
                <h3>Your Order</h3>
                <table class="checkout-cart-items">
                  <thead>
                    <tr>
                      <th>PRODUCT</th>
                      <th align="right">SUBTOTAL</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach (Surfsidemedia\Shoppingcart\Facades\Cart::instance('cart') as $item)
                    <tr>
                      <td>
                        {{ $item->name }} x {{ $item->qty }}
                      </td>
                      <td align="right">
                        ${{ $item->subtotal() }}
                      </td>
                    </tr>
                    
                    @endforeach
                  </tbody>
                </table>
                @if(Session::has('discounts'))
                <table class="checkout-totals">
                    <tbody>
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-right">${{ Surfsidemedia\Shoppingcart\Facades\Cart::instance('cart')->subTotal() }}</td>
                        </tr>
                        @if(Session::has('coupon'))
                        <tr>
                            <th>Discount {{ Session::get('coupon')['code'] }}</th>
                            <td class="text-right">${{ Session::get('discounts')['discount'] }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Subtotal after discount</th>
                            <td class="text-right">${{ Session::get('discounts')['subtotal'] }}</td>
                        </tr>
                        <tr>
                            <th>Shipping</th>
                            <td class="text-right">Free</td>
                        </tr>
                        <tr>
                            <th>VAT</th>
                            <td class="text-right">${{ Session::get('discounts')['tax'] }}</td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td class="text-right">${{ Session::get('discounts')['total'] }}</td>
                        </tr>
                    </tbody>
                  </table>

                @else
                <table class="checkout-totals">
                  <tbody>
                    <tr>
                      <th>SUBTOTAL</th>
                      <td align="right">${{ Surfsidemedia\Shoppingcart\Facades\Cart::instance('cart')->subtotal() }}</td>
                    </tr>
                    <tr>
                      <th>SHIPPING</th>
                      <td align="right">Free shipping</td>
                    </tr>
                    <tr>
                      <th>VAT</th>
                      <td align="right">${{ Surfsidemedia\Shoppingcart\Facades\Cart::instance('cart')->tax() }}</td>
                    </tr>
                    <tr>
                      <th>TOTAL</th>
                      <td align="right">${{ Surfsidemedia\Shoppingcart\Facades\Cart::instance('cart')->total() }}</td>
                    </tr>
                  </tbody>
                </table>
                @endif
              </div>
             
              <button id="btn-place-order" class="btn btn-primary btn-checkout">PLACE ORDER</button>
            </div>
          </div>
        </div>
      </form>
    </section>
  </main>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#checkout-form').on('submit', function (e) {
            e.preventDefault(); // Ngăn chặn hành vi mặc định (reload trang)

            // Thu thập dữ liệu form
            let formData = $(this).serialize();

            // Gửi AJAX request
            $.ajax({
                url: $(this).attr('action'), // URL từ action của form
                method: 'POST',
                data: formData,
                success: function (response) {
                    // Xử lý khi thành công
                    alert('Order placed successfully!');
                    window.location.href = '/order-confirmation';
                    console.log(response);
                },
                error: function (xhr) {
                    // Xử lý khi có lỗi
                    alert('An error occurred while placing the order.');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endpush