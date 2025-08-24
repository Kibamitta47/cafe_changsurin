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
        // แก้ไขตาราง cafes
        Schema::table('cafes', function (Blueprint $table) {
            // เพิ่ม foreign key สำหรับ user_id ไปยังตาราง users
            // onDelete('cascade') หมายความว่าถ้า user ถูกลบ cafe ของ user นั้นก็จะถูกลบไปด้วย
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // เพิ่ม foreign key สำหรับ admin_id ไปยังตาราง admin_id (สมมติว่าชื่อตารางคือ admin_id)
            $table->foreign('admin_id')->references('AdminID')->on('admin_id')->onDelete('set null');
        });

        // แก้ไขตาราง reviews
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cafe_id')->references('id')->on('cafes')->onDelete('cascade');
        });

        // แก้ไขตาราง cafe_likes
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
    }
};