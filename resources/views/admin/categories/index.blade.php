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

        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-black text-slate-800">Quản lý Danh mục</h2>
            <button onclick="document.getElementById('add-modal').style.display='flex'" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-slate-800 transition-colors shadow-lg flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Thêm Danh mục</button>
        </div>
        
        @if(session('success')) <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl font-bold mb-6 border border-emerald-200">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="bg-red-50 text-red-600 p-4 rounded-xl font-bold mb-6 border border-red-200">{{ session('error') }}</div> @endif
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-4 font-bold text-slate-600">ID</th>
                        <th class="p-4 font-bold text-slate-600">Tên Danh Mục</th>
                        <th class="p-4 font-bold text-slate-600 text-center">Số Món</th>
                        <th class="p-4 font-bold text-slate-600 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($categories as $category)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-medium text-slate-500">#{{ $category->id }}</td>
                            <td class="p-4 font-bold text-slate-800">{{ $category->name }}</td>
                            <td class="p-4 font-medium text-slate-500 text-center">
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">{{ $category->products_count }}</span>
                            </td>
                            <td class="p-4 text-right">
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa danh mục này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Add Modal -->
        <div id="add-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
            <div class="bg-white rounded-2xl p-6 w-full max-w-sm">
                <h3 class="text-xl font-bold mb-4">Thêm Danh Mục</h3>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <input type="text" name="name" required placeholder="Nhập tên danh mục..." class="w-full border-2 border-slate-200 px-4 py-2 rounded-xl mb-4 focus:outline-none focus:border-orange-500 font-medium">
                    <div class="flex gap-2 justify-end">
                        <button type="button" onclick="document.getElementById('add-modal').style.display='none'" class="px-4 py-2 text-slate-500 font-bold hover:bg-slate-100 rounded-xl">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-600 shadow-lg">Lưu</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</body>
</html>
