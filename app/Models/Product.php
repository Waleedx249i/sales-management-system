<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['code', 'name', 'description', 'quantity', 'image'];

    public function priceListItems()
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function cost()
    {
        return $this->hasOne(ProductCost::class, 'product_id');
    }
}
