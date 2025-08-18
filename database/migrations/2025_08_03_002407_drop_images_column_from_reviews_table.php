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
    Schema::table('reviews', function (Blueprint $table) {
        // ตรวจสอบก่อนว่ามีคอลัมน์นี้อยู่หรือไม่ แล้วจึงลบ
        if (Schema::hasColumn('reviews', 'images')) {
            $table->dropColumn('images');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            //
        });
    }
};
