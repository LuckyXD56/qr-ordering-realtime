
import os

def insert_logout(path, find_str, replace_str):
    with open(path, "r", encoding="utf-8") as f:
        c = f.read()
    c = c.replace(find_str, replace_str)
    with open(path, "w", encoding="utf-8") as f:
        f.write(c)

# 1. Reports
insert_logout(
    "resources/views/admin/reports/index.blade.php",
    """<div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">A</div>
                <span class="text-sm font-bold">Admin</span>
            </div>""",
    """<div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : "A" }}</div>
                    <span class="text-sm font-bold">{{ auth()->check() ? auth()->user()->name : "Admin" }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">@csrf <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-bold transition-colors bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>Thoát</button></form>
            </div>"""
)

# 2. Tables
insert_logout(
    "resources/views/admin/tables/index.blade.php",
    """<div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">A</div>
                <span class="text-sm font-bold">Admin</span>
            </div>""",
    """<div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : "A" }}</div>
                    <span class="text-sm font-bold">{{ auth()->check() ? auth()->user()->name : "Admin" }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">@csrf <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-bold transition-colors bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>Thoát</button></form>
            </div>"""
)

# 3. Kitchen
insert_logout(
    "resources/views/kitchen/index.blade.php",
    """<div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-slate-700 flex items-center justify-center font-bold text-white shadow-inner">
                    B
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-white">Bếp Chính</span>
                    <span class="text-xs text-slate-400 font-medium">Đang hoạt động</span>
                </div>
            </div>""",
    """<div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-slate-700 flex items-center justify-center font-bold text-white shadow-inner">
                        {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : "B" }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-white">{{ auth()->check() ? auth()->user()->name : "Bếp Chính" }}</span>
                        <span class="text-xs text-slate-400 font-medium">Đang hoạt động</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">@csrf <button type="submit" class="text-sm text-red-400 hover:text-red-300 font-bold transition-colors bg-slate-800 hover:bg-slate-700 px-3 py-2 rounded-lg flex items-center gap-1 border border-slate-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>Thoát</button></form>
            </div>"""
)

# 4. Cashier
with open("resources/views/cashier/index.blade.php", "r", encoding="utf-8") as f: c = f.read()
# A. Add Logout
c = c.replace(
    """<img src="https://ui-avatars.com/api/?name=Thu+Ngan&background=f97316&color=fff" class="w-10 h-10 rounded-full shadow-sm" alt="Avatar">
        </div>""",
    """<img src="https://ui-avatars.com/api/?name=Thu+Ngan&background=f97316&color=fff" class="w-10 h-10 rounded-full shadow-sm" alt="Avatar">
            <form action="{{ route('logout') }}" method="POST" class="ml-2">@csrf <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-bold transition-colors bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>Thoát</button></form>
        </div>"""
)
# B. Add Gộp bàn Button
c = c.replace(
    """<div class="p-6 bg-slate-50 border-t border-slate-200" x-show="selectedTable && selectedTable.activeOrder">""",
    """<div class="px-6 py-4 border-b border-slate-100 bg-white" x-show="selectedTable && selectedTable.activeOrder">
                <button @click="mergeModalOpen = true" class="w-full bg-white border-2 border-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold hover:bg-slate-50 hover:border-slate-300 transition-colors flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Gộp bàn này với bàn khác
                </button>
            </div>
            <div class="p-6 bg-slate-50 border-t border-slate-200" x-show="selectedTable && selectedTable.activeOrder">"""
)
# C. Add Modal and mergeModalOpen
c = c.replace("selectedTable: null,", "selectedTable: null,\n                mergeModalOpen: false,")
c = c.replace("</body>", """
    <!-- Gộp Bàn Modal -->
    <div x-show="mergeModalOpen" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" 
         style="display: none;">
        
        <div @click.away="mergeModalOpen = false" 
             x-show="mergeModalOpen"
             class="bg-white rounded-3xl p-8 max-w-sm w-full mx-4 relative shadow-2xl">
            
            <button @click="mergeModalOpen = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-full p-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <h2 class="text-2xl font-black text-slate-800 mb-6">Gộp Bàn</h2>
            
            <form action="/admin/cashier/merge" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="source_table_id" :value="selectedTable?.id">
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Chọn bàn muốn gộp vào (đích)</label>
                    <select name="target_table_id" required class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:outline-none focus:border-orange-500 font-medium text-slate-800 bg-white">
                        <option value="" disabled selected>-- Chọn bàn --</option>
                        <template x-for="t in tables" :key="t.id">
                            <option x-show="t.id != selectedTable?.id && t.activeOrder" :value="t.id" x-text="t.name"></option>
                        </template>
                    </select>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2 mt-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Xác nhận Gộp
                </button>
            </form>
        </div>
    </div>
</body>
""")
with open("resources/views/cashier/index.blade.php", "w", encoding="utf-8") as f: f.write(c)

print("Frontend done")

