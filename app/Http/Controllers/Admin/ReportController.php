<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Doanh thu hôm nay
        $todayRevenue = Invoice::whereDate('created_at', $today)->sum('total');
        
        // 2. Số đơn hàng hôm nay
        $todayOrders = Invoice::whereDate('created_at', $today)->count();

        // 3. Doanh thu 7 ngày gần nhất (để vẽ biểu đồ)
        $last7Days = collect();
        $revenueData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days->push($date->format('d/m'));
            $revenue = Invoice::whereDate('created_at', $date)->sum('total');
            $revenueData->push($revenue);
        }

        // 4. Món bán chạy nhất hôm nay
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->whereHas('order.invoice', function($q) use ($today) {
                $q->whereDate('created_at', $today);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 5. Danh sách hóa đơn gần đây
        $recentInvoices = Invoice::with(['order.table'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'todayRevenue', 
            'todayOrders', 
            'last7Days', 
            'revenueData', 
            'topProducts',
            'recentInvoices'
        ));
    }
}
