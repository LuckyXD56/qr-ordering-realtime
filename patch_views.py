import os
import re

# 1. Update Menu/index.blade.php
with open("resources/views/menu/index.blade.php", "r", encoding="utf-8") as f:
    menu = f.read()

# Replace the AlpineJS component
menu = menu.split("<script>")[0] + """<script>
        function menuApp() {
            return {
                cart: [],
                activeCategory: '{{ $categories->first()->id ?? 1 }}',
                isCheckingOut: false,
                cartTotal: 0,
                
                // Option Modal State
                optionModalOpen: false,
                cartModalOpen: false,
                currentProduct: null,
                selectedSize: 'M',
                selectedToppings: [],
                itemNote: '',
                
                openOptionModal(id, name, price) {
                    this.currentProduct = { id, name, price: parseFloat(price) };
                    this.selectedSize = 'M';
                    this.selectedToppings = [];
                    this.itemNote = '';
                    this.optionModalOpen = true;
                },
                
                getOptionPrice() {
                    if (!this.currentProduct) return 0;
                    let total = this.currentProduct.price;
                    if (this.selectedSize === 'L') total += 8000;
                    total += this.selectedToppings.length * 5000; // 5000 per topping
                    return total;
                },
                
                addToCartWithOptions() {
                    let noteParts = [];
                    if (this.selectedSize === 'L') noteParts.push('Size L (+8k)');
                    else noteParts.push('Size M');
                    
                    if (this.selectedToppings.length > 0) {
                        noteParts.push('Topping: ' + this.selectedToppings.join(', '));
                    }
                    if (this.itemNote.trim()) {
                        noteParts.push('Ghi chú: ' + this.itemNote);
                    }
                    
                    let finalNote = noteParts.join(' | ');
                    let finalPrice = this.getOptionPrice();
                    let cartItemId = this.currentProduct.id + '_' + Date.now();
                    
                    this.cart.push({
                        cart_item_id: cartItemId,
                        id: this.currentProduct.id,
                        name: this.currentProduct.name,
                        price: finalPrice,
                        quantity: 1,
                        note: finalNote
                    });
                    
                    this.calculateTotal();
                    this.optionModalOpen = false;
                },
                
                removeFromCart(cartItemId) {
                    this.cart = this.cart.filter(i => i.cart_item_id !== cartItemId);
                    this.calculateTotal();
                    if(this.cart.length === 0) this.cartModalOpen = false;
                },
                
                calculateTotal() {
                    this.cartTotal = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                
                formatCurrency(value) {
                    return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                },

                checkout() {
                    if (this.cart.length === 0) return;
                    this.isCheckingOut = true;
                    
                    fetch('/menu/{{ $table->qr_token }}/checkout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ cart: this.cart })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đã gửi món thành công! Bếp đang chuẩn bị nhé.');
                            this.cart = [];
                            this.cartTotal = 0;
                            this.cartModalOpen = false;
                        } else {
                            alert('Có lỗi xảy ra, vui lòng thử lại.');
                        }
                    })
                    .catch(err => {
                        alert('Có lỗi kết nối.');
                    })
                    .finally(() => {
                        this.isCheckingOut = false;
                    });
                }
            }
        }
    </script>
</body>
</html>"""

# Now replace the HTML of the product buttons
menu = re.sub(
    r"<div class=\"flex items-center gap-2\">[\s\S]*?</template>\s*</div>",
    """<button @click="openOptionModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})" class="bg-orange-100 text-orange-600 hover:bg-orange-200 px-4 py-1.5 rounded-full font-bold text-sm transition-colors">Chọn</button>""",
    menu
)

# Replace the Floating Cart Bar HTML to open the Cart Modal instead of direct checkout
menu = re.sub(
    r"<div class=\"fixed bottom-4 left-4 right-4 z-50 transition-all duration-300\"[\s\S]*?</body>",
    """<div class="fixed bottom-4 left-4 right-4 z-40 transition-all duration-300"
         x-show="cart.length > 0" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         style="display: none;">
        
        <div @click="cartModalOpen = true" class="bg-slate-900 rounded-2xl p-4 flex justify-between items-center shadow-2xl cursor-pointer active:scale-[0.98] transition-transform">
            <div class="flex items-center gap-3">
                <div class="relative bg-slate-800 p-3 rounded-xl border border-slate-700">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full shadow-md" x-text="cart.length"></span>
                </div>
                <div class="text-white">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Tổng cộng</p>
                    <p class="font-bold text-lg" x-text="formatCurrency(cartTotal)"></p>
                </div>
            </div>
            <div class="bg-orange-500 text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2">
                Xem giỏ <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </div>

    <!-- Option Modal -->
    <div x-show="optionModalOpen" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="optionModalOpen = false" x-show="optionModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full" class="bg-white rounded-t-3xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto relative shadow-2xl">
            <button @click="optionModalOpen = false" class="absolute top-4 right-4 text-slate-400 bg-slate-100 rounded-full p-1.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            
            <h3 class="text-xl font-black text-slate-800 pr-10 mb-1" x-text="currentProduct?.name"></h3>
            <p class="text-orange-500 font-bold mb-6 text-lg" x-text="formatCurrency(currentProduct?.price)"></p>
            
            <div class="space-y-6">
                <!-- Size -->
                <div>
                    <h4 class="font-bold text-sm text-slate-700 mb-3 uppercase">Kích cỡ (Size)</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" x-model="selectedSize" value="M" class="peer sr-only">
                            <div class="text-center py-3 rounded-xl border-2 border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 font-bold text-sm transition-all text-slate-500">Size M (Vừa)</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" x-model="selectedSize" value="L" class="peer sr-only">
                            <div class="text-center py-3 rounded-xl border-2 border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 font-bold text-sm transition-all text-slate-500 flex flex-col">
                                <span>Size L (Lớn)</span>
                                <span class="text-xs mt-0.5">+8.000đ</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Toppings -->
                <div>
                    <h4 class="font-bold text-sm text-slate-700 mb-3 uppercase">Topping thêm</h4>
                    <div class="space-y-2">
                        <template x-for="topping in ['Trân châu đen', 'Trân châu trắng 3Q', 'Thạch cà phê', 'Kem cheese']">
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
</html>""",
    menu.split("</body>")[0]
)

with open("resources/views/menu/index.blade.php", "w", encoding="utf-8") as f:
    f.write(menu)

# 2. Update Kitchen view to show notes
with open("resources/views/kitchen/index.blade.php", "r", encoding="utf-8") as f:
    kitchen = f.read()

kitchen = kitchen.replace(
    """<span class="leading-tight pt-0.5" x-text="item.product.name"></span>
                                    </li>""",
    """<div class="flex flex-col pt-0.5">
                                            <span class="leading-tight" x-text="item.product.name"></span>
                                            <template x-if="item.note">
                                                <span class="text-xs text-orange-600 mt-0.5 font-bold" x-text="item.note"></span>
                                            </template>
                                        </div>
                                    </li>"""
)
with open("resources/views/kitchen/index.blade.php", "w", encoding="utf-8") as f:
    f.write(kitchen)

# 3. Update Cashier view to show notes
with open("resources/views/cashier/index.blade.php", "r", encoding="utf-8") as f:
    cashier = f.read()

cashier = cashier.replace(
    """<div class="text-sm font-medium text-slate-500 mt-0.5" x-text="formatCurrency(item.price) + ' x ' + item.quantity"></div>
                                </div>""",
    """<div class="text-sm font-medium text-slate-500 mt-0.5" x-text="formatCurrency(item.price) + ' x ' + item.quantity"></div>
                                    <template x-if="item.note">
                                        <div class="text-xs text-orange-600 font-bold mt-1 bg-orange-50 px-2 py-1 rounded w-fit" x-text="item.note"></div>
                                    </template>
                                </div>"""
)
with open("resources/views/cashier/index.blade.php", "w", encoding="utf-8") as f:
    f.write(cashier)

print("Frontend options updated")
