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
    Schema::table('reviews', function (Blueprint $table) {
        // เพิ่มคอลัมน์ images ที่เป็นชนิด TEXT ซึ่งสามารถเก็บ JSON ได้
        // และตั้งค่าให้สามารถเป็น NULL ได้ (nullable)
        $table->text('images')->nullable()->after('rating');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            //
        });
    }
};
