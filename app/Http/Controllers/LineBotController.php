<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

            // ---------- POSTBACK (จากปุ่มใน Rich Menu/QuickReply) ----------
            if (($event['type'] ?? '') === 'postback') {
                $dataStr = $event['postback']['data'] ?? '';
                parse_str($dataStr, $pb);
                $action = $pb['action'] ?? '';

                // map postback -> ข้อความเดิมที่เราใช้
                $map = [
                    'open_main'  => 'เมนู',
                    'open_faq'   => 'FAQ',
                    'faq_wifi'   => 'FreeWiFi',
                    'faq_park'   => 'ที่จอดรถ',
                    'faq_meet'   => 'มีห้องประชุม',
                    'faq_cheap'  => 'ย่อมเยา',
                    'faq_quiet'  => 'เงียบ',
                    'faq_aircon' => 'แอร์',
                    'nearby'     => 'ค้นหาคาเฟ่ใกล้ฉัน',
                    'top10'      => 'คาเฟ่Top10',
                    'new'        => 'เปิดใหม่',
                ];

                $text = $map[$action] ?? '';
                if ($text !== '') {
                    // ทำเหมือน message type=text
                    $this->dispatchText($replyToken, $userId, $text);
                    continue;
                }

                // ถ้าเป็น postback ส่งโลเคชัน (บาง template จะส่งใน params)
                if (isset($event['postback']['params']['latitude'], $event['postback']['params']['longitude'])) {
                    $lat = (float) $event['postback']['params']['latitude'];
                    $lng = (float) $event['postback']['params']['longitude'];
                    $this->replyNearbyFromLatLng($replyToken, $lat, $lng);
                    continue;
                }
            }

            // ---------- TEXT ----------
            if (($event['type'] ?? '') === 'message' && ($event['message']['type'] ?? '') === 'text') {
                $text = trim($event['message']['text'] ?? '');
                $this->dispatchText($replyToken, $userId, $text);
                continue;
            }

            // ---------- LOCATION ----------
            if (($event['type'] ?? '') === 'message' && ($event['message']['type'] ?? '') === 'location') {
                $lat = $event['message']['latitude']  ?? null;
                $lng = $event['message']['longitude'] ?? null;
                if ($lat === null || $lng === null) {
                    $this->replyText($replyToken, "ไม่พบพิกัดที่ส่งมา ลองส่งใหม่อีกครั้งนะครับ");
                    continue;
                }
                $this->replyNearbyFromLatLng($replyToken, (float)$lat, (float)$lng);
                continue;
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // =========================================================
    // Dispatcher สำหรับข้อความ
    // =========================================================
    private function dispatchText(string $replyToken, ?string $userId, string $text): void
    {
        // Toggle Rich Menu
        if (in_array($text, ['เมนู','menu','เริ่มต้น'], true)) {
            $this->setUserRichMenu($userId, $this->richMain);
            $this->replyText($replyToken, "เปิดเมนูหลักแล้วครับ 😊");
            return;
        }

        // ✅ แก้เฉพาะส่วน FAQ: กดแล้วแสดง "เมนู FAQ" แบบรูปที่ 2
        if (in_array($text, ['FAQ','คำถามที่พบบ่อย','เมนูคำตอบ'], true)) {
            $this->setUserRichMenu($userId, $this->richFaq);                 // คงพฤติกรรมผูก Rich Menu
            $this->replyFlex($replyToken, 'เมนู FAQ', $this->faqMenuBubble()); // ส่ง Flex เมนู FAQ
            return;
        }

        // ====== NEARBY: ขอโลเคชัน ======
        $nearAliases = ['ค้นหาคาเฟ่ใกล้ฉัน','คาเฟ่ใกล้ฉัน','ใกล้ฉัน','near me','nearby'];
        if (in_array(mb_strtolower($text), array_map('mb_strtolower',$nearAliases), true)) {
            $this->askShareLocation($replyToken);
            return;
        }

        // ====== FAQ Keywords -> ค้นหา + แสดงคาเฟ่ ======
        $faqKeywords = [
            'ที่จอดรถ' => 'parking', 'จอดรถ' => 'parking', 'parking' => 'parking',
            'FreeWiFi' => 'wifi', 'freewifi' => 'wifi', 'ฟรีwifi' => 'wifi', 'wifi' => 'wifi', 'ไวไฟ' => 'wifi',
            'มีห้องประชุม' => 'meeting', 'ห้องประชุม' => 'meeting', 'ทำงานได้' => 'meeting', 'work' => 'meeting',
            'ย่อมเยา' => 'cheap', 'ราคาย่อมเยา' => 'cheap', 'ถูก' => 'cheap', 'ประหยัด' => 'cheap',
            'เงียบ' => 'quiet', 'อ่านหนังสือ' => 'quiet', 'สงบ' => 'quiet',
            'แอร์' => 'aircon', 'เครื่องปรับอากาศ' => 'aircon', 'aircon' => 'aircon',
        ];
        $key = $faqKeywords[$text] ?? null;
        if ($key) {
            $faq   = $this->getFaqEntry($key);
            $cafes = $this->findCafesByAmenity($key, 10);

            $bubbles = [];
            // หัว FAQ (อธิบายสั้น)
            $bubbles[] = $this->bubbleFaq($faq['title'], $faq['lines'], $faq['buttons']);

            if (!empty($cafes)) {
                foreach ($cafes as $c) {
                    $bubbles[] = $this->bubbleBasic(
                        $c->cafe_name ?? '-', $c->address ?? '-',
                        $this->amenitySubtitle($key),
                        $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null
                    );
                }
            } else {
                $bubbles[] = $this->bubbleInfo(
                    "ยังไม่พบทุกร้านตามเงื่อนไข",
                    "ลองดูทั้งหมดหรือปรับคีย์เวิร์ด",
                    "https://nongchangsaren.com/"
                );
            }
            $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');

            $this->replyFlex($replyToken, $faq['alt'], ["type"=>"carousel","contents"=>$bubbles]);
            return;
        }

        // ===== เมนูแนะนำคาเฟ่เมืองสุรินทร์ =====
        if ($this->isRecommendTrigger($text)) {
            $menu = $this->menuRecommendCarousel();
            $this->replyFlex($replyToken, "เมนูแนะนำคาเฟ่เมืองสุรินทร์", $menu);
            return;
        }

        // ===== Top10 =====
        if (in_array($text, ['คาเฟ่Top10','Top10','Top 10','top10'], true)) {
            $cafes = $this->getTop10Cafes();
            Log::info('[Top10] rows', ['count' => count($cafes)]);

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
            $this->replyFlex($replyToken, "คาเฟ่ Top 10 เมืองสุรินทร์", ["type"=>"carousel","contents"=>$bubbles]);
            return;
        }

        // ===== สไตล์:xxxx =====
        if (mb_strpos($text, 'สไตล์:') === 0) {
            $styleName = trim(mb_substr($text, 6));
            $cafes = $this->findCafesByFilter('style:' . $styleName);
            Log::info('[Style] query', ['style' => $styleName, 'count' => count($cafes)]);

            $bubbles = [];
            if (!empty($cafes)) {
                $cafes = array_slice($cafes, 0, 9);
                foreach ($cafes as $c) {
                    $mapUrl = ($c->lat !== null && $c->lng !== null)
                        ? "https://maps.google.com/?q={$c->lat},{$c->lng}"
                        : "https://www.google.com/maps/search/".urlencode(($c->cafe_name ?? '').' '.($c->address ?? ''));
                    $bubbles[] = $this->bubbleBasic(
                        $c->cafe_name ?? '-', $c->address ?? '-', '🎨 สไตล์: '.$styleName,
                        $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null, $mapUrl
                    );
                }
            } else {
                $bubbles[] = $this->bubbleInfo(
                    "ยังไม่พบตามสไตล์",
                    "ลองเลือกสไตล์อื่นหรือดูทั้งหมดบนเว็บ",
                    "https://nongchangsaren.com/"
                );
            }

            $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
            $this->replyFlex($replyToken, "คาเฟ่สไตล์: {$styleName}", ["type"=>"carousel","contents"=>$bubbles]);
            return;
        }

        // ===== เปิดใหม่ =====
        if ($text === 'เปิดใหม่') {
            $cafes = $this->findCafesByFilter('new');
            Log::info('[New] rows', ['count' => count($cafes)]);
            $bubbles = [];

            if (!empty($cafes)) {
                $cafes = array_slice($cafes, 0, 10);
                foreach ($cafes as $c) {
                    $bubbles[] = $this->bubbleBasic(
                        $c->cafe_name ?? '-', $c->address ?? '-', "🆕 ร้านเปิดใหม่",
                        $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null
                    );
                }
            } else {
                $bubbles[] = $this->bubbleInfo(
                    "ยังไม่พบร้านเปิดใหม่",
                    "ลองดูบนเว็บไซต์เพื่ออัปเดตเพิ่มเติม",
                    "https://nongchangsaren.com/"
                );
            }

            $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
            $this->replyFlex($replyToken, "คาเฟ่เปิดใหม่ เมืองสุรินทร์", ["type"=>"carousel","contents"=>$bubbles]);
            return;
        }

        // ===== default =====
        $this->replyText(
            $replyToken,
            "พิมพ์ “แนะนำคาเฟ่เมืองสุรินทร์” เพื่อเปิดเมนูแนะนำ หรือ “เมนู” เพื่อเปิดเมนูหลักครับ\nหรือถาม FAQ: ที่จอดรถ / FreeWiFi / มีห้องประชุม / ย่อมเยา / เงียบ / แอร์\nหรือพิมพ์ “ค้นหาคาเฟ่ใกล้ฉัน” แล้วส่งโลเคชัน"
        );
    }

    // =========================================================
    // ✅ เมนู FAQ (Flex bubble แบบรูปที่ 2)
    // =========================================================
    private function faqMenuBubble(): array
    {
        // ปุ่มสี่เหลี่ยม
        $tile = function(string $label, string $msg) {
            return [
                "type" => "box",
                "layout" => "vertical",
                "cornerRadius" => "14px",
                "backgroundColor" => "#2D7BF2",
                "paddingAll" => "14px",
                "action" => ["type" => "message", "text" => $msg],
                "contents" => [[
                    "type" => "text",
                    "text" => $label,
                    "weight" => "bold",
                    "size" => "md",
                    "align" => "center",
                    "color" => "#FFFFFF",
                    "wrap" => true
                ]]
            ];
        };

        return [
            "type" => "bubble",
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "paddingAll" => "16px",
                "spacing" => "12px",
                "contents" => [
                    ["type" => "text", "text" => "มีคำตอบ....", "weight" => "bold", "size" => "lg"],
                    [
                        "type" => "box",
                        "layout" => "vertical",
                        "spacing" => "12px",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "spacing" => "12px",
                                "contents" => [
                                    $tile("FreeWiFi", "FreeWiFi"),
                                    $tile("เปิดอยู่ตอนนี้", "คาเฟ่Top10"), // ชั่วคราวให้กดแล้วมีผล
                                    $tile("คาเฟ่ราคาย่อมเยา", "ย่อมเยา"),
                                ]
                            ],
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "spacing" => "12px",
                                "contents" => [
                                    $tile("เปิดใหม่", "เปิดใหม่"),
                                    $tile("ที่จอดรถ", "ที่จอดรถ"),
                                    $tile("มีห้องประชุม\nทำงาน ได้", "มีห้องประชุม"),
                                ]
                            ],
                        ]
                    ]
                ]
            ],
            "styles" => [
                "body" => ["backgroundColor" => "#EAF4FF"]
            ]
        ];
    }

    // =========================================================
    // Nearby helpers
    // =========================================================
    private function askShareLocation(string $replyToken): void
    {
        $message = [
            "type" => "text",
            "text" => "ส่งตำแหน่งของคุณมาได้เลยครับ (จะแสดงคาเฟ่ในรัศมี ~5 กม.)",
            "quickReply" => [
                "items" => [[
                    "type" => "action",
                    "action" => ["type" => "location", "label" => "แชร์โลเคชัน"]
                ]]
            ]
        ];
        $this->replyMessage($replyToken, $message);
    }

    private function replyNearbyFromLatLng(string $replyToken, float $lat, float $lng): void
    {
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
            ORDER BY distance ASC, cafe_id DESC
            LIMIT 5
        ", [$lat, $lng, $lat]);

        $bubbles = [];
        if (!empty($cafes)) {
            foreach ($cafes as $c) {
                $bubbles[] = $this->bubbleBasic(
                    $c->cafe_name, $c->address, "📍 ห่าง " . round($c->distance, 2) . " กม.",
                    $c->phone, $c->lat, $c->lng
                );
            }
        } else {
            $bubbles[] = $this->bubbleInfo(
                "ไม่พบคาเฟ่ในรัศมี 5 กม.",
                "ดูแผนที่ร้านทั้งหมดบนเว็บไซต์",
                "https://nongchangsaren.com/"
            );
        }
        $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
        $this->replyFlex($replyToken, "คาเฟ่ใกล้คุณ", ["type"=>"carousel","contents"=>$bubbles]);
    }

    // =========================================================
    // Trigger ตรวจจับเมนูแนะนำ
    // =========================================================
    private function isRecommendTrigger(string $text): bool
    {
        $raw = trim($text);
        $noSpace = preg_replace('/\s+/u', '', $raw);

        $aliases = [
            'แนะนำคาเฟ่เมืองสุรินทร์',
            'เมนูแนะนำคาเฟ่',
            'คาเฟ่เมืองสุรินทร์',
            'recommend',
        ];
        $aliasesNoSpace = array_map(fn($s) => preg_replace('/\s+/u', '', $s), $aliases);

        if (in_array($raw, $aliases, true) || in_array($noSpace, $aliasesNoSpace, true)) return true;

        $hasRecommend = mb_stripos($raw, 'แนะนำคาเฟ่') !== false;
        $hasSurin1    = mb_stripos($raw, 'เมืองสุรินทร์') !== false;
        $hasSurin2    = mb_stripos($raw, 'สุรินทร์') !== false;

        if ($hasRecommend && ($hasSur1 ?? false)) {} // noop
        if ($hasRecommend && ($hasSurin1 || $hasSurin2)) return true;
        if (mb_stripos($raw, 'recommend') !== false && ($hasSurin1 || $hasSurin2)) return true;

        return false;
    }

    // =========================================================
    // Rich Menu binder
    // =========================================================
    private function setUserRichMenu(?string $userId, ?string $richMenuId): void
    {
        if (!$userId || !$richMenuId) return;

        Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->post("https://api.line.me/v2/bot/user/{$userId}/richmenu/{$richMenuId}");
    }

    // =========================================================
    // เมนูแนะนำ (Top10 / เปิดใหม่ / สไตล์)
    // =========================================================
    private function menuRecommendCarousel(): array
    {
        $bubbles = [];

        // Bubble 1: Top10 / เปิดใหม่ / ใกล้ฉัน
        $bubbles[] = [
            "type" => "bubble",
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "paddingAll" => "16px",
                "spacing" => "12px",
                "contents" => [
                    ["type" => "text","text" => "แนะนำคาเฟ่เมืองสุรินทร์","weight" => "bold","size" => "lg"],
                    ["type" => "text","text" => "เลือกหมวดหรือสไตล์ที่ชอบได้เลยครับ","size" => "sm","color" => "#666666","wrap" => true],
                    ["type" => "separator","margin" => "12px"]
                ]
            ],
            "footer" => [
                "type" => "box",
                "layout" => "vertical",
                "spacing" => "md",
                "paddingAll" => "12px",
                "contents" => [
                    ["type" => "button","style" => "primary","action" => ["type" => "message","label" => "🔥 Top10","text" => "คาเฟ่Top10"],"color" => "#1E88E5"],
                    ["type" => "button","style" => "primary","action" => ["type" => "message","label" => "✨ เปิดใหม่","text" => "เปิดใหม่"],"color" => "#2ECC71"],
                    ["type" => "button","style" => "secondary","action" => ["type" => "message","label" => "📍 ใกล้ฉัน","text" => "ค้นหาคาเฟ่ใกล้ฉัน"]],
                ],
                "flex" => 0
            ],
            "styles" => ["footer" => ["separator" => true]]
        ];

        // Bubble 2: ชิปสไตล์
        $styleLabels = ['มินิมอล','โมเดิร์น','โคซี่/อบอุ่น','ยุโรป','ธรรมชาติ/สวน','ลอฟท์','อินดัสเทรียล','วินเทจ','อาร์ต/แกลเลอรี่'];
        $chips = [];
        foreach ($styleLabels as $label) {
            $chips[] = [
                "type" => "box",
                "layout" => "vertical",
                "cornerRadius" => "12px",
                "backgroundColor" => "#8A63F6",
                "paddingAll" => "10px",
                "action" => ["type" => "message","text" => "สไตล์:".$label],
                "contents" => [[
                    "type" => "text",
                    "text" => "🎨 ".$label,
                    "size" => "sm",
                    "weight" => "bold",
                    "color" => "#FFFFFF",
                    "align" => "center"
                ]]
            ];
        }
        $rows = array_chunk($chips, 3);
        $gridRows = [];
        foreach ($rows as $row) {
            $gridRows[] = ["type"=>"box","layout"=>"horizontal","spacing"=>"8px","contents"=>$row];
        }

        $bubbles[] = [
            "type" => "bubble",
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "paddingAll" => "16px",
                "spacing" => "12px",
                "contents" => array_merge(
                    [["type"=>"text","text"=>"เลือกตามสไตล์ที่ชอบ","weight"=>"bold","size"=>"md"]],
                    $gridRows
                )
            ]
        ];

        // Bubble 3: ไปหน้าเว็บ
        $bubbles[] = $this->bubbleMore('ดูทั้งหมดบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');

        return ["type" => "carousel", "contents" => $bubbles];
    }

    // =========================================================
    // Top10 (ไม่ใช้ reviews.status และไม่บังคับต้องมีรีวิว)
    // =========================================================
    private function getTop10Cafes(): array
    {
        $base = DB::table('cafes as c')
            ->leftJoin('reviews as r', 'r.cafe_id', '=', 'c.cafe_id')
            ->whereRaw("LOWER(COALESCE(c.status,''))='approved'");

        $select = "
            c.cafe_id, c.cafe_name, c.address, c.lat, c.lng, c.phone,
            COALESCE(AVG(r.rating), 0) AS avg_rating,
            COUNT(r.rating)            AS review_count
        ";

        // กรองพื้นที่ 'สุรินทร์' ก่อน
        $rows = (clone $base)
            ->where('c.address', 'LIKE', '%สุรินทร์%')
            ->selectRaw($select)
            ->groupBy('c.cafe_id','c.cafe_name','c.address','c.lat','c.lng','c.phone')
            ->orderByDesc('avg_rating')
            ->orderByDesc('review_count')
            ->orderByDesc('c.cafe_id')
            ->limit(10)
            ->get()
            ->all();

        // ถ้าไม่เจอ: ไม่กรอง address
        if (empty($rows)) {
            $rows = (clone $base)
                ->selectRaw($select)
                ->groupBy('c.cafe_id','c.cafe_name','c.address','c.lat','c.lng','c.phone')
                ->orderByDesc('avg_rating')
                ->orderByDesc('review_count')
                ->orderByDesc('c.cafe_id')
                ->limit(10)
                ->get()
                ->all();
        }

        // ถ้ายังไม่เจอ: fallback ร้านล่าสุด
        if (empty($rows)) {
            return DB::table('cafes')
                ->whereRaw("LOWER(COALESCE(status,''))='approved'")
                ->select('cafe_id','cafe_name','address','lat','lng','phone')
                ->orderByDesc('created_at')
                ->orderByDesc('cafe_id')
                ->limit(10)
                ->get()
                ->map(function ($c) {
                    $c->avg_rating = 0;
                    $c->review_count = 0;
                    return $c;
                })
                ->all();
        }

        return $rows;
    }

    // =========================================================
    // FAQ Data
    // =========================================================
    private function getFaqEntry(string $key): array
    {
        $map = [
            'parking' => [
                'alt'    => 'FAQ: มีที่จอดรถไหม',
                'title'  => 'ที่จอดรถ',
                'lines'  => [
                    'หลายคาเฟ่มีที่จอดรถด้านหน้า/ข้างร้านครับ',
                    'หากไปช่วงเสาร์-อาทิตย์ แนะนำเผื่อเวลาหรือโทรเช็คก่อน',
                ],
                'buttons'=> [
                    ['label' => 'ดูร้านที่มีที่จอดรถ', 'uri' => 'https://nongchangsaren.com/'],
                    ['label' => 'เปิดเมนูแนะนำ', 'message' => 'แนะนำคาเฟ่เมืองสุรินทร์'],
                ]
            ],
            'wifi' => [
                'alt'   => 'FAQ: Free WiFi',
                'title' => 'Free WiFi',
                'lines' => [
                    'หลายร้านมี Free WiFi ให้บริการ',
                    'ความเร็ว/รหัสผ่านต่างกันไปในแต่ละร้าน',
                ],
                'buttons'=> [
                    ['label' => 'ดูร้าน WiFi ดี', 'uri' => 'https://nongchangsaren.com/'],
                    ['label' => 'คาเฟ่Top10', 'message' => 'คาเฟ่Top10'],
                ]
            ],
            'meeting' => [
                'alt'   => 'FAQ: มีห้องประชุม/ทำงาน',
                'title' => 'มีห้องประชุม / ทำงานได้',
                'lines' => [
                    'บางร้านมีปลั๊กไฟ/โต๊ะยาว/ห้องประชุมเล็ก',
                    'วันคนเยอะควรจองล่วงหน้า',
                ],
                'buttons'=> [
                    ['label' => 'ค้นหาร้านสำหรับทำงาน', 'uri' => 'https://nongchangsaren.com/'],
                    ['label' => 'เปิดเมนูแนะนำ', 'message' => 'แนะนำคาเฟ่เมืองสุรินทร์'],
                ]
            ],
            'cheap' => [
                'alt'   => 'FAQ: ราคาย่อมเยา',
                'title' => 'คาเฟ่ราคาย่อมเยา',
                'lines' => [
                    'มีเมนูเริ่มต้นราว 35–50 บาทในหลายร้าน',
                    'โปรโมชันขึ้นกับช่วงเวลา',
                ],
                'buttons'=> [
                    ['label' => 'ดูร้านราคาย่อมเยา', 'uri' => 'https://nongchangsaren.com/'],
                    ['label' => 'คาเฟ่Top10', 'message' => 'คาเฟ่Top10'],
                ]
            ],
            'quiet' => [
                'alt'   => 'FAQ: ร้านเงียบ',
                'title' => 'บรรยากาศเงียบ/อ่านหนังสือ',
                'lines' => [
                    'ช่วงเช้าวันธรรมดามักจะคนไม่เยอะ',
                    'บางร้านมีโซนเงียบ ลองสอบถามพนักงาน',
                ],
                'buttons'=> [
                    ['label' => 'ค้นหาร้านบรรยากาศเงียบ', 'uri' => 'https://nongchangsaren.com/'],
                    ['label' => 'เปิดเมนูแนะนำ', 'message' => 'แนะนำคาเฟ่เมืองสุรินทร์'],
                ]
            ],
            'aircon' => [
                'alt'   => 'FAQ: มีแอร์',
                'title' => 'เครื่องปรับอากาศ',
                'lines' => [
                    'ส่วนใหญ่มีแอร์ทั้งร้านหรือบางโซน',
                    'ถ้าต้องการเย็นจัด เลือกที่นั่งด้านใน',
                ],
                'buttons'=> [
                    ['label' => 'ดูร้านมีแอร์', 'uri' => 'https://nongchangsaren.com/'],
                    ['label' => 'คาเฟ่Top10', 'message' => 'คาเฟ่Top10'],
                ]
            ],
        ];

        return $map[$key] ?? [
            'alt'   => 'FAQ',
            'title' => 'FAQ',
            'lines' => ['ยังไม่มีคำตอบสำหรับหัวข้อนี้', 'ลองเปิดเมนูแนะนำดูได้นะครับ'],
            'buttons'=> [['label'=>'เปิดเมนูแนะนำ','message'=>'แนะนำคาเฟ่เมืองสุรินทร์']]
        ];
    }

    private function amenitySubtitle(string $key): string
    {
        return match ($key) {
            'parking' => '🅿️ มีที่จอดรถ',
            'wifi'    => '📶 Free WiFi',
            'meeting' => '💻 เหมาะทำงาน/มีห้องประชุม',
            'cheap'   => '💸 ราคาย่อมเยา',
            'quiet'   => '🤫 บรรยากาศเงียบ',
            'aircon'  => '❄️ มีแอร์',
            default   => 'ข้อมูลจากเงื่อนไข',
        };
    }

    // =========================================================
    // ค้นหาร้านตาม Amenity (ตรวจคอลัมน์ที่มีจริงก่อน)
    // =========================================================
    private function findCafesByAmenity(string $amenity, int $limit = 10): array
    {
        $q = DB::table('cafes')->whereRaw("LOWER(COALESCE(status,''))='approved'");
        // โฟกัสพื้นที่สุรินทร์ก่อน
        $q->where('address', 'LIKE', '%สุรินทร์%');

        // ตัวช่วยตรวจคอลัมน์
        $has = fn(string $col) => Schema::hasColumn('cafes', $col);

        // สร้างเงื่อนไขอย่างปลอดภัย (ถ้ามีคอลัมน์จึงค่อยอ้าง)
        $likeColumns = array_values(array_filter([
            $has('tags')        ? 'tags'        : null,
            $has('features')    ? 'features'    : null,
            $has('description') ? 'description' : null,
            $has('other_style') ? 'other_style' : null,
        ]));

        // ฟังก์ชัน OR LIKE หลายคอลัมน์
        $orLike = function($builder, array $cols, array $words) {
            $builder->where(function($inner) use ($cols, $words) {
                foreach ($cols as $c) {
                    foreach ($words as $w) {
                        $inner->orWhere($c, 'LIKE', "%{$w}%");
                    }
                }
            });
        };

        switch ($amenity) {
            case 'wifi':
                if ($has('has_wifi')) {
                    $q->whereRaw('CAST(COALESCE(has_wifi,1) AS UNSIGNED)=1');
                }
                if ($likeColumns) {
                    $orLike($q, $likeColumns, ['wifi','wi-fi','ไวไฟ','free wifi']);
                }
                break;

            case 'parking':
                if ($has('has_parking')) {
                    $q->whereRaw('CAST(COALESCE(has_parking,0) AS UNSIGNED)=1');
                }
                if ($likeColumns) {
                    $orLike($q, $likeColumns, ['parking','จอดรถ','ที่จอดรถ','มีที่จอด']);
                }
                break;

            case 'meeting':
                if ($has('has_meeting_room')) {
                    $q->whereRaw('CAST(COALESCE(has_meeting_room,0) AS UNSIGNED)=1');
                }
                if ($likeColumns) {
                    $orLike($q, $likeColumns, ['ห้องประชุม','meeting','ทำงาน','ปลั๊กไฟ','นั่งทำงาน']);
                }
                break;

            case 'cheap':
                if ($has('price_min')) {
                    $q->where('price_min', '<=', 50);
                } elseif ($has('price_avg')) {
                    $q->where('price_avg', '<=', 60);
                }
                if ($likeColumns) {
                    $orLike($q, $likeColumns, ['ย่อมเยา','ถูก','ประหยัด','คุ้มค่า']);
                }
                break;

            case 'quiet':
                if ($likeColumns) {
                    $orLike($q, $likeColumns, ['เงียบ','อ่านหนังสือ','สงบ','chill']);
                }
                break;

            case 'aircon':
                if ($has('has_aircon')) {
                    $q->whereRaw('CAST(COALESCE(has_aircon,1) AS UNSIGNED)=1');
                }
                if ($likeColumns) {
                    $orLike($q, $likeColumns, ['แอร์','aircon','เครื่องปรับอากาศ']);
                }
                break;
        }

        return $q->select('cafe_id','cafe_name','address','lat','lng','phone')
                 ->orderByDesc('updated_at')
                 ->orderByDesc('cafe_id')
                 ->limit($limit)
                 ->get()
                 ->all();
    }

    // =========================================================
    // ตัวกรองอื่น ๆ (สไตล์/เปิดใหม่)
    // =========================================================
    private function findCafesByFilter(string $type): array
    {
        // สไตล์
        if (str_starts_with($type, 'style:')) {
            $kw = trim(mb_substr($type, 6));
            if ($kw === '') return [];
            $like = "%{$kw}%";

            return DB::table('cafes')
                ->whereRaw("LOWER(COALESCE(status,''))='approved'")
                ->where('address', 'LIKE', '%สุรินทร์%')
                ->where(function($q) use ($like) {
                    if (Schema::hasColumn('cafes', 'cafe_styles')) {
                        $q->orWhereRaw("(JSON_VALID(cafe_styles) AND JSON_SEARCH(cafe_styles, 'one', ?) IS NOT NULL)", [$like]);
                    }
                    $q->orWhere('other_style', 'LIKE', $like);
                })
                ->select('cafe_id','cafe_name','address','lat','lng','phone')
                ->orderByDesc('updated_at')
                ->orderByDesc('cafe_id')
                ->limit(20)
                ->get()
                ->all();
        }

        switch ($type) {
            case 'new':
                $q = DB::table('cafes')
                    ->whereRaw("LOWER(COALESCE(status,''))='approved'");
                if (Schema::hasColumn('cafes','is_new_opening')) {
                    $q->whereRaw('CAST(COALESCE(is_new_opening,0) AS UNSIGNED)=1');
                }
                return $q->select('cafe_id','cafe_name','address','lat','lng','phone')
                    ->orderByDesc('updated_at')
                    ->orderByDesc('created_at')
                    ->orderByDesc('cafe_id')
                    ->limit(20)
                    ->get()
                    ->all();

            default:
                return [];
        }
    }

    // =========================================================
    // Flex Components
    // =========================================================
    private function bubbleFaq(string $title, array $lines, array $buttons): array
    {
        $lineBoxes = [];
        foreach ($lines as $t) {
            $lineBoxes[] = ["type"=>"text","text"=>$t,"size"=>"sm","color"=>"#555555","wrap"=>true];
        }

        $btns = [];
        foreach ($buttons as $b) {
            if (isset($b['uri'])) {
                $btns[] = [
                    "type"=>"button","style"=>"primary","height"=>"sm",
                    "action"=>["type"=>"uri","label"=>$b['label'],"uri"=>$b['uri']],
                    "color"=>"#1E88E5"
                ];
            } else {
                $btns[] = [
                    "type"=>"button","style"=>"secondary","height"=>"sm",
                    "action"=>["type"=>"message","label"=>$b['label'],"text"=>$b['message']]
                ];
            }
        }

        return [
            "type"=>"bubble",
            "body"=>[
                "type"=>"box","layout"=>"vertical","paddingAll"=>"16px","spacing"=>"10px",
                "contents"=>array_merge(
                    [["type"=>"text","text"=>$title,"weight"=>"bold","size"=>"lg","wrap"=>true]],
                    $lineBoxes,
                    [["type"=>"separator","margin"=>"12px"]]
                )
            ],
            "footer"=>[
                "type"=>"box","layout"=>"vertical","spacing"=>"md","paddingAll"=>"12px",
                "contents"=>$btns,"flex"=>0
            ],
            "styles"=>["footer"=>["separator"=>true]]
        ];
    }

    private function bubbleBasic($name, $addr, $sub, $phone, $lat, $lng, $mapUrl = null): array
    {
        $mapUrl    = $mapUrl ?: (($lat !== null && $lng !== null)
            ? "https://maps.google.com/?q={$lat},{$lng}"
            : "https://www.google.com/maps/search/".urlencode((string)$name.' '.(string)$addr));
        $phoneText = $phone ?: "ไม่มีข้อมูล";
        $telUri    = $this->buildTelUri($phone);

        $buttons = [[
            "type" => "button",
            "style" => "primary",
            "height" => "sm",
            "action" => ["type" => "uri", "label" => "เปิดแผนที่", "uri" => $mapUrl],
            "color" => "#1E88E5"
        ]];
        if ($telUri) {
            $buttons[] = [
                "type" => "button",
                "style" => "secondary",
                "height" => "sm",
                "action" => ["type" => "uri", "label" => "โทรเลย", "uri" => $telUri]
            ];
        }

        return [
            "type" => "bubble",
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "paddingAll" => "16px",
                "spacing" => "12px",
                "contents" => [
                    [
                        "type" => "box",
                        "layout" => "vertical",
                        "spacing" => "8px",
                        "contents" => [
                            ["type" => "text","text" => (string)$name,"weight" => "bold","size" => "lg","wrap" => true],
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "spacing" => "sm",
                                "contents" => [[
                                    "type" => "box",
                                    "layout" => "baseline",
                                    "backgroundColor" => "#EEF7FF",
                                    "cornerRadius" => "6px",
                                    "paddingAll" => "6px",
                                    "contents" => [[
                                        "type" => "text",
                                        "text" => (string)$sub,
                                        "size" => "xs",
                                        "color" => "#1E88E5",
                                        "wrap" => true
                                    ]]
                                ]]
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "spacing" => "sm",
                        "contents" => [
                            ["type" => "text","text" => "📍","size" => "sm","flex" => 0],
                            ["type" => "text","text" => (string)($addr ?? "-"),"size" => "sm","color" => "#555555","wrap" => true]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "spacing" => "sm",
                        "contents" => [
                            ["type" => "text","text" => "☎","size" => "sm","flex" => 0],
                            ["type" => "text","text" => $phoneText,"size" => "sm","color" => "#555555","wrap" => true]
                        ]
                    ],
                    ["type" => "separator","margin" => "12px"]
                ]
            ],
            "footer" => [
                "type" => "box",
                "layout" => "horizontal",
                "spacing" => "md",
                "paddingAll" => "12px",
                "contents" => $buttons,
                "flex" => 0
            ],
            "styles" => ["footer" => ["separator" => true]]
        ];
    }

    private function bubbleMore(string $title, string $sub, string $url): array
    {
        return [
            "type" => "bubble",
            "body" => [
                "type" => "box","layout" => "vertical","paddingAll" => "16px","spacing" => "10px",
                "contents" => [
                    ["type" => "text","text" => $title,"weight" => "bold","size" => "lg","wrap" => true],
                    ["type" => "text","text" => $sub,"size" => "sm","color" => "#666666","wrap" => true],
                    ["type" => "separator","margin" => "12px"]
                ]
            ],
            "footer" => [
                "type" => "box","layout" => "vertical","spacing" => "md","paddingAll" => "12px",
                "contents" => [[
                    "type" => "button","style" => "primary",
                    "action" => ["type" => "uri","label" => "ไปที่เว็บไซต์","uri" => $url],
                    "color" => "#1E88E5"
                ]],
                "flex" => 0
            ],
            "styles" => ["footer" => ["separator" => true]]
        ];
    }

    private function bubbleInfo(string $title, string $sub, string $url): array
    {
        return [
            "type" => "bubble",
            "body" => [
                "type" => "box","layout" => "vertical","paddingAll" => "16px","spacing" => "10px",
                "contents" => [
                    ["type" => "text","text" => $title,"weight" => "bold","size" => "lg","wrap" => true],
                    ["type" => "text","text" => $sub,"size" => "sm","color" => "#666666","wrap" => true],
                    ["type" => "separator","margin" => "12px"]
                ]
            ],
            "footer" => [
                "type" => "box","layout" => "vertical","spacing" => "md","paddingAll" => "12px",
                "contents" => [[
                    "type" => "button","style" => "primary",
                    "action" => ["type" => "uri","label" => "ไปที่เว็บไซต์","uri" => $url],
                    "color" => "#1E88E5"
                ]],
                "flex" => 0
            ],
            "styles" => ["footer" => ["separator" => true]]
        ];
    }

    // =========================================================
    // Reply helpers
    // =========================================================
    private function replyText(string $replyToken, string $text): void
    {
        $this->replyMessage($replyToken, ["type" => "text", "text" => $text]);
    }

    private function replyFlex(string $replyToken, string $altText, array $contents): void
    {
        if (!isset($contents['type'])) {
            $contents = ["type"=>"carousel","contents"=>[$contents]];
        }
        $this->replyMessage($replyToken, ["type" => "flex","altText" => $altText,"contents" => $contents]);
    }

    private function replyMessage(string $replyToken, array $message): void
    {
        Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer '.$this->token,
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => [$message]
        ]);
    }

    // =========================================================
    // Utils
    // =========================================================
    private function buildTelUri(?string $raw): ?string
    {
        if (!$raw) return null;
        $digits = preg_replace('/[^0-9+]/', '', $raw);
        if (!$digits) return null;
        return "tel:{$digits}";
    }
}
