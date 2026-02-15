<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = ['account_type', 'amount', 'description'];

    /**
     * دالة ثابتة لحساب إجمالي الرصيد لأي حساب
     * الاستخدام: FinancialAccount::getBalance('costs')
     */
    public static function getBalance($type)
    {
        return self::where('account_type', $type)->sum('amount');
    }
}
