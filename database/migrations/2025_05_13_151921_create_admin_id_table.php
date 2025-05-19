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
        Schema::create('admin_id', function (Blueprint $table) {
            $table->id('AdminID');
            $table->string('UserName', 50)->unique();
            $table->string('Email')->unique();
            $table->string('Password', 100)->unique();
            $table->timestamps(); // เพิ่มคอลัมน์ created_at และ updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_id');
    }
};
