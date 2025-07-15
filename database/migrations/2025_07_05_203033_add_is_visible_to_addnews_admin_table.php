<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // เพิ่มคอลัมน์ 'is_visible' ในตาราง 'addnews_admin'
        // กำหนดให้เป็น boolean และมีค่าเริ่มต้นเป็น true
        // ตำแหน่งของคอลัมน์จะอยู่หลังคอลัมน์ 'id'
        Schema::table('addnews_admin', function (Blueprint $table) {
            $table->boolean('is_visible')->default(true)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // เมื่อย้อนกลับ (rollback) migration นี้ ให้ลบคอลัมน์ 'is_visible' ออก
        Schema::table('addnews_admin', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
};
