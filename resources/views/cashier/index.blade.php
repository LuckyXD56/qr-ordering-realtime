<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Màn hình Thu ngân (Cashier)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 h-screen flex flex-col text-slate-800" x-data="cashierApp()">
    
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center z-20 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="bg-slate-900 p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">CASHIER POS</h1>
        </div>
        <div class="flex items-center gap-6">
            <a href="/admin/tables" class="text-sm font-semibold text-slate-500 hover:text-orange-500 transition-colors">Quản lý Bàn</a>
            <a href="/admin/reports" class="text-sm font-semibold text-slate-500 hover:text-orange-500 transition-colors">Báo Cáo</a>
            <div class="h-6 w-px bg-slate-300"></div>
            <div class="text-right">
                <p class="text-sm font-bold text-slate-800">Thu Ngân</p>
                <p class="text-xs text-slate-500">Ca sáng</p>
            </div>
            <img src="https://ui-avatars.com/api/?name=Thu+Ngan&background=f97316&color=fff" class="w-10 h-10 rounded-full shadow-sm" alt="Avatar">
        </div>
    </header>

    <main class="flex-1 flex overflow-hidden">
        
        <!-- Danh sách bàn (Tables Grid) -->
        <section class="w-2/3 p-8 overflow-y-auto bg-slate-50 relative">
            
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Sơ đồ Bàn</h2>
                    <p class="text-sm text-slate-500 mt-1">Chọn bàn đang có khách để thanh toán.</p>
                </div>
                <div class="flex gap-4 text-sm font-medium">
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-slate-200"></div>Trống</div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-orange-500"></div>Có khách</div>
                </div>
            </div>
            
            <div class="grid grid-cols-3 xl:grid-cols-4 gap-5">
                <template x-for="table in tables" :key="table.id">
                    <div @click="selectTable(table)" 
                         class="cursor-pointer rounded-2xl p-6 border-2 transition-all duration-200 flex flex-col items-center justify-center min-h-[140px] relative overflow-hidden group"
                         :class="[
                            selectedTable && selectedTable.id === table.id ? 'border-slate-800 shadow-lg scale-[1.02]' : 'border-transparent shadow-sm hover:shadow-md',
                            table.status === 'empty' ? 'bg-white' : 'bg-orange-50 border-orange-200 hover:bg-orange-100'
                         ]">
                        
                        <!-- Table status dot -->
                        <div class="absolute top-4 right-4 w-3 h-3 rounded-full shadow-sm"
                             :class="table.status === 'empty' ? 'bg-slate-200' : 'bg-orange-500'"></div>

                        <h3 class="text-3xl font-black" :class="table.status === 'empty' ? 'text-slate-400' : 'text-orange-900'" x-text="table.name"></h3>
                        
                        <div x-show="table.activeOrder" class="mt-3 text-sm font-bold text-orange-600 bg-white/60 px-3 py-1 rounded-full backdrop-blur-sm">
                            <span x-text="formatCurrency(table.activeOrderTotal)"></span>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <!-- Chi tiết hóa đơn (Order Summary) -->
        <section class="w-1/3 bg-white flex flex-col shadow-2xl z-10 border-l border-slate-200">
            
            @if(session('success'))
                <div class="bg-emerald-50 border-b border-emerald-200 text-emerald-700 px-6 py-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-2xl font-black text-slate-800" x-text="selectedTable ? selectedTable.name : 'Chưa chọn bàn'"></h2>
                <p class="text-sm font-medium text-slate-500 mt-1" x-show="selectedTable && selectedTable.activeOrder">
                    Mã Đơn: <span class="text-slate-700 font-bold" x-text="'#' + selectedTable.activeOrder.id"></span>
                </p>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <template x-if="!selectedTable">
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 space-y-4">
                        <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <p class="font-medium">Vui lòng chọn một bàn để xem chi tiết</p>
                    </div>
                </template>

                <template x-if="selectedTable && !selectedTable.activeOrder">
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 space-y-4">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        </div>
                        <p class="font-medium">Bàn này chưa gọi món.</p>
                    </div>
                </template>

                <template x-if="selectedTable && selectedTable.activeOrder">
                    <div class="space-y-4">
                        <template x-for="item in selectedTable.activeOrder.order_items" :key="item.id">
                            <div class="flex justify-between items-start py-3 border-b border-dashed border-slate-200 last:border-0">
                                <div class="pr-4">
                                    <span class="font-bold text-slate-800 text-lg" x-text="item.product.name"></span>
                                    <div class="text-sm font-medium text-slate-500 mt-0.5" x-text="formatCurrency(item.price) + ' x ' + item.quantity"></div>
                                </div>
                                <span class="font-bold text-slate-800 text-lg whitespace-nowrap" x-text="formatCurrency(item.price * item.quantity)"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-200" x-show="selectedTable && selectedTable.activeOrder">
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-slate-500 text-sm font-medium">
                        <span>Tạm tính</span>
                        <span x-text="formatCurrency(selectedTable ? selectedTable.activeOrderTotal : 0)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500 text-sm font-medium">
                        <span>Thuế & Phí (0%)</span>
                        <span>0đ</span>
                    </div>
                    <div class="flex justify-between items-end pt-3 border-t border-slate-200">
                        <span class="text-slate-800 font-bold uppercase tracking-wider text-sm">Thành tiền</span>
                        <span class="font-black text-4xl text-orange-600" x-text="formatCurrency(selectedTable ? selectedTable.activeOrderTotal : 0)"></span>
                    </div>
                </div>
                
                <form :action="'/admin/cashier/' + selectedTable?.id + '/checkout'" method="POST" @submit="return confirm('Bạn có chắc chắn muốn thanh toán bàn này?')">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="peer sr-only" checked>
                            <div class="text-center py-3 rounded-xl border-2 border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 font-bold transition-all text-slate-500">
                                Tiền mặt
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="transfer" class="peer sr-only">
                            <div class="text-center py-3 rounded-xl border-2 border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 font-bold transition-all text-slate-500">
                                Chuyển khoản
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-slate-800 active:scale-[0.98] transition-all text-lg">
                        THANH TOÁN
                    </button>
                </form>
            </div>
        </section>
    </main>

    <script>
        function cashierApp() {
            const tablesData = @json($tables);
            
            tablesData.forEach(t => {
                // Find first active order (since we fixed concurrency, there should only be one active order per table anyway)
                let activeOrder = t.orders.find(o => ['active', 'cooking', 'ready'].includes(o.status));
                if (activeOrder) {
                    t.activeOrder = activeOrder;
                    t.activeOrderTotal = activeOrder.order_items.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
                    t.status = 'occupied';
                } else {
                    t.activeOrder = null;
                    t.activeOrderTotal = 0;
                }
            });

            return {
                tables: tablesData,
                selectedTable: null,
                
                selectTable(table) {
                    this.selectedTable = table;
                },
                
                formatCurrency(value) {
                    return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                }
            }
        }
    </script>
</body>
</html>
