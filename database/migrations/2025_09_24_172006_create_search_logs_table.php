<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('query');                         // ข้อความที่ค้นหา
            $table->string('normalized_query')->nullable();  // แปลงเล็ก/ตัดเว้นวรรค เพื่อรวมสถิติ
            $table->boolean('has_results')->default(false);  // พบผลลัพธ์ไหม
            $table->unsignedInteger('result_count')->default(0);
            $table->string('source', 50)->nullable();        // web/line/api ฯลฯ
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // ดัชนีสำหรับทำสถิติเร็ว
            $table->index(['created_at']);
            $table->index(['has_results']);
            $table->index(['normalized_query']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
