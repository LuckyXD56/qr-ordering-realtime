<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn #{{ $invoice->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS for printing */
        @page {
            margin: 0;
            size: 80mm 200mm; /* Thermal printer typical size */
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }
        .receipt-container {
            width: 80mm;
            margin: 0 auto;
            background: #fff;
            padding: 15px;
            color: #000;
        }
        .receipt-font {
            font-family: 'Courier Prime', monospace;
        }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .receipt-container { width: 100%; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body class="py-8 flex flex-col items-center">
    
    <!-- Action buttons (Hidden when printing) -->
    <div class="no-print mb-8 flex gap-4">
        <a href="{{ route('cashier.index') }}" class="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại Thu Ngân
        </a>
        <button onclick="window.print()" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-orange-500/30 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            In Hóa Đơn
        </button>
    </div>

    <!-- Receipt -->
    <div class="receipt-container shadow-xl border border-slate-200">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold mb-1">GOURMET EATS</h1>
            <p class="text-xs text-gray-600 mb-1">123 Đường Tôn Đức Thắng, Liên Chiểu, Đà Nẵng</p>
            <p class="text-xs text-gray-600">SĐT: 0123.456.789</p>
        </div>

        <div class="border-b border-dashed border-gray-400 pb-3 mb-3 text-xs">
            <div class="flex justify-between mb-1">
                <span>Số HĐ: #{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span>Bàn: <span class="font-bold">{{ $invoice->order->table->name }}</span></span>
            </div>
            <div class="flex justify-between mb-1">
                <span>Ngày: {{ $invoice->created_at->format('d/m/Y') }}</span>
                <span>Giờ: {{ $invoice->created_at->format('H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Thu ngân: Admin</span>
            </div>
        </div>

        <!-- Items -->
        <div class="mb-4 text-sm receipt-font">
            <div class="flex font-bold border-b border-gray-800 pb-1 mb-2">
                <div class="w-1/2">Tên món</div>
                <div class="w-1/6 text-center">SL</div>
                <div class="w-1/3 text-right">T.Tiền</div>
            </div>
            
            @foreach($invoice->order->orderItems as $item)
                <div class="flex mb-2">
                    <div class="w-1/2 break-words pr-1">{{ $item->product->name }}</div>
                    <div class="w-1/6 text-center">{{ $item->quantity }}</div>
                    <div class="w-1/3 text-right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</div>
                </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="border-t border-dashed border-gray-400 pt-3 mb-4 text-sm">
            <div class="flex justify-between mb-1">
                <span>Cộng tiền hàng:</span>
                <span>{{ number_format($invoice->subtotal, 0, ',', '.') }}đ</span>
            </div>
            <div class="flex justify-between mb-1">
                <span>Phí dịch vụ/VAT:</span>
                <span>0đ</span>
            </div>
            <div class="flex justify-between items-end mt-2">
                <span class="font-bold uppercase">Tổng thanh toán:</span>
                <span class="text-xl font-bold">{{ number_format($invoice->total, 0, ',', '.') }}đ</span>
            </div>
        </div>

        <div class="text-xs text-center border-t border-dashed border-gray-400 pt-4 mt-2">
            <p class="mb-1">Hình thức thanh toán: 
                @if($invoice->payment_method == 'cash') Tiền mặt 
                @else Chuyển khoản @endif
            </p>
            <p class="font-bold text-sm mt-3">XIN CẢM ƠN QUÝ KHÁCH & HẸN GẶP LẠI!</p>
            <p class="mt-2 text-gray-500 text-[10px]">Powered by Student W04</p>
        </div>

    </div>

</body>
</html>
