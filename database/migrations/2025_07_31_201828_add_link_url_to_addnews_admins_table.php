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
    Schema::table('addnews_admins', function (Blueprint $table) {
        // เพิ่มคอลัมน์ link_url หลังคอลัมน์ content
        $table->string('link_url', 2048)->nullable()->after('content');
    });
}

public function down(): void
{
    Schema::table('addnews_admins', function (Blueprint $table) {
        $table->dropColumn('link_url');
    });
}
};
