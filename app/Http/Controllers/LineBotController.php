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
                $text = trim((string)($event['message']['text'] ?? ''));

                // Rich menu toggle
                if (in_array($text, ['เมนู','menu','เริ่มต้น'], true)) {
                    $this->setUserRichMenu($userId, $this->richMain);
                    $this->replyText($replyToken, "เปิดเมนูหลักแล้วครับ 😊");
                    continue;
                }
                if (in_array($text, ['FAQ','คำถามที่พบบ่อย','เมนูคำตอบ'], true)) {
                    $this->setUserRichMenu($userId, $this->richFaq);
                    $this->replyText($replyToken, "เมนู FAQ พร้อมใช้งานครับ ❓\nพิมพ์: ที่จอดรถ / FreeWiFi / มีห้องประชุม / ทำงานได้ / ย่อมเยา / เงียบ / แอร์ / เปิดอยู่ตอนนี้ / เปิดใหม่");
                    continue;
                }

                // ===== FAQ keywords -> search cafes =====
                $faqMap = [
                    // wifi
                    'freewifi' => 'wifi', 'FreeWiFi' => 'wifi', 'ไวไฟ' => 'wifi', 'wifi' => 'wifi',
                    // parking
                    'ที่จอดรถ' => 'parking', 'จอดรถ' => 'parking', 'parking' => 'parking',
                    // meeting / work
                    'มีห้องประชุม' => 'meeting', 'มีห้องประชุมทำงานได้' => 'meeting', 'ห้องประชุม' => 'meeting',
                    'ทำงานได้' => 'meeting', 'นั่งทำงาน' => 'meeting', 'work' => 'meeting', 'cowork' => 'meeting',
                    // cheap
                    'ย่อมเยา' => 'cheap', 'คาเฟ่ราคาย่อมเยา' => 'cheap', 'ถูก' => 'cheap', 'ประหยัด' => 'cheap',
                    // quiet
                    'เงียบ' => 'quiet', 'อ่านหนังสือ' => 'quiet', 'สงบ' => 'quiet',
                    // aircon
                    'แอร์' => 'aircon', 'เครื่องปรับอากาศ' => 'aircon', 'aircon' => 'aircon',
                    // open now / new
                    'เปิดอยู่ตอนนี้' => 'open_now', 'เปิดอยู่ตอนนี่' => 'open_now',
                    'เปิดใหม่' => 'new',
                ];
                $faqKey = $faqMap[$text] ?? null;
                if ($faqKey) {
                    $cafes = $this->findCafesByFilter('faq:' . $faqKey);
                    Log::info('[FAQ] key', ['key' => $faqKey, 'count' => count($cafes)]);

                    $bubbles = [];
                    if (!empty($cafes)) {
                        foreach (array_slice($cafes, 0, 10) as $c) {
                            $note = $this->faqNoteLabel($faqKey);
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
                            "ลองค้นหาบนเว็บไซต์ได้เลยครับ",
                            "https://nongchangsaren.com/"
                        );
                    }
                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "ผลลัพธ์: {$text}", ["type"=>"carousel","contents"=>$bubbles]);
                    continue;
                }

                // ===== Recommend menu =====
                if ($this->isRecommendTrigger($text)) {
                    $this->replyFlex($replyToken, "เมนูแนะนำคาเฟ่เมืองสุรินทร์", $this->menuRecommendCarousel());
                    continue;
                }

                // ===== Top10 =====
                if (in_array($text, ['คาเฟ่Top10','Top10','Top 10','top10'], true)) {
                    $cafes = $this->getTop10Cafes();
                    $bubbles = [];

                    if (!empty($cafes)) {
                        foreach ($cafes as $c) {
                            $note = '⭐ '.number_format((float)($c->avg_rating ?? 0), 1).' ('.(int)($c->review_count ?? 0).' รีวิว)';
                            $bubbles[] = $this->bubbleBasic(
                                $c->cafe_name ?? '-', $c->address ?? '-', $note,
                                $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null
                            );
                        }
                    } else {
                        $bubbles[] = $this->bubbleInfo("ยังไม่มีข้อมูล Top10","ลองดูข้อมูลล่าสุดบนเว็บไซต์","https://nongchangsaren.com/");
                    }

                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "คาเฟ่ Top 10 เมืองสุรินทร์", ["type"=>"carousel","contents"=>$bubbles]);
                    continue;
                }

                // ===== Style: "สไตล์:xxx" =====
                if (mb_strpos($text, 'สไตล์:') === 0) {
                    $styleName = trim(mb_substr($text, 6));
                    $cafes = $this->findCafesByFilter('style:'.$styleName);

                    $bubbles = [];
                    if (!empty($cafes)) {
                        foreach (array_slice($cafes, 0, 9) as $c) {
                            $mapUrl = ($c->lat !== null && $c->lng !== null)
                                ? "https://maps.google.com/?q={$c->lat},{$c->lng}"
                                : "https://www.google.com/maps/search/".urlencode(($c->cafe_name ?? '').' '.($c->address ?? ''));
                            $bubbles[] = $this->bubbleBasic(
                                $c->cafe_name ?? '-', $c->address ?? '-', '🎨 สไตล์: '.$styleName,
                                $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null, $mapUrl
                            );
                        }
                    } else {
                        $bubbles[] = $this->bubbleInfo("ยังไม่พบตามสไตล์","ลองเลือกสไตล์อื่นหรือดูทั้งหมดบนเว็บ","https://nongchangsaren.com/");
                    }
                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "คาเฟ่สไตล์: {$styleName}", ["type"=>"carousel","contents"=>$bubbles]);
                    continue;
                }

                // ===== New =====
                if ($text === 'เปิดใหม่') {
                    $cafes = $this->findCafesByFilter('new');
                    $bubbles = [];

                    if (!empty($cafes)) {
                        foreach (array_slice($cafes, 0, 10) as $c) {
                            $bubbles[] = $this->bubbleBasic(
                                $c->cafe_name ?? '-', $c->address ?? '-', "🆕 ร้านเปิดใหม่",
                                $c->phone ?? '-', $c->lat ?? null, $c->lng ?? null
                            );
                        }
                    } else {
                        $bubbles[] = $this->bubbleInfo("ยังไม่พบร้านเปิดใหม่","ลองดูบนเว็บไซต์เพื่ออัปเดตเพิ่มเติม","https://nongchangsaren.com/");
                    }

                    $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
                    $this->replyFlex($replyToken, "คาเฟ่เปิดใหม่ เมืองสุรินทร์", ["type"=>"carousel","contents"=>$bubbles]);
                    continue;
                }

                // Default help
                $this->replyText(
                    $replyToken,
                    "พิมพ์ “แนะนำคาเฟ่เมืองสุรินทร์” เพื่อเปิดเมนูแนะนำ หรือ “เมนู” เพื่อเปิดเมนูหลักครับ\nหรือถาม FAQ: ที่จอดรถ / FreeWiFi / มีห้องประชุม / ทำงานได้ / ย่อมเยา / เงียบ / แอร์ / เปิดอยู่ตอนนี้ / เปิดใหม่"
                );
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
                    SELECT c.cafe_id, c.cafe_name, c.address, c.lat, c.lng, c.phone,
                           (6371 * acos(
                               cos(radians(?)) * cos(radians(c.lat)) *
                               cos(radians(c.lng) - radians(?)) +
                               sin(radians(?)) * sin(radians(c.lat))
                           )) AS distance
                    FROM cafes c
                    WHERE LOWER(COALESCE(c.status,''))='approved'
                    HAVING distance < 5
                    ORDER BY distance ASC, cafe_id DESC
                    LIMIT 5
                ", [$lat, $lng, $lat]);

                $bubbles = [];
                if (!empty($cafes)) {
                    foreach ($cafes as $c) {
                        $bubbles[] = $this->bubbleBasic(
                            $c->cafe_name, $c->address, "📍 ห่าง ".round($c->distance, 2)." กม.",
                            $c->phone, $c->lat, $c->lng
                        );
                    }
                } else {
                    $bubbles[] = $this->bubbleInfo("ไม่พบคาเฟ่ในรัศมี 5 กม.","ดูแผนที่ร้านทั้งหมดบนเว็บไซต์","https://nongchangsaren.com/");
                }
                $bubbles[] = $this->bubbleMore('ดูเพิ่มเติมบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');
                $this->replyFlex($replyToken, "คาเฟ่ใกล้คุณ", ["type"=>"carousel","contents"=>$bubbles]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ---------- Helpers ----------
    private function isRecommendTrigger(string $text): bool
    {
        $raw = trim($text);
        $noSpace = preg_replace('/\s+/u', '', $raw);
        $aliases = ['แนะนำคาเฟ่เมืองสุรินทร์','เมนูแนะนำคาเฟ่','คาเฟ่เมืองสุรินทร์','recommend'];
        $aliasesNoSpace = array_map(fn($s)=>preg_replace('/\s+/u','',$s), $aliases);

        if (in_array($raw, $aliases, true) || in_array($noSpace, $aliasesNoSpace, true)) return true;
        $hasRecommend = mb_stripos($raw, 'แนะนำคาเฟ่') !== false;
        $hasSurin1    = mb_stripos($raw, 'เมืองสุรินทร์') !== false;
        $hasSurin2    = mb_stripos($raw, 'สุรินทร์') !== false;
        if ($hasRecommend && ($hasSurin1 || $hasSurin2)) return true;
        if (mb_stripos($raw, 'recommend') !== false && ($hasSurin1 || $hasSurin2)) return true;
        return false;
    }

    private function setUserRichMenu(?string $userId, ?string $richMenuId): void
    {
        if (!$userId || !$richMenuId) return;
        Http::withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->post("https://api.line.me/v2/bot/user/{$userId}/richmenu/{$richMenuId}");
    }

    private function menuRecommendCarousel(): array
    {
        $bubbles = [];

        // Bubble 1: main
        $bubbles[] = [
            "type" => "bubble",
            "body" => [
                "type" => "box","layout" => "vertical","paddingAll" => "16px","spacing" => "12px",
                "contents" => [
                    ["type"=>"text","text"=>"แนะนำคาเฟ่เมืองสุรินทร์","weight"=>"bold","size"=>"lg"],
                    ["type"=>"text","text"=>"เลือกหมวดหรือสไตล์ที่ชอบได้เลยครับ","size"=>"sm","color"=>"#666666","wrap"=>true],
                    ["type"=>"separator","margin"=>"12px"]
                ]
            ],
            "footer" => [
                "type"=>"box","layout"=>"vertical","spacing"=>"md","paddingAll"=>"12px","flex"=>0,
                "contents" => [
                    ["type"=>"button","style"=>"primary","action"=>["type"=>"message","label"=>"🔥 Top10","text"=>"คาเฟ่Top10"],"color"=>"#1E88E5"],
                    ["type"=>"button","style"=>"primary","action"=>["type"=>"message","label"=>"✨ เปิดใหม่","text"=>"เปิดใหม่"],"color"=>"#2ECC71"],
                    ["type"=>"button","style"=>"secondary","action"=>["type"=>"message","label"=>"📶 Free Wi-Fi","text"=>"FreeWiFi"]],
                    ["type"=>"button","style"=>"secondary","action"=>["type"=>"message","label"=>"🅿 ที่จอดรถ","text"=>"ที่จอดรถ"]],
                    ["type"=>"button","style"=>"secondary","action"=>["type"=>"message","label"=>"🏢 ห้องประชุม/ทำงาน","text"=>"มีห้องประชุมทำงานได้"]],
                    ["type"=>"button","style"=>"secondary","action"=>["type"=>"message","label"=>"💸 ย่อมเยา","text"=>"ย่อมเยา"]],
                    ["type"=>"button","style"=>"secondary","action"=>["type"=>"message","label"=>"🟢 เปิดอยู่ตอนนี้","text"=>"เปิดอยู่ตอนนี้"]],
                ]
            ],
            "styles" => ["footer" => ["separator" => true]]
        ];

        // Bubble 2: to website
        $bubbles[] = $this->bubbleMore('ดูทั้งหมดบนเว็บไซต์','เปิดเว็บ น้องช้างสะเร็น','https://nongchangsaren.com/');

        return ["type"=>"carousel","contents"=>$bubbles];
    }

    private function getTop10Cafes(): array
    {
        $base = DB::table('cafes as c')
            ->leftJoin('reviews as r','r.cafe_id','=','c.cafe_id')
            ->whereRaw("LOWER(COALESCE(c.status,''))='approved'");

        $rows = (clone $base)
            ->where('c.address','LIKE','%สุรินทร์%')
            ->selectRaw("
                c.cafe_id, c.cafe_name, c.address, c.lat, c.lng, c.phone,
                COALESCE(AVG(r.rating),0) AS avg_rating,
                COUNT(r.rating)          AS review_count
            ")
            ->groupBy('c.cafe_id','c.cafe_name','c.address','c.lat','c.lng','c.phone')
            ->orderByDesc('avg_rating')->orderByDesc('review_count')->orderByDesc('c.cafe_id')
            ->limit(10)->get()->all();

        if (empty($rows)) {
            $rows = (clone $base)
                ->selectRaw("
                    c.cafe_id, c.cafe_name, c.address, c.lat, c.lng, c.phone,
                    COALESCE(AVG(r.rating),0) AS avg_rating,
                    COUNT(r.rating)          AS review_count
                ")
                ->groupBy('c.cafe_id','c.cafe_name','c.address','c.lat','c.lng','c.phone')
                ->orderByDesc('avg_rating')->orderByDesc('review_count')->orderByDesc('c.cafe_id')
                ->limit(10)->get()->all();
        }
        return $rows;
    }

    private function findCafesByFilter(string $type): array
    {
        // STYLE
        if (str_starts_with($type, 'style:')) {
            $kw = trim(mb_substr($type, 6));
            if ($kw === '') return [];
            $like = "%{$kw}%";

            return DB::table('cafes')
                ->whereRaw("LOWER(COALESCE(status,''))='approved'")
                ->where('address','LIKE','%สุรินทร์%')
                ->where(function($q) use ($like) {
                    $q->whereRaw("(JSON_VALID(cafe_styles) AND JSON_SEARCH(cafe_styles,'one', ?) IS NOT NULL)", [$like])
                      ->orWhere('other_style','LIKE',$like);
                })
                ->select('cafe_id','cafe_name','address','lat','lng','phone')
                ->orderByDesc('updated_at')->orderByDesc('cafe_id')
                ->limit(20)->get()->all();
        }

        // FAQ
        if (str_starts_with($type, 'faq:')) {
            $key = substr($type, 4);

            $q = DB::table('cafes')->whereRaw("LOWER(COALESCE(status,''))='approved'");
            // ถ้าคุณไม่อยากจำกัดพื้นที่ ให้คอมเมนต์บรรทัดนี้ออก
            $q->where('address','LIKE','%สุรินทร์%');

            $jsonCols = ['facilities','other_services','tags','notes'];
            $likeAny = function($builder, array $phrases) use ($jsonCols) {
                $builder->where(function($qq) use ($phrases, $jsonCols) {
                    foreach ($jsonCols as $col) {
                        $qq->orWhereRaw("(JSON_VALID($col) AND (".
                            implode(' OR ', array_map(fn($p)=>"JSON_SEARCH($col,'one', ?) IS NOT NULL", $phrases))
                        ."))", $phrases);
                        foreach ($phrases as $p) {
                            $qq->orWhere($col, 'LIKE', "%$p%");
                        }
                    }
                });
            };

            switch ($key) {
                case 'wifi':
                    $q->where(function($qq) use ($likeAny) {
                        $likeAny($qq, ['%Wi-Fi%','%WiFi%','%wifi%','%ไวไฟ%']);
                        // ถ้ามีคอลัมน์บูลีน
                        $qq->orWhereRaw('CAST(COALESCE(has_wifi,0) AS UNSIGNED)=1');
                    });
                    break;

                case 'parking':
                    $q->where(function($qq) use ($likeAny) {
                        $likeAny($qq, ['%ที่จอดรถ%','%จอดรถ%','%parking%','%ที่จอด%']);
                        $qq->orWhereRaw('CAST(COALESCE(has_parking,0) AS UNSIGNED)=1');
                    });
                    break;

                case 'meeting':
                    $q->where(function($qq) use ($likeAny) {
                        $likeAny($qq, ['%ห้องประชุม%','%ประชุม%','%meeting%','%co-work%','%cowork%','%ทำงาน%']);
                        $qq->orWhereRaw('CAST(COALESCE(has_meeting_room,0) AS UNSIGNED)=1');
                        $qq->orWhereRaw('CAST(COALESCE(work_friendly,0) AS UNSIGNED)=1');
                    });
                    break;

                case 'cheap':
                    $q->orderByRaw("
                        CASE
                          WHEN price_range REGEXP 'ย่อมเยา|ถูก|ประหยัด|cheap|low' THEN 1
                          ELSE 9
                        END ASC
                    ");
                    break;

                case 'quiet':
                    $q->where(function($qq) use ($likeAny) {
                        $likeAny($qq, ['%เงียบ%','%สงบ%','%อ่านหนังสือ%','%study%','%quiet%']);
                    });
                    break;

                case 'aircon':
                    $q->where(function($qq) use ($likeAny) {
                        $likeAny($qq, ['%แอร์%','%เครื่องปรับอากาศ%','%aircon%','%air conditioner%']);
                        $qq->orWhereRaw('CAST(COALESCE(has_aircon,0) AS UNSIGNED)=1');
                    });
                    break;

                case 'open_now':
                    $now = Carbon::now('Asia/Bangkok')->format('H:i:s');
                    $q->whereNotNull('open_time')->whereNotNull('close_time')
                      ->where(function($qq) use ($now) {
                          // ช่วงเวลาปกติ
                          $qq->where(function($z) use ($now){
                              $z->whereRaw('close_time >= open_time')
                                ->whereRaw('? BETWEEN open_time AND close_time', [$now]);
                          })
                          // ร้านที่ปิดข้ามวัน (close < open)
                          ->orWhere(function($z) use ($now){
                              $z->whereRaw('close_time < open_time')
                                ->where(function($w) use ($now){
                                    $w->whereRaw('? >= open_time', [$now])
                                      ->orWhereRaw('? <= close_time', [$now]);
                                });
                          });
                      });
                    break;

                default:
                    // ไม่รู้จักคีย์
                    return [];
            }

            return $q->select('cafe_id','cafe_name','address','lat','lng','phone')
                     ->orderByDesc('updated_at')->orderByDesc('cafe_id')
                     ->limit(30)->get()->all();
        }

        // NEW
        if ($type === 'new') {
            return DB::table('cafes')
                ->whereRaw("LOWER(COALESCE(status,''))='approved'")
                ->whereRaw('CAST(COALESCE(is_new_opening,0) AS UNSIGNED)=1')
                ->select('cafe_id','cafe_name','address','lat','lng','phone')
                ->orderByDesc('updated_at')->orderByDesc('created_at')->orderByDesc('cafe_id')
                ->limit(20)->get()->all();
        }

        return [];
    }

    private function faqNoteLabel(string $key): string
    {
        return match ($key) {
            'wifi'      => '📶 Free Wi-Fi',
            'parking'   => '🅿️ มีที่จอดรถ',
            'meeting'   => '🏢 มีห้องประชุม/ทำงานได้',
            'cheap'     => '💸 ราคาย่อมเยา',
            'quiet'     => '🤫 บรรยากาศเงียบ',
            'aircon'    => '❄️ ห้องแอร์',
            'open_now'  => '🟢 เปิดอยู่ตอนนี้',
            'new'       => '🆕 ร้านเปิดใหม่',
            default     => '',
        };
    }

    // ---------- Flex ----------
    private function bubbleBasic($name, $addr, $sub, $phone, $lat, $lng, $mapUrl = null): array
    {
        $mapUrl    = $mapUrl ?: (($lat !== null && $lng !== null)
            ? "https://maps.google.com/?q={$lat},{$lng}"
            : "https://www.google.com/maps/search/".urlencode((string)$name.' '.(string)$addr));
        $phoneText = $phone ?: "ไม่มีข้อมูล";
        $telUri    = $this->buildTelUri($phone);

        $buttons = [[
            "type"=>"button","style"=>"primary","height"=>"sm",
            "action"=>["type"=>"uri","label"=>"เปิดแผนที่","uri"=>$mapUrl],
            "color"=>"#1E88E5"
        ]];
        if ($telUri) {
            $buttons[] = ["type"=>"button","style"=>"secondary","height"=>"sm",
                "action"=>["type"=>"uri","label"=>"โทรเลย","uri"=>$telUri]];
        }

        return [
            "type"=>"bubble",
            "body"=>[
                "type"=>"box","layout"=>"vertical","paddingAll"=>"16px","spacing"=>"12px",
                "contents"=>[
                    ["type"=>"box","layout"=>"vertical","spacing"=>"8px","contents"=>[
                        ["type"=>"text","text"=>(string)$name,"weight"=>"bold","size"=>"lg","wrap"=>true],
                        ["type"=>"box","layout"=>"horizontal","spacing"=>"sm","contents"=>[[
                            "type"=>"box","layout"=>"baseline","backgroundColor"=>"#EEF7FF","cornerRadius"=>"6px","paddingAll"=>"6px",
                            "contents"=>[["type"=>"text","text"=>(string)$sub,"size"=>"xs","color"=>"#1E88E5","wrap"=>true]]
                        ]]]
                    ]],
                    ["type"=>"box","layout"=>"horizontal","spacing"=>"sm","contents"=>[
                        ["type"=>"text","text"=>"📍","size"=>"sm","flex"=>0],
                        ["type"=>"text","text"=>(string)($addr ?? "-"),"size"=>"sm","color"=>"#555555","wrap"=>true]
                    ]],
                    ["type"=>"box","layout"=>"horizontal","spacing"=>"sm","contents"=>[
                        ["type"=>"text","text"=>"☎","size"=>"sm","flex"=>0],
                        ["type"=>"text","text"=>$phoneText,"size"=>"sm","color"=>"#555555","wrap"=>true]
                    ]],
                    ["type"=>"separator","margin"=>"12px"]
                ]
            ],
            "footer"=>["type"=>"box","layout"=>"horizontal","spacing"=>"md","paddingAll"=>"12px","contents"=>$buttons,"flex"=>0],
            "styles"=>["footer"=>["separator"=>true]]
        ];
    }

    private function bubbleMore(string $title, string $sub, string $url): array
    {
        return [
            "type"=>"bubble",
            "body"=>["type"=>"box","layout"=>"vertical","paddingAll"=>"16px","spacing"=>"10px","contents"=>[
                ["type"=>"text","text"=>$title,"weight"=>"bold","size"=>"lg","wrap"=>true],
                ["type"=>"text","text"=>$sub,"size"=>"sm","color"=>"#666666","wrap"=>true],
                ["type"=>"separator","margin"=>"12px"]
            ]],
            "footer"=>["type"=>"box","layout"=>"vertical","spacing"=>"md","paddingAll"=>"12px","flex"=>0,"contents"=>[[
                "type"=>"button","style"=>"primary","action"=>["type"=>"uri","label"=>"ไปที่เว็บไซต์","uri"=>$url],"color"=>"#1E88E5"
            ]]],
            "styles"=>["footer"=>["separator"=>true]]
        ];
    }

    private function bubbleInfo(string $title, string $sub, string $url): array
    {
        return $this->bubbleMore($title, $sub, $url);
    }

    // ---------- Reply ----------
    private function replyText(string $replyToken, string $text): void
    {
        $this->replyMessage($replyToken, ["type"=>"text","text"=>$text]);
    }
    private function replyFlex(string $replyToken, string $altText, array $contents): void
    {
        $this->replyMessage($replyToken, ["type"=>"flex","altText"=>$altText,"contents"=>$contents]);
    }
    private function replyMessage(string $replyToken, array $message): void
    {
        Http::withHeaders([
            'Content-Type'=>'application/json',
            'Authorization'=>'Bearer '.$this->token,
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken'=>$replyToken,
            'messages'=>[$message]
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
