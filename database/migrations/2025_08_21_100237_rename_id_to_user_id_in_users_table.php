<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIdToUserIdInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // ทำการเปลี่ยนชื่อคอลัมน์ id เป็น user_id
            $table->renameColumn('id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // หากต้องการย้อนกลับ (rollback) ให้เปลี่ยนชื่อกลับเป็น id
            $table->renameColumn('user_id', 'id');
        });
    }
}