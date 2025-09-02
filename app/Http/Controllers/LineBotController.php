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

            // ถ้าผู้ใช้กดปุ่ม Rich Menu → มันจะส่งข้อความ "ค้นหาคาเฟ่ใกล้ฉัน"
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $userText = trim($event['message']['text']);

                if ($userText === 'ค้นหาคาเฟ่ใกล้ฉัน') {
                    $this->sendLocationQuickReply($replyToken);
                }
            }

            // ถ้าผู้ใช้แชร์ Location จริง ๆ
            if ($event['type'] === 'message' && $event['message']['type'] === 'location') {
                $lat = $event['message']['latitude'];
                $lng = $event['message']['longitude'];

                $this->replyMessage($replyToken, [
                    "type" => "text",
                    "text" => "ได้รับพิกัดแล้ว 📍\nLat: {$lat}, Lng: {$lng}"
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ส่ง Quick Reply ขอแชร์ Location
    private function sendLocationQuickReply($replyToken)
    {
        $message = [
            "type" => "text",
            "text" => "📍 กรุณาส่งพิกัดของคุณเพื่อค้นหาคาเฟ่ใกล้เคียง",
            "quickReply" => [
                "items" => [
                    [
                        "type" => "action",
                        "action" => [
                            "type" => "location",
                            "label" => "แชร์ตำแหน่งของฉัน"
                        ]
                    ]
                ]
            ]
        ];
        $this->replyMessage($replyToken, $message);
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
