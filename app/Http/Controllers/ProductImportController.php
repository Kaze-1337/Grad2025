<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Size;
use App\Models\ProductImport;
use Illuminate\Http\Request;

class ProductImportController extends Controller
{
    public function index()
    {
        $imports = ProductImport::with(['product', 'size'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.product-imports.index', compact('imports'));
    }

    public function create()
    {
        $products = Product::where('has_size', true)->get();
        $sizes = Size::all();
        return view('admin.product-imports.create', compact('products', 'sizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'required|exists:sizes,id',
            'quantity' => 'required|integer|min:1',
            'import_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'import_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Create new import record
        $import = new ProductImport();
        $import->product_id = $request->product_id;
        $import->size_id = $request->size_id;
        $import->quantity = $request->quantity;
        $import->import_price = $request->import_price;
        $import->selling_price = $request->selling_price;
        $import->import_date = $request->import_date;
        $import->notes = $request->notes;
        $import->save();

        // Update product size quantity
        $product = Product::findOrFail($request->product_id);
        $product->sizes()->updateExistingPivot($request->size_id, [
            'quantity' => \DB::raw('quantity + ' . $request->quantity)
        ]);

        return redirect()->route('admin.product-imports.index')
            ->with('success', 'Product import has been added successfully!');
    }
} 