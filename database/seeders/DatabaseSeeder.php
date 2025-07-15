<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run()
{
    $this->call([
        CafeSeeder::class,
        // เพิ่ม Seeder อื่นๆ ถ้ามี เช่น UserSeeder, ReviewSeeder ฯลฯ
    ]);
}

}
