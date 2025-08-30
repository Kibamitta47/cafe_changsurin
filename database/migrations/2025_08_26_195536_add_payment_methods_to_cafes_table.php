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
    if (!Schema::hasColumn('cafes', 'payment_methods')) {
        Schema::table('cafes', function (Blueprint $table) {
            $table->json('payment_methods')->nullable()->after('cafe_styles');
        });
    }
}

public function down(): void
{
    if (Schema::hasColumn('cafes', 'payment_methods')) {
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropColumn('payment_methods');
        });
    }
}

};
