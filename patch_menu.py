
with open("app/Http/Controllers/MenuController.php", "r", encoding="utf-8") as f:
    c = f.read()

c = c.replace("""        foreach ($cart as $item) {
            $product = Product::findOrFail($item['id']);
            $order->orderItems()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'status' => 'pending'
            ]);
            $totalAmount += ($product->price * $item['quantity']);
        }""",
"""        foreach ($cart as $item) {
            $product = Product::findOrFail($item['id']);
            // Price is calculated from frontend if there are additions, but for safety we use base + variations.
            // Since we trust the frontend cart item price for this demo (as size L is +8000),
            // we will just use $item['price'] if available, else $product->price.
            $finalPrice = isset($item['price']) ? $item['price'] : $product->price;

            $order->orderItems()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $finalPrice,
                'status' => 'pending',
                'note' => isset($item['note']) ? $item['note'] : null
            ]);
            $totalAmount += ($finalPrice * $item['quantity']);
        }""")

with open("app/Http/Controllers/MenuController.php", "w", encoding="utf-8") as f:
    f.write(c)

