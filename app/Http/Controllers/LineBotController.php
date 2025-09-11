<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

                // สลับ Rich Menu (รูปอยู่ใน LINE)
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

                // ===== ปุ่ม FAQ ให้ขึ้นคำตอบ =====
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
                            $c->cafe_name,
                            $c->address,
                            $note,
                            $c->phone,
                            $c->lat,
                            $c->lng
                        );
                    }
                    $this->replyFlex($replyToken, "ผลลัพธ์: {$text}", [
                        "type" => "carousel", "contents" => $bubbles
                    ]);
                    continue;
                }

                // ค้นหาคาเฟ่ใกล้ฉัน (คงไว้)
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
                        WHERE address LIKE '%สุรินทร์%'
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

    // ---------- FAQ Filters (อิงโครงสร้างคอลัมน์จริงของคุณ) ----------
    private function findCafesByFilter(string $type): array
    {
        return match ($type) {
            // Free WiFi: ค้นจากช่อง facilities/other_services ที่มีคำว่า wifi/wi-fi
            'wifi' => DB::select("
                SELECT cafe_id,cafe_name,address,lat,lng,phone
                FROM cafes
                WHERE (facilities      LIKE '%wifi%' OR facilities      LIKE '%wi-fi%' OR facilities      LIKE '%ไวไฟ%')
                   OR (other_services  LIKE '%wifi%' OR other_services  LIKE '%wi-fi%' OR other_services  LIKE '%ไวไฟ%')
                ORDER BY updated_at DESC, cafe_id DESC
                LIMIT 10
            "),

            // เปิดอยู่ตอนนี้: รองรับกรณีข้ามเที่ยงคืน (close_time < open_time)
            'open_now' => DB::select("
                SELECT cafe_id,cafe_name,address,lat,lng,phone
                FROM cafes
                WHERE open_time IS NOT NULL AND close_time IS NOT NULL AND
                (
                    (close_time >= open_time AND TIME(NOW()) BETWEEN open_time AND close_time)
                    OR
                    (close_time < open_time AND (TIME(NOW()) >= open_time OR TIME(NOW()) <= close_time))
                )
                ORDER BY cafe_id DESC
                LIMIT 10
            "),

            // ราคาย่อมเยา: ใช้ price_range เป็นข้อความ (เช่น 'ย่อมเยา','ถูก','ประหยัด', 'cheap','low')
            'cheap' => DB::select("
                SELECT cafe_id,cafe_name,address,lat,lng,phone
                FROM cafes
                WHERE price_range REGEXP 'ย่อมเยา|ถูก|ประหยัด|cheap|low'
                ORDER BY updated_at DESC, cafe_id DESC
                LIMIT 10
            "),

            // เปิดใหม่: ใช้ is_new_opening = 1 หรือเพิ่งสร้างภายใน 60 วัน
            'new' => DB::select("
                SELECT cafe_id,cafe_name,address,lat,lng,phone
                FROM cafes
                WHERE is_new_opening = 1
                   OR (created_at IS NOT NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY))
                ORDER BY created_at DESC, cafe_id DESC
                LIMIT 10
            "),

            // มีที่จอดรถ: คอลัมน์ parking (tinyint)
            'parking' => DB::select("
                SELECT cafe_id,cafe_name,address,lat,lng,phone
                FROM cafes
                WHERE parking = 1
                ORDER BY updated_at DESC, cafe_id DESC
                LIMIT 10
            "),

            // ห้องประชุม/ทำงาน: ค้นคำจาก facilities หรือ other_services
            'meeting' => DB::select("
                SELECT cafe_id,cafe_name,address,lat,lng,phone
                FROM cafes
                WHERE (facilities     LIKE '%ห้องประชุม%' OR facilities     LIKE '%meeting%' OR facilities     LIKE '%co-work%' OR facilities     LIKE '%cowork%')
                   OR (other_services LIKE '%ห้องประชุม%' OR other_services LIKE '%meeting%' OR other_services LIKE '%co-work%' OR other_services LIKE '%cowork%')
                ORDER BY updated_at DESC, cafe_id DESC
                LIMIT 10
            "),
            default => [],
        };
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
