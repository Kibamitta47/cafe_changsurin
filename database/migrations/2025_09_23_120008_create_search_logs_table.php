<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();

            // ผู้ใช้ที่ค้นหา (อาจไม่ล็อกอินก็ได้)
            $table->foreignId('user_id')->nullable()
                  ->constrained('users')->nullOnDelete();

            // คำค้นหา
            $table->string('term', 191)->index();

            // ตัวกรองที่ใช้ (เก็บเป็น JSON)
            $table->json('filters')->nullable();

            // จำนวนผลลัพธ์ที่ได้ตอนค้นหา (ไว้ดู zero-result rate)
            $table->unsignedInteger('results_count')->default(0);

            // บันทึกเบสิคเพิ่ม
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            // ช่วยให้สรุปตามวันได้เร็ว
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
