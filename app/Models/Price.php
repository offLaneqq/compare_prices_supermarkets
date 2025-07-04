<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    public function product() { 
        return $this->belongsTo(Product::class); 
    }

    public function market() { 
        return $this->belongsTo(Market::class);
    }
}
