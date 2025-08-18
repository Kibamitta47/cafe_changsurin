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
        Schema::table('users', function (Blueprint $table) {
            // เพิ่ม ->nullable() เพื่ออนุญาตให้เป็นค่าว่าง
            // เพิ่ม ->change() เพื่อสั่งให้แก้ไขคอลัมน์ที่มีอยู่แล้ว
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ส่วนนี้สำหรับย้อนกลับ (ไม่ต้องแก้ไข)
            $table->string('email')->nullable(false)->change();
        });
    }
};