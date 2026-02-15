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
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_item_id')
                ->constrained('pos_consignment_items') // أضف pos_ هنا
                ->onDelete('cascade');
            $table->integer('quantity_sold');
            $table->decimal('unit_price', 15, 2); // السعر وقت البيع
            $table->decimal('total_amount', 15, 2);
            $table->dateTime('sale_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sales');
    }
};
