<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_id', function (Blueprint $table) {
            $table->id('AdminID');
            $table->string('UserName');
            $table->string('Email')->unique();
            $table->string('password');
            // บรรทัดนี้ควรจะมีอยู่แล้วในไฟล์นี้
            $table->string('profile_image')->nullable(); 
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_id');
    }
};