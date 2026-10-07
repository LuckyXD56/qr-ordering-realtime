<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menu - {{ $table->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 pb-28 text-slate-800" x-data="{
    cart: [],
    isCheckingOut: false,
    activeCategory: '{{ $categories->first()->id ?? '' }}',
    addToCart(id, name, price) {
        let item = this.cart.find(i => i.id === id);
        if (item) {
            item.quantity++;
        } else {
            this.cart.push({ id, name, price, quantity: 1 });
        }
        // Haptic feedback if supported
        if (navigator.vibrate) navigator.vibrate(50);
    },
    removeFromCart(id) {
        let item = this.cart.find(i => i.id === id);
        if (item) {
            item.quantity--;
            if(item.quantity <= 0) {
                this.cart = this.cart.filter(i => i.id !== id);
            }
        }
    },
    get cartTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    },
    formatCurrency(value) {
        return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
    },
    checkout() {
        if(this.cart.length === 0) return;
        this.isCheckingOut = true;
        fetch('{{ route('menu.checkout', $table->qr_token) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cart: this.cart })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('🎉 Đã gửi món thành công! Bếp đang chuẩn bị món cho bạn.');
                this.cart = [];
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(err => {
            alert('Có lỗi xảy ra: Xin vui lòng thử lại.');
            console.error(err);
        })
        .finally(() => {
            this.isCheckingOut = false;
        });
    }
}">

    <!-- Header Banner -->
    <header class="bg-orange-500 text-white p-6 rounded-b-3xl shadow-md relative overflow-hidden">
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Gourmet Eats</h1>
                <p class="text-orange-100 mt-1 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    {{ $table->name }}
                </p>
            </div>
            <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
        </div>
        <!-- Decorative circle -->
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
    </header>

    <!-- Category Tabs (Sticky) -->
    <div class="sticky top-0 z-30 bg-slate-50/90 backdrop-blur-md pt-4 pb-2 px-4 shadow-sm">
        <div class="flex overflow-x-auto gap-3 no-scrollbar pb-2">
            @foreach($categories as $category)
                <a href="#category-{{ $category->id }}" 
                   @click="activeCategory = '{{ $category->id }}'"
                   :class="activeCategory === '{{ $category->id }}' ? 'bg-orange-500 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200'"
                   class="px-5 py-2 rounded-full whitespace-nowrap text-sm font-semibold transition-all duration-300">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Product List -->
    <main class="px-4 mt-2 space-y-8">
        @foreach($categories as $category)
            <div id="category-{{ $category->id }}" class="scroll-mt-24 pt-2">
                <h2 class="text-xl font-bold mb-4 text-slate-800">{{ $category->name }}</h2>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($category->products as $product)
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 flex gap-4 active:scale-[0.98] transition-transform">
                            <!-- Placeholder image -->
                            <div class="w-28 h-28 bg-slate-100 rounded-xl flex-shrink-0 flex items-center justify-center text-slate-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            
                            <div class="flex flex-col flex-1 justify-between py-1">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-base leading-tight">{{ $product->name }}</h3>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                                </div>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="font-bold text-orange-500 text-lg"><span x-text="formatCurrency({{ $product->price }})"></span></span>
                                    
                                    <!-- Add button logic -->
                                    <div class="flex items-center gap-2">
                                        <template x-if="cart.find(i => i.id === {{ $product->id }})">
                                            <div class="flex items-center gap-2 bg-slate-100 rounded-full p-1">
                                                <button @click="removeFromCart({{ $product->id }})" class="w-7 h-7 flex items-center justify-center bg-white rounded-full shadow-sm text-slate-600 font-bold">-</button>
                                                <span class="w-4 text-center text-sm font-bold" x-text="cart.find(i => i.id === {{ $product->id }}).quantity"></span>
                                                <button @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})" class="w-7 h-7 flex items-center justify-center bg-orange-500 rounded-full shadow-sm text-white font-bold">+</button>
                                            </div>
                                        </template>
                                        <template x-if="!cart.find(i => i.id === {{ $product->id }})">
                                            <button 
                                                @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})"
                                                class="bg-orange-100 text-orange-600 hover:bg-orange-200 w-9 h-9 rounded-full flex items-center justify-center font-bold text-xl transition-colors">
                                                +
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </main>

    <!-- Floating Cart Bar -->
    <div class="fixed bottom-4 left-4 right-4 z-50 transition-all duration-300"
         x-show="cart.length > 0" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         style="display: none;">
        
        <div class="bg-slate-900 rounded-2xl p-4 flex justify-between items-center shadow-2xl">
            <div class="flex items-center gap-3">
                <div class="relative bg-slate-800 p-3 rounded-xl border border-slate-700">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full shadow-md" x-text="cart.reduce((a, b) => a + b.quantity, 0)"></span>
                </div>
                <div class="text-white">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Tổng cộng</p>
                    <p class="font-bold text-lg" x-text="formatCurrency(cartTotal)"></p>
                </div>
            </div>
            <button @click="checkout()" :disabled="isCheckingOut" 
                    class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-orange-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2">
                <span x-text="isCheckingOut ? 'Đang gửi...' : 'Gửi bếp'"></span>
                <svg x-show="!isCheckingOut" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </div>

</body>
</html>
