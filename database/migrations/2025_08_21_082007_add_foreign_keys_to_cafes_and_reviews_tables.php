<?php

use Illuminate from\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // === แก้ไขตาราง cafes ===
        Schema::table('cafes', function (Blueprint $table) {
            // Foreign Key 'user_id' เชื่อมกับ PK 'id' ของตาราง users (ค่ามาตรฐาน)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Foreign Key 'admin_id' เชื่อมกับ PK 'AdminID' ของตาราง admin_id (ตามโค้ดเดิม)
            // หมายเหตุ: ตรวจสอบให้แน่ใจว่า PK ของตาราง admin_id ชื่อ 'AdminID' จริงๆ
            $table->foreign('admin_id')->references('AdminID')->on('admin_id')->onDelete('set null');
        });

        // === แก้ไขตาราง reviews ===
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // สำคัญ: Foreign Key 'cafe_id' ต้องเชื่อมกับ PK 'cafe_id' ของตาราง cafes
            $table->foreign('cafe_id')->references('cafe_id')->on('cafes')->onDelete('cascade');
        });

        // === แก้ไขตาราง cafe_likes ===
        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // สำคัญ: Foreign Key 'cafe_id' ต้องเชื่อมกับ PK 'cafe_id' ของตาราง cafes
            $table->foreign('cafe_id')->references('cafe_id')->on('cafes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ... ส่วนของ down ไม่ต้องแก้ไข ...
    }
};