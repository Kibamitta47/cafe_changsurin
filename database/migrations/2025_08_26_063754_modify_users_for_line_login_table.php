<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ทำให้ email และ password ไม่บังคับกรอก (nullable)
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();

            // เปลี่ยนแปลงคอลัมน์ line_id
            $table->string('line_id')->change();

            // --- นี่คือส่วนที่แก้ไขแล้ว ---
            // ตรวจสอบก่อนลบคอลัมน์ที่ไม่จำเป็น
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('users', 'two_factor_secret')) {
                $table->dropColumn('two_factor_secret');
            }
            if (Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->dropColumn('two_factor_recovery_codes');
            }
            if (Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->dropColumn('two_factor_confirmed_at');
            }
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // โค้ดสำหรับย้อนกลับ (เผื่อต้องการ rollback)
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
            
            // ไม่จำเป็นต้อง dropUnique ที่นี่ เพราะเราไม่ได้เพิ่มใน up() แล้ว
            // $table->dropUnique(['line_id']); 

            // เพิ่มคอลัมน์กลับเข้ามา (มีการตรวจสอบก่อนเพิ่มเพื่อความปลอดภัย)
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable();
            }
             if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable();
            }
            if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }
};