<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Market;
use App\Models\Price;

class PriceSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        $markets  = Market::all();

        foreach ($products as $product) {
            foreach ($markets as $market) {
                Price::create([
                    'product_id'  => $product->id,
                    'market_id'   => $market->id,
                    'price'       => rand(20, 100) + rand(0,99)/100, // наприклад 21.57
                    'recorded_at' => now()->subHours(rand(1,24)),
                ]);
            }
        }
    }
}