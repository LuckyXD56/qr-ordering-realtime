import os
import re

# 1. ProductController.php
with open("app/Http/Controllers/Admin/ProductController.php", "r", encoding="utf-8") as f:
    ctrl = f.read()

ctrl = ctrl.replace(
"""        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);
        
        $data = $request->all();
        $data['is_available'] = $request->has('is_available');
        
        Product::create($data);""",
"""        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        
        $data = $request->all();
        $data['is_available'] = $request->has('is_available');
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }
        
        Product::create($data);""")

ctrl = ctrl.replace(
"""        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_available'] = $request->has('is_available');

        $product->update($data);""",
"""        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();
        $data['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            // Delete old image if needed
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);""")

with open("app/Http/Controllers/Admin/ProductController.php", "w", encoding="utf-8") as f:
    f.write(ctrl)


# 2. Product Model (ensure 'image' is in fillable)
with open("app/Models/Product.php", "r", encoding="utf-8") as f:
    model = f.read()
if "'image'" not in model:
    model = model.replace("'price',", "'price', 'image',")
    with open("app/Models/Product.php", "w", encoding="utf-8") as f: f.write(model)


# 3. Create.blade.php
with open("resources/views/admin/products/create.blade.php", "r", encoding="utf-8") as f:
    create_html = f.read()

create_html = create_html.replace(
    """<form action="{{ route('products.store') }}" method="POST" class="space-y-6">""",
    """<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">"""
)
create_html = create_html.replace(
    """<div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mô tả (Tùy chọn)</label>""",
    """<div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Hình ảnh sản phẩm</label>
                        <input type="file" name="image" accept="image/*" class="w-full border-2 border-slate-200 px-4 py-2.5 rounded-xl font-medium text-slate-800 bg-slate-50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mô tả (Tùy chọn)</label>"""
)
with open("resources/views/admin/products/create.blade.php", "w", encoding="utf-8") as f: f.write(create_html)


# 4. Edit.blade.php
with open("resources/views/admin/products/edit.blade.php", "r", encoding="utf-8") as f:
    edit_html = f.read()

edit_html = edit_html.replace(
    """<form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">""",
    """<form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">"""
)
edit_html = edit_html.replace(
    """<div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mô tả (Tùy chọn)</label>""",
    """<div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Hình ảnh sản phẩm</label>
                        @if($product->image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-24 h-24 object-cover rounded-xl shadow-sm border border-slate-200">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*" class="w-full border-2 border-slate-200 px-4 py-2.5 rounded-xl font-medium text-slate-800 bg-slate-50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mô tả (Tùy chọn)</label>"""
)
with open("resources/views/admin/products/edit.blade.php", "w", encoding="utf-8") as f: f.write(edit_html)


# 5. Products Index view (show tiny thumbnail)
with open("resources/views/admin/products/index.blade.php", "r", encoding="utf-8") as f:
    index_html = f.read()
index_html = index_html.replace(
    """<p class="font-bold text-slate-800">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ $product->description }}</p>""",
    """<div class="flex items-center gap-3">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-12 h-12 rounded-lg object-cover bg-slate-100 flex-shrink-0 border border-slate-200">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300 flex-shrink-0"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-500 truncate max-w-[200px]">{{ $product->description }}</p>
                                    </div>
                                </div>"""
)
with open("resources/views/admin/products/index.blade.php", "w", encoding="utf-8") as f: f.write(index_html)


# 6. Menu Index (Customer App) show images
with open("resources/views/menu/index.blade.php", "r", encoding="utf-8") as f:
    menu = f.read()
menu = menu.replace(
    """<div class="w-28 h-28 bg-slate-100 rounded-xl flex-shrink-0 flex items-center justify-center text-slate-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>""",
    """@if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-28 h-28 rounded-xl object-cover shadow-sm flex-shrink-0 border border-slate-100">
                            @else
                                <div class="w-28 h-28 bg-slate-50 rounded-xl flex-shrink-0 flex items-center justify-center text-slate-300 border border-slate-100">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif"""
)
# Also show image in the Options Modal
menu = menu.replace(
    """<h3 class="text-xl font-black text-slate-800 pr-10 mb-1" x-text="currentProduct?.name"></h3>""",
    """<div class="flex items-center gap-4 mb-4 mt-2">
                <template x-if="currentProduct?.image">
                    <img :src="currentProduct?.image" class="w-20 h-20 rounded-2xl object-cover shadow-sm border border-slate-200">
                </template>
                <div>
                    <h3 class="text-xl font-black text-slate-800 mb-1" x-text="currentProduct?.name"></h3>
                    <p class="text-orange-500 font-bold text-lg" x-text="formatCurrency(currentProduct?.price)"></p>
                </div>
            </div>"""
)
# Remove the old price since it's now in the header block
menu = menu.replace("""<p class="text-orange-500 font-bold mb-6 text-lg" x-text="formatCurrency(currentProduct?.price)"></p>""", """""")
# Need to pass product image to openOptionModal
menu = menu.replace(
    """<button @click="openOptionModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})""",
    """<button @click="openOptionModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->image ? asset('storage/' . $product->image) : '' }}')"""
)
menu = menu.replace(
    """openOptionModal(id, name, price) {""",
    """openOptionModal(id, name, price, image) {"""
)
menu = menu.replace(
    """this.currentProduct = { id, name, price: parseFloat(price) };""",
    """this.currentProduct = { id, name, price: parseFloat(price), image };"""
)

with open("resources/views/menu/index.blade.php", "w", encoding="utf-8") as f: f.write(menu)

print("Images integrated")
