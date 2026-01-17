<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Laptop Pro 15', 'sku' => 'LAPTOP-001', 'price' => 1299.99, 'stock' => 25, 'is_active' => true],
            ['name' => 'USB-C Cable', 'sku' => 'CABLE-001', 'price' => 12.99, 'stock' => 500, 'is_active' => true],
            ['name' => 'Wireless Mouse', 'sku' => 'MOUSE-001', 'price' => 34.99, 'stock' => 150, 'is_active' => true],
            ['name' => 'Mechanical Keyboard', 'sku' => 'KEYBOARD-001', 'price' => 89.99, 'stock' => 75, 'is_active' => true],
            ['name' => 'Monitor 4K 27"', 'sku' => 'MONITOR-001', 'price' => 399.99, 'stock' => 40, 'is_active' => true],
            ['name' => 'USB Hub 7-Port', 'sku' => 'HUB-001', 'price' => 29.99, 'stock' => 200, 'is_active' => true],
            ['name' => 'Webcam 1080p', 'sku' => 'WEBCAM-001', 'price' => 49.99, 'stock' => 120, 'is_active' => true],
            ['name' => 'Headphones Wireless', 'sku' => 'HEADPHONE-001', 'price' => 79.99, 'stock' => 90, 'is_active' => true],
            ['name' => 'SSD 1TB', 'sku' => 'SSD-001', 'price' => 129.99, 'stock' => 100, 'is_active' => true],
            ['name' => 'RAM 16GB DDR4', 'sku' => 'RAM-001', 'price' => 59.99, 'stock' => 80, 'is_active' => true],
            ['name' => 'Power Supply 850W', 'sku' => 'PSU-001', 'price' => 119.99, 'stock' => 50, 'is_active' => true],
            ['name' => 'GPU RTX 4070', 'sku' => 'GPU-001', 'price' => 599.99, 'stock' => 15, 'is_active' => true],
            ['name' => 'CPU Intel i7-13700K', 'sku' => 'CPU-001', 'price' => 419.99, 'stock' => 20, 'is_active' => true],
            ['name' => 'Motherboard Z790', 'sku' => 'MOBO-001', 'price' => 289.99, 'stock' => 25, 'is_active' => true],
            ['name' => 'CPU Cooler Tower', 'sku' => 'COOLER-001', 'price' => 49.99, 'stock' => 60, 'is_active' => true],
            ['name' => 'Case ATX Premium', 'sku' => 'CASE-001', 'price' => 159.99, 'stock' => 35, 'is_active' => true],
            ['name' => 'Desk Pad XL', 'sku' => 'DESKPAD-001', 'price' => 39.99, 'stock' => 110, 'is_active' => true],
            ['name' => 'Monitor Arm Mount', 'sku' => 'MOUNT-001', 'price' => 44.99, 'stock' => 85, 'is_active' => true],
            ['name' => 'HDMI 2.1 Cable', 'sku' => 'HDMI-001', 'price' => 15.99, 'stock' => 300, 'is_active' => true],
            ['name' => 'DisplayPort Cable', 'sku' => 'DP-001', 'price' => 19.99, 'stock' => 250, 'is_active' => true],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
