
import os

# 1. Update CategoryController
cat_ctrl = """<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create($request->only('name'));
        return back()->with('success', 'Đã thêm danh mục thành công.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category->update($request->only('name'));
        return back()->with('success', 'Đã cập nhật danh mục thành công.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục đang có sản phẩm.');
        }
        $category->delete();
        return back()->with('success', 'Đã xóa danh mục.');
    }
}
"""

with open("app/Http/Controllers/Admin/CategoryController.php", "w", encoding="utf-8") as f:
    f.write(cat_ctrl)

# 2. Update ProductController
prod_ctrl = """<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);
        
        $data = $request->all();
        $data['is_available'] = $request->has('is_available');
        
        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Đã thêm món mới thành công.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_available'] = $request->has('is_available');

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Đã cập nhật món ăn.');
    }

    public function destroy(Product $product)
    {
        // Should check if it's in active orders, but for demo:
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Đã xóa món ăn.');
    }
}
"""

with open("app/Http/Controllers/Admin/ProductController.php", "w", encoding="utf-8") as f:
    f.write(prod_ctrl)


header_html = """
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
"""

# Let's patch Admin Reports and Admin Tables to use this identical header so the navigation is consistent!
def update_admin_header(path):
    if not os.path.exists(path): return
    with open(path, "r", encoding="utf-8") as f: content = f.read()
    import re
    # We replace from <header> to </header> or </nav>\n    </header>
    content = re.sub(r"<header[\s\S]*?</header>", header_html.strip(), content)
    with open(path, "w", encoding="utf-8") as f: f.write(content)

update_admin_header("resources/views/admin/reports/index.blade.php")
update_admin_header("resources/views/admin/tables/index.blade.php")

print("Backend & Headers prepared")

