<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIdInAdminIdTableAndUpdateForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. เข้าไปที่ตาราง cafes เพื่อลบ Foreign Key เก่าก่อน
        Schema::table('cafes', function (Blueprint $table) {
            // เราจะให้ Laravel ลองเดาชื่อ Constraint มาตรฐานไปก่อน
            // ถ้า Error ค่อยไปหาชื่อจริงมาใส่ครับ
            $table->dropForeign(['admin_id']);
        });

        // 2. เปลี่ยนชื่อ Primary Key ในตาราง admin_id
        Schema::table('admin_id', function (Blueprint $table) {
            // จาก ER Diagram คอลัมน์ Primary Key ชื่อ AdminID (ตัวพิมพ์ใหญ่)
            $table->renameColumn('AdminID', 'admin_id_pk'); // เราจะใช้ชื่อใหม่ว่า admin_id_pk นะครับ เพราะ admin_id ถูกใช้เป็น Foreign Key ไปแล้ว
        });

        // 3. กลับไปสร้าง Foreign Key ใหม่ที่ตาราง cafes
        Schema::table('cafes', function (Blueprint $table) {
            // ให้ชี้ไปที่ Primary Key ใหม่ที่เราเพิ่งเปลี่ยนชื่อ
            $table->foreign('admin_id')->references('admin_id_pk')->on('admin_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ทำย้อนกลับ
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('admin_id', function (Blueprint $table) {
            $table->renameColumn('admin_id_pk', 'AdminID');
        });

        Schema::table('cafes', function (Blueprint $table) {
            $table->foreign('admin_id')->references('AdminID')->on('admin_id')->onDelete('cascade');
        });
    }
}