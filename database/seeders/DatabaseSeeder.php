<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Price;
use App\Models\Product;
use App\Models\Market;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test123 User',
        //     'email' => 'test123@example.com',
        // ]);

        $this->call([
            CategorySeeder::class,
            MarketSeeder::class,
            ProductSeeder::class,
            PriceSeeder::class,
        ]);
    }
}
