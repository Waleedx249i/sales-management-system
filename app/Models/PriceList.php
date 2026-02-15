<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = ['name', 'is_active'];

    // جلب كافة الأسعار المرتبطة بهذه القائمة
    public function items()
    {
        return $this::hasMany(PriceListItem::class);
    }
}
