{{-- resources/views/cafes/by-features.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ค้นหาคาเฟ่ตามคุณสมบัติ</title>
  <style>
    :root{ --brand:#e63946; --text:#111827; --muted:#667085; --chip:#e0f2fe; --chip-br:#bae6fd; }
    *{box-sizing:border-box}
    body{margin:0; font-family:"Prompt",system-ui,-apple-system,Segoe UI,Roboto,sans-serif; background:linear-gradient(135deg,#fdfbfb,#ebedee); color:var(--text)}
    .wrap{width:min(1100px,94%); margin:28px auto 72px}
    h1.title{font:700 1.95rem/1.2 "Prompt",system-ui; text-align:center; margin:10px 0 6px; color:#444}
    h1.title span{color:var(--brand)}
    .update{color:var(--muted); text-align:center; margin:0 0 18px; font-size:.95rem}
    .shortcuts{display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin:12px auto 26px}
    .shortcuts button{background:var(--brand); color:#fff; border:0; border-radius:999px; padding:10px 14px; font:700 .9rem/1 "Prompt"; cursor:pointer; box-shadow:0 6px 16px rgba(230,57,70,.28); transition:.18s}
    .shortcuts button:hover{filter:brightness(.95); transform:translateY(-1px)}
    .section{background:#fff; border-radius:16px; box-shadow:0 10px 26px rgba(0,0,0,.08); padding:18px; margin:16px 0}
    .section h2{margin:0 0 6px; font:800 1.25rem/1.25 "Prompt"}
    .section h2 .tag{display:inline-block; margin-inline-start:6px; font:800 .75rem/1; letter-spacing:.3px; color:#fff; background:var(--brand); padding:6px 8px; border-radius:10px}
    .section p.desc{margin:0 0 14px; color:var(--muted)}
    .grid{display:grid; gap:14px; grid-template-columns: repeat(auto-fill, minmax(240px,1fr))}
    .card{background:#fff; border:1px solid #eef2f7; border-radius:14px; overflow:hidden; display:flex; flex-direction:column; transition:.18s}
    .card:hover{transform:translateY(-3px); box-shadow:0 12px 28px rgba(0,0,0,.10)}
    .media{position:relative; aspect-ratio:16/10; background:#f3f5f7}
    .media img{width:100%; height:100%; object-fit:cover; display:block}
    .badge{position:absolute; left:10px; top:10px; background:#0ea5e9; color:#fff; padding:6px 10px; border-radius:999px; font:700 .75rem/1 "Prompt"; box-shadow:0 6px 16px rgba(14,165,233,.35)}
    .body{padding:12px 12px 14px; display:flex; flex-direction:column; gap:8px}
    .name{margin:0; font:800 1rem/1.3 "Prompt"}
    .chips{display:flex; flex-wrap:wrap; gap:8px}
    .chip{font:700 .78rem/1 "Prompt"; color:#155e75; background:var(--chip); border:1px solid var(--chip-br); padding:6px 8px; border-radius:999px}
    .actions{margin-top:4px}
    .btn{display:inline-block; text-decoration:none; background:#111827; color:#fff; padding:9px 12px; border-radius:10px; font:800 .85rem/1; transition:.15s}
    .btn:hover{background:#000}
    .btn.disabled{background:#cbd5e1; pointer-events:none}
    .empty{border:2px dashed #e2e8f0; background:#f8fafc; color:#64748b; border-radius:14px; padding:18px; text-align:center; font-weight:700}
    .debug{font: 500 .85rem/1.5 ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace; background:#0b1020; color:#92f5d9; padding:14px; border-radius:10px; white-space:pre-wrap; overflow:auto; margin:16px 0}
    @media (max-width:520px){
      h1.title{font-size:1.6rem}
      .shortcuts{gap:8px}
      .shortcuts button{padding:8px 12px; font-size:.8rem}
    }
  </style>
</head>
<body>
<div class="wrap">
  <h1 class="title">✨ ค้นหา <span>คาเฟ่ตามคุณสมบัติ</span></h1>
  <p class="update">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>

  <div class="shortcuts">
    <button onclick="jumpTo('wifi')">💻 Wi-Fi</button>
    <button onclick="jumpTo('meeting')">🏢 ห้องประชุม</button>
    <button onclick="jumpTo('cheap')">💰 ราคาย่อมเยา</button>
    <button onclick="jumpTo('kids')">🧸 โซนเด็กเล่น</button>
    <button onclick="jumpTo('parking')">🚗 ที่จอดรถ</button>
    <button onclick="jumpTo('minimal')">🎨 มินิมอล</button>
  </div>

  @php
    use Illuminate\Support\Str;

    // สร้าง URL ให้ตรงกับ http/https ปัจจุบัน
    $scheme_asset = function(string $relative){
      $rel = ltrim($relative, '/');
      return request()->isSecure() ? secure_asset($rel) : asset($rel);
    };

    // ตัวแก้ปัญหารูป: หาไฟล์ตามลำดับความน่าจะเป็น (หลายโฟลเดอร์ หลายนามสกุล หลายชื่อ)
    $IMG_DIRS = ['images/Top10_', 'images/cafes', 'images'];
    $IMG_EXTS = ['png','jpg','jpeg','webp','gif'];

    $resolve_image = function(string $preferred, string $name, string $alias) use ($scheme_asset, $IMG_DIRS, $IMG_EXTS){
      $candidates = [];
      $preferred = ltrim($preferred, '/');
      if ($preferred !== '') {
        $candidates[] = $preferred;
        // ลองนามสกุลอื่นด้วย ถ้าตัดนามสกุลเดิมได้
        if (preg_match('/\.[A-Za-z0-9]+$/', $preferred)) {
          $base = preg_replace('/\.[A-Za-z0-9]+$/', '', $preferred);
          foreach ($IMG_EXTS as $ext) $candidates[] = $base.'.'.$ext;
        }
      }

      // slug จาก alias และชื่อร้าน
      $slugs = array_values(array_unique(array_filter([
        trim($alias),
        Str::slug($name, '-')
      ])));

      foreach ($slugs as $slug) {
        foreach ($IMG_DIRS as $dir) {
          foreach ($IMG_EXTS as $ext) {
            $candidates[] = rtrim($dir,'/').'/'.$slug.'.'.$ext;
          }
        }
      }

      // ตรวจตัวเลือกทั้งหมด
      $seen = [];
      foreach ($candidates as $rel) {
        if (isset($seen[$rel])) continue; $seen[$rel]=1;
        $abs = public_path($rel);
        if (file_exists($abs)) return $scheme_asset($rel);
      }

      // glob แบบหลวม: หาไฟล์ในไดเรกทอรีด้วยคีย์เวิร์ดจากชื่อ
      $keywords = array_values(array_unique(array_filter([
        $alias,
        ...array_map(fn($w)=>Str::slug($w,'-'), preg_split('/\s+/', preg_replace('/[^\p{L}\p{N}\s-]+/u',' ', $name)))
      ])));
      $keywords = array_slice($keywords, 0, 4); // จำกัด

      foreach ($IMG_DIRS as $dir) {
        $absDir = public_path($dir);
        if (!is_dir($absDir)) continue;
        $files = @scandir($absDir) ?: [];
        foreach ($files as $fn) {
          if ($fn === '.' || $fn === '..') continue;
          $lower = mb_strtolower($fn);
          foreach ($keywords as $kw) {
            if ($kw && (str_contains($lower, mb_strtolower($kw)))) {
              foreach ($IMG_EXTS as $ext) {
                if (preg_match('/\.'.preg_quote($ext,'/').'$/i', $fn)) {
                  return $scheme_asset(rtrim($dir,'/').'/'.$fn);
                }
              }
            }
          }
        }
      }

      // ใช้ placeholder ถ้ามี
      $placeholderRel = 'images/placeholder-cafe.jpg';
      if (file_exists(public_path($placeholderRel))) return $scheme_asset($placeholderRel);

      // สุดท้าย SVG data URL (ไม่มีวัน 404)
      $svg = rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500"><rect fill="#f1f5f9" width="100%" height="100%"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-size="24" font-family="Arial, sans-serif">No Image</text></svg>');
      return "data:image/svg+xml;charset=UTF-8,".$svg;
    };

    /**
     * ข้อมูลคาเฟ่
     * - image: ใส่ "ภาพที่อยากให้ใช้ก่อน" ได้ (ถ้าไม่เจอ โค้ดจะลองหาให้เอง)
     * - alias: คีย์สั้น ๆ ช่วยจับคู่ชื่อไฟล์ เช่น little-elephant, dammachat
     */
    $cafes = [
      'follow' => [
        'name' => 'Follow the Sun.Home Cafe’',
        'alias'=> 'follow-sun',
        'image'=> '/images/Top10_/follow-sun.cafe.png', // อันนี้ขึ้นแล้วตามที่แจ้ง
        'url'  => url('/cafes/1'),
        'features' => ['wifi','cheap','minimal'],
      ],
      'little-elephant' => [
        'name' => 'Little Elephant Patisserie & Special Coffee Bar',
        'alias'=> 'little-elephant',
        'image'=> '/images/Top10_/little-elephant.png', // ถ้าไฟล์นี้ไม่มี โค้ดจะค้นหาในหลายรูปแบบให้
        'url'  => url('/cafes/2'),
        'features' => ['wifi','meeting','parking'],
      ],
      'dammachat' => [
        'name' => 'ดัมมะชาติ Eatery & Coffee by Jaokao Vol.3',
        'alias'=> 'dammachat',
        'image'=> '/images/Top10_/dammachat.png',
        'url'  => url('/cafes/4'),
        'features' => ['wifi','minimal'],
      ],
      'journey' => [
        'name' => 'Journe’y Roastery & Special Coffee',
        'alias'=> 'journey',
        'image'=> '/images/Top10_/journey.png',
        'url'  => url('/cafes/5'),
        'features' => ['wifi','parking'],
      ],
      'craft' => [
        'name' => 'Craft Cafe Surin',
        'alias'=> 'craft-surin',
        'image'=> '/images/Top10_/craft-surin.png',
        'url'  => url('/cafes/12'),
        'features' => ['wifi','cheap','parking'],
      ],
      'charoensuk' => [
        'name' => 'CHAROENSUK Café เจริญสุข คาเฟ่',
        'alias'=> 'charoensuk',
        'image'=> '/images/Top10_/charoensuk.png',
        'url'  => url('/cafes/9'),
        'features' => ['cheap','parking'],
      ],
      'life' => [
        'name' => 'Life Coffee at Home',
        'alias'=> 'life',
        'image'=> '/images/Top10_/life.png',
        'url'  => url('/cafes/11'),
        'features' => ['cheap','parking'],
      ],
      'healing' => [
        'name' => 'Healing Cafe',
        'alias'=> 'healing',
        'image'=> '/images/Top10_/healing.png',
        'url'  => url('/cafes/14'),
        'features' => ['minimal','cheap'],
      ],
      'kind' => [
        'name' => 'Kind Cafe',
        'alias'=> 'kind',
        'image'=> '/images/Top10_/kind.png',
        'url'  => url('/cafes/15'),
        'features' => ['minimal'],
      ],
      'parich' => [
        'name' => 'Parich พาริช คาเฟ่สุรินทร์',
        'alias'=> 'parich',
        'image'=> '/images/Top10_/parich.png',
        'url'  => url('/cafes/16'),
        'features' => ['minimal'],
      ],
      'bscups' => [
        'name' => 'B’s cups coffee',
        'alias'=> 'bscups',
        'image'=> '/images/Top10_/bscups.png',
        'url'  => url('/cafes/18'),
        'features' => ['meeting'],
      ],
    ];

    // หมวด
    $categories = [
      'wifi'    => ['title' => '💻 Wi-Fi', 'desc' => 'คาเฟ่ต่อเน็ตฟรี ทำงาน/เรียนออนไลน์ลื่นไหล', 'keys' => ['follow','little-elephant','dammachat','journey','craft']],
      'meeting' => ['title' => '🏢 ห้องประชุม', 'desc' => 'มีห้องประชุม/โซนเงียบ เหมาะนัดคุยงาน', 'keys' => ['little-elephant','bscups']],
      'cheap'   => ['title' => '💰 ราคาย่อมเยา', 'desc' => 'เมนูเข้าถึงง่าย ราคาสบายกระเป๋า', 'keys' => ['follow','craft','charoensuk','life','healing']],
      'kids'    => ['title' => '🧸 โซนเด็กเล่น', 'desc' => 'มุมสำหรับเด็ก ๆ เล่นเพลิน ผู้ปกครองนั่งชิล', 'keys' => []],
      'parking' => ['title' => '🚗 ที่จอดรถ', 'desc' => 'มีที่จอดรถยนต์/มอเตอร์ไซค์สะดวก', 'keys' => ['little-elephant','journey','craft','charoensuk','life']],
      'minimal' => ['title' => '🎨 มินิมอล', 'desc' => 'โทนมินิมอล สว่างคลีน ถ่ายรูปสวย', 'keys' => ['follow','dammachat','healing','kind','parich']],
    ];

    // แผง DEBUG: ?debug=1
    $debugOutput = null;
    if (request()->boolean('debug')) {
      $lines = [];
      foreach ($cafes as $key => $c){
        $src = $resolve_image($c['image'] ?? '', $c['name'] ?? '', $c['alias'] ?? $key);
        $rel = ltrim($c['image'] ?? '', '/');
        $abs = $rel ? public_path($rel) : '(none)';
        $exists = $rel && file_exists($abs) ? '✅ preferred exists' : '❌ preferred missing';
        $lines[] = "{$c['name']}\n  preferred: /{$rel}\n  path: {$abs}\n  {$exists}\n  RESOLVED → {$src}\n";
      }
      $debugOutput = implode("\n", $lines);
    }
  @endphp

  @if ($debugOutput)
    <div class="debug">{{ $debugOutput }}</div>
  @endif

  {{-- RENDER --}}
  @foreach ($categories as $slug => $cat)
    <section id="{{ $slug }}" class="section">
      <h2>{{ $cat['title'] }} <span class="tag">{{ count($cat['keys']) ?: '0' }} แห่ง</span></h2>
      <p class="desc">{{ $cat['desc'] }}</p>

      @if (count($cat['keys']) === 0)
        <div class="empty">ยังไม่มีคาเฟ่ในหมวดนี้ — แนะนำร้านให้เราเพิ่มได้เลย!</div>
      @else
        <div class="grid">
          @foreach ($cat['keys'] as $key)
            @php $c = $cafes[$key] ?? null; @endphp
            @if ($c)
              @php $imgSrc = $resolve_image($c['image'] ?? '', $c['name'] ?? '', $c['alias'] ?? $key); @endphp
              <article class="card">
                <div class="media">
                  <img src="{{ $imgSrc }}" alt="รูปภาพ {{ $c['name'] }}" loading="lazy" />
                  <span class="badge">
                    @switch($slug)
                      @case('wifi')     📶 Wi-Fi @break
                      @case('meeting')  🧩 ห้องประชุม @break
                      @case('cheap')    💰 ประหยัด @break
                      @case('parking')  🚗 ที่จอดรถ @break
                      @case('minimal')  🎨 มินิมอล @break
                      @default          ⭐ แนะนำ
                    @endswitch
                  </span>
                </div>
                <div class="body">
                  <h3 class="name">{{ $c['name'] }}</h3>
                  <div class="chips">
                    @php
                      $map = ['wifi'=>'Wi-Fi ฟรี','meeting'=>'ห้องประชุม','cheap'=>'ราคาย่อมเยา','parking'=>'ที่จอดรถ','minimal'=>'มินิมอล'];
                    @endphp
                    @foreach (($c['features'] ?? []) as $f)
                      @if (isset($map[$f])) <span class="chip">{{ $map[$f] }}</span> @endif
                    @endforeach
                  </div>
                  <div class="actions">
                    @if (!empty($c['url']))
                      <a class="btn" href="{{ $c['url'] }}">ดูรายละเอียด</a>
                    @else
                      <span class="btn disabled">เร็ว ๆ นี้</span>
                    @endif
                  </div>
                </div>
              </article>
            @endif
          @endforeach
        </div>
      @endif
    </section>
  @endforeach
</div>

<script>
  function jumpTo(id){
    const el = document.getElementById(id);
    if(!el) return;
    window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 8, behavior:'smooth' });
  }
</script>
</body>
</html>
