<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSale extends Model
{
    protected $table = 'pos_sales';

    protected $fillable = [
        'consignment_item_id',
        'quantity_sold',
        'unit_price',
        'total_amount',
        'sale_date',
        'notes',
    ];

    public function consignmentItem()
    {
        return $this->belongsTo(PosConsignmentItem::class, 'consignment_item_id');
    }
}
