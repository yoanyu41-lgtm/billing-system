<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::create([
            'name' => 'Laptop Dell',
            'price' => 1000,
            'stock' => 10,
            'description' => 'Gaming laptop',
        ]);

        \App\Models\Product::create([
            'name' => 'Desktop PC',
            'price' => 800,
            'stock' => 5,
            'description' => 'Office desktop',
        ]);
    }
}
