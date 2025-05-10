<?php

namespace App\Http\Controllers;

use App;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Session;
use App\Models\Size;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart',compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        $options = [];
        if ($request->has('sizes') && !empty($request->sizes)) {
            $size = Size::find($request->sizes[0]);
            if ($size) {
                $options['size'] = 'EU ' . $size->name;
            }
        }
        
        Cart::instance('cart')->add([
            'id' => $request->id,
            'name' => $request->name,
            'qty' => $request->quantity,
            'price' => $request->price,
            'options' => $options
        ])->associate('App\Models\Product');
        
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
       $product = Cart::instance('cart')->get($rowId);
       $qty = $product->qty + 1;
       Cart::instance('cart')->update($rowId, $qty);
       return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
       $product = Cart::instance('cart')->get($rowId);
       $qty = $product->qty - 1;
       Cart::instance('cart')->update($rowId, $qty);
       return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        if (Cart::instance('cart')->content()->count() <= 0) {
            return redirect()->back()->with('error', 'Your cart is empty. Add items before applying a coupon.');
        }

        $request->validate([
            'coupon_code' => 'required|string',
        ]);
    
        $coupon = Coupon::where('code', $request->coupon_code)->first();
    
        if (!$coupon) {
            return redirect()->back()->with('error', 'Invalid coupon code.');
        }
    
        if (!$coupon->expiry_date || $coupon->expiry_date < now()) {
            return redirect()->back()->with('error', 'This coupon has expired.');
        }
    
        if (!$coupon->cart_value || Cart::instance('cart')->subtotal() < $coupon->cart_value) {
            return redirect()->back()->with('error', 'Your cart total does not meet the minimum requirement for this coupon.');
        }
    
        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'cart_value' => $coupon->cart_value,
        ]);
    
        // Gọi phương thức tính toán giảm giá
        $this->caculateDiscount();
    
        return redirect()->back()->with('success', 'Coupon applied successfully!');
    }

    public function caculateDiscount()
    {
        if (Cart::instance('cart')->content()->count() <= 0) {
            Session::forget('discounts');
            return;
        }

        $discount = 0;
        if (Session::has('coupon')) {
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
            }

            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', ''),
            ]);
        }
    }

    public function remove_coupon_code(){
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success', 'Coupon removed successfully!');
    }

    public function checkout()
    {
        if(!Auth::check())
        {
            return redirect()->route('login');
        }
        $address = Address::where('user_id', Auth::user()->id)->where('isdefault',1)->first();
        return view('checkout',compact('address'));
    }

   public function place_an_order(Request $request)
   {
    $user_id = Auth::user()->id;
    $address = Address::where('user_id', $user_id)->where('isdefault',true)->first();

    if(!$address)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|numeric|digits:10',
            'zip' => 'required|numeric|digits:6',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = new Address();
        $address = new Address();
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->zip = $request->zip;
        $address->state = $request->state;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country = '';
        $address->user_id = $user_id;
        $address->isdefault = true;
        $address->save();
    }

    $this->setAmountforCheckout();

    $order = new Order();
    $order->user_id = $user_id;
    $order->subtotal = Session::get('checkout')['subtotal'];
    $order->discount = Session::get('checkout')['discount'];
    $order->tax = Session::get('checkout')['tax'];
    $order->total = Session::get('checkout')['total'];
    $order->name = $address->name;
    $order->phone = $address->phone;
    $order->locality = $address->locality;
    $order->address = $address->address;
    $order->city = $address->city;
    $order->state = $address->state;
    $order->country = $address->country;
    $order->landmark = $address->landmark;
    $order->zip = $address->zip;
    $order->save();

    foreach(Cart::instance('cart')->content() as $item)
    {
        $orderItem = new OrderItem();
        $orderItem->product_id = $item->id;
        $orderItem->order_id = $order->id;
        $orderItem->price = $item->price;
        $orderItem->quantity = $item->qty;
        if ($item->options->has('size')) {
            $orderItem->options = json_encode(['size' => $item->options->size]);
            
            // Cập nhật số lượng trong bảng product_sizes
            $size_name = str_replace('EU ', '', $item->options->size);
            $size = Size::where('name', $size_name)->first();
            if ($size) {
                \DB::table('product_sizes')
                    ->where('product_id', $item->id)
                    ->where('size_id', $size->id)
                    ->decrement('quantity', $item->qty);
            }
        }
        $orderItem->save();
    }

    if($request->mode == "card"){
        //
    } 
    else if ($request->mode == "paypal")
    {
        //
    }

    else if($request->mode == "cod")
    {
        $transaction = new Transaction();
        $transaction->user_id = $user_id;
        $transaction->order_id = $order->id;
        $transaction->status = "pending";
        $transaction->save();
    }

    

    Cart::instance('cart')->destroy();
    Session::forget('checkout');
    Session::forget('coupon');
    Session::forget('discounts');
    Session::put('order_id',$order->id);
    return redirect()->route('cart.order.confirmation');
    
    


    
   }
   public function setAmountforCheckout()
    {
        if(!Cart::instance('cart')->content()->count() > 0)
        {
            Session::forget('checkout');
            return;
        }

        if(Session::has('coupon'))
        {
            Session::put('checkout',[
                'discount' =>Session::get('discounts')['discount'],
                'subtotal' =>Session::get('discounts')['subtotal'],
                'tax' =>Session::get('discounts')['tax'],
                'total' =>Session::get('discounts')['total'],
            ]);
        }
        else
        {
            Session::put('checkout',[
                'discount' =>0,
                'subtotal' =>Cart::instance("cart")->subtotal(),
                'tax' =>Cart::instance("cart")->tax(),
                'total' =>Cart::instance("cart")->total(),
            ]);
        }
    }

    public function order_confirmation()
    {
        if(Session::has('order_id'))
        {
            $order = Order::find(Session::get('order_id'));
            return view('order-confirmation',compact('order'));
        }
        return redirect()->route('cart.index');
    }

    
}