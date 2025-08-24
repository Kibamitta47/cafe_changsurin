<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIdInCafesAndUpdateForeignKeys extends Migration
{
    public function up()
    {
        // --- ส่วนของตาราง cafes ---
        Schema::table('cafes', function (Blueprint $table) {
            // 1. เปลี่ยนชื่อ Primary Key จาก id เป็น cafe_id
            $table->renameColumn('id', 'cafe_id');
        });


        // --- สร้าง Foreign Key ให้กับตารางที่อ้างอิง ---
        Schema::table('cafe_likes', function (Blueprint $table) {
            // 2. สร้าง Foreign Key ใหม่ให้ชี้ไปที่ cafes.cafe_id
            $table->foreign('cafe_id')->references('cafe_id')->on('cafes')->onDelete('cascade');
        });

        // 3. แก้ชื่อตารางเป็น 'reviews'
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('cafe_id')->references('cafe_id')->on('cafes')->onDelete('cascade');
        });
    }

    public function down()
    {
        // --- เขียนโค้ดย้อนกลับในทิศทางตรงกันข้าม ---

        // 1. ลบ Foreign Key ที่สร้างไปใน up() ก่อน
        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->dropForeign(['cafe_id']);
        });

        // 2. แก้ชื่อตารางเป็น 'reviews'
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['cafe_id']);
        });


        // 3. เปลี่ยนชื่อคอลัมน์กลับคืน
        Schema::table('cafes', function (Blueprint $table) {
            // แก้ไข logic ตรงนี้: เปลี่ยนจาก cafe_id กลับไปเป็น id
            $table->renameColumn('cafe_id', 'id');
        });

        /*
         * ไม่ต้องสร้าง Foreign Key เก่ากลับคืน
         * เพราะในตอนแรกมันไม่มีอยู่แล้ว
         */
    }
}