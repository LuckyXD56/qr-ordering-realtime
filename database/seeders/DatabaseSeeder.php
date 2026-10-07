<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin (Quản lý)',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Đầu Bếp',
            'email' => 'kitchen@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'kitchen'
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Thu Ngân',
            'email' => 'cashier@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'cashier'
        ]);

        \App\Models\Table::create([
            'name' => 'Bàn 1',
            'qr_token' => 'table1_token_123',
            'status' => 'empty'
        ]);

        $coffee = \App\Models\Category::create(['name' => 'Cà phê', 'sort_order' => 1]);
        $food = \App\Models\Category::create(['name' => 'Đồ ăn nhẹ', 'sort_order' => 2]);

        \App\Models\Product::create([
            'category_id' => $coffee->id,
            'name' => 'Cà phê Sữa đá',
            'description' => 'Cà phê pha phin truyền thống với sữa đặc',
            'price' => 25000,
            'is_available' => true
        ]);

        \App\Models\Product::create([
            'category_id' => $coffee->id,
            'name' => 'Bạc Xỉu',
            'description' => 'Nhiều sữa, ít cà phê',
            'price' => 30000,
            'is_available' => true
        ]);

        \App\Models\Product::create([
            'category_id' => $food->id,
            'name' => 'Bánh mì Thịt nướng',
            'description' => 'Bánh mì giòn rụm với thịt nướng đậm đà',
            'price' => 20000,
            'is_available' => true
        ]);

        \App\Models\Product::create([
            'category_id' => $food->id,
            'name' => 'Bánh sừng bò',
            'description' => 'Bánh ngọt bơ Pháp',
            'price' => 35000,
            'is_available' => true
        ]);
    }
}
