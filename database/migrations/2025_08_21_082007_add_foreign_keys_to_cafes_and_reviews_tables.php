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
        // === ส่วนของการลบ Foreign Key เก่า (ถ้ามี) ===
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['admin_id']);
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cafe_id']);
        });
        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cafe_id']);
        });

        // === ส่วนของการสร้าง Foreign Key ใหม่ทั้งหมด ===
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
        // ... ไม่ต้องแก้ไขส่วนนี้ ...
    }
};