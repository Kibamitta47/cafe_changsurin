<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; 
use Laravel\Socialite\Facades\Socialite;

class LineLoginController extends Controller
{
    /**
     * Redirect the user to the LINE authentication page.
     */
    public function redirectToLine()
    {
        return Socialite::driver('line')->redirect();
    }

    /**
     * Obtain the user information from LINE.
     */
    public function handleLineCallback()
    {
        try {
            $lineUser = Socialite::driver('line')->user();

            

            $user = User::updateOrCreate([
                'line_id' => $lineUser->getId(),
            ], [
                'name' => $lineUser->getName(),
                'email' => $lineUser->getEmail(),
                // แก้ไขจาก str_random() เป็น Str::random()
                'password' => bcrypt(Str::random(16)) 
            ]);

            Auth::login($user);

            // คุณสามารถเปลี่ยน '/dashboard' เป็นหน้าที่ต้องการไปหลังล็อกอินสำเร็จ
              return redirect()->route('welcome');

        } catch (\Exception $e) {
            // ส่งกลับไปหน้า login พร้อมข้อความ error
            return redirect('/login')->with('error', 'เกิดข้อผิดพลาดในการล็อกอินผ่าน LINE: ' . $e->getMessage());
        }
    }
}