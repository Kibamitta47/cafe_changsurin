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
    Schema::create('cafe_images', function (Blueprint $table) {
        $table->id('image_id');
        $table->unsignedBigInteger('cafe_id');
        $table->string('image_path');
        $table->timestamps();

        $table->foreign('cafe_id')
              ->references('cafe_id')->on('cafes')
              ->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_images');
    }
};
