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
        $table->unsignedBigInteger('admin_id')->nullable()->after('user_id');
        // เพิ่ม foreign key หากต้องการ: $table->foreign('admin_id')->references('id')->on('admin_id');
    });
}

public function down()
{
    Schema::table('cafes', function (Blueprint $table) {
        $table->dropColumn('admin_id');
    });
}
};
