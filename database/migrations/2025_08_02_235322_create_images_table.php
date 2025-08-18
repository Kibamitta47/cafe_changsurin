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
    Schema::create('images', function (Blueprint $table) {
        $table->id();
        // สร้าง Foreign Key เพื่อเชื่อมไปยังตาราง reviews
        $table->foreignId('review_id')->constrained()->onDelete('cascade');
        $table->string('path'); // สำหรับเก็บชื่อไฟล์และที่อยู่
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
