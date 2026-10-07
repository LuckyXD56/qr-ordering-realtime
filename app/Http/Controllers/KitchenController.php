<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class KitchenController extends Controller
{
    public function index()
    {
        // Load active orders (e.g. pending/cooking)
        $orders = Order::with(['table', 'orderItems.product'])
            ->whereIn('status', ['active', 'cooking'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('kitchen.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:active,cooking,ready,completed'
        ]);

        $order->update(['status' => $request->status]);

        // Optional: broadcast event to sync multiple kitchen screens
        
        return response()->json(['success' => true, 'status' => $order->status]);
    }
}
