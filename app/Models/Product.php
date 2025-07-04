<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    public function category() { 
        return $this->belongsTo(Category::class); 
    }

    public function prices() {
        return $this->hasMany(Price::class); 
    }
}
