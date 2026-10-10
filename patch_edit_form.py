import os

with open("resources/views/admin/products/edit.blade.php", "r", encoding="utf-8") as f:
    c = f.read()

c = c.replace(
    'value="{{ $product->name }}"',
    'value="{{ old(\'name\', $product->name) }}"'
)
c = c.replace(
    'value="{{ $product->price }}"',
    'value="{{ old(\'price\', $product->price) }}"'
)
c = c.replace(
    '>{{ $product->description }}</textarea>',
    '>{{ old(\'description\', $product->description) }}</textarea>'
)

with open("resources/views/admin/products/edit.blade.php", "w", encoding="utf-8") as f:
    f.write(c)

with open("resources/views/admin/products/create.blade.php", "r", encoding="utf-8") as f:
    c2 = f.read()

c2 = c2.replace(
    'name="name" required class=',
    'name="name" value="{{ old(\'name\') }}" required class='
)
c2 = c2.replace(
    'name="price" required min="0" class=',
    'name="price" value="{{ old(\'price\') }}" required min="0" class='
)
c2 = c2.replace(
    'name="description" rows="3" class="w-full border-2 border-slate-200 px-4 py-3 rounded-xl focus:outline-none focus:border-orange-500 font-medium text-slate-800"></textarea>',
    'name="description" rows="3" class="w-full border-2 border-slate-200 px-4 py-3 rounded-xl focus:outline-none focus:border-orange-500 font-medium text-slate-800">{{ old(\'description\') }}</textarea>'
)
with open("resources/views/admin/products/create.blade.php", "w", encoding="utf-8") as f:
    f.write(c2)
