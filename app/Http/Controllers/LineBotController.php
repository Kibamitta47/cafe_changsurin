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
            if (($event['type'] ?? '') === 'message' && ($event['message']['type'] ?? '') === 'text') {
                $text = trim($event['message']['text'] ?? '');

                // Toggle Rich Menu
                if (in_array($text, ['เมนู','menu','เริ่มต้น'], true)) {
                    $this->setUserRichMenu($userId, $this->richMain);
                    $this->replyText($replyToken, "เปิดเมนูหลักแล้วครับ 😊");
                    continue;
                }
                if (in_array($text, ['FAQ','คำถามที่พบบ่อย','เมนูคำตอบ'], true)) {
                    $this->setUserRichMenu($userId, $this->richFaq);
                    $this->replyText($replyToken, "เมนู FAQ พร้อมใช้งานครับ ❓");
                    continue;
                }

                // เมนูแนะนำคาเฟ่เมืองสุรินทร์
                if ($this->isRecommendTrigger($text)) {
                    $menu = $this->menuRecommendCarousel();
                    $this->replyFlex($replyToken, "เมนูแนะนำคาเฟ่เมืองสุรินทร์", $menu);
                    continue;
                }

                // ===== Top10 =====
                if (in_array($text, ['คาเฟ่Top10','Top10','Top 10','top10'], true)) {
                    $cafes = $this->getTop10Cafes();
                    $bubbles = [];
                    if (!empty($cafes)) {
                        foreach ($cafes as $c) {
                            $note = '⭐ ' . number_format((float)($c->avg_rating ?? 0), 1)
                                  . ' (' . (int)($c->review_count ?? 0) . ' รีวิว)';
                            $bubbles[] = $this->bubbleBasic(
                                $c->cafe_name ?? '-',
                                $c->address   ?? '-',
                                $note,
                                $c->phone     ?? '-',
                                $c->lat       ?? null,
                                $c->lng       ?? null
                            );
                        }
                    } else {
                        $bubbles[] = $this->bubbleInfo(
                            "ยังไม่มีข้อมูล Top10",
                            "ลองดูข้อมูลล่าสุดบนเว็บไซต์",
                            "https://nongchangsaren.com/"
                        );
                    }
                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "คาเฟ่ Top 10 เมืองสุรินทร์", [
                        "type" => "carousel", "contents" => $bubbles
                    ]);
                    continue;
                }

                // ===== เปิดใหม่ =====
                if ($text === 'เปิดใหม่') {
                    $cafes = $this->findCafesByFilter('new');
                    $bubbles = [];
                    if (!empty($cafes)) {
                        foreach ($cafes as $c) {
                            $bubbles[] = $this->bubbleBasic(
                                $c->cafe_name ?? '-', $c->address ?? '-', '🆕 เปิดใหม่',
                                $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null
                            );
                        }
                    } else {
                        $bubbles[] = $this->bubbleInfo("ยังไม่มีร้านเปิดใหม่","ลองเช็คข้อมูลบนเว็บไซต์","https://nongchangsaren.com/");
                    }
                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','ไปยังเว็บ','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "คาเฟ่เปิดใหม่", [
                        "type" => "carousel","contents" => $bubbles
                    ]);
                    continue;
                }

                // ===== เลือกสไตล์ =====
                if (str_starts_with($text, 'สไตล์:')) {
                    $style = trim(str_replace('สไตล์:', '', $text));
                    $cafes = DB::select("
                        SELECT cafe_id,cafe_name,address,lat,lng,phone
                        FROM cafes
                        WHERE LOWER(COALESCE(status,''))='approved'
                          AND (cafe_style LIKE ? OR description LIKE ?)
                        ORDER BY updated_at DESC, cafe_id DESC
                        LIMIT 10
                    ", ["%{$style}%", "%{$style}%"]);

                    $bubbles = [];
                    if (!empty($cafes)) {
                        foreach ($cafes as $c) {
                            $bubbles[] = $this->bubbleBasic(
                                $c->cafe_name ?? '-', $c->address ?? '-', "🎨 {$style}",
                                $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null
                            );
                        }
                    } else {
                        $bubbles[] = $this->bubbleInfo(
                            "ยังไม่พบร้านสไตล์ {$style}",
                            "ลองค้นหาเพิ่มเติมบนเว็บไซต์",
                            "https://nongchangsaren.com/"
                        );
                    }
                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','ไปยังเว็บ','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "คาเฟ่สไตล์ {$style}", [
                        "type" => "carousel","contents" => $bubbles
                    ]);
                    continue;
                }

                // default
                $this->replyText($replyToken, "พิมพ์ “แนะนำคาเฟ่เมืองสุรินทร์” เพื่อเปิดเมนูแนะนำ หรือ “เมนู” เพื่อเปิดเมนูหลักครับ");
                continue;
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ---------- Helper ----------
    private function isRecommendTrigger(string $text): bool
    {
        $raw = trim($text);
        return in_array($raw, ['แนะนำคาเฟ่เมืองสุรินทร์','เมนูแนะนำคาเฟ่','คาเฟ่เมืองสุรินทร์','recommend'], true);
    }

    private function setUserRichMenu(?string $userId, ?string $richMenuId): void
    {
        if (!$userId || !$richMenuId) return;
        Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->post("https://api.line.me/v2/bot/user/{$userId}/richmenu/{$richMenuId}");
    }

    // ---------- เมนูแนะนำคาเฟ่ ----------
    private function menuRecommendCarousel(): array
    {
        $bubbles = [];

        // Bubble 1: Top10 / เปิดใหม่
        $bubbles[] = [
            "type" => "bubble",
            "body" => [
                "type" => "box","layout" => "vertical","paddingAll" => "16px","spacing" => "12px",
                "contents" => [
                    ["type"=>"text","text"=>"แนะนำคาเฟ่เมืองสุรินทร์","weight"=>"bold","size"=>"lg"],
                    ["type"=>"separator","margin"=>"12px"]
                ]
            ],
            "footer" => [
                "type"=>"box","layout"=>"vertical","spacing"=>"md","paddingAll"=>"12px",
                "contents"=>[
                    ["type"=>"button","style"=>"primary","action"=>["type"=>"message","label"=>"🔥 Top10","text"=>"คาเฟ่Top10"],"color"=>"#1E88E5"],
                    ["type"=>"button","style"=>"primary","action"=>["type"=>"message","label"=>"✨ เปิดใหม่","text"=>"เปิดใหม่"],"color"=>"#2ECC71"],
                ]
            ]
        ];

        // Bubble 2: ชิปสไตล์
        $styleLabels = ['มินิมอล','โมเดิร์น','โคซี่/อบอุ่น','ยุโรป','ธรรมชาติ/สวน','ลอฟท์','อินดัสเทรียล','วินเทจ','อาร์ต/แกลเลอรี่'];
        $chips = [];
        foreach ($styleLabels as $label) {
            $chips[] = [
                "type"=>"box","layout"=>"vertical","cornerRadius"=>"12px","backgroundColor"=>"#8A63F6","paddingAll"=>"10px",
                "action"=>["type"=>"message","text"=>"สไตล์:".$label],
                "contents"=>[["type"=>"text","text"=>"🎨 ".$label,"size"=>"sm","weight"=>"bold","color"=>"#FFFFFF","align"=>"center"]]
            ];
        }
        $rows = array_chunk($chips, 3);
        $gridRows = [];
        foreach ($rows as $row) {
            $gridRows[] = ["type"=>"box","layout"=>"horizontal","spacing"=>"8px","contents"=>$row];
        }

        $bubbles[] = [
            "type"=>"bubble",
            "body"=>[
                "type"=>"box","layout"=>"vertical","paddingAll"=>"16px","spacing"=>"12px",
                "contents"=>array_merge([["type"=>"text","text"=>"เลือกตามสไตล์ที่ชอบ","weight"=>"bold","size"=>"md"]],$gridRows)
            ]
        ];

        // Bubble 3: ไปเว็บ
        $bubbles[] = $this->bubbleMore('ดูทั้งหมดบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');

        return ["type"=>"carousel","contents"=>$bubbles];
    }

    // ---------- Top10 ----------
    private function getTop10Cafes(): array
    {
        return DB::select("
            SELECT c.cafe_id,c.cafe_name,c.address,c.lat,c.lng,c.phone,
                   COALESCE(AVG(r.rating),0) AS avg_rating,
                   COUNT(r.cafe_id) AS review_count
            FROM cafes c
            LEFT JOIN reviews r ON r.cafe_id=c.cafe_id AND COALESCE(r.status,'approved')='approved'
            WHERE LOWER(COALESCE(c.status,''))='approved'
            GROUP BY c.cafe_id
            ORDER BY avg_rating DESC, review_count DESC
            LIMIT 10
        ");
    }

    // ---------- Filter ----------
    private function findCafesByFilter(string $type): array
    {
        if ($type === 'new') {
            return DB::select("
                SELECT cafe_id,cafe_name,address,lat,lng,phone
                FROM cafes
                WHERE LOWER(COALESCE(status,''))='approved'
                  AND CAST(COALESCE(is_new_opening,0) AS UNSIGNED)=1
                ORDER BY updated_at DESC
                LIMIT 20
            ");
        }
        return [];
    }

    // ---------- Flex Components ----------
    private function bubbleBasic($name,$addr,$sub,$phone,$lat,$lng,$mapUrl=null): array
    {
        $mapUrl = $mapUrl ?: (($lat&&$lng)?"https://maps.google.com/?q={$lat},{$lng}":"https://www.google.com/maps/search/".urlencode($name.' '.$addr));
        $telUri = $this->buildTelUri($phone);
        $buttons = [[
            "type"=>"button","style"=>"primary","height"=>"sm","action"=>["type"=>"uri","label"=>"เปิดแผนที่","uri"=>$mapUrl],"color"=>"#1E88E5"
        ]];
        if ($telUri) {
            $buttons[] = ["type"=>"button","style"=>"secondary","height"=>"sm","action"=>["type"=>"uri","label"=>"โทรเลย","uri"=>$telUri]];
        }
        return [
            "type"=>"bubble",
            "body"=>[
                "type"=>"box","layout"=>"vertical","paddingAll"=>"16px","spacing"=>"12px",
                "contents"=>[
                    ["type"=>"text","text"=>$name,"weight"=>"bold","size"=>"lg","wrap"=>true],
                    ["type"=>"text","text"=>$sub,"size"=>"sm","color"=>"#1E88E5","wrap"=>true],
                    ["type"=>"text","text"=>"📍 ".$addr,"size"=>"sm","wrap"=>true],
                    ["type"=>"text","text"=>"☎ ".($phone ?: "ไม่มีข้อมูล"),"size"=>"sm","wrap"=>true],
                ]
            ],
            "footer"=>["type"=>"box","layout"=>"horizontal","spacing"=>"md","contents"=>$buttons]
        ];
    }

    private function bubbleMore(string $title,string $sub,string $url): array
    {
        return [
            "type"=>"bubble",
            "body"=>["type"=>"box","layout"=>"vertical","paddingAll"=>"16px","contents"=>[
                ["type"=>"text","text"=>$title,"weight"=>"bold","size"=>"lg","wrap"=>true],
                ["type"=>"text","text"=>$sub,"size"=>"sm","color"=>"#666666","wrap"=>true]
            ]],
            "footer"=>["type"=>"box","layout"=>"vertical","contents"=>[[
                "type"=>"button","style"=>"primary","action"=>["type"=>"uri","label"=>"ไปที่เว็บไซต์","uri"=>$url],"color"=>"#1E88E5"
            ]]]
        ];
    }

    private function bubbleInfo(string $title,string $sub,string $url): array
    {
        return [
            "type"=>"bubble",
            "body"=>["type"=>"box","layout"=>"vertical","paddingAll"=>"16px","contents"=>[
                ["type"=>"text","text"=>$title,"weight"=>"bold","size"=>"lg"],
                ["type"=>"text","text"=>$sub,"size"=>"sm","color"=>"#666666","wrap"=>true]
            ]],
            "footer"=>["type"=>"box","layout"=>"vertical","contents"=>[[
                "type"=>"button","style"=>"primary","action"=>["type"=>"uri","label"=>"ไปที่เว็บไซต์","uri"=>$url],"color"=>"#1E88E5"
            ]]]
        ];
    }

    // ---------- Reply ----------
    private function replyText(string $replyToken,string $text): void
    {
        $this->replyMessage($replyToken,["type"=>"text","text"=>$text]);
    }

    private function replyFlex(string $replyToken,string $altText,array $contents): void
    {
        $this->replyMessage($replyToken,["type"=>"flex","altText"=>$altText,"contents"=>$contents]);
    }

    private function replyMessage(string $replyToken,array $message): void
    {
        Http::withHeaders([
            'Content-Type'=>'application/json',
            'Authorization'=>'Bearer '.$this->token,
        ])->post('https://api.line.me/v2/bot/message/reply',[
            'replyToken'=>$replyToken,
            'messages'=>[$message]
        ]);
    }

    // ---------- Utils ----------
    private function buildTelUri(?string $raw): ?string
    {
        if (!$raw) return null;
        $digits = preg_replace('/[^0-9+]/','',$raw);
        if (!$digits) return null;
        return "tel:{$digits}";
    }
}
