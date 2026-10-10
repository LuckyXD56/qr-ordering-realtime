<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bàn & QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 h-screen flex flex-col text-slate-800" x-data="{ qrModalOpen: false, currentQrUrl: '', currentTableName: '' }">
    
    <header class="bg-white shadow-sm border-b border-slate-200 px-8 py-5 flex justify-between items-center z-10">
        <div class="flex items-center gap-4">
            <div class="bg-orange-500 p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">QUẢN LÝ BÀN</h1>
        </div>
        <div class="flex items-center gap-4">
            <a href="/admin/reports" class="text-sm font-semibold text-slate-500 hover:text-orange-500 transition-colors">Báo Cáo Doanh Thu</a>
            <div class="h-4 w-px bg-slate-300"></div>
            <a href="/admin/cashier" class="text-sm font-semibold text-slate-500 hover:text-orange-500 transition-colors">Về trang Thu Ngân</a>
        </div>
    </header>

    <main class="flex-1 p-8 max-w-7xl mx-auto w-full overflow-y-auto">
        
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl shadow-sm flex items-center gap-3 mb-8">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Danh sách Bàn ({{ $tables->count() }})</h2>
                <p class="text-sm text-slate-500 mt-1">Quản lý mã QR cho từng bàn để khách hàng có thể quét và gọi món.</p>
            </div>
            
            <form action="{{ route('tables.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-start">
                @csrf
                <div class="w-full sm:w-64">
                    <input type="text" name="name" placeholder="Tên bàn mới (vd: Bàn 12)" required class="border-2 border-slate-200 rounded-xl px-4 py-3 w-full focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all font-medium text-slate-800 placeholder:text-slate-400">
                    @error('name')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-slate-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2 whitespace-nowrap h-[52px]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Thêm Bàn
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($tables as $table)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col items-center group hover:shadow-md transition-shadow relative overflow-hidden">
                    
                    <!-- Decorative background pattern -->
                    <div class="absolute -right-6 -top-6 text-slate-50 opacity-50 group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    </div>

                    <div class="w-16 h-16 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mb-4 border-4 border-white shadow-sm relative z-10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>

                    <h3 class="text-2xl font-black text-slate-800 mb-1 relative z-10">{{ $table->name }}</h3>
                    
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-6 relative z-10 {{ $table->status == 'empty' ? 'bg-slate-100 text-slate-500' : 'bg-orange-100 text-orange-600' }}">
                        {{ $table->status == 'empty' ? 'Đang trống' : 'Có khách' }}
                    </span>
                    
                    <div class="w-full space-y-3 mt-auto relative z-10">
                        <button 
                            @click="qrModalOpen = true; currentTableName = '{{ $table->name }}'; currentQrUrl = '{{ url('/menu/'.$table->qr_token) }}'; setTimeout(() => { document.getElementById('qrcode-container').innerHTML = ''; new QRCode(document.getElementById('qrcode-container'), {text: currentQrUrl, width: 200, height: 200, colorDark: '#0f172a', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.H}); }, 50);"
                            class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold py-3 rounded-xl transition-colors flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            Mã QR
                        </button>

                        <form action="{{ route('tables.generate-qr', $table->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-slate-400 hover:text-red-500 font-semibold py-2 rounded-xl transition-colors text-sm flex items-center justify-center gap-1" onclick="return confirm('⚠️ CẢNH BÁO: Tạo mã QR mới sẽ làm mã cũ bị vô hiệu hóa. Khách hàng đang dùng mã cũ sẽ không thể gọi thêm món. Bạn có chắc chắn?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Reset QR Code
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- QR Code Modal -->
    <div x-show="qrModalOpen" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" 
         style="display: none;">
        
        <div @click.away="qrModalOpen = false" 
             x-show="qrModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-8"
             class="bg-white rounded-3xl p-8 max-w-sm w-full mx-4 flex flex-col items-center relative shadow-2xl">
            
            <button @click="qrModalOpen = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-full p-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <div class="w-full text-center mb-8">
                <h2 class="text-3xl font-black text-slate-800" x-text="currentTableName"></h2>
                <p class="text-slate-500 mt-2 font-medium">Quét mã QR để xem menu & đặt món</p>
            </div>
            
            <div class="bg-white p-4 rounded-2xl shadow-[0_0_40px_rgba(0,0,0,0.08)] mb-8 border border-slate-100">
                <div id="qrcode-container" class="w-[200px] h-[200px]"></div>
            </div>
            
            <div class="w-full flex gap-3">
                <button onclick="window.print()" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    In Mã QR
                </button>
            </div>
            
            <a :href="currentQrUrl" target="_blank" class="mt-6 text-slate-400 hover:text-orange-500 font-medium text-sm transition-colors text-center w-full truncate px-4" x-text="currentQrUrl"></a>
        </div>
    </div>

</body>
</html>
