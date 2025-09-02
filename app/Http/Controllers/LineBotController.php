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
        Log::info($data);

        $events = $data['events'] ?? [];

        foreach ($events as $event) {
            $replyToken = $event['replyToken'] ?? null;
            if (!$replyToken) continue;

            // 🟢 กรณีส่งข้อความปกติ
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $userText = trim($event['message']['text']);

                if (mb_strpos($userText, 'ค้นหาคาเฟ่ใกล้ฉัน') !== false) {
                    $this->sendLocationQuickReply($replyToken);
                }
            }

            // 🟢 กรณีกดปุ่ม Rich Menu (Postback)
            if ($event['type'] === 'postback') {
                $data = $event['postback']['data'] ?? '';

                if ($data === 'search_nearby_cafe') {
                    $this->sendLocationQuickReply($replyToken);
                }
            }

            // 🟢 กรณีผู้ใช้แชร์ Location
            if ($event['type'] === 'message' && $event['message']['type'] === 'location') {
                $lat = $event['message']['latitude'];
                $lng = $event['message']['longitude'];

                // ค้นหาคาเฟ่ใน DB (รัศมี 30 กม.)
                $cafes = DB::select("
                    SELECT cafe_id, cafe_name, address, lat, lng, phone,
                    ( 6371 * acos( cos( radians(?) ) * cos( radians(lat) )
                    * cos( radians(lng) - radians(?) )
                    + sin( radians(?) ) * sin( radians(lat) ) ) ) AS distance
                    FROM cafes
                    HAVING distance < 30
                    ORDER BY distance ASC
                    LIMIT 5
                ", [$lat, $lng, $lat]);

                if (empty($cafes)) {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "ไม่พบคาเฟ่ในรัศมี 30 กม. จากคุณ 😢"
                    ]);
                    return;
                }

                // Flex Message แสดงคาเฟ่
                $bubbles = [];
                foreach ($cafes as $cafe) {
                    $bubbles[] = [
                        "type" => "bubble",
                        "body" => [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => $cafe->cafe_name,
                                    "weight" => "bold",
                                    "size" => "lg"
                                ],
                                [
                                    "type" => "text",
                                    "text" => $cafe->address,
                                    "wrap" => true,
                                    "size" => "sm",
                                    "color" => "#666666"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "📍 ห่าง " . round($cafe->distance, 2) . " กม.",
                                    "size" => "sm",
                                    "color" => "#999999"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "☎ " . ($cafe->phone ?? "ไม่มีข้อมูล"),
                                    "size" => "sm",
                                    "color" => "#999999"
                                ]
                            ]
                        ],
                        "footer" => [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "button",
                                    "style" => "link",
                                    "action" => [
                                        "type" => "uri",
                                        "label" => "เปิดแผนที่",
                                        "uri" => "https://maps.google.com/?q={$cafe->lat},{$cafe->lng}"
                                    ]
                                ]
                            ]
                        ]
                    ];
                }

                $flexMessage = [
                    "type" => "flex",
                    "altText" => "คาเฟ่ใกล้คุณ",
                    "contents" => [
                        "type" => "carousel",
                        "contents" => $bubbles
                    ]
                ];

                $this->replyMessage($replyToken, $flexMessage);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ✅ ส่ง Quick Reply ให้แชร์ Location
    private function sendLocationQuickReply($replyToken)
    {
        $quickReplyMessage = [
            "type" => "text",
            "text" => "กรุณาส่งพิกัดของคุณเพื่อค้นหาคาเฟ่ใกล้คุณ 🐘☕",
            "quickReply" => [
                "items" => [
                    [
                        "type" => "action",
                        "action" => [
                            "type" => "location",
                            "label" => "📍 แชร์ตำแหน่งของฉัน"
                        ]
                    ]
                ]
            ]
        ];
        $this->replyMessage($replyToken, $quickReplyMessage);
    }

    // ✅ ฟังก์ชันตอบกลับ
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
