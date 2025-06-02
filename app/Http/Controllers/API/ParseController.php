<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ParseMarketPrices;


class ParseController extends Controller
{
    // POST /api/v1/parse
    public function handle(Request $request)
    {
        // Приймаємо optional параметр market (його slug)
        $market = $request->input('market');

        if ($market) {
            ParseMarketPrices::dispatch($market);
        } else {
            // якщо потрібно — розгорнути на всі ринки
            foreach (config('scraper.markets') as $slug) {
                ParseMarketPrices::dispatch($slug);
            }
        }

        return response()->json([
            'status' => 'dispatched',
            'market' => $market ?: 'all',
            'message' => 'Parsing jobs queued',
        ], 202);
    }
}
