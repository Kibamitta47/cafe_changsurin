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
    Schema::table('addnews_admin', function (Blueprint $table) {
        // $table->tinyInteger('is_visible')->default(0); // คอมเมนต์บรรทัดนี้ไป หรือ
        // if (!Schema::hasColumn('addnews_admin', 'is_visible')) { // เพิ่มเงื่อนไขตรวจสอบ
        //     $table->tinyInteger('is_visible')->default(0);
        // }
    });
    // หากคุณมั่นใจว่าคอลัมน์นี้มีอยู่แล้ว ให้ใส่ return; เพื่อข้ามการดำเนินการ
    return; // <<<<<<<< เพิ่มบรรทัดนี้
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Addnews_admin', function (Blueprint $table) {
            //
        });
    }
};
