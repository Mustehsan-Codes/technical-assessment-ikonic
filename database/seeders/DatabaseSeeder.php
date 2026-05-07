<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users
        User::create([
            'name' => 'User 1',
            'email' => 'user1@ikonicdev.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'User 2',
            'email' => 'user2@ikonicdev.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ikonicdev.com',
            'password' => Hash::make('admin12345'),
        ]);

        // Create categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic gadgets and devices',
        ]);

        $clothing = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Fashion and apparel',
        ]);

        $books = Category::create([
            'name' => 'Books',
            'slug' => 'books',
            'description' => 'Books and publications',
        ]);

        $home = Category::create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
            'description' => 'Home improvement and garden supplies',
        ]);

        // Electronics products
        $electronicsProducts = [
            ['name' => 'Wireless Bluetooth Headphones', 'price' => 79.99, 'stock' => 50],
            ['name' => 'USB-C Charging Cable', 'price' => 12.99, 'stock' => 200],
            ['name' => 'Portable Power Bank 10000mAh', 'price' => 29.99, 'stock' => 75],
            ['name' => 'Smart Watch Pro', 'price' => 199.99, 'stock' => 30],
            ['name' => 'Mechanical Keyboard RGB', 'price' => 89.99, 'stock' => 45],
            ['name' => 'Wireless Mouse', 'price' => 34.99, 'stock' => 100],
            ['name' => '4K Webcam', 'price' => 129.99, 'stock' => 25],
            ['name' => 'Noise Cancelling Earbuds', 'price' => 149.99, 'stock' => 60],
        ];

        foreach ($electronicsProducts as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => 'High quality ' . strtolower($product['name']) . ' for everyday use.',
                'price' => $product['price'],
                'stock' => $product['stock'],
                'category_id' => $electronics->id,
                'is_active' => true,
            ]);
        }

        // Clothing products
        $clothingProducts = [
            ['name' => 'Classic Cotton T-Shirt', 'price' => 24.99, 'stock' => 150],
            ['name' => 'Slim Fit Jeans', 'price' => 59.99, 'stock' => 80],
            ['name' => 'Wool Blend Sweater', 'price' => 69.99, 'stock' => 40],
            ['name' => 'Running Sneakers', 'price' => 119.99, 'stock' => 65],
            ['name' => 'Leather Belt', 'price' => 39.99, 'stock' => 90],
            ['name' => 'Winter Jacket', 'price' => 149.99, 'stock' => 35],
        ];

        foreach ($clothingProducts as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => 'Premium ' . strtolower($product['name']) . ' made with quality materials.',
                'price' => $product['price'],
                'stock' => $product['stock'],
                'category_id' => $clothing->id,
                'is_active' => true,
            ]);
        }

        // Books
        $booksProducts = [
            ['name' => 'Clean Code', 'price' => 39.99, 'stock' => 100],
            ['name' => 'Design Patterns', 'price' => 44.99, 'stock' => 70],
            ['name' => 'The Pragmatic Programmer', 'price' => 42.99, 'stock' => 55],
            ['name' => 'Refactoring', 'price' => 47.99, 'stock' => 40],
        ];

        foreach ($booksProducts as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => 'A must-read book: ' . $product['name'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'category_id' => $books->id,
                'is_active' => true,
            ]);
        }

        // Home & Garden
        $homeProducts = [
            ['name' => 'Ceramic Plant Pot Set', 'price' => 34.99, 'stock' => 60],
            ['name' => 'LED Desk Lamp', 'price' => 49.99, 'stock' => 45],
            ['name' => 'Bamboo Cutting Board', 'price' => 22.99, 'stock' => 80],
            ['name' => 'Stainless Steel Water Bottle', 'price' => 18.99, 'stock' => 120],
        ];

        foreach ($homeProducts as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => 'Beautiful ' . strtolower($product['name']) . ' for your home.',
                'price' => $product['price'],
                'stock' => $product['stock'],
                'category_id' => $home->id,
                'is_active' => true,
            ]);
        }
    }
}
