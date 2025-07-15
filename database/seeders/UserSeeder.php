<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // สร้าง User ที่มี ID = 1 ถ้ายังไม่มี
        if (!User::where('id', 1)->exists()) {
            User::create([
                'id' => 1,
                'name' => 'Default Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // เปลี่ยนเป็นรหัสที่ต้องการ
                'email_verified_at' => now(),
            ]);
        }
    }
}
