<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportInvoiceItem extends Model
{
    // أضف هذا السطر للسماح بتخزين البيانات في هذه الحقول
    protected $fillable = [
        'import_invoice_id',
        'product_id',
        'item_name',
        'item_code',
        'price_egp',
        'quantity',
        'final_unit_cost',
        'image_path',
    ];

    // علاقة عكسية مع الفاتورة (اختياري لكن مفيد)
    public function invoice()
    {
        return $this->belongsTo(ImportInvoice::class, 'import_invoice_id');
    }
}
