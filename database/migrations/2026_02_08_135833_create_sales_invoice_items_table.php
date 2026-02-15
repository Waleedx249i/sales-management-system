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
        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained(); // ربط بالصنف
            $table->integer('quantity'); // الكمية المباعة
            $table->decimal('unit_price', 15, 2); // سعر البيع وقتها
            $table->decimal('unit_cost', 15, 2);  // التكلفة (المتوسط المرجح) وقت البيع
            $table->decimal('subtotal', 15, 2); // الكمية * السعر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
    }
};
