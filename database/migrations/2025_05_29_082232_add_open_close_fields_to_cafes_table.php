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
    Schema::table('cafes', function (Blueprint $table) {
        if (!Schema::hasColumn('cafes', 'open_day')) {
            $table->string('open_day')->nullable();
        }
        if (!Schema::hasColumn('cafes', 'close_day')) {
            $table->string('close_day')->nullable();
        }
        if (!Schema::hasColumn('cafes', 'open_time')) {
            $table->time('open_time')->nullable();
        }
        if (!Schema::hasColumn('cafes', 'close_time')) {
            $table->time('close_time')->nullable();
        }
    });

}

public function down()
{
    Schema::table('cafes', function (Blueprint $table) {
        $table->dropColumn(['open_day', 'close_day', 'open_time', 'close_time']);
    });
}

};
