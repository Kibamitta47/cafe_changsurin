<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();

            // ถ้าต้องการ FK ทีหลังค่อยเติม — ตอนนี้ทำเป็น index ธรรมดาไปก่อน
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('query', 255);
            $table->string('normalized_query', 255)->nullable()->index();
            $table->json('filters')->nullable();
            $table->unsignedInteger('results_count')->default(0);
            $table->boolean('has_results')->default(false)->index();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
