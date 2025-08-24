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
        // === แก้ไขตาราง cafes ===
        Schema::table('cafes', function (Blueprint $table) {
            // ลบ Foreign Key เก่า (ถ้ามี) โดยอ้างอิงจากชื่อคอลัมน์
            $table->dropForeign(['user_id']);
            $table->dropForeign(['admin_id']);
        });

        // === แก้ไขตาราง reviews ===
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cafe_id']);
        });
        
        // === แก้ไขตาราง cafe_likes ===
        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cafe_id']);
        });

        // === ทำการสร้าง Foreign Key ทั้งหมดใหม่อีกครั้ง ===
        Schema::table('cafes', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('AdminID')->on('admin_id')->onDelete('set null');
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cafe_id')->references('id')->on('cafes')->onDelete('cascade');
        });
        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cafe_id')->references('id')->on('cafes')->onDelete('cascade');
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