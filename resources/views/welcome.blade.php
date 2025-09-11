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

            // ---------- ข้อความ ----------
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $text = trim($event['message']['text']);

                // สลับ Rich Menu
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

                // ===== ปุ่ม FAQ → ตอบตาม DB จริง =====
                $map = [
                    'FreeWiFi'               => 'wifi',
                    'เปิดอยู่ตอนนี้'        => 'open_now',
                    'คาเฟ่ราคาย่อมเยา'      => 'cheap',
                    'เปิดใหม่'               => 'new',
                    'ที่จอดรถ'               => 'parking',
                    'มีห้องประชุมทำงานได้'   => 'meeting',
                ];

                if (isset($map[$text])) {
                    $cafes = $this->findCafesByFilter($map[$text]);
                    Log::info("FAQ {$map[$text]} found: ".count($cafes));
                    if (empty($cafes)) {
                        $this->replyText($replyToken, "ยังไม่พบร้านตามเงื่อนไข “{$text}” ในระบบครับ");
                        continue;
                    }
                    $bubbles = [];
                    foreach ($cafes as $c) {
                        $note = match ($map[$text]) {
                            'wifi'     => '📶 Free Wi-Fi',
                            'open_now' => '🟢 เปิดอยู่ตอนนี้',
                            'cheap'    => '💸 ราคาย่อมเยา',
                            'new'      => '🆕 ร้านเปิดใหม่',
                            'parking'  => '🅿️ มีที่จอดรถ',
                            'meeting'  => '🏢 มีห้องประชุม/ทำงาน',
                        };
                        $bubbles[] = $this->bubbleBasic(
                            $c->cafe_name, $c->address, $note,
                            $c->phone, $c->lat, $c->lng
                        );
                    }
                    $this->replyFlex($replyToken, "ผลลัพธ์: {$text}", [
                        "type" => "carousel", "contents" => $bubbles
                    ]);
                    continue;
                }

                // ค้นหาคาเฟ่ใกล้ฉัน
                if ($text === 'ค้นหาคาเฟ่ใกล้ฉัน') {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "กรุณาส่งพิกัดของคุณเพื่อค้นหาคาเฟ่ใกล้คุณ 🐘☕",
                        "quickReply" => ["items" => [[
                            "type" => "action",
                            "action" => ["type" => "location","label" => "📍 แชร์ตำแหน่งของฉัน"]
                        ]]]
                    ]);
                    continue;
                }

                // แนะนำคาเฟ่ในเมืองสุรินทร์
                if (in_array($text, ['แนะนำคาเฟ่เมืองสุรินทร์','คาเฟ่เมืองสุรินทร์','recommend'])) {
                    $cafes = DB::select("
                        SELECT cafe_id,cafe_name,address,lat,lng,phone
                        FROM cafes
                        WHERE LOWER(COALESCE(status,''))='approved' AND address LIKE '%สุรินทร์%'
                        ORDER BY created_at DESC
                        LIMIT 9
                    ");
                    if (empty($cafes)) {
                        $this->replyText($replyToken, "ยังไม่มีข้อมูลคาเฟ่ในเมืองสุรินทร์");
                        continue;
                    }
                    $bubbles = [];
                    foreach ($cafes as $c) {
                        $bubbles[] = $this->bubbleBasic(
                            $c->cafe_name, $c->address, "⭐ แนะนำในเมืองสุรินทร์",
                            $c->phone, $c->lat, $c->lng
                        );
                    }
                    $this->replyFlex($replyToken, "แนะนำคาเฟ่เมืองสุรินทร์", [
                        "type" => "carousel","contents" => $bubbles
                    ]);
                    continue;
                }

                // ค่า default
                $this->replyText($replyToken, "พิมพ์ “เมนู” เพื่อเปิดเมนูหลัก หรือ “FAQ” เพื่อดูเมนูคำตอบครับ");
                continue;
            }

            // ---------- Location ----------
            if ($event['type'] === 'message' && $event['message']['type'] === 'location') {
                $lat = $event['message']['latitude'];
                $lng = $event['message']['longitude'];

                $cafes = DB::select("
                    SELECT cafes.cafe_id, cafes.cafe_name, cafes.address, cafes.lat, cafes.lng, cafes.phone,
                           (6371 * acos(
                               cos(radians(?)) * cos(radians(cafes.lat)) *
                               cos(radians(cafes.lng) - radians(?)) +
                               sin(radians(?)) * sin(radians(cafes.lat))
                           )) AS distance
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                    HAVING distance < 5
                    ORDER BY distance ASC
                    LIMIT 5
                ", [$lat, $lng, $lat]);

                if (empty($cafes)) {
                    $this->replyText($replyToken, "ไม่พบคาเฟ่ในรัศมี 5 กม. จากคุณ 😢");
                    continue;
                }

                $bubbles = [];
                foreach ($cafes as $c) {
                    $bubbles[] = $this->bubbleBasic(
                        $c->cafe_name, $c->address, "📍 ห่าง ".round($c->distance,2)." กม.",
                        $c->phone, $c->lat, $c->lng
                    );
                }
                $this->replyFlex($replyToken, "คาเฟ่ใกล้คุณ", [
                    "type" => "carousel","contents" => $bubbles
                ]);
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

    // ---------- FAQ Filters (ปรับตาม DB จริง) ----------
    private function findCafesByFilter(string $type): array
    {
        switch ($type) {
            // Wi-Fi: รองรับ JSON และข้อความปกติ
            case 'wifi':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved' AND (
                        (JSON_VALID(facilities) AND (
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%Wi-Fi%') IS NOT NULL OR
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%WiFi%')  IS NOT NULL OR
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%wifi%')  IS NOT NULL OR
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%ไวไฟ%')  IS NOT NULL
                        )) OR
                        (JSON_VALID(other_services) AND (
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%Wi-Fi%') IS NOT NULL OR
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%WiFi%')  IS NOT NULL OR
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%wifi%')  IS NOT NULL OR
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%ไวไฟ%')  IS NOT NULL
                        )) OR
                        facilities     LIKE '%wifi%' OR facilities     LIKE '%Wi-Fi%' OR facilities     LIKE '%ไวไฟ%' OR
                        other_services LIKE '%wifi%' OR other_services LIKE '%Wi-Fi%' OR other_services LIKE '%ไวไฟ%'
                    )
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            // เปิดอยู่ตอนนี้ (เวลาไทย + รองรับข้ามเที่ยงคืน)
            case 'open_now':
                $now = Carbon::now('Asia/Bangkok')->format('H:i:s');
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND open_time  IS NOT NULL
                      AND close_time IS NOT NULL
                      AND (
                            (close_time >= open_time AND ? BETWEEN open_time AND close_time)
                            OR
                            (close_time <  open_time AND (? >= open_time OR ? <= close_time))
                          )
                    ORDER BY cafe_id DESC
                    LIMIT 20
                ", [$now, $now, $now]);

            // ราคาย่อมเยา (เรียงถูก→แพง)
            case 'cheap':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone,price_range
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND price_range IS NOT NULL AND price_range <> ''
                    ORDER BY
                        CASE
                            WHEN price_range LIKE '%ต่ำกว่า 100%'   THEN 1
                            WHEN price_range LIKE '%101 - 250%'      THEN 2
                            WHEN price_range LIKE '%251 - 500%'      THEN 3
                            WHEN price_range LIKE '%501 - 1,000%'    THEN 4
                            WHEN price_range LIKE '%มากกว่า 1,000%'  THEN 5
                            ELSE 99
                        END ASC,
                        updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            // เปิดใหม่ (ยึดธง is_new_opening เป็นหลัก ให้ผลตรงหน้าเว็บ)
            case 'new':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND CAST(COALESCE(is_new_opening,0) AS UNSIGNED) = 1
                    ORDER BY updated_at DESC, created_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            // ที่จอดรถ (CAST เป็น UNSIGNED กันค่าที่เป็น '1'/'0'/TRUE/FALSE)
            case 'parking':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND CAST(COALESCE(parking,0) AS UNSIGNED) = 1
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            // ห้องประชุม/ทำงาน (JSON + ข้อความ)
            case 'meeting':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved' AND (
                        (JSON_VALID(facilities) AND (
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%ห้องประชุม%') IS NOT NULL OR
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%meeting%')  IS NOT NULL OR
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%ประชุม%')   IS NOT NULL OR
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%co-work%')  IS NOT NULL OR
                            JSON_SEARCH(CAST(facilities AS JSON),'one','%cowork%')   IS NOT NULL
                        )) OR
                        (JSON_VALID(other_services) AND (
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%ห้องประชุม%') IS NOT NULL OR
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%meeting%')  IS NOT NULL OR
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%ประชุม%')   IS NOT NULL OR
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%co-work%')  IS NOT NULL OR
                            JSON_SEARCH(CAST(other_services AS JSON),'one','%cowork%')   IS NOT NULL
                        )) OR
                        facilities     LIKE '%ห้องประชุม%' OR facilities     LIKE '%meeting%' OR facilities     LIKE '%ประชุม%' OR
                        other_services LIKE '%ห้องประชุม%' OR other_services LIKE '%meeting%' OR other_services LIKE '%ประชุม%'
                    )
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            default:
                return [];
        }
    }

    // ---------- Flex Components ----------
    private function bubbleBasic($name, $addr, $sub, $phone, $lat, $lng): array
    {
        return [
            "type" => "bubble",
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "spacing" => "sm",
                "contents" => [
                    ["type" => "text","text" => (string)$name,"weight" => "bold","size" => "lg","wrap" => true],
                    ["type" => "text","text" => (string)($addr ?? "-"),"size" => "sm","color" => "#666666","wrap" => true],
                    ["type" => "text","text" => (string)$sub,"size" => "xs","color" => "#999999","wrap" => true],
                    ["type" => "text","text" => "☎ ".($phone ?: "ไม่มีข้อมูล"),"size" => "xs","color" => "#999999","wrap" => true]
                ]
            ],
            "footer" => [
                "type" => "box",
                "layout" => "vertical",
                "spacing" => "md",
                "contents" => [[
                    "type" => "button","style" => "primary",
                    "action" => [
                        "type" => "uri","label" => "เปิดแผนที่",
                        "uri"  => "https://maps.google.com/?q={$lat},{$lng}"
                    ]
                ]],
                "flex" => 0
            ]
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
