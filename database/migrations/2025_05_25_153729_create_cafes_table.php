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
            Schema::create('cafes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // หากมี user_id
                $table->string('cafe_name');
                $table->string('place_name');
                $table->text('address');
                $table->decimal('lat', 10, 7); // ละติจูด ความแม่นยำสูง
                $table->decimal('lng', 10, 7); // ลองจิจูด ความแม่นยำสูง
                $table->string('price_range', 50);
                $table->string('phone', 20)->nullable();
                $table->string('email')->nullable();
                $table->string('website', 2048)->nullable();
                $table->string('facebook_page', 2048)->nullable();
                $table->string('instagram_page', 2048)->nullable();
                $table->string('line_id', 255)->nullable();
                $table->string('open_day', 255)->nullable();
                $table->string('close_day', 255)->nullable();
                $table->time('open_time')->nullable();
                $table->time('close_time')->nullable();
                $table->boolean('is_new_opening')->default(false);

                // คอลัมน์สำหรับเก็บ array ควรเป็น JSON
                $table->json('payment_methods')->nullable();
                $table->json('facilities')->nullable();
                $table->json('other_services')->nullable();
                $table->json('cafe_styles')->nullable();
                $table->string('other_style')->nullable();

                $table->json('images')->nullable(); // สำหรับเก็บ path ของรูปภาพหลายๆ รูป

                $table->boolean('parking')->default(false); // แยก field parking ออกมา
                $table->boolean('credit_card')->default(false); // แยก field credit_card ออกมา
                $table->string('status', 50)->default('pending'); // pending, approved, rejected

                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('cafes');
        }
    };
    