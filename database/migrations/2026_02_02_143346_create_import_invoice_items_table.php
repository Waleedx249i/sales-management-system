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
        Schema::create('import_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->string('item_code');
            $table->decimal('price_egp', 15, 2);        // السعر بالمصري
            $table->integer('quantity');                // الكمية
            $table->decimal('final_unit_cost', 15, 2);  // التكلفة النهائية للقطعة (سوداني)
            $table->string('image_path')->nullable();   // مسار الصورة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_invoice_items');
    }
};
