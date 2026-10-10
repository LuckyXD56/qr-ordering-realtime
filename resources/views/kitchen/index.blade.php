<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Màn hình Bếp (Kitchen KDS)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-100 h-screen flex flex-col text-slate-800" x-data="kitchenApp()">
    
    <header class="bg-slate-900 text-white p-4 shadow-lg flex justify-between items-center z-10">
        <div class="flex items-center gap-3">
            <div class="bg-orange-500 p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">KITCHEN DISPLAY</h1>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-slate-800 px-4 py-2 rounded-full border border-slate-700">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-sm font-semibold text-emerald-400">Live Sync</span>
            </div>
            
            <div class="h-8 w-px bg-slate-700 mx-2"></div>
            
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-full bg-slate-800 border-2 border-slate-700 flex items-center justify-center font-bold text-white shadow-inner">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : "B" }}
                </div>
                <div class="flex flex-col mr-2">
                    <span class="text-sm font-bold text-white leading-tight">{{ auth()->check() ? auth()->user()->name : "Bếp Chính" }}</span>
                    <span class="text-xs text-slate-400 font-medium leading-tight">Đang hoạt động</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-red-400 hover:text-red-300 font-bold transition-colors bg-slate-800 hover:bg-slate-700 px-3 py-2 rounded-lg flex items-center gap-1 border border-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Thoát
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-x-auto p-6">
        <div class="flex gap-6 h-full min-w-max">
            
            <!-- Đơn mới (New Orders) Column -->
            <div class="w-96 bg-slate-200/50 rounded-2xl p-4 flex flex-col border border-slate-200">
                <div class="flex justify-between items-center mb-4 px-2">
                    <h2 class="font-bold text-slate-700 text-lg uppercase tracking-wider">Mới nhận</h2>
                    <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm" x-text="newOrders.length"></span>
                </div>
                <div class="flex-1 overflow-y-auto space-y-4 no-scrollbar pb-4">
                    <template x-for="order in newOrders" :key="order.id">
                        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-red-500 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-bl-lg" x-text="formatTime(order.created_at)"></div>
                            <div class="mb-4">
                                <h3 class="font-black text-2xl text-slate-800" x-text="order.table.name"></h3>
                                <p class="text-xs text-slate-500">#Mã đơn: <span x-text="order.id"></span></p>
                            </div>
                            <ul class="space-y-2 mb-5">
                                <template x-for="item in order.order_items" :key="item.id">
                                    <li class="flex items-start gap-3 text-slate-700 font-medium">
                                        <span class="bg-slate-100 text-slate-700 font-bold px-2 py-0.5 rounded text-sm" x-text="item.quantity + 'x'"></span>
                                        <div class="flex flex-col pt-0.5">
                                            <span class="leading-tight" x-text="item.product.name"></span>
                                            <template x-if="item.note">
                                                <span class="text-xs text-orange-600 mt-0.5 font-bold" x-text="item.note"></span>
                                            </template>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                            <button @click="moveToCooking(order)" class="w-full bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                BẮT ĐẦU NẤU
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Đang nấu (Cooking) Column -->
            <div class="w-96 bg-slate-200/50 rounded-2xl p-4 flex flex-col border border-slate-200">
                <div class="flex justify-between items-center mb-4 px-2">
                    <h2 class="font-bold text-slate-700 text-lg uppercase tracking-wider">Đang nấu</h2>
                    <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm" x-text="cookingOrders.length"></span>
                </div>
                <div class="flex-1 overflow-y-auto space-y-4 no-scrollbar pb-4">
                    <template x-for="order in cookingOrders" :key="order.id">
                        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-orange-500">
                            <div class="mb-4">
                                <h3 class="font-black text-2xl text-slate-800" x-text="order.table.name"></h3>
                                <p class="text-xs text-slate-500">Đang thực hiện...</p>
                            </div>
                            <ul class="space-y-2 mb-5">
                                <template x-for="item in order.order_items" :key="item.id">
                                    <li class="flex items-start gap-3 text-slate-700 font-medium">
                                        <span class="bg-orange-100 text-orange-700 font-bold px-2 py-0.5 rounded text-sm" x-text="item.quantity + 'x'"></span>
                                        <div class="flex flex-col pt-0.5">
                                            <span class="leading-tight" x-text="item.product.name"></span>
                                            <template x-if="item.note">
                                                <span class="text-xs text-orange-600 mt-0.5 font-bold" x-text="item.note"></span>
                                            </template>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                            <button @click="moveToReady(order)" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition-colors shadow-lg shadow-orange-500/30 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                BÁO XONG
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Đã xong (Ready) Column -->
            <div class="w-96 bg-slate-200/50 rounded-2xl p-4 flex flex-col border border-slate-200 opacity-60 hover:opacity-100 transition-opacity">
                <div class="flex justify-between items-center mb-4 px-2">
                    <h2 class="font-bold text-slate-700 text-lg uppercase tracking-wider">Đã báo xong</h2>
                </div>
                <div class="flex-1 overflow-y-auto space-y-4 no-scrollbar pb-4">
                    <template x-for="order in readyOrders" :key="order.id">
                        <div class="bg-white/80 p-4 rounded-xl border border-slate-200">
                            <h3 class="font-bold text-lg text-slate-600" x-text="order.table.name"></h3>
                            <p class="text-xs text-slate-500 mt-1">Đang chờ phục vụ mang ra...</p>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </main>

    <!-- Notification Sound -->
    <audio id="notify-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script>
        function kitchenApp() {
            return {
                orders: @json($orders),
                get newOrders() {
                    return this.orders.filter(o => o.status === 'active');
                },
                get cookingOrders() {
                    return this.orders.filter(o => o.status === 'cooking');
                },
                get readyOrders() {
                    return this.orders.filter(o => o.status === 'ready');
                },
                init() {
                    // Initialize Echo
                    window.Echo = new Echo({
                        broadcaster: 'reverb',
                        key: '{{ env('REVERB_APP_KEY') }}',
                        wsHost: '{{ env('REVERB_HOST', '127.0.0.1') }}',
                        wsPort: '{{ env('REVERB_PORT', 8080) }}',
                        wssPort: '{{ env('REVERB_PORT', 8080) }}',
                        forceTLS: false,
                        enabledTransports: ['ws', 'wss'],
                    });

                    // Listen for OrderPlaced event via Echo
                    if(window.Echo) {
                        window.Echo.channel('kitchen')
                        .listen('OrderPlaced', (e) => {
                            // Check if order already exists
                            let existing = this.orders.find(o => o.id === e.order.id);
                            if(existing) {
                                // Update existing order (in case of appending)
                                existing.order_items = e.order.order_items;
                            } else {
                                // Add new order
                                this.orders.unshift(e.order);
                            }
                            
                            // Play sound notification
                            document.getElementById('notify-sound').play().catch(e => console.log('Audio play blocked'));
                        });
                    }
                },
                formatTime(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                },
                moveToCooking(order) {
                    order.status = 'cooking';
                    fetch('/kitchen/orders/' + order.id + '/status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: 'cooking' })
                    });
                },
                moveToReady(order) {
                    order.status = 'ready';
                    fetch('/kitchen/orders/' + order.id + '/status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: 'ready' })
                    });
                }
            }
        }
    </script>
</body>
</html>
