
import os
import re

# 1. MenuController.php
with open("app/Http/Controllers/MenuController.php", "r", encoding="utf-8") as f:
    c = f.read()
c = c.replace(
"""        if (!$order) {
            $order = \App\Models\Order::create([
                'table_id' => $table->id,
                'status' => 'active',
                'total_amount' => 0,
            ]);
        }""",
"""        if (!$order) {
            $order = \App\Models\Order::create([
                'table_id' => $table->id,
                'status' => 'active',
                'total_amount' => 0,
            ]);
            $table->update(['status' => 'active']);
        }"""
)
with open("app/Http/Controllers/MenuController.php", "w", encoding="utf-8") as f: f.write(c)

# 2. AuthController.php
auth_ctrl = """<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function showLogin() {
        if (Auth::check()) return $this->redirectBasedOnRole(Auth::user()->role);
        return view('auth.login');
    }
    public function login(Request $request) {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return back()->withErrors(['email' => 'Thông tin đăng nhập không chính xác.'])->onlyInput('email');
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
    private function redirectBasedOnRole($role) {
        if ($role === 'admin') return redirect()->route('reports.index');
        if ($role === 'cashier') return redirect()->route('cashier.index');
        if ($role === 'kitchen') return redirect()->route('kitchen.index');
        return redirect('/');
    }
}
"""
os.makedirs("app/Http/Controllers", exist_ok=True)
with open("app/Http/Controllers/AuthController.php", "w", encoding="utf-8") as f: f.write(auth_ctrl)

# 3. login.blade.php
os.makedirs("resources/views/auth", exist_ok=True)
login_blade = """<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Quản lý Nhà hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="bg-slate-900 px-8 py-10 text-center relative overflow-hidden">
            <div class="w-16 h-16 bg-orange-500 rounded-2xl mx-auto flex items-center justify-center mb-4 relative z-10 shadow-lg shadow-orange-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
            </div>
            <h1 class="text-2xl font-black text-white relative z-10">WRAITH COFFEE</h1>
            <p class="text-slate-400 font-medium mt-1 relative z-10">Hệ thống quản lý nội bộ</p>
        </div>
        <div class="p-8">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf
                @if($errors->any())
                    <div class="bg-red-50 text-red-500 p-3 rounded-xl text-sm font-semibold border border-red-100">{{ $errors->first() }}</div>
                @endif
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:outline-none focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Mật khẩu</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:outline-none focus:border-orange-500" value="password">
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white font-bold py-3.5 rounded-xl hover:bg-orange-600 transition-all shadow-lg">Đăng nhập</button>
            </form>
            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400 font-medium mb-3">Tài khoản Demo (Pass: password)</p>
                <div class="flex justify-center gap-2 text-xs font-semibold text-slate-500">
                    <span class="px-3 py-1 bg-slate-100 rounded-full cursor-pointer hover:bg-slate-200" onclick="document.querySelector('input[name=email]').value='admin@gmail.com'">Admin</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-full cursor-pointer hover:bg-slate-200" onclick="document.querySelector('input[name=email]').value='cashier@gmail.com'">Thu Ngân</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-full cursor-pointer hover:bg-slate-200" onclick="document.querySelector('input[name=email]').value='kitchen@gmail.com'">Bếp</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
"""
with open("resources/views/auth/login.blade.php", "w", encoding="utf-8") as f: f.write(login_blade)

# 4. web.php
routes = """<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;

Route::get('/', function () { return redirect()->route('login'); });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/menu/{qr_token}', [MenuController::class, 'index'])->name('menu.index');
Route::post('/menu/{qr_token}/checkout', [MenuController::class, 'checkout'])->name('menu.checkout');

Route::middleware(['auth'])->group(function () {
    Route::get('/kitchen', [\App\Http\Controllers\KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('/kitchen/orders/{order}/status', [\App\Http\Controllers\KitchenController::class, 'updateStatus'])->name('kitchen.update-status');

    Route::prefix('admin')->group(function () {
        Route::get('cashier', [\App\Http\Controllers\CashierController::class, 'index'])->name('cashier.index');
        Route::post('cashier/{table}/checkout', [\App\Http\Controllers\CashierController::class, 'checkout'])->name('cashier.checkout');
        Route::post('cashier/merge', [\App\Http\Controllers\CashierController::class, 'merge'])->name('cashier.merge');
        Route::get('cashier/invoice/{invoice}', [\App\Http\Controllers\CashierController::class, 'showInvoice'])->name('cashier.invoice');

        Route::post('tables/{table}/generate-qr', [\App\Http\Controllers\Admin\TableController::class, 'generateQr'])->name('tables.generate-qr');
        Route::resource('tables', \App\Http\Controllers\Admin\TableController::class);
        
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    });
});
"""
with open("routes/web.php", "w", encoding="utf-8") as f: f.write(routes)

# 5. CashierController.php
with open("app/Http/Controllers/CashierController.php", "r", encoding="utf-8") as f: c = f.read()
c = c.replace(
"""        return view('cashier.index', compact('tables'));
    }

    public function checkout(Request $request, Table $table)""",
"""        return view('cashier.index', compact('tables'));
    }

    public function merge(Request $request)
    {
        $request->validate([
            'source_table_id' => 'required|exists:tables,id',
            'target_table_id' => 'required|exists:tables,id|different:source_table_id',
        ]);
        $sourceTable = \App\Models\Table::findOrFail($request->source_table_id);
        $targetTable = \App\Models\Table::findOrFail($request->target_table_id);
        $sourceOrder = $sourceTable->orders()->whereIn('status', ['active', 'cooking', 'ready'])->first();
        $targetOrder = $targetTable->orders()->whereIn('status', ['active', 'cooking', 'ready'])->first();

        if (!$sourceOrder || !$targetOrder) return back()->with('error', 'Cả hai bàn đều phải đang có khách.');

        foreach ($sourceOrder->orderItems as $item) $item->update(['order_id' => $targetOrder->id]);
        $newTotal = $targetOrder->orderItems()->get()->sum(function($item) { return $item->price * $item->quantity; });
        $targetOrder->update(['total_amount' => $newTotal]);

        $sourceOrder->update(['status' => 'completed', 'total_amount' => 0]);
        $sourceTable->update(['status' => 'empty']);
        $sourceTable->generateQrToken();

        return back()->with('success', "Đã gộp {$sourceTable->name} vào {$targetTable->name} thành công!");
    }

    public function checkout(Request $request, \App\Models\Table $table)"""
).replace("'cashier_id' => 1,", "'cashier_id' => auth()->id(),")
with open("app/Http/Controllers/CashierController.php", "w", encoding="utf-8") as f: f.write(c)

print("Backend done")

