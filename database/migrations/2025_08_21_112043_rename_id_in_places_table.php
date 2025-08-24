<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIdInPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // เลือกตาราง 'places'
        Schema::table('places', function (Blueprint $table) {
            // ทำการเปลี่ยนชื่อคอลัมน์จาก 'id' เป็น 'place_id'
            $table->renameColumn('id', 'place_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // สำหรับการ rollback (ย้อนกลับ)
        Schema::table('places', function (Blueprint $table) {
            // ให้เปลี่ยนชื่อคอลัมน์จาก 'place_id' กลับไปเป็น 'id'
            $table->renameColumn('place_id', 'id');
        });
    }
}