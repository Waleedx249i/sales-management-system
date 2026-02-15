<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            // الربط مع جدول قوائم الأسعار
            $table->foreignId('price_list_id')->constrained('price_lists')->onDelete('cascade');
            // الربط مع جدول الأصناف (تأكد أن اسم الجدول عندك هو products)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            // السعر الخاص بهذا الصنف في هذه القائمة
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list_items');
    }
};
