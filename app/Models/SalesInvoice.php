<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_name', 'total_amount',
        'discount', 'final_amount', 'paid_amount',
        'remaining_amount', 'status', 'is_approved',
    ];

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }
}
