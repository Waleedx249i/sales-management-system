<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosConsignment extends Model
{
    protected $fillable = ['consignment_number', 'pos_name', 'notes'];

    // علاقة مع الأصناف
    public function items()
    {
        return $this->hasMany(PosConsignmentItem::class);
    }

    // حسبة إجمالي القروش المطلوبة من الفاتورة دي كلها
    public function getTotalMoneyDueAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->sold_qty * $item->unit_price;
        });
    }
}
