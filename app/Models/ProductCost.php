<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCost extends Model
{
    protected $fillable = ['product_id', 'weighted_average_cost'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
