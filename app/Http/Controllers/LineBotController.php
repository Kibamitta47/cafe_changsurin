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
                    $cafes = $this->getTop10Cafes(); // อิง avg_rating + review_count
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
                    continue;
                }

                // ===== สไตล์ (ข้อความขึ้นต้น "สไตล์:") =====
                if (mb_strpos($text, 'สไตล์:') === 0) {
                    // 🔧 แก้ index จาก 7 -> 6 เพื่อไม่ให้ตัด 'ม' ของ "มินิมอล"
                    $styleName = trim(mb_substr($text, 6)); // "สไตล์:" ยาว 6 ตัว
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
                    continue;
                }

                // ===== "เปิดใหม่" =====
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

    // ---------- Helper: Trigger ----------
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

        // Bubble 1: Top10 / เปิดใหม่
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

    // ---------- Top10 ----------
    // ---------- Top10 ----------
private function getTop10Cafes(): array
{
    // รวมรีวิวเฉพาะที่สถานะ approved (ไม่ใช้ HAVING)
    $reviewAgg = DB::table('reviews as r')
        ->selectRaw("
            r.cafe_id,
            AVG(CASE WHEN LOWER(COALESCE(r.status,'approved'))='approved' THEN r.rating END)    AS avg_rating,
            SUM(CASE WHEN LOWER(COALESCE(r.status,'approved'))='approved' THEN 1 ELSE 0 END)   AS review_count
        ")
        ->groupBy('r.cafe_id');

    // จับคู่กับร้านที่ approved แล้วจัดอันดับ (ให้ร้านที่ไม่มีรีวิวได้คะแนน 0 แต่ยังติดลิสต์)
    $rows = DB::table('cafes as c')
        ->leftJoinSub($reviewAgg, 'ra', fn($j) => $j->on('ra.cafe_id','=','c.cafe_id'))
        ->whereRaw("LOWER(COALESCE(c.status,''))='approved'")
        ->selectRaw("
            c.cafe_id, c.cafe_name, c.address, c.lat, c.lng, c.phone,
            COALESCE(ra.avg_rating, 0)   AS avg_rating,
            COALESCE(ra.review_count, 0) AS review_count
        ")
        ->orderByDesc('avg_rating')
        ->orderByDesc('review_count')
        ->orderByDesc('c.cafe_id')
        ->limit(10)
        ->get()
        ->all();

    // Fallback: ถ้าไม่พบร้าน approved เลย (กรณีฐานข้อมูลว่าง)
    if (empty($rows)) {
        $rows = DB::table('cafes as c')
            ->whereRaw("LOWER(COALESCE(c.status,''))='approved'")
            ->select('c.cafe_id','c.cafe_name','c.address','c.lat','c.lng','c.phone')
            ->orderByDesc('c.created_at')
            ->orderByDesc('c.cafe_id')
            ->limit(10)
            ->get()
            ->map(function ($c) {
                $c->avg_rating   = 0;
                $c->review_count = 0;
                return $c;
            })
            ->all();
    }

    Log::info('[Top10] result', ['count' => count($rows), 'sample' => array_slice(array_map(fn($x)=>$x->cafe_name,$rows),0,3)]);
    return $rows;
}


    // ---------- Filters (สไตล์/เปิดใหม่ ฯลฯ) ----------
    private function findCafesByFilter(string $type): array
    {
        // สไตล์
        if (str_starts_with($type, 'style:')) {
            $kw = trim(mb_substr($type, 6));
            if ($kw === '') return [];
            $like = "%{$kw}%";

            return DB::table('cafes')
                ->whereRaw("LOWER(COALESCE(status,''))='approved'")
                ->where('address', 'LIKE', '%สุรินทร์%') // คงกรองพื้นที่ไว้ เพราะอันนี้ในรูปใช้งานได้ปกติ
                ->where(function($q) use ($like) {
                    $q->whereRaw("(JSON_VALID(cafe_styles) AND JSON_SEARCH(cafe_styles, 'one', ?) IS NOT NULL)", [$like])
                      ->orWhere('other_style', 'LIKE', $like);
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
                return DB::table('cafes')
                    ->whereRaw("LOWER(COALESCE(status,''))='approved'")
                    ->whereRaw('CAST(COALESCE(is_new_opening,0) AS UNSIGNED)=1')
                    ->select('cafe_id','cafe_name','address','lat','lng','phone')
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
