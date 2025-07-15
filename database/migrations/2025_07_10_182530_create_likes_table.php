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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // อ้างอิง user_id จากตาราง users
            $table->foreignId('cafe_id')->constrained()->onDelete('cascade'); // อ้างอิง cafe_id จากตาราง cafes
            $table->timestamps();

            // กำหนดให้ user_id และ cafe_id เป็น unique เพื่อป้องกันการกด Like ซ้ำ
            $table->unique(['user_id', 'cafe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};