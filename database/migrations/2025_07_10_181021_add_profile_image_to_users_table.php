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
            // เพิ่มคอลัมน์ profile_image
            $table->string('profile_image')->nullable()->after('email'); // หรือตำแหน่งที่คุณต้องการ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ลบคอลัมน์ profile_image เมื่อ rollback
            $table->dropColumn('profile_image');
        });
    }
};