<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;
use App\Models\Invoice;

class CashierController extends Controller
{
    public function index()
    {
        // Load all tables with their active orders and items
        $tables = Table::with(['orders' => function($query) {
            $query->whereIn('status', ['active', 'cooking', 'ready'])->with('orderItems.product');
        }])->get();
        
        return view('cashier.index', compact('tables'));
    }

    public function checkout(Request $request, Table $table)
    {
        $activeOrder = $table->orders()->whereIn('status', ['active', 'cooking', 'ready'])->first();
        
        if (!$activeOrder) {
            return back()->with('error', 'Không tìm thấy đơn hàng đang hoạt động.');
        }

        // Calculate total (optional if total_amount is already fully calculated)
        $total = $activeOrder->orderItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        // Create Invoice
        $invoice = Invoice::create([
            'order_id' => $activeOrder->id,
            'cashier_id' => 1, // hardcoded user for now since auth is not fully hooked
            'subtotal' => $total,
            'total' => $total,
            'status' => 'paid',
            'payment_method' => $request->input('payment_method', 'cash'),
        ]);

        // Mark order as completed
        $activeOrder->update(['status' => 'completed']);
        
        // Reset table status
        $table->update(['status' => 'empty']);
        $table->generateQrToken();

        return redirect()->route('cashier.invoice', $invoice->id)->with('success', 'Đã thanh toán thành công Hóa đơn #' . $invoice->id);
    }

    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['order.table', 'order.orderItems.product']);
        return view('cashier.invoice', compact('invoice'));
    }
}
