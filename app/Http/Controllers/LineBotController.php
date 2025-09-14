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

                // เมนูแนะนำคาเฟ่เมืองสุรินทร์ (ยืดหยุ่น)
                if ($this->isRecommendTrigger($text)) {
                    $menu = $this->menuRecommendCarousel();
                    $this->replyFlex($replyToken, "เมนูแนะนำคาเฟ่เมืองสุรินทร์", $menu);
                    continue;
                }

                // ===== หมวด Top10 =====
                if (in_array($text, ['คาเฟ่Top10','Top10','Top 10','top10'], true)) {
                    $cafes = $this->getTop10Cafes(); // จัดอันดับ: avg_rating ↓, review_count ↓
                    $bubbles = [];

                    if (!empty($cafes)) {
                        foreach ($cafes as $c) {
                            $note = '⭐ ' . number_format((float)($c->avg_rating ?? 0), 1)
                                  . ' (' . (int)($c->review_count ?? 0) . ' รีวิว)';
                            $bubbles[] = $this->bubbleBasic(
                                $c->cafe_name ?? '-', $c->address ?? '-', $note,
                                $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null
                            );
                        }
                    } else {
                        // ถ้าไม่มีรีวิวเลย ให้ fallback ร้านล่าสุดในสุรินทร์
                        $fallback = DB::select("
                            SELECT cafe_id,cafe_name,address,lat,lng,phone
                            FROM cafes
                            WHERE LOWER(COALESCE(status,''))='approved'
                              AND (address LIKE ? OR province LIKE ?)
                            ORDER BY created_at DESC, cafe_id DESC
                            LIMIT 10
                        ", ['%สุรินทร์%', '%สุรินทร์%']);

                        if (!empty($fallback)) {
                            foreach ($fallback as $c) {
                                $bubbles[] = $this->bubbleBasic(
                                    $c->cafe_name ?? '-', $c->address ?? '-', "⭐ แนะนำ",
                                    $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null
                                );
                            }
                        } else {
                            $bubbles[] = $this->bubbleInfo(
                                "ยังไม่มีข้อมูล Top10",
                                "ลองดูข้อมูลล่าสุดบนเว็บไซต์",
                                "https://nongchangsaren.com/"
                            );
                        }
                    }

                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "คาเฟ่ Top 10 เมืองสุรินทร์", ["type"=>"carousel","contents"=>$bubbles]);
                    continue;
                }

                // ===== จับข้อความ "สไตล์:..." =====
                if (mb_strpos($text, 'สไตล์:') === 0) {
                    $styleName = trim(mb_substr($text, 7)); // ตัด "สไตล์:"
                    $cafes = $this->findCafesByFilter('style:' . $styleName);

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
                    continue;
                }

                // ===== FAQ mapping (ของเดิม) =====
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
                                default    => '',
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

                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "ผลลัพธ์: {$text}", ["type"=>"carousel","contents"=>$bubbles]);
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

                // default
                $this->replyText($replyToken, "พิมพ์ “แนะนำคาเฟ่เมืองสุรินทร์” เพื่อเปิดเมนูแนะนำ หรือ “เมนู” เพื่อเปิดเมนูหลักครับ");
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
        }

        return response()->json(['status' => 'ok']);
    }

    // ---------- Helper: Trigger ตรวจข้อความแนะนำคาเฟ่ ----------
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

        if ($hasRecommend && ($hasSurin1 || $hasSurin2)) return true;
        if (mb_stripos($raw, 'recommend') !== false && ($hasSurin1 || $hasSurin2)) return true;

        return false;
    }

    // ---------- Rich Menu ----------
    private function setUserRichMenu(?string $userId, ?string $richMenuId): void
    {
        if (!$userId || !$richMenuId) return;

        Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->post("https://api.line.me/v2/bot/user/{$userId}/richmenu/{$richMenuId}");
    }

   // ---------- เมนูแนะนำคาเฟ่ (Top10 / เปิดใหม่ / สไตล์ชิป) ----------
private function menuRecommendCarousel(): array
{
    $bubbles = [];

    // Bubble 1: หมวดหลัก Top10 / เปิดใหม่ (ไม่มีปุ่ม ราคา / WiFi / ใกล้ฉัน)
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
            ],
            "flex" => 0
        ],
        "styles" => ["footer" => ["separator" => true]]
    ];

    // Bubble 2: ชิปสไตล์ (3 คอลัมน์)
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

    // ---------- Top10 Cafes ----------
    private function getTop10Cafes(): array
    {
        $rows = DB::select("
            SELECT
                c.cafe_id, c.cafe_name, c.address, c.lat, c.lng, c.phone,
                COALESCE(AVG(r.rating), 0) AS avg_rating,
                COUNT(r.cafe_id)           AS review_count
            FROM cafes c
            LEFT JOIN reviews r
              ON r.cafe_id = c.cafe_id
             AND COALESCE(r.status,'approved') = 'approved'
            WHERE LOWER(COALESCE(c.status,''))='approved'
              AND (c.address LIKE ? OR c.province LIKE ?)
            GROUP BY c.cafe_id, c.cafe_name, c.address, c.lat, c.lng, c.phone
            HAVING review_count >= 1 OR avg_rating > 0
            ORDER BY avg_rating DESC, review_count DESC, c.cafe_id DESC
            LIMIT 10
        ", ['%สุรินทร์%', '%สุรินทร์%']);
        return $rows;
    }

    // ---------- FAQ/Style Filters ----------
    private function findCafesByFilter(string $type): array
    {
        // (ใหม่) กรองตามสไตล์
        if (str_starts_with($type, 'style:')) {
            $kw   = trim(mb_substr($type, 6));
            $like = '%'.$kw.'%';
            return DB::select("
                SELECT cafe_id,cafe_name,address,lat,lng,phone
                FROM cafes
                WHERE LOWER(COALESCE(status,''))='approved' AND (
                    -- ถ้าเก็บเป็น JSON
                    (JSON_VALID(cafe_styles) AND JSON_SEARCH(CAST(cafe_styles AS JSON),'one', CONCAT('%', ?, '%')) IS NOT NULL)
                    OR (JSON_VALID(style) AND JSON_SEARCH(CAST(style AS JSON),'one', CONCAT('%', ?, '%')) IS NOT NULL)
                    OR (JSON_VALID(theme) AND JSON_SEARCH(CAST(theme AS JSON),'one', CONCAT('%', ?, '%')) IS NOT NULL)
                    -- ถ้าเก็บเป็นสตริง
                    OR cafe_styles LIKE ? OR style LIKE ? OR theme LIKE ?
                )
                ORDER BY updated_at DESC, cafe_id DESC
                LIMIT 20
            ", [$kw, $kw, $kw, $like, $like, $like]);
        }

        switch ($type) {
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
                        facilities     LIKE '%wifi%' OR facilities LIKE '%Wi-Fi%' OR facilities LIKE '%ไวไฟ%' OR
                        other_services LIKE '%wifi%' OR other_services LIKE '%Wi-Fi%' OR other_services LIKE '%ไวไฟ%'
                    )
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

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

            case 'cheap':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone,price_range
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                    ORDER BY
                        CASE
                            WHEN price_range REGEXP 'ย่อมเยา|ถูก|ประหยัด|cheap|low' THEN 1
                            ELSE 9
                        END ASC,
                        CASE
                            WHEN REPLACE(REPLACE(REPLACE(price_range,' ',''),',',''),'บาท','') LIKE '%ต่ำกว่า100%' THEN 2
                            WHEN REPLACE(REPLACE(REPLACE(price_range,' ',''),',',''),'บาท','') LIKE '%<100%' THEN 2
                            WHEN REPLACE(REPLACE(REPLACE(price_range,' ',''),',',''),'บาท','') LIKE '%101-250%' THEN 3
                            WHEN price_range LIKE '%101 - 250%' THEN 3
                            WHEN REPLACE(REPLACE(REPLACE(price_range,' ',''),',',''),'บาท','') LIKE '%251-500%' THEN 4
                            WHEN price_range LIKE '%251 - 500%' THEN 4
                            WHEN price_range LIKE '%501 - 1,000%' THEN 5
                            WHEN REPLACE(REPLACE(REPLACE(price_range,' ',''),',',''),'บาท','') REGEXP '501[-–—]1000' THEN 5
                            WHEN price_range LIKE '%มากกว่า 1,000%' THEN 6
                            WHEN REPLACE(REPLACE(REPLACE(price_range,' ',''),',',''),'บาท','') LIKE '%>1000%' THEN 6
                            ELSE 98
                        END ASC,
                        updated_at DESC,
                        cafe_id DESC
                    LIMIT 20
                ");

            case 'new':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved'
                      AND CAST(COALESCE(is_new_opening,0) AS UNSIGNED) = 1
                    ORDER BY updated_at DESC, created_at DESC, cafe_id DESC
                    LIMIT 20
                ");

            case 'parking':
                return DB::select("
                    SELECT cafe_id,cafe_name,address,lat,lng,phone
                    FROM cafes
                    WHERE LOWER(COALESCE(status,''))='approved' AND (
                        (JSON_VALID(facilities) AND JSON_SEARCH(CAST(facilities AS JSON),'one','%ที่จอดรถ%') IS NOT NULL)
                        OR (JSON_VALID(other_services) AND JSON_SEARCH(CAST(other_services AS JSON),'one','%ที่จอดรถ%') IS NOT NULL)
                        OR facilities     LIKE '%ที่จอดรถ%'
                        OR other_services LIKE '%ที่จอดรถ%'
                        OR CAST(COALESCE(parking,0) AS UNSIGNED) = 1
                    )
                    ORDER BY updated_at DESC, cafe_id DESC
                    LIMIT 20
                ");

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
                        facilities     LIKE '%ห้องประชุม%' OR facilities LIKE '%meeting%' OR facilities LIKE '%ประชุม%' OR
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
            'messages'   => [$message]
        ]);
    }

    // ---------- Utils ----------
    private function buildTelUri(?string $raw): ?string
    {
        if (!$raw) return null;
        $digits = preg_replace('/[^0-9+]/', '', $raw);
        if (!$digits) return null;
        return "tel:{$digits}";
    }
}
