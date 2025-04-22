<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class POSController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $products = Product::with('variations')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->paginate(6);

        return view('pos.index', compact('products'));
    }

    public function placeOrder(Request $request)
    {
        try {
            Log::info('Place Order Request Data:', $request->all());

            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.product_id' => 'required|string',
                'items.*.variation_id' => 'nullable|string',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric',
                'items.*.discount' => 'required|numeric',
                'items.*.tax' => 'required|numeric',
                'items.*.quantity' => 'required|integer|min:1',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
            ]);

            Order::create([
                'items' => json_encode($request->items),
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'tax' => $request->tax,
                'total' => $request->total,
            ]);

            return response()->json(['success' => true, 'message' => 'Order placed successfully!']);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Place Order Error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }
}