<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">
    <header class="bg-white shadow-sm border-b border-slate-200 px-8 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <div class="bg-orange-500 p-2 rounded-lg"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div>
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
    <main class="p-8 max-w-7xl mx-auto w-full">

        <div class="max-w-2xl mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('products.index') }}" class="text-slate-400 hover:text-slate-700 bg-slate-200 p-2 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
                <h2 class="text-3xl font-black text-slate-800">Thêm Món Mới</h2>
            </div>
            
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl shadow-sm mb-6">
                    <ul class="list-disc list-inside font-bold">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tên món ăn <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-2 border-slate-200 px-4 py-3 rounded-xl focus:outline-none focus:border-orange-500 font-medium text-slate-800">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Giá tiền (VNĐ) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0" class="w-full border-2 border-slate-200 px-4 py-3 rounded-xl focus:outline-none focus:border-orange-500 font-bold text-orange-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Danh mục <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full border-2 border-slate-200 px-4 py-3 rounded-xl focus:outline-none focus:border-orange-500 font-medium text-slate-800 bg-white">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Hình ảnh sản phẩm</label>
                        <input type="file" name="image" accept="image/*" class="w-full border-2 border-slate-200 px-4 py-2.5 rounded-xl font-medium text-slate-800 bg-slate-50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mô tả (Tùy chọn)</label>
                        <textarea name="description" rows="3" class="w-full border-2 border-slate-200 px-4 py-3 rounded-xl focus:outline-none focus:border-orange-500 font-medium text-slate-800">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_available" id="is_available" checked value="1" class="w-5 h-5 text-orange-500 rounded border-slate-300 focus:ring-orange-500">
                        <label for="is_available" class="font-bold text-slate-700 cursor-pointer">Sản phẩm đang mở bán (Còn hàng)</label>
                    </div>

                    <hr class="border-slate-100">
                    
                    <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl shadow-xl hover:bg-slate-800 active:scale-[0.98] transition-all">Lưu & Thêm Món</button>
                </form>
            </div>
        </div>

    </main>
</body>
</html>
