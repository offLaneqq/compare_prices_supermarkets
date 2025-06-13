<?php

namespace App\Jobs;

use App\Models\Market;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\DomCrawler\Crawler;

class ParseMarketPrices implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // Порожній конструктор
    }

    public function handle()
    {
        $market = Market::skip(1)->first();
        if ($market) {
            $scraperConfig = config('scraper');
            $slug = $market->slug;

            if (!isset($scraperConfig['markets'][$slug])) {
                echo ("Для ринку '{$slug}' немає конфігурації парсингу.");
                return;
            }

            $categories = $scraperConfig['markets'][$slug];
            $selectors = $scraperConfig['selectors'][$slug] ?? [];

            foreach ($categories as $category) {
                $url = rtrim($market->url, '/') . '/catalog/' . $category;

                try {
                    $escapedUrl = escapeshellarg($url);
                    $nodeScript = base_path('node/playwright_scrape.js');
                    $command = "node {$nodeScript} {$escapedUrl} 2>&1";
                    $html = shell_exec($command);

                    if (!$html || strlen($html) < 1000) {
                        echo ("Не вдалося отримати HTML для {$url}: Playwright повернув порожній результат" . PHP_EOL);
                        echo "Playwright STDERR:\n$html\n";
                        continue;
                    }

                    echo "Отримано HTML для {$url}, довжина: " . strlen($html) . PHP_EOL;

                    // Парсимо HTML через DomCrawler
                    $crawler = new Crawler($html);
                    $itemSelector = $selectors['item_container'];
                    $items = $crawler->filter($itemSelector);

                    echo "Знайдено товарів: " . $items->count() . PHP_EOL;

                    if ($items->count() === 0) {
                        $debugFile = '/tmp/atb_debug_' . md5($url) . '.html';
                        file_put_contents($debugFile, $html);
                        echo "DEBUG: HTML збережено у {$debugFile}" . PHP_EOL;
                    }

                    foreach ($items as $itemNode) {
                        $item = new Crawler($itemNode);

                        // Назва
                        $name = $item->filter($selectors['name'])->count()
                            ? trim($item->filter($selectors['name'])->text())
                            : null;

                        // ID товару
                        $productId = $item->filter('div.b-addToCart')->count()
                            ? $item->filter('div.b-addToCart')->attr($selectors['product_id_attr'])
                            : null;

                        // Ціна
                        $price = null;
                        if ($item->filter($selectors['price_weighted'])->count()) {
                            $raw = $item->filter($selectors['price_weighted'])->attr($selectors['price_value_attr']);
                            $price = floatval(str_replace(',', '.', $raw));
                        } elseif ($item->filter($selectors['price_unit'])->count()) {
                            $raw = $item->filter($selectors['price_unit'])->attr($selectors['price_value_attr']);
                            $price = floatval(str_replace(',', '.', $raw));
                        }

                        echo "Товар: {$name}, ID: {$productId}, Ціна: {$price}" . PHP_EOL;
                        // Тут можна зберігати у БД або далі обробляти
                    }
                } catch (\Exception $e) {
                    echo ("Інша помилка для {$url}: " . $e->getMessage() . PHP_EOL);
                    continue;
                }
            }
        } else {
            echo ("У базі немає другого маркета.");
        }
    }
}
