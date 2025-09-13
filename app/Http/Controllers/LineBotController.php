<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LineBotController extends Controller
{
    private string $token;
    private string $secret;
    private ?string $richMain;
    private ?string $richFaq;

    public function __construct()
    {
        $this->token    = (string) config('services.line.channel_access_token');
        $this->secret   = (string) config('services.line.channel_secret');
        $this->richMain = config('services.line.richmenu_main_id');
        $this->richFaq  = config('services.line.richmenu_faq_id');
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::info("Raw Webhook: " . json_encode($data, JSON_UNESCAPED_UNICODE));

        $events = $data['events'] ?? [];
        foreach ($events as $event) {
            if (!isset($event['replyToken'])) continue;

            $replyToken = $event['replyToken'];
            $userId     = $event['source']['userId'] ?? null;

            // ---------- TEXT ----------
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $text = trim($event['message']['text']);

                // Rich Menu
                if (in_array($text, ['เมนู','menu','เริ่มต้น'])) {
                    $this->setUserRichMenu($userId, $this->richMain);
                    $this->replyText($replyToken, "เปิดเมนูหลักแล้วครับ 😊");
                    continue;
                }
                if (in_array($text, ['FAQ','คำถามที่พบบ่อย','เมนูคำตอบ'])) {
                    $this->setUserRichMenu($userId, $this->richFaq);
                    $this->replyText($replyToken, "เมนู FAQ พร้อมใช้งานครับ ❓");
                    continue;
                }

                // FAQ Map
                $map = [
                    'FreeWiFi'               => 'wifi',
                    'เปิดอยู่ตอนนี้'        => 'open_now',
                    'เปิดอยู่ตอนนี่'        => 'open_now',
                    'คาเฟ่ราคาย่อมเยา'      => 'cheap',
                    'คาเฟ่ราคาย่อมเยส'      => 'cheap',
                    'เปิดใหม่'               => 'new',
                    'ที่จอดรถ'               => 'parking',
                    'มีห้องประชุมทำงานได้'   => 'meeting',
                ];

                if (isset($map[$text])) {
                    $cafes = $this->findCafesByFilter($map[$text]);
                    $bubbles = [];

                    if (!empty($cafes)) {
                        $cafes = array_slice($cafes, 0, 9);
                        foreach ($cafes as $c) {
                            $note = match ($map[$text]) {
                                'wifi'     => '📶 Free Wi-Fi',
                                'open_now' => '🟢 เปิดอยู่ตอนนี้',
                                'cheap'    => '💸 ราคาย่อมเยา',
                                'new'      => '🆕 ร้านเปิดใหม่',
                                'parking'  => '🅿️ มีที่จอดรถ',
                                'meeting'  => '🏢 มีห้องประชุม/ทำงาน',
                            };

                            $mapUrl = ($c->lat !== null && $c->lng !== null)
                                ? "https://maps.google.com/?q={$c->lat},{$c->lng}"
                                : "https://www.google.com/maps/search/".urlencode(($c->cafe_name ?? '').' '.($c->address ?? ''));

                            $bubbles[] = $this->bubbleBasic(
                                $c->cafe_name ?? '-', $c->address ?? '-', $note,
                                $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null, $mapUrl
                            );
                        }
                    } else {
                        $bubbles[] = $this->bubbleInfo(
                            "ยังไม่พบร้านตามเงื่อนไข",
                            "ลองค้นหาเพิ่มเติมบนเว็บไซต์",
                            "https://nongchangsaren.com/"
                        );
                    }

                    $bubbles[] = $this->bubbleMore(
                        'ดูเพิ่มเติมบนเว็บไซต์',
                        'เปิดเว็บ น้องช้างสะเร็น',
                        'https://nongchangsaren.com/'
                    );

                    $this->replyFlex($replyToken, "ผลลัพธ์: {$text}", [
                        "type" => "carousel", "contents" => $bubbles
                    ]);
                    continue;
                }

                // Default
                $this->replyText($replyToken, "พิมพ์ “เมนู” เพื่อเปิดเมนูหลัก หรือ “FAQ” เพื่อดูเมนูคำตอบครับ");
                continue;
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ---------- Rich Menu ----------
    private function setUserRichMenu(?string $userId, ?string $richMenuId): void
    {
        if (!$userId || !$richMenuId) return;
        Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->post("https://api.line.me/v2/bot/user/{$userId}/richmenu/{$richMenuId}");
    }

    // ---------- FAQ Filters ----------
    private function findCafesByFilter(string $type): array
    {
        switch ($type) {
            case 'wifi':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND (facilities LIKE '%wifi%' COLLATE utf8mb4_general_ci
                           OR other_services LIKE '%wifi%' COLLATE utf8mb4_general_ci
                           OR facilities LIKE '%ไวไฟ%' COLLATE utf8mb4_general_ci
                           OR other_services LIKE '%ไวไฟ%' COLLATE utf8mb4_general_ci)
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            case 'open_now':
                $now = Carbon::now('Asia/Bangkok')->format('H:i:s');
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND open_time IS NOT NULL
                      AND close_time IS NOT NULL
                      AND (
                            (close_time >= open_time AND ? BETWEEN open_time AND close_time)
                            OR
                            (close_time < open_time AND (? >= open_time OR ? <= close_time))
                          )
                    ORDER BY cafe_id DESC
                    LIMIT 20
                ", [$now, $now, $now]);

            case 'cheap':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            case 'new':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND CAST(COALESCE(is_new_opening,0) AS UNSIGNED) = 1
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            case 'parking':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND (facilities LIKE '%ที่จอดรถ%' COLLATE utf8mb4_general_ci
                           OR other_services LIKE '%ที่จอดรถ%' COLLATE utf8mb4_general_ci)
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            case 'meeting':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND (facilities LIKE '%ประชุม%' COLLATE utf8mb4_general_ci
                           OR other_services LIKE '%ประชุม%' COLLATE utf8mb4_general_ci
                           OR facilities LIKE '%meeting%' COLLATE utf8mb4_general_ci
                           OR other_services LIKE '%meeting%' COLLATE utf8mb4_general_ci)
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            default:
                return [];
        }
    }

    // ---------- Flex Components (ตกแต่ง) ----------
    private function bubbleBasic($name, $addr, $sub, $phone, $lat, $lng, $mapUrl = null): array
    {
        $mapUrl = $mapUrl ?: "https://maps.google.com/?q={$lat},{$lng}";
        return [
            "type" => "bubble",
            "size" => "mega",
            "action" => ["type" => "uri","uri" => $mapUrl],
            "hero" => [
                "type" => "image",
                "url" => "https://i.imgur.com/5B2xqV3.png",
                "size" => "full",
                "aspectRatio" => "20:9",
                "aspectMode" => "cover"
            ],
            "body" => [
                "type" => "box","layout" => "vertical","paddingAll" => "16px","spacing" => "12px",
                "contents" => [
                    [
                        "type" => "box","layout" => "baseline","contents" => [[
                            "type" => "text","text" => $sub,"size" => "12px","color" => "#1D4ED8","weight" => "bold","wrap" => true
                        ]],"backgroundColor" => "#E0EAFF","cornerRadius" => "12px","paddingAll" => "6px"
                    ],
                    ["type" => "text","text" => (string)$name,"size" => "lg","weight" => "bold","wrap" => true,"color" => "#1f2937"],
                    ["type" => "box","layout" => "baseline","spacing" => "6px","contents" => [
                        ["type" => "icon","url" => "https://i.imgur.com/1QmNq4r.png","size" => "sm"],
                        ["type" => "text","text" => (string)($addr ?: "-"),"wrap" => true,"size" => "sm","color" => "#6b7280"]
                    ]],
                    ["type" => "box","layout" => "baseline","spacing" => "6px","contents" => [
                        ["type" => "icon","url" => "https://i.imgur.com/1gI3zJv.png","size" => "sm"],
                        ["type" => "text","text" => "โทร: ".($phone ?: "ไม่มีข้อมูล"),"size" => "sm","color" => "#6b7280","wrap" => true]
                    ]]
                ]
            ],
            "footer" => [
                "type" => "box","layout" => "vertical","spacing" => "12px","paddingAll" => "14px","contents" => [[
                    "type" => "button","style" => "primary","height" => "sm","color" => "#1D4ED8",
                    "action" => ["type" => "uri","label" => "เปิดแผนที่","uri" => $mapUrl]
                ]],"backgroundColor" => "#F3F4F6"
            ],
            "styles" => [
                "body" => ["backgroundColor" => "#FFFFFF"],
                "footer" => ["separator" => true,"separatorColor" => "#E5E7EB"]
            ]
        ];
    }

    private function bubbleMore(string $title, string $sub, string $url): array
    {
        return [
            "type" => "bubble","size" => "mega",
            "body" => [
                "type" => "box","layout" => "vertical","paddingAll" => "20px","spacing" => "12px",
                "contents" => [
                    [
                        "type" => "box","layout" => "baseline","contents" => [[
                            "type" => "text","text" => "ดูเพิ่มเติม","weight" => "bold","size" => "md","color" => "#0f172a"
                        ]],"backgroundColor" => "#FEF3C7","cornerRadius" => "12px","paddingAll" => "6px"
                    ],
                    ["type" => "text","text" => $title,"weight" => "bold","size" => "lg","wrap" => true,"color" => "#111827"],
                    ["type" => "text","text" => $sub,"size" => "sm","wrap" => true,"color" => "#6b7280"]
                ]
            ],
            "footer" => [
                "type" => "box","layout" => "vertical","contents" => [[
                    "type" => "button","style" => "link","action" => ["type" => "uri","label" => "ไปที่เว็บไซต์","uri" => $url]
                ]],"backgroundColor" => "#F3F4F6","paddingAll" => "14px"
            ],
            "styles" => ["body" => ["backgroundColor" => "#FFFFFF"],"footer" => ["separator" => true,"separatorColor" => "#E5E7EB"]]
        ];
    }

    private function bubbleInfo(string $title, string $sub, string $url): array
    {
        return [
            "type" => "bubble","size" => "mega",
            "body" => [
                "type" => "box","layout" => "vertical","paddingAll" => "20px","spacing" => "12px",
                "contents" => [
                    [
                        "type" => "box","layout" => "baseline","contents" => [[
                            "type" => "text","text" => "แจ้งเตือน","weight" => "bold","size" => "md","color" => "#7c2d12"
                        ]],"backgroundColor" => "#FEE2E2","cornerRadius" => "12px","paddingAll" => "6px"
                    ],
                    ["type" => "text","text" => $title,"weight" => "bold","size" => "lg","wrap" => true,"color" => "#111827"],
                    ["type" => "text","text" => $sub,"size" => "sm","wrap" => true,"color" => "#6b7280"]
                ]
            ],
            "footer" => [
                "type" => "box","layout" => "vertical","contents" => [[
                    "type" => "button","style" => "primary","color" => "#DC2626",
                    "action" => ["type" => "uri","label" => "ไปที่เว็บไซต์","uri" => $url]
                ]],"backgroundColor" => "#F3F4F6","paddingAll" => "14px"
            ],
            "styles" => ["body" => ["backgroundColor" => "#FFFFFF"],"footer" => ["separator" => true,"separatorColor" => "#E5E7EB"]]
        ];
    }

    // ---------- Reply Helpers ----------
    private function replyText(string $replyToken, string $text): void
    {
        $this->replyMessage($replyToken, ["type" => "text", "text" => $text]);
    }

    private function replyFlex(string $replyToken, string $altText, array $contents): void
    {
        $this->replyMessage($replyToken, ["type" => "flex","altText" => $altText,"contents" => $contents]);
    }

    private function replyMessage(string $replyToken, array $message): void
    {
        Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer '.$this->token,
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => [$message],
        ]);
    }
}
