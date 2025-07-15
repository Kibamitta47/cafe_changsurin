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
        Schema::table('cafes', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['user_id']);

            // Modify the column to be nullable
            $table->foreignId('user_id')->nullable()->change();

            // Re-add the foreign key constraint with ON DELETE SET NULL
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['user_id']);

            // Revert the column to be not nullable (if it was originally)
            // Note: If you have existing NULL values, this will fail.
            // You might need to handle existing NULLs before reverting.
            $table->foreignId('user_id')->nullable(false)->change();

            // Re-add the foreign key constraint without ON DELETE SET NULL (if it was originally)
            // Or adjust based on your original migration
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users'); // Assuming original was just this
        });
    }
};
