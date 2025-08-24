<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIdInCafeLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->renameColumn('id', 'cafe_like_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->renameColumn('cafe_like_id', 'id');
        });
    }
}