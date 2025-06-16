<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Database\Factories\ProductFactory; 

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product::insert([
        //     [
        //         'name' => 'Produk A',
        //         'description' => 'Deskripsi Produk A',
        //         'price' => 10000,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Produk B',
        //         'description' => 'Deskripsi Produk B',
        //         'price' => 20000,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Produk C',
        //         'description' => 'Deskripsi Produk C',
        //         'price' => 15000,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);

        ProductFactory::new()->count(500000)->create();
    }
}
