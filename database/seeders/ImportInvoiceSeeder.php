<?php

namespace Database\Seeders;

use App\Models\ImportInvoice;
use App\Models\Product;
use App\Models\ProductCost;
use Illuminate\Database\Seeder;

class ImportInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء فاتورة استيراد واحدة مكتملة
        $invoice = ImportInvoice::create([
            'invoice_number' => time(), // إضافة رقم الفاتورة المفقود
            'exchange_rate' => 55.5,
            'supplier_id' => 1, // تأكد من وجود مورد بهذا المعرف في قاعدة البيانات
            'total_goods_sdg' => 120000,
            'total_logistic' => 15000,
            'cost_ratio_percent' => '12.5',
            'status' => 'completed',
        ]);

        // 2. اختيار أول 4 منتجات لإضافتها للفاتورة
        $products = Product::take(4)->get();

        foreach ($products as $product) {
            $newQty = 10;
            $newUnitCost = 4500.00;

            // إضافة الصنف للفاتورة
            $invoice->items()->create([
                'product_id' => $product->id,
                'item_name' => $product->name,
                'item_code' => $product->code,
                'price_egp' => 80.00,
                'quantity' => $newQty,
                'final_unit_cost' => $newUnitCost,
            ]);

            // جلب سجل التكلفة الحالي أو إنشاء واحد جديد
            $costRecord = ProductCost::firstOrCreate(['product_id' => $product->id]);

            $oldQty = (int) $product->quantity;
            $oldCost = (float) $costRecord->weighted_average_cost;

            $totalQty = $oldQty + $newQty;

            $average = $totalQty > 0
                ? (($oldQty * $oldCost) + ($newQty * $newUnitCost)) / $totalQty
                : $newUnitCost;

            // تحديث سجل التكلفة
            $costRecord->update(['weighted_average_cost' => round($average, 2)]);

            // تحديث مخزن المنتج
            $product->increment('quantity', $newQty);
        }
    }
}
