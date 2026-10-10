
import os

content = """<!DOCTYPE html>
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
    optionModalOpen: false,
    cartModalOpen: false,
    currentProduct: null,
    selectedSize: 'M',
    selectedToppings: [],
    itemNote: '',
    
    openOptionModal(id, name, price, image) {
        this.currentProduct = { id, name, price: parseFloat(price), image };
        this.selectedSize = 'M';
        this.selectedToppings = [];
        this.itemNote = '';
        this.optionModalOpen = true;
    },

    getOptionPrice() {
        if (!this.currentProduct) return 0;
        let total = this.currentProduct.price;
        if (this.selectedSize === 'L') total += 8000;
        total += this.selectedToppings.length * 5000;
        return total;
    },

    addToCartWithOptions() {
        if (!this.currentProduct) return;
        
        const finalPrice = this.getOptionPrice();
        let noteParts = [];
        
        if (this.selectedSize === 'L') noteParts.push('Size L');
        else noteParts.push('Size M');
        
        if (this.selectedToppings.length > 0) noteParts.push('Topping: ' + this.selectedToppings.join(', '));
        if (this.itemNote.trim() !== '') noteParts.push('Ghi chú: ' + this.itemNote.trim());
        
        const note = noteParts.join(' | ');
        const cartItemId = this.currentProduct.id + '_' + Date.now();

        this.cart.push({
            cart_item_id: cartItemId,
            id: this.currentProduct.id,
            name: this.currentProduct.name,
            price: finalPrice,
            quantity: 1,
            note: note
        });

        this.optionModalOpen = false;
    },

    removeFromCart(cartItemId) {
        this.cart = this.cart.filter(item => item.cart_item_id !== cartItemId);
        if (this.cart.length === 0) {
            this.cartModalOpen = false;
        }
    },
    
    get cartTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    },
    
    formatCurrency(value) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    
    checkout() {
        if (this.cart.length === 0 || this.isCheckingOut) return;
        this.isCheckingOut = true;
        
        fetch('{{ route('menu.checkout', $table->qr_token) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ items: this.cart })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Đã gửi yêu cầu đặt món tới bếp!');
                this.cart = [];
                this.cartModalOpen = false;
            } else {
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            }
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
        </div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
    </header>

    <!-- Category Tabs -->
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
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-28 h-28 rounded-xl object-cover shadow-sm flex-shrink-0 border border-slate-100">
                            @else
                                <div class="w-28 h-28 bg-slate-50 rounded-xl flex-shrink-0 flex items-center justify-center text-slate-300 border border-slate-100">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            <div class="flex flex-col flex-1 justify-between py-1">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-base leading-tight">{{ $product->name }}</h3>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                                </div>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="font-bold text-orange-500 text-lg"><span x-text="formatCurrency({{ $product->price }})"></span></span>
                                    <button @click="openOptionModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->image ? asset('storage/' . $product->image) : '' }}')" class="bg-orange-100 text-orange-600 hover:bg-orange-200 px-4 py-1.5 rounded-full font-bold text-sm transition-colors">Chọn</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </main>

    <!-- Floating Cart Bar -->
    <div class="fixed bottom-4 left-4 right-4 z-40 transition-all duration-300 cursor-pointer"
         x-show="cart.length > 0" 
         @click="cartModalOpen = true"
         style="display: none;">
        
        <div class="bg-slate-900 rounded-2xl p-4 flex justify-between items-center shadow-2xl">
            <div class="flex items-center gap-3">
                <div class="relative bg-slate-800 p-3 rounded-xl border border-slate-700">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full shadow-md" x-text="cart.length"></span>
                </div>
                <div class="text-white">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Giỏ hàng</p>
                    <p class="font-bold text-lg" x-text="formatCurrency(cartTotal)"></p>
                </div>
            </div>
            <button class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all flex items-center gap-2">
                <span>Xem</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>

    <!-- Options Modal -->
    <div x-show="optionModalOpen" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="optionModalOpen = false" x-show="optionModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full" class="bg-white rounded-t-3xl p-6 w-full max-w-md h-[85vh] overflow-y-auto relative shadow-2xl pb-safe">
            
            <button @click="optionModalOpen = false" class="absolute top-5 right-5 text-slate-400 bg-slate-100 rounded-full p-1.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>

            <div class="flex items-center gap-4 mb-4 mt-2">
                <template x-if="currentProduct?.image">
                    <img :src="currentProduct?.image" class="w-20 h-20 rounded-2xl object-cover shadow-sm border border-slate-200">
                </template>
                <div>
                    <h3 class="text-xl font-black text-slate-800 mb-1" x-text="currentProduct?.name"></h3>
                    <p class="text-orange-500 font-bold text-lg" x-text="formatCurrency(currentProduct?.price)"></p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Size -->
                <div>
                    <h4 class="font-bold text-sm text-slate-700 mb-3 uppercase tracking-wider flex justify-between">Kích cỡ</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-between p-3.5 border-2 rounded-xl cursor-pointer transition-colors" :class="selectedSize === 'M' ? 'border-orange-500 bg-orange-50/50' : 'border-slate-100 hover:border-orange-200'">
                            <div class="flex items-center gap-3">
                                <input type="radio" x-model="selectedSize" value="M" class="w-5 h-5 text-orange-500 border-slate-300 focus:ring-orange-500">
                                <span class="font-bold text-slate-700 text-sm">Size M</span>
                            </div>
                            <span class="text-sm font-bold text-slate-400">+0đ</span>
                        </label>
                        <label class="flex items-center justify-between p-3.5 border-2 rounded-xl cursor-pointer transition-colors" :class="selectedSize === 'L' ? 'border-orange-500 bg-orange-50/50' : 'border-slate-100 hover:border-orange-200'">
                            <div class="flex items-center gap-3">
                                <input type="radio" x-model="selectedSize" value="L" class="w-5 h-5 text-orange-500 border-slate-300 focus:ring-orange-500">
                                <span class="font-bold text-slate-700 text-sm">Size L</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500">+8.000đ</span>
                        </label>
                    </div>
                </div>

                <!-- Toppings -->
                <div>
                    <h4 class="font-bold text-sm text-slate-700 mb-3 uppercase tracking-wider flex justify-between">Topping thêm <span class="text-slate-400 text-xs font-medium lowercase">Có thể chọn nhiều</span></h4>
                    <div class="space-y-3">
                        <template x-for="topping in ['Trân châu đen', 'Trân châu trắng', 'Thạch đào', 'Kem Cheese']" :key="topping">
                            <label class="flex items-center justify-between p-3.5 border-2 border-slate-100 hover:border-orange-200 rounded-xl cursor-pointer hover:bg-orange-50/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" x-model="selectedToppings" :value="topping" class="w-5 h-5 text-orange-500 rounded border-slate-300 focus:ring-orange-500">
                                    <span class="font-bold text-slate-700 text-sm" x-text="topping"></span>
                                </div>
                                <span class="text-sm font-bold text-slate-500">+5.000đ</span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Note -->
                <div>
                    <h4 class="font-bold text-sm text-slate-700 mb-3 uppercase">Ghi chú</h4>
                    <textarea x-model="itemNote" rows="2" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all font-medium placeholder:text-slate-400 text-sm" placeholder="Ghi chú thêm cho món (vd: ít ngọt, nhiều sữa)..."></textarea>
                </div>
            </div>

            <button @click="addToCartWithOptions()" class="w-full bg-orange-500 text-white font-bold py-4 rounded-xl mt-8 shadow-lg active:scale-[0.98] transition-all text-lg flex items-center justify-center gap-2">
                <span>Thêm vào giỏ</span>
                <span class="w-1.5 h-1.5 bg-white/50 rounded-full"></span>
                <span x-text="formatCurrency(getOptionPrice())"></span>
            </button>
        </div>
    </div>

    <!-- Cart Modal -->
    <div x-show="cartModalOpen" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="cartModalOpen = false" x-show="cartModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full" class="bg-white rounded-t-3xl p-6 w-full max-w-md h-[80vh] flex flex-col relative shadow-2xl">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-black text-slate-800">Giỏ hàng</h3>
                <button @click="cartModalOpen = false" class="text-slate-400 bg-slate-100 rounded-full p-1.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            
            <div class="flex-1 overflow-y-auto space-y-4 no-scrollbar pb-4">
                <template x-for="item in cart" :key="item.cart_item_id">
                    <div class="flex justify-between items-start border-b border-slate-100 pb-4">
                        <div class="flex-1 pr-4">
                            <h4 class="font-bold text-slate-800 text-lg" x-text="item.name"></h4>
                            <p class="text-sm text-slate-500 mt-1 leading-relaxed font-medium" x-text="item.note"></p>
                            <p class="text-orange-500 font-bold mt-1.5 text-base" x-text="formatCurrency(item.price)"></p>
                        </div>
                        <button @click="removeFromCart(item.cart_item_id)" class="text-red-400 hover:text-red-600 p-2 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </template>
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 space-y-4 mt-20">
                        <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="font-medium">Giỏ hàng trống</p>
                    </div>
                </template>
            </div>
            
            <div class="pt-4 border-t border-slate-100 mt-auto">
                <div class="flex justify-between items-end mb-4 px-2">
                    <span class="text-slate-500 font-bold">Tổng cộng</span>
                    <span class="text-3xl font-black text-slate-800" x-text="formatCurrency(cartTotal)"></span>
                </div>
                <button @click="checkout()" :disabled="isCheckingOut || cart.length === 0" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 text-lg disabled:opacity-50 disabled:active:scale-100 active:scale-[0.98]">
                    <span x-text="isCheckingOut ? 'Đang gửi bếp...' : 'Xác nhận Gọi Món'"></span>
                    <svg x-show="!isCheckingOut" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>
</body>
</html>"""

with open("resources/views/menu/index.blade.php", "w", encoding="utf-8") as f:
    f.write(content)

