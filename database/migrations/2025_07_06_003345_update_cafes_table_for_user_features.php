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
        Schema::table('cafes', function (Blueprint $table) {
            // เปลี่ยนจาก 'image' เป็น 'images' (JSON) สำหรับรูปภาพหลายรูป
            $table->json('images')->nullable()->change();

            // ❌ คอลัมน์ lat/lng มีอยู่แล้ว จึงคอมเมนต์ไว้
            // $table->decimal('lat', 10, 7)->nullable()->after('address');
            // $table->decimal('lng', 10, 7)->nullable()->after('lat');

            // ตัวเลือกอื่น ๆ ถ้าคุณต้องการเพิ่ม สามารถปลดคอมเมนต์ได้
            // $table->string('price_range', 50)->nullable();
            // $table->json('cafe_styles')->nullable();
            // $table->string('other_style')->nullable();
            // $table->string('phone', 20)->nullable();
            // $table->string('email')->nullable();
            // $table->string('website')->nullable();
            // $table->string('facebook_page')->nullable();
            // $table->string('instagram_page')->nullable();
            // $table->string('line_id')->nullable();
            // $table->string('place_name')->nullable();
            // $table->string('open_day', 50)->nullable();
            // $table->string('close_day', 50)->nullable();
            // $table->time('open_time')->nullable();
            // $table->time('close_time')->nullable();
            // $table->json('payment_methods')->nullable();
            // $table->json('facilities')->nullable();
            // $table->json('other_services')->nullable();
            // $table->boolean('is_new_opening')->default(false);
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            // เปลี่ยนกลับจาก images -> image (string)
            $table->string('images')->nullable()->change();
            $table->renameColumn('images', 'image');

            // ลบคอลัมน์ lat/lng ถ้าเคยเพิ่มไว้
            $table->dropColumn(['lat', 'lng']);

            // ลบคอลัมน์อื่นๆ ถ้าเคยเพิ่มไว้
            $table->dropColumn([
                // 'price_range',
                // 'cafe_styles',
                // 'other_style',
                // 'phone',
                // 'email',
                // 'website',
                // 'facebook_page',
                // 'instagram_page',
                // 'line_id',
                // 'place_name',
                // 'open_day',
                // 'close_day',
                // 'open_time',
                // 'close_time',
                // 'payment_methods',
                // 'facilities',
                // 'other_services',
                // 'is_new_opening',
            ]);

            // $table->dropSoftDeletes();
        });
    }
};
