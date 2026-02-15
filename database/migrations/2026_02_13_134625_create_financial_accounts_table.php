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
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();

            // نوع الحساب: تكاليف (costs) أو أرباح شخصية (personal_profits)
            $table->enum('account_type', ['costs', 'personal_profits']);

            // المبلغ (15 رقم منها 2 بعد الفاصلة عشان الملايين)
            $table->decimal('amount', 15, 2);

            // البيان أو الوصف (إيجار، سحب شخصي، نثرية..)
            $table->string('description')->nullable();

            // التاريخ (تلقائي)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};
