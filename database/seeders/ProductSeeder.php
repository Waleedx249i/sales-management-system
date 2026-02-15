<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCost;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'طقم ذهب صيني فخم', 'code' => 'ACC-101', 'quantity' => 12],
            ['name' => 'ساعة يد كارتير (ماستر)', 'code' => 'WAT-202', 'quantity' => 8],
            ['name' => 'شنطة يد قوتشي ميني', 'code' => 'BAG-303', 'quantity' => 5],
            ['name' => 'نظارة شمسية فيندي', 'code' => 'SUN-404', 'quantity' => 10],
            ['name' => 'عطر ميني ماركة جادور', 'code' => 'PER-505', 'quantity' => 20],
            ['name' => 'خاتم توينز فضة تركي', 'code' => 'RNG-606', 'quantity' => 15],
            ['name' => 'خلخال ذهب عيار 21 (طلي)', 'code' => 'ANK-707', 'quantity' => 25],
        ];

        foreach ($products as $item) {
            $product = Product::create($item);

            // إنشاء سجل تكلفة ابتدائي (علاقة One-to-One)
            ProductCost::create([
                'product_id' => $product->id,
                'weighted_average_cost' => 0, // سيبدأ بصفر وسيحسب عند تشغيل سيدر الفواتير
            ]);
        }
    }
}
