<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_person', 'phone', 'description'];

    public function importInvoices()
    {
        return $this->hasMany(ImportInvoice::class);
    }
}
