<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            // เปลี่ยนคอลัมน์ 'images' ให้เป็นประเภท JSON และอนุญาตให้เป็น null ได้
            // หาก column เดิมเป็น TEXT หรือ VARCHAR และต้องการเปลี่ยนเป็น JSON
            // ตรวจสอบว่าไม่มีข้อมูลที่ invalid JSON อยู่ก่อนเปลี่ยน
            $table->json('images')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            // หากต้องการย้อนกลับ (เปลี่ยนกลับเป็น TEXT)
            $table->text('images')->nullable()->change(); // หรือประเภทเดิมที่คุณใช้ก่อนหน้านี้
        });
    }
};