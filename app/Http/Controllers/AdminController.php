<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Slide;
use App\Models\Contact;
use App\Models\Size;
use App\Models\ProductImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get()->take(10);

        $dashboardDatas = DB::select("SELECT 
            SUM(total) AS TotalAmount,
            SUM(CASE WHEN status = 'ordered' THEN total ELSE 0 END) AS TotalOrderedAmount,
            SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) AS TotalDeliveredAmount,
            SUM(CASE WHEN status = 'canceled' THEN total ELSE 0 END) AS TotalCanceledAmount,
            COUNT(*) AS Total,
            SUM(CASE WHEN status = 'ordered' THEN 1 ELSE 0 END) AS TotalOrdered,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS TotalDelivered,
            SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) AS TotalCanceled
        FROM orders");

        $importStats = DB::select("SELECT 
            SUM(quantity * import_price) as TotalImportAmount,
            COUNT(*) as TotalImports,
            SUM(quantity) as TotalQuantity
        FROM product_imports");

       
        $monthlyData = DB::select("SELECT 
            MONTH(created_at) as month,
            SUM(total) as TotalAmount,
            SUM(CASE WHEN status = 'ordered' THEN total ELSE 0 END) as TotalOrderedAmount,
            SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) as TotalDeliveredAmount,
            SUM(CASE WHEN status = 'canceled' THEN total ELSE 0 END) as TotalCanceledAmount
        FROM orders 
        WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
        GROUP BY MONTH(created_at)
        ORDER BY MONTH(created_at)");

        $monthlyImports = DB::select("SELECT 
            MONTH(import_date) as month,
            SUM(quantity * import_price) as TotalImportAmount,
            SUM(quantity) as TotalQuantity
        FROM product_imports 
        WHERE YEAR(import_date) = YEAR(CURRENT_DATE())
        GROUP BY MONTH(import_date)
        ORDER BY MONTH(import_date)");

        $monthlyAmounts = array_fill(0, 12, 0);
        $monthlyOrderedAmounts = array_fill(0, 12, 0);
        $monthlyDeliveredAmounts = array_fill(0, 12, 0);
        $monthlyCanceledAmounts = array_fill(0, 12, 0);
        $monthlyImportAmounts = array_fill(0, 12, 0);
        $monthlyImportQuantities = array_fill(0, 12, 0);

        foreach ($monthlyData as $data) {
            $index = $data->month - 1; 
            $monthlyAmounts[$index] = (float)$data->TotalAmount;
            $monthlyOrderedAmounts[$index] = (float)$data->TotalOrderedAmount;
            $monthlyDeliveredAmounts[$index] = (float)$data->TotalDeliveredAmount;
            $monthlyCanceledAmounts[$index] = (float)$data->TotalCanceledAmount;
        }

        foreach ($monthlyImports as $data) {
            $index = $data->month - 1;
            $monthlyImportAmounts[$index] = (float)$data->TotalImportAmount;
            $monthlyImportQuantities[$index] = (int)$data->TotalQuantity;
        }

        $AmountM = implode(',', $monthlyAmounts);
        $OrderedAmountM = implode(',', $monthlyOrderedAmounts);
        $DeliveredAmountM = implode(',', $monthlyDeliveredAmounts);
        $CancelededAmountM = implode(',', $monthlyCanceledAmounts);
        $ImportAmountM = implode(',', $monthlyImportAmounts);
        $ImportQuantityM = implode(',', $monthlyImportQuantities);

        $TotalAmount = array_sum($monthlyAmounts);
        $TotalOrderedAmount = array_sum($monthlyOrderedAmounts);
        $TotalDeliveredAmount = array_sum($monthlyDeliveredAmounts);
        $TotalCanceledAmount = array_sum($monthlyCanceledAmounts);
        $TotalImportAmount = array_sum($monthlyImportAmounts);
        $TotalImportQuantity = array_sum($monthlyImportQuantities);

        return view('admin.index', compact(
            'orders',
            'dashboardDatas',
            'importStats',
            'AmountM',
            'OrderedAmountM',
            'DeliveredAmountM',
            'CancelededAmountM',
            'ImportAmountM',
            'ImportQuantityM',
            'TotalAmount',
            'TotalOrderedAmount',
            'TotalDeliveredAmount',
            'TotalCanceledAmount',
            'TotalImportAmount',
            'TotalImportQuantity'
        ));
    }

//For Brands
    public function brands()
    {
        $brands = Brand::orderBy('id', 'desc')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand()
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        $image = $request->file('image');

        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;

        $this->GenerateBrandThumbnailsImage($image, $file_name);

        $brand->image = $file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'mimes:jpg,jpeg,png|max:2048',
        ]);
        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasfile('image')) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }
            $image = $request->file('image');

            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;

            $this->GenerateBrandThumbnailsImage($image, $file_name);

            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully');
    }

    public function GenerateBrandThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->save($destinationPath . '/' . $imageName);
    }

    public function brand_delete($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully');
    }
//For Categories
    public function categories()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    public function category_add()
    {
        return view('admin.category-add');
    }

    public function category_store(Request $request)
    {
        {
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:categories,slug',
                'image' => 'mimes:jpg,jpeg,png|max:2048',
            ]);

            $category = new Category();
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);

            $image = $request->file('image');

            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;

            $this->GenerateCategoryThumbnailsImage($image, $file_name);

            $category->image = $file_name;
            $category->save();

            return redirect()->route('admin.categories')->with('status', 'Category has been added successfully');
        }
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->save($destinationPath . '/' . $imageName);
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'mimes:jpg,jpeg,png|max:2048',
        ]);
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->hasfile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
                File::delete(public_path('uploads/categories') . '/' . $category->image);
            }
            $image = $request->file('image');

            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;

            $this->GenerateCategoryThumbnailsImage($image, $file_name);

            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully');
    }

    public function category_delete($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully');
    }
//For Products
    public function products()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_add()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $sizes = Size::all();
        return view('admin.product-add', compact('categories', 'brands', 'sizes'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sizes' => 'required_if:has_size,1|array',
            'sizes.*' => 'exists:sizes,id'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->featured = $request->has('featured');
        $product->has_size = $request->has('sizes');
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/products'), $image_name);
            $product->image = $image_name;
            $this->GenerateProductThumbnail(public_path('uploads/products/') . $image_name, $image_name);
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $image_name = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products'), $image_name);
                $images[] = $image_name;
            }
            $product->images = json_encode($images);
        }

        $product->save();

        if ($request->has('sizes')) {
            $sizes = [];
            foreach ($request->sizes as $size_id) {
                $sizes[$size_id] = ['quantity' => 0];
            }
            $product->sizes()->sync($sizes);
        }

        return redirect()->route('admin.products')
            ->with('success', 'Product has been added successfully!');
    }

    public function product_edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        $sizes = Size::all();
        return view('admin.product-edit', compact('product', 'categories', 'brands', 'sizes'));
    }

    public function product_update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $id,
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sizes' => 'required_if:has_size,1|array',
            'sizes.*' => 'exists:sizes,id'
        ]);

        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->featured = $request->has('featured');
        $product->has_size = $request->has('has_size');
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        if ($request->hasFile('image')) {

            if ($product->image && file_exists(public_path('uploads/products/' . $product->image))) {
                unlink(public_path('uploads/products/' . $product->image));
            }

            
            if ($product->image && file_exists(public_path('uploads/products/thumbnails/' . $product->image))) {
                unlink(public_path('uploads/products/thumbnails/' . $product->image));
            }

            $image = $request->file('image');
            $image_name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/products'), $image_name);
            $product->image = $image_name;
           
            $this->GenerateProductThumbnail(public_path('uploads/products/') . $image_name, $image_name);
        }

        if ($request->hasFile('images')) {
        
            if ($product->images) {
                $old_images = json_decode($product->images);
                foreach ($old_images as $old_image) {
                    if (file_exists(public_path('uploads/products/' . $old_image))) {
                        unlink(public_path('uploads/products/' . $old_image));
                    }
                }
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $image_name = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products'), $image_name);
                $images[] = $image_name;
            }
            $product->images = json_encode($images);
        }

        $product->save();

        if ($request->has('sizes')) {
            $sizes = [];
            foreach ($request->sizes as $size_id) {
                $sizes[$size_id] = ['quantity' => 0];
            }
            $product->sizes()->sync($sizes);
        }

        return redirect()->route('admin.products')
            ->with('status', 'Product has been updated successfully!');
    }

    public function product_delete($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && file_exists(public_path('uploads/products/' . $product->image))) {
            unlink(public_path('uploads/products/' . $product->image));
        }

        if ($product->images) {
            $images = json_decode($product->images);
            foreach ($images as $image) {
                if (file_exists(public_path('uploads/products/' . $image))) {
                    unlink(public_path('uploads/products/' . $image));
                }
            }
        }

        $product->delete();

        return redirect()->route('admin.products')
            ->with('success', 'Product has been deleted successfully!');
    }

    
    public function GenerateProductThumbnail($imagePath, $imageName)
    {
        $destinationPath = public_path('uploads/products/thumbnails');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $img = Image::read($imagePath);
        $img->cover(124, 124, 'top');
        $img->save($destinationPath . '/' . $imageName);
    }

    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }

    public function coupon_add()
    {
        return view('admin.coupon-add');
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully');
    }

    public function coupon_edit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit', compact('coupon'));
    }

    public function coupon_update(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully');
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfully');
    }

    public function orders()
    {
        $orders = Order::orderBy('created_at', 'DESC')->paginate(12);
        return view('admin.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view('admin.order-details', compact('order', 'orderItems', 'transaction'));
    }

    public function update_order_status(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!$order) {
            return back()->with('error', 'Order not found.');
        }

        if ($request->order_status == 'canceled' && $order->status != 'canceled') {
 
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            foreach ($orderItems as $item) {
                if ($item->options) {
                    $options = json_decode($item->options, true);
                    if (isset($options['size'])) {
                
                        $size_name = str_replace('EU ', '', $options['size']);
                        $size = Size::where('name', $size_name)->first();

                        if ($size) {
                          
                            \DB::table('product_sizes')
                                ->where('product_id', $item->product_id)
                                ->where('size_id', $size->id)
                                ->increment('quantity', $item->quantity);
                        }
                    }
                }
            }
        }

        $order->status = $request->order_status;

        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } else if ($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
        }

        $order->save();

        if ($request->order_status == 'delivered') {
            $transaction = Transaction::where('order_id', $request->order_id)->first();

            if ($transaction) {
                $transaction->status = 'approved';
                $transaction->save();
            }
        }

        Cache::forget('transaction_' . $request->order_id);

        $order->refresh();

        return back()->with('status', 'Order status has been updated successfully');
    }

    public function slides()
    {
        $slides = Slide::orderBy('id', 'DESC')->paginate(12);
        return view('admin.slides', compact('slides'));
    }

    public function slide_add()
    {
        return view('admin.slide-add');
    }

    public function slide_store(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:0,1',
        ]);
        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        $image = $request->file('image');

        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;

        $this->GenerateSlideThumbnailsImage($image, $file_name);

        $slide->image = $file_name;
        $slide->save();
        return redirect()->route('admin.slides')->with("status", "Slide added successfully");
    }

    public function GenerateSlideThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->save($destinationPath . '/' . $imageName);
    }

    public function slide_edit($id)
    {
        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide'));
    }

    public function slide_update(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:0,1',
        ]);
        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
                File::delete(public_path('uploads/slides') . '/' . $slide->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateSlideThumbnailsImage($image, $file_name);
            $slide->image = $file_name;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with("status", "Slide updated successfully");
    }

    public function slide_delete($id)
    {
        $slide = Slide::find($id);
        if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
            File::delete(public_path('uploads/slides') . '/' . $slide->image);
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with("status", "Slide deleted successfully");
    }

    public function contacts()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.contacts', compact('contacts'));
    }

    public function contact_delete($id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('admin.contacts')->with("status", "Message deleted successfully");
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name','LIKE',"%{$query}%")->get()->take(5);
        return response()->json($results);
    }

    public function product_imports()
    {
        $imports = ProductImport::with(['product', 'size'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.product-imports.index', compact('imports'));
    }

    public function product_import_create()
    {
        $products = Product::all();
        $sizes = Size::all();
        return view('admin.product-imports.create', compact('products', 'sizes'));
    }

    public function product_import_store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'size_id' => 'required|exists:sizes,id',
                'quantity' => 'required|integer|min:1',
                'import_price' => 'required|numeric|min:0',
                'import_date' => 'required|date'
            ]);

            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            $import = new ProductImport();
            $import->product_id = $request->product_id;
            $import->size_id = $request->size_id;
            $import->quantity = $request->quantity;
            $import->import_price = $request->import_price;
            $import->import_date = $request->import_date;
            $import->save();

        
            if (!$product->has_size) {
                $product->has_size = true;
                $product->save();
            }

            $existingSize = DB::table('product_sizes')
                ->where('product_id', $request->product_id)
                ->where('size_id', $request->size_id)
                ->first();

            if ($existingSize) {
                DB::table('product_sizes')
                    ->where('product_id', $request->product_id)
                    ->where('size_id', $request->size_id)
                    ->update([
                        'quantity' => DB::raw('quantity + ' . $request->quantity),
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('product_sizes')->insert([
                    'product_id' => $request->product_id,
                    'size_id' => $request->size_id,
                    'quantity' => $request->quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return redirect()->route('admin.product-imports')->with('status', 'Product import has been added successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            \Log::error('Product Import Error - Product not found: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'The selected product was not found.')
                ->withInput();
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            \Log::error('Product Import Error - Validation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Product Import Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'An error occurred while importing product. Please try again.')
                ->withInput();
        }
    }


//For Users
    public function users()
    {
        $users = \App\Models\User::orderBy('id', 'asc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function userProfile($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $orders = Order::where('user_id', $id)->orderBy('created_at', 'desc')->get();

        $monthlyOrders = Order::selectRaw('
            MONTH(created_at) as month,
            COUNT(*) as total_orders,
            SUM(total) as total_amount,
            SUM(CASE WHEN status = "ordered" THEN 1 ELSE 0 END) as ordered_count,
            SUM(CASE WHEN status = "ordered" THEN total ELSE 0 END) as ordered_amount,
            SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_count,
            SUM(CASE WHEN status = "delivered" THEN total ELSE 0 END) as delivered_amount,
            SUM(CASE WHEN status = "canceled" THEN 1 ELSE 0 END) as canceled_count,
            SUM(CASE WHEN status = "canceled" THEN total ELSE 0 END) as canceled_amount
        ')
            ->where('user_id', $id)
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $ordersPerMonth = array_fill(0, 12, 0);
        $amountPerMonth = array_fill(0, 12, 0);
        $orderedPerMonth = array_fill(0, 12, 0);
        $orderedAmountPerMonth = array_fill(0, 12, 0);
        $deliveredPerMonth = array_fill(0, 12, 0);
        $deliveredAmountPerMonth = array_fill(0, 12, 0);
        $canceledPerMonth = array_fill(0, 12, 0);
        $canceledAmountPerMonth = array_fill(0, 12, 0);

        foreach ($monthlyOrders as $item) {
            $index = $item->month - 1;
            $ordersPerMonth[$index] = $item->total_orders;
            $amountPerMonth[$index] = (float)$item->total_amount;
            $orderedPerMonth[$index] = $item->ordered_count;
            $orderedAmountPerMonth[$index] = (float)$item->ordered_amount;
            $deliveredPerMonth[$index] = $item->delivered_count;
            $deliveredAmountPerMonth[$index] = (float)$item->delivered_amount;
            $canceledPerMonth[$index] = $item->canceled_count;
            $canceledAmountPerMonth[$index] = (float)$item->canceled_amount;
        }

        $totalOrders = $orders->count();
        $totalAmount = $orders->sum('total');
        $deliveredOrders = $orders->where('status', 'delivered')->count();
        $canceledOrders = $orders->where('status', 'canceled')->count();

        return view('admin.user-profile', compact(
            'user', 'orders',
            'ordersPerMonth', 'amountPerMonth',
            'orderedPerMonth', 'orderedAmountPerMonth',
            'deliveredPerMonth', 'deliveredAmountPerMonth',
            'canceledPerMonth', 'canceledAmountPerMonth',
            'totalOrders', 'totalAmount', 'deliveredOrders', 'canceledOrders'
        ));
    }

    public function editUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('admin.user-edit', compact('user'));
    }

    public function deleteUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users')->with('status', 'User deleted successfully!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->utype = $request->utype;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|confirmed|min:6',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')->with('status', 'User updated successfully!');
    }

    public function user_delete($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('status', 'User deleted successfully!');
    }

    public function productStatistics()
    {
        $products = Product::with('sizes')->get();
    
        $totalProducts = $products->count();
        $totalQuantity = $products->sum(function($product) {
            return $product->total_quantity;
        });
        $inStock = $products->filter(function($product) {
            return $product->total_quantity > 0;
        })->count();
        $outOfStock = $products->filter(function($product) {
            return $product->total_quantity <= 0;
        })->count();
    
        $soldQuantity = OrderItem::whereHas('order', function($q) {
            $q->where('status', 'delivered');
        })->sum('quantity');
    
        return view('admin.product-statistics', compact(
            'totalProducts', 'inStock', 'outOfStock', 'totalQuantity', 'soldQuantity', 'products'
        ));
    }

    public function salesStatistics(Request $request)
    {
        $period = $request->period ?? 'month';

        $startDate = now();
        switch($period) {
            case 'day':
                $startDate = $startDate->startOfDay();
                break;
            case 'week':
                $startDate = $startDate->startOfWeek();
                break;
            case 'month':
                $startDate = $startDate->startOfMonth();
                break;
            case 'year':
                $startDate = $startDate->startOfYear();
                break;
        }

  
        $sales = OrderItem::with(['product', 'order'])
            ->whereHas('order', function($q) use ($startDate, $period) {
                $q->where('status', 'delivered')
                  ->whereNotNull('delivered_date');
                
                if($period == 'day') {
                    $q->whereDate('delivered_date', now()->today());
                } elseif($period == 'week') {
                    $q->whereBetween('delivered_date', [
                        now()->startOfWeek(), 
                        now()->endOfWeek()
                    ]);
                } else {
                    $q->where('delivered_date', '>=', $startDate);
                }
            })
            ->select('product_id', 
                    DB::raw('SUM(quantity) as total_quantity'), 
                    DB::raw('SUM(quantity * price) as total_amount'))
            ->groupBy('product_id')
            ->get();

        $expectedSales = OrderItem::with(['product', 'order'])
            ->whereHas('order', function($q) use ($startDate, $period) {
                $q->where('status', 'ordered');
                
                if($period == 'day') {
                    $q->whereDate('created_at', now()->today());
                } elseif($period == 'week') {
                    $q->whereBetween('created_at', [
                        now()->startOfWeek(), 
                        now()->endOfWeek()
                    ]);
                } else {
                    $q->where('created_at', '>=', $startDate);
                }
            })
            ->select('product_id', 
                    DB::raw('SUM(quantity) as total_quantity'), 
                    DB::raw('SUM(quantity * price) as total_amount'))
            ->groupBy('product_id')
            ->get();

        $bestSellers = OrderItem::with(['product', 'order'])
            ->whereHas('order', function($q) {
                $q->where('status', 'delivered');
            })
            ->select('product_id', 
                    DB::raw('SUM(quantity) as total_quantity'), 
                    DB::raw('SUM(quantity * price) as total_amount'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $dailySales = Order::where('status', 'delivered')
            ->whereNotNull('delivered_date')
            ->where('delivered_date', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(delivered_date) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyExpectedSales = Order::where('status', 'ordered')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthlySales = Order::where('status', 'delivered')
            ->whereNotNull('delivered_date')
            ->where('delivered_date', '>=', now()->subMonths(12))
            ->select(
                DB::raw('YEAR(delivered_date) as year'),
                DB::raw('MONTH(delivered_date) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.sales-statistics', compact(
            'period', 
            'sales',
            'expectedSales', 
            'bestSellers', 
            'dailySales',
            'dailyExpectedSales', 
            'monthlySales'
        ));
    }
}
