<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_costs', function (Blueprint $table) {
            $table->id();
            // ربط مع جدول المنتجات
            $table->foreignId('product_id')->unique()->constrained()->onDelete('cascade');

            // التكلفة المتوسطة النهائية فقط
            $table->decimal('weighted_average_cost', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_costs');
    }
};
