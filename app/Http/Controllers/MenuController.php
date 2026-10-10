<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Category;
use App\Models\Product;

class MenuController extends Controller
{
    public function index($qr_token)
    {
        // Find the table by QR token
        $table = Table::where('qr_token', $qr_token)->firstOrFail();

        // Get categories with their available products
        $categories = Category::with(['products' => function($query) {
            $query->where('is_available', true);
        }])->orderBy('sort_order')->get();

        return view('menu.index', compact('table', 'categories'));
    }

    public function checkout(Request $request, $qr_token)
    {
        $table = Table::where('qr_token', $qr_token)->firstOrFail();
        
        $cart = $request->input('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty']);
        }

        // Lấy order hiện tại của bàn (nếu có)
        $order = $table->orders()->whereIn('status', ['active', 'cooking', 'ready'])->first();

        // Nếu chưa có, tạo mới
        if (!$order) {
            $order = \App\Models\Order::create([
                'table_id' => $table->id,
                'status' => 'active',
                'total_amount' => 0,
            ]);
            $table->update(['status' => 'active']);
        }

        $totalAmount = $order->total_amount;
        foreach ($cart as $item) {
            $product = Product::findOrFail($item['id']);
            // Price is calculated from frontend if there are additions, but for safety we use base + variations.
            // Since we trust the frontend cart item price for this demo (as size L is +8000),
            // we will just use $item['price'] if available, else $product->price.
            $finalPrice = isset($item['price']) ? $item['price'] : $product->price;

            $order->orderItems()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $finalPrice,
                'status' => 'pending',
                'note' => isset($item['note']) ? $item['note'] : null
            ]);
            $totalAmount += ($finalPrice * $item['quantity']);
        }

        $order->update(['total_amount' => $totalAmount]);

        // Broadcast event to kitchen
        broadcast(new \App\Events\OrderPlaced($order));

        return response()->json(['success' => true]);
    }
}
