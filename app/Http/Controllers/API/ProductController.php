<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // GET /api/v1/products
    public function index()
    {
        // Залежно від обсягу, можна пагінувати
        $products = Product::with(['prices.market'])
            ->get();

        return ProductResource::collection($products);
    }

    // GET /api/v1/products/{id}
    public function show($id)
    {
        $product = Product::with(['prices.market'])
            ->findOrFail($id);

        return response()->json([
            'product' => new ProductResource($product),
            'prices'  => PriceResource::collection($product->prices),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
