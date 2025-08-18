<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addnews_admins', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->json('images')->nullable(); // สำหรับเก็บรูปภาพ
            $table->boolean('is_visible')->default(false); // สำหรับเปิด/ปิดการแสดงผล
            $table->timestamps(); // สร้าง created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addnews_admins');
    }
};