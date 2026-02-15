<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pos_consignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_consignment_id')->constrained()->onDelete('cascade');

            // بيانات المنتج كـ لقطة ثابتة (Snapshot)
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->string('product_code')->nullable();
            $table->string('product_image')->nullable(); // تخزين مسار الصورة

            $table->integer('delivered_qty');
            $table->integer('sold_qty')->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('unit_cost', 15, 2); // التكلفة (المتوسط المرجح) وقت التسليم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_consignment_items');
    }
};
