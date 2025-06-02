<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'market_id'   => $this->market_id,
            'market_name' => $this->market->name,
            'price'       => $this->price,
            'recorded_at' => $this->recorded_at->toDateTimeString(),
        ];
    }
}
