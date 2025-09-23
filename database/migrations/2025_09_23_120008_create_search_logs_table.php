<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                  ->constrained('users')->nullOnDelete();

            $table->string('query');                      // raw query
            $table->string('normalized_query')->index();  // normalize ไว้ group
            $table->boolean('has_results')->default(false)->index();
            $table->unsignedInteger('results_count')->default(0);

            $table->json('meta')->nullable();             // เก็บฟิลเตอร์/หน้า/แหล่งที่มา
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
