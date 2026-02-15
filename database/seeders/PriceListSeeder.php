<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء قائمة أسعار أساسية
        $priceListId = DB::table('price_lists')->insertGetId([
            'name' => 'قائمة أسعار الجملة - 2026',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. جلب المنتجات الموجودة لربطها بالأسعار
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️  لا توجد منتجات في جدول products! الرجاء إضافة منتجات أولاً.');

            return;
        }

        // 3. إضافة أسعار لكل منتج في هذه القائمة
        foreach ($products as $product) {
            DB::table('price_list_items')->insert([
                'price_list_id' => $priceListId,
                'product_id' => $product->id,
                'price' => $product->base_price ? $product->base_price * 1.1 : rand(1000, 5000), // سعر افتراضي
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ تم إنشاء قائمة الأسعار وربطها بـ '.$products->count().' منتج بنجاح.');
    }
}
