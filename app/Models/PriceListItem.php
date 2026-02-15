<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $fillable = ['price_list_id', 'product_id', 'price'];

    // الانتماء للقائمة
    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    // الانتماء للصنف
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
