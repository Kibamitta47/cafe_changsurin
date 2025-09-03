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
        Log::info("Webhook Data: ", $data);

        $events = $data['events'] ?? [];

        foreach ($events as $event) {
            if (!isset($event['replyToken'])) {
                continue;
            }

            $replyToken = $event['replyToken'];

            // 🟢 ถ้าผู้ใช้ส่งข้อความ
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

            // 🟢 ถ้าผู้ใช้แชร์ Location
            if ($event['type'] === 'message' && $event['message']['type'] === 'location') {
                $lat = $event['message']['latitude'];
                $lng = $event['message']['longitude'];

                $cafes = DB::select("
                    SELECT cafe_id, cafe_name, address, lat, lng, phone, images,
                    ( 6371 * acos( cos( radians(?) ) * cos( radians(lat) )
                    * cos( radians(lng) - radians(?) )
                    + sin( radians(?) ) * sin( radians(lat) ) ) ) AS distance
                    FROM cafes
                    HAVING distance < 5
                    ORDER BY distance ASC
                    LIMIT 5
                ", [$lat, $lng, $lat]);

                if (empty($cafes)) {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "ไม่พบคาเฟ่ในรัศมี 5 กม. จากคุณ 😢"
                    ]);
                    return;
                }

                // 🧩 สร้าง Flex Message Carousel
                $bubbles = [];
                foreach ($cafes as $cafe) {
                    // ✅ ดึงรูปภาพ (array หรือ string)
                    $images = json_decode($cafe->images ?? '[]', true);
                    $firstImage = null;

                    if (!empty($images)) {
                        $firstImage = $images[0];

                        // ถ้าเป็นชื่อไฟล์ → เติม URL หน้าเว็บ
                        if (!str_starts_with($firstImage, 'http')) {
                            $firstImage = url('images/' . $firstImage);
                        }
                    }

                    $bubble = [
                        "type" => "bubble",
                        "hero" => $firstImage ? [
                            "type" => "image",
                            "url" => $firstImage,
                            "size" => "full",
                            "aspectRatio" => "20:13",
                            "aspectMode" => "cover"
                        ] : null,
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
                            "contents" => [[
                                "type" => "button",
                                "style" => "link",
                                "action" => [
                                    "type" => "uri",
                                    "label" => "เปิดแผนที่",
                                    "uri" => "https://maps.google.com/?q={$cafe->lat},{$cafe->lng}"
                                ]
                            ]]
                        ]
                    ];

                    // เอา null ออกถ้าไม่มีรูป
                    if ($bubble['hero'] === null) {
                        unset($bubble['hero']);
                    }

                    $bubbles[] = $bubble;
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
