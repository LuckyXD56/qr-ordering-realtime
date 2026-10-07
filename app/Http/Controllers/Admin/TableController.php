<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Table::create([
            'name' => $request->name,
            'status' => 'empty',
            'qr_token' => Str::random(16)
        ]);
        return back()->with('success', 'Đã thêm bàn mới thành công!');
    }

    public function generateQr(Request $request, Table $table)
    {
        // Generate a unique token for the table
        $token = Str::random(16);
        $table->update(['qr_token' => $token]);
        
        return back()->with('success', 'Đã tạo mã QR mới cho: ' . $table->name);
    }
}
