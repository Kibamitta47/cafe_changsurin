<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cafe_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->text('content');
        $table->tinyInteger('rating');  // คะแนน 1-5
        $table->json('images')->nullable(); // เก็บชื่อไฟล์รูปภาพใน JSON
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
