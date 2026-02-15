<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = Carbon::now()->subDays(60);
        $now = Carbon::now();

        $this->command->info('🚀 Starting the Mega Seeder for Accessories Shop...');

        // 1. Suppliers
        $this->command->info('Step 1: Creating Suppliers...');
        $supplierNames = ['Shine Elegance Group', 'Dubai Jewels Factory', 'China Fashion Trends', 'Riyadh Trading Est', 'Turkey Accessories Agent'];
        foreach ($supplierNames as $name) {
            DB::table('suppliers')->insert([
                'name' => $name,
                'contact_person' => 'Sales Manager',
                'phone' => '09'.rand(11111111, 99999999),
                'created_at' => $startDate,
                'updated_at' => $startDate,
            ]);
        }
        $this->command->warn('✔ Suppliers created successfully.');

        // 2. Products & Categories
        $this->command->info('Step 2: Generating Products with AI Images...');
        $categories = [
            'Rings' => ['Butterfly Gold Ring', '925 Silver Ring', 'Royal Chinese Ring', 'Pearl Studded Ring'],
            'Necklaces' => ['Natural Pearl Necklace', 'Golden Choker', 'Infinity Silver Chain', 'Crystal Heart Necklace'],
            'Watches' => ['Rolex Replica Watch', 'Cartier Women Watch', 'Guess Pink Watch'],
            'Hair' => ['Pearl Hair Clip', 'Studded Hair Band', 'Velvet Bandana', 'Shape Hair Pins'],
            'Perfumes' => ['Royal Makhmariya', 'Original Sudanese Khumra', 'Sandalwood Incense'],
        ];

        $productIds = [];
        foreach ($categories as $cat => $items) {
            foreach ($items as $itemName) {
                $keyword = match ($cat) {
                    'Rings' => 'ring,jewelry',
                    'Necklaces' => 'necklace,jewelry',
                    'Watches' => 'watch,luxury',
                    'Hair' => 'hair-accessory',
                    'Perfumes' => 'perfume',
                    default => 'jewelry'
                };
                $imageUrl = "https://loremflickr.com/400/400/{$keyword}?random=".rand(1, 1000);

                $pid = DB::table('products')->insertGetId([
                    'name' => $itemName,
                    'code' => strtoupper(Str::random(3)).'-'.rand(100, 999),
                    'description' => 'High quality '.$cat.' accessory, 2026 collection.',
                    'quantity' => rand(150, 500),
                    'image' => $imageUrl,
                    'created_at' => $startDate,
                    'updated_at' => $startDate,
                ]);
                $productIds[] = $pid;

                DB::table('product_costs')->insert([
                    'product_id' => $pid,
                    'weighted_average_cost' => rand(800, 2500),
                    'created_at' => $startDate,
                    'updated_at' => $startDate,
                ]);
            }
        }
        $this->command->warn('✔ Products and initial costs populated.');

        // 3. Price Lists
        $this->command->info('Step 3: Setting up Price Lists (Retail & Wholesale)...');
        $priceLists = [
            ['name' => 'Main Showroom Prices', 'markup' => 1.6],
            ['name' => 'Wholesale Prices', 'markup' => 1.3],
        ];
        foreach ($priceLists as $pl) {
            $plId = DB::table('price_lists')->insertGetId(['name' => $pl['name'], 'is_active' => 1, 'created_at' => $startDate]);
            foreach ($productIds as $pid) {
                $cost = DB::table('product_costs')->where('product_id', $pid)->first()->weighted_average_cost;
                DB::table('price_list_items')->insert([
                    'price_list_id' => $plId,
                    'product_id' => $pid,
                    'price' => $cost * $pl['markup'],
                    'created_at' => $startDate,
                ]);
            }
        }

        // 4. Import Invoices
        $this->command->info('Step 4: Processing 10 Import Invoices (Stock In)...');
        for ($i = 0; $i < 10; $i++) {
            $invDate = $startDate->copy()->addDays(rand(1, 40));
            $invId = DB::table('import_invoices')->insertGetId([
                'invoice_number' => 'IMP-2026-'.(1000 + $i),
                'supplier_id' => rand(1, 5),
                'exchange_rate' => rand(15, 20),
                'total_goods_sdg' => rand(50000, 150000),
                'total_logistic' => rand(5000, 10000),
                'cost_ratio_percent' => rand(5, 12),
                'status' => 'completed',
                'created_at' => $invDate,
            ]);

            $selectedProducts = array_rand(array_flip($productIds), 4);
            foreach ($selectedProducts as $pid) {
                $pInfo = DB::table('products')->where('id', $pid)->first();
                DB::table('import_invoice_items')->insert([
                    'import_invoice_id' => $invId,
                    'product_id' => $pid,
                    'item_name' => $pInfo->name,
                    'item_code' => $pInfo->code,
                    'price_egp' => rand(50, 150),
                    'quantity' => rand(20, 50),
                    'final_unit_cost' => rand(1000, 2000),
                    'image_path' => $pInfo->image,
                    'created_at' => $invDate,
                ]);
            }
        }

        // 5. POS & Consignments
        $this->command->info('Step 5: Distributing stock to 5 POS Locations & generating sales...');
        $posLocations = ['Afra Mall Branch', 'Al-Waha Branch', 'Riyadh Corner', 'Distribution Agent 1', 'Bahri Branch'];
        foreach ($posLocations as $index => $loc) {
            $consId = DB::table('pos_consignments')->insertGetId([
                'consignment_number' => 'POS-2026-00'.($index + 1),
                'pos_name' => $loc,
                'created_at' => $startDate->copy()->addDays(5),
            ]);

            $posProducts = array_rand(array_flip($productIds), 10);
            foreach ($posProducts as $pid) {
                $product = DB::table('products')->where('id', $pid)->first();
                $cost = DB::table('product_costs')->where('product_id', $pid)->first()->weighted_average_cost;
                $price = DB::table('price_list_items')->where('product_id', $pid)->first()->price;

                $soldCount = rand(15, 35);

                $itemId = DB::table('pos_consignment_items')->insertGetId([
                    'pos_consignment_id' => $consId,
                    'product_id' => $pid,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'product_image' => $product->image,
                    'delivered_qty' => 100,
                    'sold_qty' => $soldCount,
                    'unit_price' => $price,
                    'unit_cost' => $cost,
                    'created_at' => $startDate->copy()->addDays(6),
                ]);

                for ($s = 0; $s < $soldCount; $s++) {
                    DB::table('pos_sales')->insert([
                        'consignment_item_id' => $itemId,
                        'quantity_sold' => 1,
                        'unit_price' => $price,
                        'total_amount' => $price,
                        'sale_date' => $startDate->copy()->addDays(rand(7, 59)),
                        'created_at' => $now,
                    ]);
                }
            }
            $this->command->info("  -> Finished seeding for: $loc");
        }

        // 6. Direct Sales
        $this->command->info('Step 6: Creating 20 Direct Sales Invoices...');
        for ($v = 0; $v < 20; $v++) {
            $vDate = $startDate->copy()->addDays(rand(10, 55));
            $invId = DB::table('sales_invoices')->insertGetId([
                'invoice_number' => 'SAL-'.(7000 + $v),
                'customer_name' => 'Premium Customer '.rand(1, 50),
                'total_amount' => 0,
                'final_amount' => 0,
                'status' => 'paid',
                'created_at' => $vDate,
            ]);

            $totalInvoice = 0;
            for ($j = 0; $j < 2; $j++) {
                $pid = $productIds[array_rand($productIds)];
                $cost = DB::table('product_costs')->where('product_id', $pid)->first()->weighted_average_cost;
                $price = $cost * 1.8;
                $qty = rand(1, 2);
                $subtotal = $price * $qty;
                $totalInvoice += $subtotal;

                DB::table('sales_invoice_items')->insert([
                    'sales_invoice_id' => $invId,
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'unit_cost' => $cost,
                    'subtotal' => $subtotal,
                    'created_at' => $vDate,
                ]);
            }
            DB::table('sales_invoices')->where('id', $invId)->update([
                'total_amount' => $totalInvoice,
                'final_amount' => $totalInvoice,
            ]);
        }

        $this->command->info('✨ DATABASE SEEDING COMPLETED SUCCESSFULLY! ✨');
    }
}
