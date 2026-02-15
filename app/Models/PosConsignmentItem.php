<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosConsignmentItem extends Model
{
    protected $fillable = [
        'product_id', 'product_name', 'product_code',
        'product_image', 'delivered_qty', 'sold_qty', 'unit_price', 'unit_cost',
    ];

    // دالة مساعدة لعرض الصورة أو صورة افتراضية
    public function getImagePathAttribute()
    {
        return $this->product_image ? asset('storage/'.$this->product_image) : asset('images/default-product.png');
    }

    public function sales()
    {
        return $this->hasMany(PosSale::class, 'consignment_item_id');
    }

    // خاصية سحرية لحساب المجموع تلقائياً
    public function getCalculatedSoldQtyAttribute()
    {
        return $this->sales()->sum('quantity_sold');
    }
}
