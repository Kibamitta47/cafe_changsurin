<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LineBotController extends Controller
{
    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::info("Raw Webhook: " . json_encode($data, JSON_UNESCAPED_UNICODE));

        $events = $data['events'] ?? [];

        foreach ($events as $event) {
            if (!isset($event['replyToken'])) {
                continue;
            }

            $replyToken = $event['replyToken'];

            // ✅ ถ้าเป็นข้อความ
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $userText = trim($event['message']['text']);

                if ($userText === 'ค้นหาคาเฟ่ใกล้ฉัน') {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "กรุณาส่งพิกัดของคุณเพื่อค้นหาคาเฟ่ใกล้คุณ 🐘☕",
                        "quickReply" => [
                            "items" => [[
                                "type" => "action",
                                "action" => [
                                    "type" => "location",
                                    "label" => "📍 แชร์ตำแหน่งของฉัน"
                                ]
                            ]]
                        ]
                    ]);
                }
            }

            // ✅ ถ้าเป็น location
            if ($event['type'] === 'message' && $event['message']['type'] === 'location') {
                $lat = $event['message']['latitude'];
                $lng = $event['message']['longitude'];

                Log::info("User Location: lat={$lat}, lng={$lng}");

                // ✅ Query หาคาเฟ่
                $cafes = DB::select("
                    SELECT cafes.cafe_id, cafes.cafe_name, cafes.address, cafes.lat, cafes.lng, cafes.phone,
                           (6371 * acos(
                               cos(radians(?)) * cos(radians(cafes.lat)) *
                               cos(radians(cafes.lng) - radians(?)) +
                               sin(radians(?)) * sin(radians(cafes.lat))
                           )) AS distance
                    FROM cafes
                    HAVING distance < 5
                    ORDER BY distance ASC
                    LIMIT 5
                ", [$lat, $lng, $lat]);

                Log::info("Nearby Cafes: " . json_encode($cafes, JSON_UNESCAPED_UNICODE));

                if (empty($cafes)) {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "ไม่พบคาเฟ่ในรัศมี 5 กม. จากคุณ 😢"
                    ]);
                    return;
                }

                // ✅ แปลงผลลัพธ์เป็น text ธรรมดา
                $msg = "เจอคาเฟ่ " . count($cafes) . " ร้านใกล้คุณ 📍\n\n";
                foreach ($cafes as $cafe) {
                    $msg .= "☕ " . $cafe->cafe_name . "\n";
                    $msg .= "ที่อยู่: " . ($cafe->address ?? "-") . "\n";
                    $msg .= "☎ " . ($cafe->phone ?? "ไม่มีข้อมูล") . "\n";
                    $msg .= "ห่าง: " . round($cafe->distance, 2) . " กม.\n";
                    $msg .= "---------------------\n";
                }

                $this->replyMessage($replyToken, [
                    "type" => "text",
                    "text" => $msg
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function replyMessage($replyToken, $message)
    {
        $accessToken = config('services.line.channel_access_token');

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => [$message],
        ]);
    }
}
