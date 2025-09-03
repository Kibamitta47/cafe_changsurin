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
                continue; // กัน error กรณี event ไม่มี replyToken เช่น unfollow
            }

            $replyToken = $event['replyToken'];

            // 🟢 ถ้าผู้ใช้ส่งข้อความ
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $userText = trim($event['message']['text']);
                Log::info("User Text: " . $userText);

                if ($userText === 'ค้นหาคาเฟ่ใกล้ฉัน') {
                    Log::info("Matched keyword: ค้นหาคาเฟ่ใกล้ฉัน → ส่ง Quick Reply");

                    $this->replyMessage($replyToken, [
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
                    ]);
                }
            }

            // 🟢 ถ้าผู้ใช้แชร์ Location
            if ($event['type'] === 'message' && $event['message']['type'] === 'location') {
                $lat = $event['message']['latitude'];
                $lng = $event['message']['longitude'];

                Log::info("User Location Received: lat={$lat}, lng={$lng}");

                // Query หา 5 คาเฟ่ใกล้สุดในรัศมี 5 กม.
                $cafes = DB::select("
                    SELECT cafe_id, cafe_name, address, lat, lng, phone,
                    ( 6371 * acos( cos( radians(?) ) * cos( radians(lat) )
                    * cos( radians(lng) - radians(?) )
                    + sin( radians(?) ) * sin( radians(lat) ) ) ) AS distance
                    FROM cafes
                    HAVING distance < 5
                    ORDER BY distance ASC
                    LIMIT 5
                ", [$lat, $lng, $lat]);

                Log::info("Nearby Cafes Query Result: ", $cafes);

                if (empty($cafes)) {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "ไม่พบคาเฟ่ในรัศมี 5 กม. จากคุณ 😢"
                    ]);
                    return;
                }

                // 🧩 สร้าง Flex Message Carousel พร้อมรูป
                $bubbles = [];
                foreach ($cafes as $cafe) {
                    // ดึงรูปจากตาราง cafe_images (เอารูปแรก)
                    $image = DB::table('cafe_images')
                        ->where('cafe_id', $cafe->cafe_id)
                        ->value('image_path');

                    $bubbles[] = [
                        "type" => "bubble",
                        "hero" => [
                            "type" => "image",
                            "url" => $image ? url($image) : url('/images/logo.png'),
                            "size" => "full",
                            "aspectRatio" => "20:13",
                            "aspectMode" => "cover"
                        ],
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

                Log::info("Flex Message Built: ", $flexMessage);

                $this->replyMessage($replyToken, $flexMessage);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ✅ ฟังก์ชันส่งข้อความกลับไปที่ LINE
    private function replyMessage($replyToken, $message)
    {
        $accessToken = config('services.line.channel_access_token'); // ✅ ดึงจาก config/services.php

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => [$message], // ✅ ต้องเป็น array
        ]);
    }
}
