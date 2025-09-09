<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $cafe->cafe_name }}</title>

  <!-- Tailwind & Alpine -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <style>
    :root{
      --bg:#f6f8fc; --card:#fff; --line:#e5e7eb; --ink:#0f172a; --muted:#64748b;
      --brand:#0ea5e9; --brand-2:#22c55e; --accent:#f59e0b;
      --shadow:0 10px 25px rgba(15,23,42,.06);
    }
    html,body{background:var(--bg);color:var(--ink);font-family:'Kanit',sans-serif}
    [x-cloak]{display:none!important}
    .container{max-width:1120px}
    .card{background:var(--card);border:1px solid var(--line);border-radius:1rem;box-shadow:var(--shadow)}
    .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.38rem .7rem;border-radius:999px;border:1px solid var(--line);background:#fff;font-size:.9rem}
    .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.65rem 1rem;border-radius:.75rem;border:1px solid var(--line);font-weight:600}
    .btn-brand{background:var(--brand);color:#fff;border-color:transparent}
    .btn-brand:hover{filter:brightness(.95)}
    .btn-lite:hover{background:#f8fafc}
    .thumb{aspect-ratio:3/2;object-fit:cover;width:100%;height:100%}
    .badge-star{display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .6rem;border-radius:.6rem;background:#fff;border:1px solid var(--line)}
    .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
  </style>
</head>
<body
  x-data="{
    tab:'info', lightbox:false, lightboxSrc:'', copied:false,
    share(title,url){ if(navigator.share){ navigator.share({title, url}); } else { navigator.clipboard.writeText(url); this.copied=true; setTimeout(()=>this.copied=false,1400); } }
  }"
  class="min-h-screen"
>

  {{-- Navbar --}}
  @guest @include('components.1navbar') @endguest
  @auth  @include('components.2navbar') @endauth

  @php
    // ---------- Data Helpers ----------
    $imgs = is_string($cafe->images) ? (json_decode($cafe->images, true) ?: []) : (is_array($cafe->images) ? $cafe->images : []);
    $hero = $imgs[0] ?? null;
    $thumbs = array_slice($imgs, 0, 9);

    $toArray=function($v){
      if (is_array($v)) return array_values(array_filter($v,fn($x)=>trim((string)$x)!=''));
      if (is_string($v)){
        $j=json_decode($v,true);
        if(json_last_error()===JSON_ERROR_NONE && is_array($j)) return array_values(array_filter($j,fn($x)=>trim((string)$x)!=''));
        return array_values(array_filter(array_map('trim',explode(',',$v)),fn($x)=>$x!==''));
      }
      return [];
    };

    $facilities=$toArray($cafe->facilities);
    $styles=$toArray($cafe->cafe_styles);
    $payments=$toArray($cafe->payment_methods);
    $services=$toArray($cafe->other_services);
    $desc = trim((string)($cafe->description ?? ''));

    $hasParking=(int)($cafe->parking ?? 0)===1;
    $hasCC=(int)($cafe->credit_card ?? 0)===1;

    $reviewCount = $reviews->count() ?? 0;
    $avgRating = $reviewCount ? round($reviews->avg('rating'),1) : null;

    $hasPos = !empty($cafe->lat) && !empty($cafe->lng);
  @endphp

  <!-- HERO -->
  <header class="w-full">
    <div class="mx-auto container px-4 sm:px-6 lg:px-8">
      <div class="card overflow-hidden">
        <div class="relative">
          @if($hero)
            <img src="{{ asset('storage/'.$hero) }}" alt="ภาพหน้าปก {{ $cafe->cafe_name }}" class="w-full h-[220px] sm:h-[280px] md:h-[340px] object-cover">
          @else
            <div class="w-full h-[200px] sm:h-[240px] md:h-[300px] bg-white"></div>
          @endif
          <div class="absolute inset-x-0 bottom-0">
            <div class="px-5 sm:px-7 pb-5">
              <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div class="min-w-0">
                  <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 drop-shadow-sm">{{ $cafe->cafe_name }}</h1>
                  @if(!empty($cafe->place_name))
                    <p class="mt-1 text-slate-700">{{ $cafe->place_name }}</p>
                  @endif
                  <div class="mt-3 flex flex-wrap gap-2">
                    @if(!empty($cafe->is_new_opening))
                      <span class="chip"><i class="fa-solid fa-bolt text-amber-500"></i> เปิดใหม่</span>
                    @endif
                    @if(!empty($cafe->price_range))
                      <span class="chip"><i class="fa-solid fa-tags text-cyan-600"></i> {{ $cafe->price_range }}</span>
                    @endif
                    <span class="chip"><i class="fa-solid fa-square-parking {{ $hasParking?'text-emerald-600':'text-slate-400' }}"></i> จอดรถ {{ $hasParking?'ได้':'-' }}</span>
                    <span class="chip"><i class="fa-regular fa-credit-card {{ $hasCC?'text-emerald-600':'text-slate-400' }}"></i> บัตรเครดิต {{ $hasCC?'รองรับ':'-' }}</span>
                    @if($avgRating)
                      <span class="badge-star">
                        <i class="fa-solid fa-star text-amber-500"></i>
                        <span>{{ number_format($avgRating,1) }}/5 • {{ $reviewCount }} รีวิว</span>
                      </span>
                    @endif
                  </div>
                </div>
                <div class="flex flex-wrap gap-2">
                  <button class="btn btn-lite" @click="share(`{{ addslashes($cafe->cafe_name) }}`, location.href)">
                    <i class="fa-solid fa-share-nodes"></i> แชร์
                  </button>
                  @auth
                    <a href="{{ route('user.reviews.create', ['cafe_id' => $cafe->cafe_id ?? $cafe->id]) }}" class="btn btn-brand">
                      <i class="fa-solid fa-pen-to-square"></i> เขียนรีวิว
                    </a>
                  @endauth
                </div>
              </div>
            </div>
          </div>
        </div> <!-- /relative -->
      </div>
    </div>
  </header>

  <!-- BODY -->
  <main class="mx-auto container px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">

      <!-- LEFT -->
      <div class="lg:col-span-2 space-y-7">

        <!-- แกลเลอรี -->
        <section class="card p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
              <i class="fa-regular fa-images text-cyan-600"></i> รูปภาพ
            </h2>
            @if($imgs && count($imgs) > 9)
              <span class="text-sm text-slate-500">แสดง 9 จาก {{ count($imgs) }} รูป</span>
            @endif
          </div>

          @if(empty($thumbs))
            <div class="text-center py-10 text-slate-500">
              <i class="fa-regular fa-image text-4xl mb-2"></i>
              <p>ยังไม่มีรูปภาพ</p>
            </div>
          @else
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              @foreach($thumbs as $t)
                <button class="group aspect-[3/2] overflow-hidden rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500"
                        @click="lightboxSrc='{{ asset('storage/'.$t) }}'; lightbox=true">
                  <img src="{{ asset('storage/'.$t) }}"
                       alt="ภาพ {{ $cafe->cafe_name }}"
                       class="thumb group-hover:scale-105 transition-transform duration-300"/>
                </button>
              @endforeach
            </div>
          @endif
        </section>

        <!-- ข้อมูล + รีวิว -->
        <section class="card p-0 overflow-hidden">
          <div class="px-6 pt-4 border-b">
            <nav class="flex gap-6" aria-label="Tabs">
              <button class="py-3 font-medium"
                      :class="tab==='info' ? 'text-cyan-700 border-b-2 border-cyan-600 -mb-[1px]' : 'text-slate-500'"
                      @click="tab='info'">ข้อมูล</button>
              <button class="py-3 font-medium"
                      :class="tab==='reviews' ? 'text-cyan-700 border-b-2 border-cyan-600 -mb-[1px]' : 'text-slate-500'"
                      @click="tab='reviews'">รีวิว ({{ $reviewCount }})</button>
            </nav>
          </div>

          <!-- INFO -->
          <div x-show="tab==='info'" x-cloak class="px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4 text-slate-700">
              <div class="flex items-start">
                <i class="fa-solid fa-location-dot text-cyan-600 w-5 mt-1 mr-3 shrink-0"></i>
                <div class="min-w-0">
                  <strong>ที่อยู่:</strong>
                  <p class="break-words">{{ $cafe->address }}</p>
                  <div class="mt-2 flex flex-wrap gap-2">
                    <button class="btn btn-lite"
                            @click="await navigator.clipboard.writeText(`{{ trim(preg_replace('/\s+/', ' ', $cafe->address)) }}`); copied=true; setTimeout(()=>copied=false,1400)">
                      <i class="fa-regular fa-copy"></i> คัดลอก
                    </button>
                    @if($hasPos)
                      <a class="btn btn-brand"
                         href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}" target="_blank" rel="noopener">
                        <i class="fa-brands fa-google"></i> เปิดในแผนที่
                      </a>
                    @endif
                  </div>
                </div>
              </div>

              <div class="flex items-center">
                <i class="fa-solid fa-phone text-cyan-600 w-5 mr-3 shrink-0"></i>
                <span><strong>โทรศัพท์:</strong>
                  @if(!empty($cafe->phone))
                    <a href="tel:{{ preg_replace('/\s+/', '', $cafe->phone) }}" class="text-cyan-700 hover:underline">{{ $cafe->phone }}</a>
                  @else - @endif
                </span>
              </div>

              <div class="flex items-center">
                <i class="fa-solid fa-envelope text-cyan-600 w-5 mr-3 shrink-0"></i>
                <span><strong>อีเมล:</strong>
                  @if(!empty($cafe->email))
                    <a href="mailto:{{ $cafe->email }}" class="text-cyan-700 hover:underline break-all">{{ $cafe->email }}</a>
                  @else - @endif
                </span>
              </div>

              <div class="flex items-center">
                <i class="fa-solid fa-globe text-cyan-600 w-5 mr-3 shrink-0"></i>
                <span><strong>เว็บไซต์:</strong>
                  @if(!empty($cafe->website))
                    <a href="{{ $cafe->website }}" target="_blank" rel="noopener" class="text-cyan-700 hover:underline break-all">{{ $cafe->website }}</a>
                  @else - @endif
                </span>
              </div>

              <div class="flex items-center">
                <i class="fa-brands fa-facebook text-cyan-600 w-5 mr-3 shrink-0"></i>
                <span><strong>Facebook:</strong>
                  @if(!empty($cafe->facebook_page))
                    <a href="{{ str_starts_with($cafe->facebook_page,'http') ? $cafe->facebook_page : 'https://facebook.com/'.$cafe->facebook_page }}"
                       target="_blank" rel="noopener" class="text-cyan-700 hover:underline break-all">{{ $cafe->facebook_page }}</a>
                  @else - @endif
                </span>
              </div>

              <div class="flex items-center">
                <i class="fa-brands fa-instagram text-cyan-600 w-5 mr-3 shrink-0"></i>
                <span><strong>Instagram:</strong>
                  @if(!empty($cafe->instagram_page))
                    <a href="{{ str_starts_with($cafe->instagram_page,'http') ? $cafe->instagram_page : 'https://instagram.com/'.$cafe->instagram_page }}"
                       target="_blank" rel="noopener" class="text-cyan-700 hover:underline break-all">{{ $cafe->instagram_page }}</a>
                  @else - @endif
                </span>
              </div>

              <div class="flex items-center">
                <i class="fa-brands fa-line text-cyan-600 w-5 mr-3 shrink-0"></i>
                <span><strong>LINE:</strong> {{ $cafe->line_id ?? '-' }}</span>
              </div>

              <div class="flex items-start md:col-span-2">
                <i class="fa-solid fa-clock text-cyan-600 w-5 mt-1 mr-3 shrink-0"></i>
                <span>
                  <strong>เวลาทำการ:</strong>
                  @php
                    $hasStructured = $cafe->open_day && $cafe->open_time && $cafe->close_time;
                    $closeDayText = $cafe->close_day ? ' - ' . $cafe->close_day : '';
                  @endphp
                  @if($hasStructured)
                    {{ $cafe->open_day }}{{ $closeDayText }},
                    {{ \Carbon\Carbon::parse($cafe->open_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($cafe->close_time)->format('H:i') }}
                  @else
                    {{ $cafe->opening_hours ?? '-' }}
                  @endif
                </span>
              </div>
            </div>

            @if($desc !== '')
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                  <i class="fa-solid fa-circle-info text-cyan-600"></i> รายละเอียด
                </h3>
                <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $desc }}</p>
              </div>
            @endif

            @if($facilities)
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                  <i class="fa-solid fa-wifi text-cyan-600"></i> สิ่งอำนวยความสะดวก
                </h3>
                <div class="flex flex-wrap gap-2">
                  @foreach($facilities as $i) <span class="chip">{{ $i }}</span> @endforeach
                </div>
              </div>
            @endif

            @if($styles)
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                  <i class="fa-solid fa-palette text-cyan-600"></i> สไตล์คาเฟ่
                </h3>
                <div class="flex flex-wrap gap-2">
                  @foreach($styles as $i) <span class="chip">{{ $i }}</span> @endforeach
                </div>
                @if(!empty($cafe->other_style))
                  <p class="mt-2 text-slate-700"><strong>สไตล์อื่นๆ:</strong> {{ $cafe->other_style }}</p>
                @endif
              </div>
            @endif

            @if($payments)
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                  <i class="fa-regular fa-credit-card text-cyan-600"></i> ช่องทางชำระเงิน
                </h3>
                <div class="flex flex-wrap gap-2">
                  @foreach($payments as $i) <span class="chip">{{ $i }}</span> @endforeach
                </div>
              </div>
            @endif

            @if($services)
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
                  <i class="fa-solid fa-bell-concierge text-cyan-600"></i> บริการเพิ่มเติม
                </h3>
                <div class="flex flex-wrap gap-2">
                  @foreach($services as $i) <span class="chip">{{ $i }}</span> @endforeach
                </div>
              </div>
            @endif
          </div>

          <!-- REVIEWS -->
          <div x-show="tab==='reviews'" x-cloak class="px-6 py-6">
            @if($reviews->isEmpty())
              <div class="text-center py-12">
                <i class="fa-solid fa-comment-slash text-4xl text-slate-400 mb-3"></i>
                <p class="text-slate-500">ยังไม่มีรีวิวสำหรับคาเฟ่นี้</p>
              </div>
            @else
              <div class="space-y-6">
                @foreach($reviews as $review)
                  <article class="border rounded-xl p-4">
                    <div class="flex items-center justify-between">
                      <p class="font-semibold text-slate-900 truncate">{{ $review->user_name ?? 'ผู้ใช้ไม่ระบุชื่อ' }}</p>
                      <time class="text-sm text-slate-500">{{ optional($review->created_at)->format('d/m/Y') }}</time>
                    </div>
                    <p class="mt-1 font-bold">
                      @for($i=1;$i<=5;$i++)
                        <i class="fa-solid fa-star {{ $i <= (int)($review->rating ?? 0) ? 'text-amber-500' : 'text-slate-300' }}"></i>
                      @endfor
                      <span class="text-sm ml-1 text-slate-600">({{ $review->rating ?? '-' }}/5)</span>
                    </p>
                    @if(!empty($review->title))
                      <h4 class="font-semibold mt-2 text-slate-900">{{ $review->title }}</h4>
                    @endif
                    @if(!empty($review->content))
                      <p class="mt-1 text-slate-700 whitespace-pre-line leading-relaxed">{{ $review->content }}</p>
                    @endif>

                    @php
                      $revImages = is_string($review->images) ? (json_decode($review->images,true) ?: []) : (is_array($review->images) ? $review->images : []);
                    @endphp
                    @if($revImages)
                      <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($revImages as $image)
                          <button class="aspect-square rounded-md overflow-hidden border focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500"
                                  @click="lightboxSrc='{{ asset('storage/'.$image) }}'; lightbox=true">
                            <img src="{{ asset('storage/'.$image) }}"
                                 alt="รูปรีวิวของ {{ $review->user_name ?? 'ผู้ใช้' }}"
                                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"/>
                          </button>
                        @endforeach
                      </div>
                    @endif
                  </article>
                @endforeach
              </div>
            @endif
          </div>
        </section>
      </div>

      <!-- RIGHT -->
      <aside class="space-y-7 lg:sticky lg:top-24 h-max">
        <section class="card p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center">
            <i class="fa-solid fa-map-location-dot text-amber-600 mr-2"></i> แผนที่
          </h3>
          <div id="map" class="w-full h-[320px] rounded-lg border"></div>
          @if($hasPos)
            <a href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}"
               target="_blank" rel="noopener"
               class="mt-4 inline-flex w-full justify-center items-center gap-2 px-4 py-3 rounded-lg bg-amber-500 text-white font-semibold hover:bg-amber-600">
              <i class="fa-brands fa-google"></i> เปิดด้วย Google Maps
            </a>
          @endif
        </section>

        <section class="card p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center">
            <i class="fa-solid fa-bolt text-emerald-600 mr-2"></i> ด่วน & ติดต่อ
          </h3>
          <div class="grid grid-cols-2 gap-3">
            <a href="{{ !empty($cafe->phone) ? 'tel:'.preg_replace('/\s+/', '', $cafe->phone) : '#' }}"
               class="px-4 py-3 rounded-lg bg-emerald-600 text-white text-center font-semibold hover:bg-emerald-700 {{ empty($cafe->phone)?'pointer-events-none opacity-50':'' }}">
              <i class="fa-solid fa-phone"></i> โทร
            </a>
            <button class="px-4 py-3 rounded-lg border hover:bg-slate-50"
                    @click="share(`{{ addslashes($cafe->cafe_name) }}`, location.href)">
              <i class="fa-solid fa-share-nodes"></i> แชร์
            </button>
            <a href="{{ !empty($cafe->website) ? $cafe->website : '#' }}" target="_blank" rel="noopener"
               class="px-4 py-3 rounded-lg bg-cyan-600 text-white text-center font-semibold hover:bg-cyan-700 {{ empty($cafe->website)?'pointer-events-none opacity-50':'' }}">
              <i class="fa-solid fa-globe"></i> เว็บไซต์
            </a>
            @auth
              <a href="{{ route('user.reviews.create', ['cafe_id' => $cafe->cafe_id ?? $cafe->id]) }}"
                 class="px-4 py-3 rounded-lg bg-amber-500 text-white text-center font-semibold hover:bg-amber-600">
                <i class="fa-solid fa-pen-to-square"></i> รีวิว
              </a>
            @endauth
          </div>
        </section>
      </aside>
    </div>
  </main>

  {{-- MOBILE STICKY CTA --}}
  <div class="fixed bottom-3 left-3 right-3 z-40 lg:hidden">
    <div class="card p-2 grid grid-cols-4 gap-2">
      <a href="{{ !empty($cafe->phone) ? 'tel:'.preg_replace('/\s+/', '', $cafe->phone) : '#' }}"
         class="flex flex-col items-center py-2 rounded-lg border {{ empty($cafe->phone)?'pointer-events-none opacity-50':'' }}">
        <i class="fa-solid fa-phone"></i><span class="text-xs mt-1">โทร</span>
      </a>
      <button class="flex flex-col items-center py-2 rounded-lg border"
              @click="document.getElementById('map')?.scrollIntoView({behavior:'smooth'})">
        <i class="fa-solid fa-map-location-dot"></i><span class="text-xs mt-1">แผนที่</span>
      </button>
      <button class="flex flex-col items-center py-2 rounded-lg border" @click="tab='reviews'; window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fa-solid fa-star"></i><span class="text-xs mt-1">รีวิว</span>
      </button>
      <button class="flex flex-col items-center py-2 rounded-lg bg-amber-500 text-white"
              @click="share(`{{ addslashes($cafe->cafe_name) }}`, location.href)">
        <i class="fa-solid fa-share-nodes"></i><span class="text-xs mt-1">แชร์</span>
      </button>
    </div>
  </div>

  {{-- LIGHTBOX --}}
  <div x-show="lightbox" x-cloak x-transition.opacity
       class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4"
       @click.self="lightbox=false" role="dialog" aria-modal="true">
    <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 text-white hover:bg-white/20"
            @click="lightbox=false" aria-label="ปิดภาพตัวอย่าง">
      <i class="fa-solid fa-xmark text-xl"></i>
    </button>
    <img :src="lightboxSrc" alt="" class="max-w-full max-h-full rounded-lg shadow-2xl"/>
  </div>

  {{-- TOAST --}}
  <div x-show="copied" x-cloak x-transition
       class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50">
    <div class="px-4 py-2 rounded-lg bg-black/80 text-white text-sm shadow">
      คัดลอกแล้ว
    </div>
  </div>

  <footer class="py-8 text-center text-slate-500">
    © {{ date('Y') }} ระบบคาเฟ่น้องช้างสะเร็น
  </footer>

  <!-- MAP INIT -->
  <script>
    (function () {
      const lat = Number('{{ $cafe->lat ?? '' }}') || 14.885;
      const lng = Number('{{ $cafe->lng ?? '' }}') || 103.490;
      const hasPos = Boolean('{{ $cafe->lat }}' && '{{ $cafe->lng }}');
      const mapEl = document.getElementById('map');
      if (!mapEl) return;
      const map = L.map('map', { scrollWheelZoom:true, tap:true }).setView([lat, lng], hasPos ? 16 : 12);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19, attribution:'&copy; OpenStreetMap' }).addTo(map);
      if (hasPos) L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($cafe->cafe_name ?? 'ตำแหน่ง') }}`);
      setTimeout(() => map.invalidateSize(), 250);
    })();
  </script>
</body>
</html>
