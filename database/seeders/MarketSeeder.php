<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Market;

class MarketSeeder extends Seeder
{
    public function run()
    {
        $markets = [
            ['name' => 'Сільпо', 'slug' => 'silpo', 'url' => 'https://silpo.ua'],
            ['name' => 'АТБ',    'slug' => 'atb',   'url' => 'https://atbmarket.com'],
            ['name' => 'Novus',  'slug' => 'novus', 'url' => 'https://novus.online'],
        ];

        foreach ($markets as $data) {
            Market::create($data);
        }
    }
}