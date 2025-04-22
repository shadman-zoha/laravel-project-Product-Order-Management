<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'unit' => 'required|string|max:50',
            'unit_value' => 'required|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'tax' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variations.*.attribute_name' => 'required|string|max:50',
            'variations.*.attribute_value' => 'required|string|max:50',
            'variations.*.purchase_price' => 'required|numeric|min:0',
            'variations.*.selling_price' => 'required|numeric|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'unit' => $request->unit,
            'unit_value' => $request->unit_value,
            'selling_price' => $request->selling_price,
            'purchase_price' => $request->purchase_price,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'image' => $imagePath,
        ]);

        if ($request->has('variations')) {
            foreach ($request->variations as $variation) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'attribute_name' => $variation['attribute_name'],
                    'attribute_value' => $variation['attribute_value'],
                    'purchase_price' => $variation['purchase_price'],
                    'selling_price' => $variation['selling_price'],
                ]);
            }
        }

        return redirect()->route('products.create')->with('success', 'Product added successfully!');
    }
}