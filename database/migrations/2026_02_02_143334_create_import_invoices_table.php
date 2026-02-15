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
        Schema::create('import_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->comment('رقم الفاتورة: YYYYMMDD-001');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->decimal('exchange_rate', 15, 2);    // سعر الصرف
            $table->decimal('total_goods_sdg', 15, 2);  // إجمالي البضاعة بالسوداني
            $table->decimal('total_logistic', 15, 2);   // إجمالي اللوجيستي
            $table->decimal('cost_ratio_percent', 8, 2); // نسبة التكلفة المضافة (مثلاً 5.5%)
            $table->string('status', 20)->default('pending'); // حالة الفاتورة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_invoices');
    }
};
