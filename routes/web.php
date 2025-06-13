<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\ParseMarketPrices;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/parse-atb', function () {
    $job = new ParseMarketPrices();
    return $job->handle();

});