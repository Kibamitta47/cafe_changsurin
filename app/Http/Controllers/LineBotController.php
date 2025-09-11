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
        $this->token   = (string) config('services.line.channel_access_token');
        $this->secret  = (string) config('services.line.channel_secret');
        $this->richMain = config('services.line.richmenu_main_id'); // อาจเป็น null ได้
        $this->richFaq  = config('services.line.richmenu_faq_id');  // อาจเป็น null ได้
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

                if ($text === 'ค้นหาคาเฟ่ใกล้ฉัน') {
                    $this->replyMessage($replyToken, [
                        "type" => "text",
                        "text" => "กรุณาส่งพิกัดของคุณเพื่อค้นหาคาเฟ่ใกล้คุณ 🐘☕",
                        "quickReply" => [
                            "items" => [[
                                "type" => "action",
                                "action" => ["type" => "location","label" => "📍 แชร์ตำแหน่งของฉัน"]
                            ]]
                        ]
                    ]);
                    continue;
                }

                // ... (ส่วนแนะนำคาเฟ่/คำตอบอื่น ๆ คงเดิม)
                $this->replyText($replyToken, "พิมพ์ “เมนู” เพื่อเปิดเมนูหลัก หรือ “FAQ” เพื่อสลับเมนูคำตอบครับ");
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
