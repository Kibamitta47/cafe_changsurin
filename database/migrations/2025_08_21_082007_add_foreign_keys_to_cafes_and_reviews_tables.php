<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /* helper: ลบ FK ถ้ามีอยู่แล้ว */
    protected function dropFkIfExists(string $table, string $constraint): void
    {
        $exists = DB::selectOne(
            "SELECT 1
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?",
            [$table, $constraint]
        );

        if ($exists) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }
    }

    public function up(): void
    {
        /** cafes */
        // ให้ column รองรับเงื่อนไข FK ที่จะตั้ง (admin_id ต้อง nullable ถ้าจะ set null)
        Schema::table('cafes', function (Blueprint $table) {
            if (Schema::hasColumn('cafes', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->change();
            }
        });

        // ลบ FK เดิมถ้ามี แล้วค่อยเพิ่มใหม่ (ตั้งชื่อ constraint ให้แน่นอน)
        $this->dropFkIfExists('cafes', 'cafes_user_id_foreign');
        $this->dropFkIfExists('cafes', 'cafes_admin_id_foreign');

        Schema::table('cafes', function (Blueprint $table) {
            // users(id)
            $table->foreign('user_id', 'cafes_user_id_foreign')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // admin_id(AdminID)  <- ชื่อตาราง/คอลัมน์ตามที่คุณใช้จริง
            $table->foreign('admin_id', 'cafes_admin_id_foreign')
                  ->references('AdminID')->on('admin_id')
                  ->nullOnDelete();   // เทียบเท่า onDelete('set null')
        });

        /** reviews */
        $this->dropFkIfExists('reviews', 'reviews_user_id_foreign');
        $this->dropFkIfExists('reviews', 'reviews_cafe_id_foreign');

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('user_id', 'reviews_user_id_foreign')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('cafe_id', 'reviews_cafe_id_foreign')
                  ->references('id')->on('cafes')
                  ->onDelete('cascade');
        });

        /** cafe_likes */
        $this->dropFkIfExists('cafe_likes', 'cafe_likes_user_id_foreign');
        $this->dropFkIfExists('cafe_likes', 'cafe_likes_cafe_id_foreign');

        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->foreign('user_id', 'cafe_likes_user_id_foreign')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('cafe_id', 'cafe_likes_cafe_id_foreign')
                  ->references('id')->on('cafes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropForeign('cafes_user_id_foreign');
            $table->dropForeign('cafes_admin_id_foreign');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign('reviews_user_id_foreign');
            $table->dropForeign('reviews_cafe_id_foreign');
        });

        Schema::table('cafe_likes', function (Blueprint $table) {
            $table->dropForeign('cafe_likes_user_id_foreign');
            $table->dropForeign('cafe_likes_cafe_id_foreign');
        });
    }
};
