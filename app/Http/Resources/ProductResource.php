<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'barcode'     => $this->barcode,
            'category'    => $this->whenLoaded('category', fn() => $this->category->name),
            // мінімальна ціна та ринок
            'lowest_price'=> optional(
                $this->prices->sortBy('price')->first()
            , fn($p) => [
                'price'  => $p->price,
                'market' => $p->market->name,
            ]),
        ];
    }
}
