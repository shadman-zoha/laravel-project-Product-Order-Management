<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date');
        $orders = Order::when($date, function ($query, $date) {
            return $query->whereDate('created_at', $date);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}