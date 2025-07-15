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
            // บรรทัดที่เคยเพิ่มคอลัมน์ 'facilities' ควรถูกลบออกไปแล้ว
            // หากคุณต้องการเพิ่มคอลัมน์อื่น ๆ ในภายหลัง สามารถเพิ่มที่นี่ได้
            // ตัวอย่าง: $table->string('new_column')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            // หาก migration นี้เคยเพิ่มคอลัมน์อื่น ๆ คุณจะต้อง drop คอลัมน์เหล่านั้นที่นี่
            // ตัวอย่าง: $table->dropColumn('new_column');
        });
    }
};
