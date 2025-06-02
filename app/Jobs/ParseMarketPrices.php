<?php

namespace App\Jobs;

use App\Models\Market;
use App\Models\Product;
use App\Models\Price;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class ParseMarketPrices implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected string $marketSlug;
    protected array $categoryIds;

    public function __construct(string $marketSlug)
    {
        $this->marketSlug  = $marketSlug;
        $this->categoryIds = config("scraper.markets.{$this->marketSlug}", []);
    }

    public function handle()
    {
        Log::info("=== Початок парсингу ATB через wloadmore === Slug: {$this->marketSlug}");

        // 1) Знайдемо ринок у базі
        $market = Market::where('slug', $this->marketSlug)->first();
        if (! $market) {
            Log::error("{$this->marketSlug}: ринок не знайдено");
            return;
        }

        // 2) Налаштовуємо Guzzle
        $jar = new CookieJar();
        $http = new Client([
            'timeout'        => 15,
            'cookies'        => $jar,
            'headers' => [
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
                                    . 'AppleWebKit/537.36 (KHTML, like Gecko) '
                                    . 'Chrome/114.0.0.0 Safari/537.36',
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection'      => 'keep-alive',
                'Referer'         => 'https://www.atbmarket.com/',
            ],
            'allow_redirects' => [
                'max'             => 10,
                'strict'          => true,
                'referer'         => true,
                'track_redirects' => true,
            ],
        ]);

        // 3) Парсимо кожну категорію за ID
        foreach ($this->categoryIds as $categoryId) {
            $page    = 1;
            $storeId = 1154; // ваш storeId

            while (true) {
                // Для page=1 не додаємо параметр page, а далі &page=N
                if ($page === 1) {
                    $loadUrl = "https://www.atbmarket.com/shop/catalog/wloadmore"
                             . "?loadMore=&customCat=&cat={$categoryId}&store={$storeId}";
                } else {
                    $loadUrl = "https://www.atbmarket.com/shop/catalog/wloadmore"
                             . "?loadMore=&customCat=&cat={$categoryId}&store={$storeId}"
                             . "&page={$page}";
                }

                try {
                    $response = $http->get($loadUrl);
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    $status = $e->getResponse()->getStatusCode();
                    Log::warning("{$this->marketSlug}: {$loadUrl} повернув {$status}. Завершую пагінацію category={$categoryId}.");
                    break;
                } catch (\Exception $e) {
                    Log::error("{$this->marketSlug}: не вдалося отримати {$loadUrl}: {$e->getMessage()}");
                    break;
                }

                $htmlFragment = (string) $response->getBody();

                // Якщо фрагмент порожній або немає article – завершуємо
                if (empty(trim($htmlFragment))) {
                    Log::info("{$this->marketSlug}: page {$page} повернув порожній фрагмент. Завершую.");
                    break;
                }

                $crawler = new Crawler($htmlFragment);
                $items = $crawler->filter('article.catalog-item.js-product-container');
                $count = $items->count();

                if ($count === 0) {
                    Log::info("{$this->marketSlug}: на page={$page} нема товарів. Завершую пагінацію.");
                    break;
                }

                // Обходимо кожен контейнер товару
                $items->each(function (Crawler $node) use ($market) {
                    // 3.1) Назва
                    $name = null;
                    if ($node->filter('.catalog-item__title a')->count()) {
                        $name = trim($node->filter('.catalog-item__title a')->text());
                    }

                    // 3.2) Product ID
                    $productId = null;
                    if ($node->filter('div.b-addToCart')->count()) {
                        $productId = $node->filter('div.b-addToCart')->attr('data-productid');
                    }

                    if (! $name || ! $productId) {
                        Log::warning("{$this->marketSlug}: пропускаю елемент (немає name або productId)");
                        return;
                    }

                    // 3.3) Ціна (вагова чи штучна)
                    $price = null;
                    if ($node->filter('.catalog-item__product-price.product-price--weight data')->count()) {
                        $raw = $node->filter('.catalog-item__product-price.product-price--weight data')
                                    ->attr('value');
                        $price = floatval(str_replace(',', '.', $raw));
                    } elseif ($node->filter('.catalog-item__product-price.product-price--unit data')->count()) {
                        $raw = $node->filter('.catalog-item__product-price.product-price--unit data')
                                    ->attr('value');
                        $price = floatval(str_replace(',', '.', $raw));
                    } else {
                        Log::warning("{$this->marketSlug}: не знайшов ціну для productId={$productId}");
                        return;
                    }

                    // 3.4) Збереження в БД
                    $product = Product::firstOrCreate(
                        ['barcode' => $productId],
                        ['name'    => $name]
                    );

                    Price::create([
                        'product_id'  => $product->id,
                        'market_id'   => $market->id,
                        'price'       => $price,
                        'recorded_at' => now(),
                    ]);
                });

                $page++;
            }
        }

        Log::info("=== Закінчення парсингу ATB через wloadmore === Slug: {$this->marketSlug}");
    }
}
