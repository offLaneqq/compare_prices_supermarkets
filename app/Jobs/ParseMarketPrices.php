<?php

namespace App\Jobs;

use App\Models\Market;
use App\Models\Product;
use App\Models\Price;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseMarketPrices implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected string $marketSlug;

    public function __construct(string $marketSlug)
    {
        $this->marketSlug = $marketSlug;
    }

    public function handle()
    {
        $market = Market::where('slug', $this->marketSlug)->firstOrFail();
        $client = new HttpBrowser(HttpClient::create());

        // 1. Завантажуємо сторінку
        $crawler = $client->request('GET', $market->url);

        // 2. Проходимо по товарах (залежить від структури сайту)
        $crawler->filter('.product-item')->each(function ($node) use ($market) {
            $name    = $node->filter('.title')->text();
            $price   = floatval(str_replace(',', '.', $node->filter('.price')->text()));
            $barcode = $node->attr('data-barcode');

            // 3. Знаходимо або створюємо продукт
            $product = Product::firstOrCreate(
                ['barcode' => $barcode],
                ['name'     => $name]
            );

            // 4. Записуємо ціну
            Price::create([
                'product_id'  => $product->id,
                'market_id'   => $market->id,
                'price'       => $price,
                'recorded_at' => now(),
            ]);
        });
    }
}
