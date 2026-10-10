
import os

def add_errors(path):
    with open(path, "r", encoding="utf-8") as f:
        c = f.read()
    
    error_html = """
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl shadow-sm mb-6">
                    <ul class="list-disc list-inside font-bold">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">"""
    
    c = c.replace('<div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">', error_html)
    with open(path, "w", encoding="utf-8") as f:
        f.write(c)

add_errors("resources/views/admin/products/create.blade.php")
add_errors("resources/views/admin/products/edit.blade.php")

