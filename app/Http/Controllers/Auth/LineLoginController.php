<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LineLoginController extends Controller
{
    // STEP 1: ส่งผู้ใช้ไปหน้า LINE Login
    public function redirectToLine(Request $request)
    {
        $clientId    = config('services.line.client_id');
        $redirectUri = config('services.line.redirect');
        $state       = bin2hex(random_bytes(16)); // สุ่มค่า state

        // เก็บ state ไว้ใน session
        $request->session()->put('line_state', $state);

        $scope = 'profile openid email';

        $url = "https://access.line.me/oauth2/v2.1/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'state'         => $state,
            'scope'         => $scope,
        ]);

        return redirect($url);
    }

    // STEP 2: LINE ส่ง callback กลับมา
    public function handleLineCallback(Request $request)
    {
        // ตรวจสอบ state
        $sessionState = $request->session()->pull('line_state'); // ดึงค่าออกจาก session
        if (!$request->has('state') || $request->input('state') !== $sessionState) {
            return redirect('/login')->with('error', 'ไม่สามารถยืนยันตัวตน (state ไม่ตรง)');
        }

        if ($request->has('error')) {
            return redirect('/login')->with('error', 'ไม่สามารถเข้าสู่ระบบด้วย LINE ได้');
        }

        // แลก token
        $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type'    => 'authorization_code',
            'code'          => $request->input('code'),
            'redirect_uri'  => config('services.line.redirect'),
            'client_id'     => config('services.line.client_id'),
            'client_secret' => config('services.line.client_secret'),
        ]);

        $tokenData = $response->json();

        if (!isset($tokenData['id_token'])) {
            return redirect('/login')->with('error', 'การยืนยันตัวตนล้มเหลว');
        }

        // decode id_token (JWT) เอาข้อมูล user
        $userInfo = $this->decodeIdToken($tokenData['id_token']);

        // หา user หรือสร้างใหม่
        $user = User::firstOrCreate(
            ['line_id' => $userInfo['sub']],
            [
                'name'     => $userInfo['name'] ?? 'Line User',
                'email'    => $userInfo['email'] ?? ($userInfo['sub'].'@line.local'),
                'password' => bcrypt(str()->random(16)),
            ]
        );

        // login user
        Auth::login($user);

        return redirect()->route('user.dashboard');
    }

    // ฟังก์ชัน decode JWT (id_token)
    private function decodeIdToken($idToken)
    {
        $parts = explode('.', $idToken);
        return json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    }
}
