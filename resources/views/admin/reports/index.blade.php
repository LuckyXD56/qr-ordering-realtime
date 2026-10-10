<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Doanh Thu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">
    
    <!-- Navbar -->
    <header class="bg-white shadow-sm border-b border-slate-200 px-8 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <div class="bg-orange-500 p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">HỆ THỐNG ADMIN</h1>
        </div>
        <nav class="flex items-center gap-6">
            <a href="/admin/reports" class="text-sm font-semibold {{ request()->is('admin/reports') ? 'text-orange-500' : 'text-slate-500 hover:text-orange-500' }} transition-colors">Báo Cáo</a>
            <a href="/admin/tables" class="text-sm font-semibold {{ request()->is('admin/tables') ? 'text-orange-500' : 'text-slate-500 hover:text-orange-500' }} transition-colors">Quản lý Bàn</a>
            <a href="/admin/categories" class="text-sm font-semibold {{ request()->is('admin/categories') ? 'text-orange-500' : 'text-slate-500 hover:text-orange-500' }} transition-colors">Danh Mục</a>
            <a href="/admin/products" class="text-sm font-semibold {{ request()->is('admin/products*') ? 'text-orange-500' : 'text-slate-500 hover:text-orange-500' }} transition-colors">Thực Đơn</a>
            <div class="h-6 w-px bg-slate-300"></div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : "A" }}</div>
                    <span class="text-sm font-bold">{{ auth()->check() ? auth()->user()->name : "Admin" }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">@csrf <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-bold transition-colors bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>Thoát</button></form>
            </div>
        </nav>
    </header>

    <main class="p-8 max-w-7xl mx-auto space-y-8">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Doanh thu hôm nay -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
                <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center text-orange-500 flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Doanh thu hôm nay</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($todayRevenue, 0, ',', '.') }}đ</p>
                </div>
            </div>

            <!-- Số đơn hôm nay -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
                <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Tổng số đơn</p>
                    <p class="text-2xl font-black text-slate-800">{{ $todayOrders }} <span class="text-sm font-medium text-slate-400">đơn</span></p>
                </div>
            </div>

            <!-- Khách trung bình / đơn (Ví dụ) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
                <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-500 flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Giá trị TB/Đơn</p>
                    <p class="text-2xl font-black text-slate-800">
                        {{ $todayOrders > 0 ? number_format($todayRevenue / $todayOrders, 0, ',', '.') : 0 }}đ
                    </p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
                <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-500 flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Ca làm việc</p>
                    <p class="text-xl font-bold text-slate-800">08:00 - 17:00</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Biểu đồ doanh thu 7 ngày -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-slate-800">Doanh thu 7 ngày gần nhất</h2>
                </div>
                <div class="h-[300px] w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Top Món bán chạy -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 mb-6">Món bán chạy hôm nay</h2>
                <div class="space-y-4">
                    @forelse($topProducts as $index => $item)
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $index == 0 ? 'bg-orange-500 text-white shadow-md' : 'bg-slate-100 text-slate-500' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-800">{{ $item->product->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $item->total_qty }} phần đã bán</p>
                            </div>
                            <div class="font-bold text-slate-800">
                                {{ number_format($item->total_revenue, 0, ',', '.') }}đ
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-500 py-8">Chưa có dữ liệu bán hàng hôm nay</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Bảng hóa đơn gần đây -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800">Giao dịch gần đây</h2>
                <a href="#" class="text-sm font-semibold text-orange-500 hover:text-orange-600">Xem tất cả &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Mã HĐ</th>
                            <th class="px-6 py-4">Bàn</th>
                            <th class="px-6 py-4">Thời gian</th>
                            <th class="px-6 py-4">PT Thanh toán</th>
                            <th class="px-6 py-4">Tổng tiền</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentInvoices as $inv)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-700">#{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $inv->order->table->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $inv->created_at->format('H:i d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $inv->payment_method == 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $inv->payment_method == 'cash' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-black text-slate-800">{{ number_format($inv->total, 0, ',', '.') }}đ</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('cashier.invoice', $inv->id) }}" target="_blank" class="text-orange-500 hover:text-orange-600 font-semibold text-sm flex items-center justify-end gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        In lại
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">Chưa có giao dịch nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            // Lấy dữ liệu từ backend pass sang
            const labels = @json($last7Days);
            const data = @json($revenueData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: data,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#f97316',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw || 0;
                                    return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                                color: '#f1f5f9'
                            },
                            ticks: {
                                callback: function(value) {
                                    if(value === 0) return '0đ';
                                    return (value / 1000) + 'k';
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
