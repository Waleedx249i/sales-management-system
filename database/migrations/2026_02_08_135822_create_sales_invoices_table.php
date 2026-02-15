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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('customer_name')->default('عميل نقدي');
            $table->decimal('total_amount', 15, 2);      // الإجمالي قبل الخصم
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2);      // الصافي المطلوب
            $table->decimal('paid_amount', 15, 2)->default(0); // ما تم دفعه فعلياً
            $table->decimal('remaining_amount', 15, 2)->default(0); // المتبقي (دين)
            $table->enum('status', ['paid', 'partial', 'pending'])->default('paid');
            $table->boolean('is_approved')->default(false)->after('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
