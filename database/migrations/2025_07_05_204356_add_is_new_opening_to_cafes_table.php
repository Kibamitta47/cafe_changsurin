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
            // เนื่องจากคอลัมน์ 'is_new_opening' ถูกเพิ่มใน migration 'create_cafes_table' แล้ว
            // และมี migration อื่น (2025_07_04_173216) ที่พยายามเพิ่มคอลัมน์นี้เช่นกัน
            // เราจึงไม่จำเป็นต้องเพิ่มมันอีกครั้งที่นี่
            // หากคุณต้องการเพิ่มคอลัมน์อื่น ๆ ในภายหลัง สามารถเพิ่มที่นี่ได้
            // ตัวอย่าง: $table->string('another_new_column')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            // หาก migration นี้เคยเพิ่มคอลัมน์อื่น ๆ คุณจะต้อง drop คอลัมน์เหล่านั้นที่นี่
            // ตัวอย่าง: $table->dropColumn('another_new_column');
        });
    }
};
