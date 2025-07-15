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
    Schema::table('addnews_admin', function (Blueprint $table) {
        $table->dateTime('start_datetime')->nullable();
        $table->dateTime('end_datetime')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addnews_admin', function (Blueprint $table) {
            //
        });
    }
};
