<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Contact;
use Illuminate\Http\Request;

class HomeController extends Controller
{
   

  
    public function index()
    {
        $slides = Slide::where('status',1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        $sproducts = Product::whereNotNull('sale_price')->where('sale_price','<>','')->inRandomOrder()->get()->take(8);
        $fproducts = Product::where('featured',1)->get()->take(8);
        return view('index', compact('slides','categories','sproducts','fproducts'));

       
    }
    public function contact()
    {
        return view('contact');
    }

    public function contact_store(Request $request)
    {
        $request->validate([
            'name'=>'required|max:100',
            'email'=>'required|email',
            'phone'=>'required|numeric|digits:10',
            'comment'=>'required',
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->back()->with('success','Your message has been sent successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        $results = Product::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'slug', 'image', 'regular_price', 'sale_price', 'category_id', 'brand_id')
            ->with(['category:id,name', 'brand:id,name'])
            ->take(8)
            ->get()
            ->map(function($product) {
                $thumbPath = public_path('uploads/products/thumbnails/' . $product->image);
                if (!$product->image || !file_exists($thumbPath)) {
                    $product->image = 'no-image.jpg';
                }
                
                $product->formatted_price = $product->sale_price ? $product->sale_price : $product->regular_price;
                $product->price_display = $product->sale_price 
                    ? "<span class='new-price'>$" . number_format($product->sale_price, 2) . "</span>
                       <span class='old-price'>$" . number_format($product->regular_price, 2) . "</span>"
                    : "<span class='price'>$" . number_format($product->regular_price, 2) . "</span>";
                    
                return $product;
            });

        return response()->json($results);
    }
}
