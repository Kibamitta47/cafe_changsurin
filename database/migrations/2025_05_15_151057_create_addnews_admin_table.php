<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addnews_admin', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->json('images')->nullable();
    $table->timestamps();
});

    }

    public function down(): void {
        Schema::dropIfExists('Addnews_admin');
    }
};
