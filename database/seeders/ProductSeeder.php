<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Ordinateur portable HP',
                'price' => 799.99,
                'stock' => 15,
            ],
            [
                'name' => 'Souris sans fil Logitech',
                'price' => 29.99,
                'stock' => 50,
            ],
            [
                'name' => 'Clavier mécanique RGB',
                'price' => 89.99,
                'stock' => 25,
            ],
            [
                'name' => 'Écran 27 pouces 4K',
                'price' => 349.99,
                'stock' => 10,
            ],
            [
                'name' => 'Webcam Full HD',
                'price' => 59.99,
                'stock' => 30,
            ],
            [
                'name' => 'Casque audio Bluetooth',
                'price' => 149.99,
                'stock' => 20,
            ],
            [
                'name' => 'Hub USB-C 7 ports',
                'price' => 45.99,
                'stock' => 40,
            ],
            [
                'name' => 'Tapis de souris XXL',
                'price' => 19.99,
                'stock' => 60,
            ],
            [
                'name' => 'Microphone USB',
                'price' => 79.99,
                'stock' => 18,
            ],
            [
                'name' => 'Support d\'ordinateur portable',
                'price' => 34.99,
                'stock' => 35,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
