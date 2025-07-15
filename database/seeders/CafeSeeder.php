<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CafeSeeder extends Seeder
{
    public function run()
    {
        DB::table('cafes')->insert([
            [
                'cafe_name' => 'บ้านกาแฟ',
                'location' => 'อำเภอเมือง สุรินทร์',
                'status' => 'active',
                'user_id' => 1
            ],
            [
                'cafe_name' => 'คาเฟ่ริมทุ่ง',
                'location' => 'อำเภอศีขรภูมิ สุรินทร์',
                'status' => 'active',
                'user_id' => 1
            ],
        ]);
    }
}
