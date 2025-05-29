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
            ['name' => 'Сільпо', 'url' => 'https://silpo.ua'],
            ['name' => 'АТБ',    'url' => 'https://atbmarket.com'],
            ['name' => 'Novus',  'url' => 'https://novus.online'],
        ];

        foreach ($markets as $data) {
            Market::create($data);
        }
    }
}