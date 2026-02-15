<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImportInvoice extends Model
{
    // أضف invoice_number هنا ليسمح للنظام بحفظه
    protected $fillable = [
        'invoice_number',
        'exchange_rate',
        'supplier_id',
        'total_goods_sdg',
        'total_logistic',
        'cost_ratio_percent',
        'status',
    ];

    /**
     * هذا الجزء يعمل تلقائياً عند محاولة حفظ فاتورة جديدة
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            // 1. الحصول على تاريخ اليوم بتنسيق (سنة شهر يوم)
            $todayDate = Carbon::now()->format('Ymd');

            // 2. نستخدم عدد الفواتير المخزنة اليوم ثم نضيف 1 ليصبح الترقيم تسلسلياً
            $countToday = self::whereDate('created_at', Carbon::today())->count();
            $newNumber = $countToday + 1;

            // 3. دمج التاريخ مع الرقم التسلسلي (مثلاً 20260209-1)
            $invoice->invoice_number = $todayDate.'-'.$newNumber;
        });
    }

    public function items()
    {
        return $this->hasMany(ImportInvoiceItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
