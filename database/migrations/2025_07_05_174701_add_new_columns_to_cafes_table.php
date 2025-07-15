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
    Schema::table('cafes', function (Blueprint $table) {
        // ไม่ต้องเพิ่มคอลัมน์ซ้ำ เพราะมีอยู่แล้ว
    });
}


    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('cafes', function (Blueprint $table) {
        $table->dropColumn('is_new_opening');
        $table->dropColumn('payment_methods');
        $table->dropColumn('facilities');
        $table->dropColumn('other_services');
    });
}

};