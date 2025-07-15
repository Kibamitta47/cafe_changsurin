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
            // ใส่การเพิ่มคอลัมน์ตรงนี้ (ถ้ามี)
            // เช่น
            // $table->string('open_day')->nullable();
            // $table->string('close_day')->nullable();
            // $table->time('open_time')->nullable();
            // $table->time('close_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            if (Schema::hasColumn('cafes', 'open_day')) {
                $table->dropColumn('open_day');
            }
            if (Schema::hasColumn('cafes', 'close_day')) {
                $table->dropColumn('close_day');
            }
            if (Schema::hasColumn('cafes', 'open_time')) {
                $table->dropColumn('open_time');
            }
            if (Schema::hasColumn('cafes', 'close_time')) {
                $table->dropColumn('close_time');
            }
        });
    }
};
