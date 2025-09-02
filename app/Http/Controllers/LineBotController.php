<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineBotController extends Controller
{
    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::info($data);

        $events = $data['events'] ?? [];

        foreach ($events as $event) {
            $replyToken = $event['replyToken'] ?? null;
            if (!$replyToken) continue;

            // ถ้าผู้ใช้กดปุ่ม Rich Menu → จะส่งข้อความ "ค้นหาคาเฟ่ใกล้ฉัน"
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $userText = trim($event['message']['text']);

                if ($userText === 'ค้นหาคาเฟ่ใกล้ฉัน') {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "📍 ระบบจะหาคาเฟ่ใกล้คุณให้นะครับ (ฟีเจอร์ค้นหาจากพิกัดจะเพิ่มทีหลัง)"
                    ]);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ฟังก์ชันส่งข้อความกลับ
    private function replyMessage($replyToken, $message)
    {
        $accessToken = env('LINE_CHANNEL_ACCESS_TOKEN');

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => [$message],
        ]);
    }
}
