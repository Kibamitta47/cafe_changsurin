<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIdInAddnewsAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addnews_admins', function (Blueprint $table) {
            $table->renameColumn('id', 'addnews_admin_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addnews_admins', function (Blueprint $table) {
            $table->renameColumn('addnews_admin_id', 'id');
        });
    }
}