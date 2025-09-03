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

        // 🟢 Debug payload ที่ LINE ส่งมา
        Log::info("Raw Webhook: " . json_encode($data, JSON_UNESCAPED_UNICODE));

        $events = $data['events'] ?? [];

        foreach ($events as $event) {
            if (!isset($event['replyToken'])) {
                continue; // กัน error event ไม่มี replyToken เช่น unfollow
            }

            $replyToken = $event['replyToken'];

            // ✅ ถ้าเป็นข้อความ
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $userText = trim($event['message']['text']);
                Log::info("User Text: " . $userText);

                if ($userText === 'ค้นหาคาเฟ่ใกล้ฉัน') {
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

            // ✅ ถ้าเป็นการแชร์ location
            if ($event['type'] === 'message' && $event['message']['type'] === 'location') {
                $lat = $event['message']['latitude'];
                $lng = $event['message']['longitude'];

                Log::info("User Location Received: lat={$lat}, lng={$lng}");

                // 🔍 Query คาเฟ่ในรัศมี 5 กม.
                $cafes = DB::table('cafes')
                    ->leftJoin('cafe_images', 'cafes.cafe_id', '=', 'cafe_images.cafe_id')
                    ->select(
                        'cafes.cafe_id',
                        'cafes.cafe_name',
                        'cafes.address',
                        'cafes.lat',
                        'cafes.lng',
                        'cafes.phone',
                        'cafe_images.image_path',
                        DB::raw('(6371 * acos(cos(radians(?)) * cos(radians(cafes.lat)) 
                            * cos(radians(cafes.lng) - radians(?)) 
                            + sin(radians(?)) * sin(radians(cafes.lat)))) AS distance')
                    )
                    ->setBindings([$lat, $lng, $lat])
                    ->having('distance', '<', 5)
                    ->orderBy('distance', 'asc')
                    ->limit(5)
                    ->get();

                Log::info("Nearby Cafes Query Result: ", $cafes->toArray());

                if ($cafes->isEmpty()) {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "ไม่พบคาเฟ่ในรัศมี 5 กม. จากคุณ 😢"
                    ]);
                    return;
                }

                // 🧩 Flex Message
                $bubbles = [];
                foreach ($cafes as $cafe) {
                    $bubbles[] = [
                        "type" => "bubble",
                        "hero" => [
                            "type" => "image",
                            "url" => $cafe->image_path 
                                ? url($cafe->image_path) 
                                : url('/images/logo.png'),
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
