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
        Schema::create('pos_consignments', function (Blueprint $table) {
            $table->id();
            $table->string('consignment_number')->unique(); // رقم الفاتورة مثلا POS-001
            $table->string('pos_name'); // اسم نقطة البيع أو المندوب
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_consignments');
    }
};
