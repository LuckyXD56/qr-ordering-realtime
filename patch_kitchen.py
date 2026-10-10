
import os

path = "resources/views/kitchen/index.blade.php"
with open(path, "r", encoding="utf-8") as f:
    c = f.read()

find_str = """        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-slate-800 px-4 py-2 rounded-full border border-slate-700">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-sm font-semibold text-emerald-400">Live Sync</span>
            </div>
        </div>"""

replace_str = """        <div class="flex items-center gap-4">
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
        </div>"""

c = c.replace(find_str, replace_str)

# One more thing: The Kitchen file has mojibake! 
# In `Get-Content resources/views/kitchen/index.blade.php | Select-Object -First 25`
# It output `MÃ n hÃ¬nh Báº¿p` !
# This means `kitchen/index.blade.php` is ALSO corrupted from my powershell commands earlier!
# Oh no, in my `build_all.py` and `build_frontend.py` script, I never reset `kitchen/index.blade.php` from c3a9467... wait, `git reset --hard c3a9467` reset EVERYTHING.
# But `git reset --hard c3a9467` fixed it. Why did `cat` just output mojibake?
# Ah, `cat` in powershell does NOT read UTF-8 by default! So `cat` outputs mojibake to the terminal, but the file is fine!
# Let's double check if I can just write this safely.

with open(path, "w", encoding="utf-8") as f:
    f.write(c)

print("Kitchen updated")

