<!DOCTYPE html>
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
