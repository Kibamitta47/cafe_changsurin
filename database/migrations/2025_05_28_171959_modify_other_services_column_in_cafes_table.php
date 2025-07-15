<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOtherServicesColumnInCafesTable extends Migration
{
    public function up()
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->text('other_services')->change();
            $table->text('payment_methods')->change();
            $table->text('facilities')->change();
        });
    }

    public function down()
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->string('other_services', 255)->change();
            $table->string('payment_methods', 255)->change();
            $table->string('facilities', 255)->change();
        });
    }
}
