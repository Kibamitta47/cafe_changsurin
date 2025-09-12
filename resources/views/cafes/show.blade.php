<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
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
    :root{ --safe-bottom: env(safe-area-inset-bottom); }
    body{font-family:'Kanit',sans-serif;background:#f5f7fb}
    [x-cloak]{display:none!important}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:1rem;box-shadow:0 4px 16px rgba(0,0,0,.04)}
    .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .65rem;border-radius:999px;border:1px solid #e5e7eb;background:#fff;font-size:.85rem}
    .no-scrollbar::-webkit-scrollbar{display:none}
    .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
  </style>
</head>
<body class="min-h-screen text-slate-800 pb-[92px]"
      x-data="{ tab:'info', lightbox:false, lightboxSrc:'', copied:false, ready:false }"
      x-init="ready=true">

  {{-- Navbar --}}
  @guest @include('components.1navbar') @endguest
  @auth  @include('components.2navbar') @endauth

  @php
    // ---------- Helper & Data ----------
    $imgs = is_string($cafe->images) ? (json_decode($cafe->images, true) ?: []) : (is_array($cafe->images) ? $cafe->images : []);
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
  @endphp

  <!-- HEADER CARD -->
  <header class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 mt-3">
    <div class="card p-4 sm:p-6">
      <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 sm:gap-5">
        <div class="min-w-0">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-snug">{{ $cafe->cafe_name }}</h1>
          @if(!empty($cafe->place_name))
            <p class="mt-0.5 text-slate-600 text-sm sm:text-base">{{ $cafe->place_name }}</p>
          @endif

          <div class="mt-3 flex flex-wrap gap-1.5 sm:gap-2">
            @if(!empty($cafe->is_new_opening))
              <span class="chip"><i class="fa-solid fa-bolt text-amber-500"></i> เปิดใหม่</span>
            @endif
            @if(!empty($cafe->price_range))
              <span class="chip"><i class="fa-solid fa-tags text-cyan-600"></i> {{ $cafe->price_range }}</span>
            @endif
            <span class="chip"><i class="fa-solid fa-square-parking {{ $hasParking?'text-emerald-600':'text-slate-400' }}"></i> จอดรถ {{ $hasParking?'ได้':'-' }}</span>
            <span class="chip"><i class="fa-regular fa-credit-card {{ $hasCC?'text-emerald-600':'text-slate-400' }}"></i> บัตรเครดิต {{ $hasCC?'รองรับ':'-' }}</span>
            @if($avgRating)
              <span class="chip">
                <i class="fa-solid fa-star text-amber-500"></i>
                {{ number_format($avgRating,1) }} / 5 • {{ $reviewCount }} รีวิว
              </span>
            @endif
          </div>
        </div>

        <div class="flex gap-2 shrink-0">
          <button class="px-3 py-2 sm:px-4 rounded-lg border hover:bg-slate-50 text-sm sm:text-base"
                  @click="navigator.share ? navigator.share({title:'{{ addslashes($cafe->cafe_name) }}', url: window.location.href}) : (async()=>{try{await navigator.clipboard.writeText(window.location.href)}finally{copied=true; setTimeout(()=>copied=false,1500)}})()">
            <i class="fa-solid fa-share-nodes"></i> <span class="hidden sm:inline">แชร์</span>
          </button>
          @auth
            <a href="{{ route('user.reviews.create', ['cafe_id' => $cafe->cafe_id ?? $cafe->id]) }}"
               class="px-3 py-2 sm:px-4 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm sm:text-base">
              <i class="fa-solid fa-pen-to-square"></i> <span class="hidden sm:inline">เขียนรีวิว</span>
            </a>
          @endauth
        </div>
      </div>
    </div>
  </header>

  <!-- BODY -->
  <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-7">

      <!-- LEFT -->
      <div class="lg:col-span-2 space-y-4 sm:space-y-7">

        <!-- GALLERY -->
        <section class="card p-4 sm:p-5">
          <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-3 sm:mb-4 flex items-center gap-2">
            <i class="fa-regular fa-images text-cyan-600"></i> รูปภาพ
          </h2>

          @if(empty($thumbs))
            <div class="text-center py-10 text-slate-500">
              <i class="fa-regular fa-image text-4xl mb-2"></i>
              <p>ยังไม่มีรูปภาพ</p>
            </div>
          @else
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">
              @foreach($thumbs as $t)
                <button class="group aspect-[4/3] overflow-hidden rounded-lg border"
                        @click="lightboxSrc='{{ asset('storage/'.$t) }}'; lightbox=true">
                  <img src="{{ asset('storage/'.$t) }}"
                       alt="ภาพ {{ $cafe->cafe_name }}"
                       loading="lazy"
                       class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
                </button>
              @endforeach
            </div>
          @endif
        </section>

        <!-- INFO / REVIEWS -->
        <section class="card p-4 sm:p-6">
          <div class="flex items-center gap-4 border-b pb-2 sm:pb-3 overflow-x-auto no-scrollbar">
            <button class="py-2 px-1 font-medium shrink-0 text-sm sm:text-base"
                    :class="tab==='info' ? 'text-cyan-700 border-b-2 border-cyan-600' : 'text-slate-500'"
                    @click="tab='info'">ข้อมูล</button>
            <button class="py-2 px-1 font-medium shrink-0 text-sm sm:text-base"
                    :class="tab==='reviews' ? 'text-cyan-700 border-b-2 border-cyan-600' : 'text-slate-500'"
                    @click="tab='reviews'">รีวิว ({{ $reviewCount }})</button>
          </div>

          <!-- INFO -->
          <div x-show="ready && tab==='info'" x-cloak class="mt-4 sm:mt-6 space-y-5 sm:space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 sm:gap-x-10 gap-y-3 sm:gap-y-4 text-slate-700 text-sm sm:text-base">
              <div class="flex items-start">
                <i class="fa-solid fa-location-dot text-cyan-600 w-5 mt-1 mr-3 shrink-0"></i>
                <div class="min-w-0">
                  <strong>ที่อยู่:</strong>
                  <p class="break-words">{{ $cafe->address }}</p>
                  <div class="mt-2 flex flex-wrap gap-2">
                    <button class="px-3 py-1.5 rounded-md border hover:bg-slate-50"
                            @click="navigator.clipboard.writeText(`{{ trim(preg_replace('/\s+/', ' ', $cafe->address)) }}`); copied=true; setTimeout(()=>copied=false,1500)">
                      <i class="fa-regular fa-copy"></i> คัดลอก
                    </button>
                    @if(!empty($cafe->lat) && !empty($cafe->lng))
                      <a class="px-3 py-1.5 rounded-md bg-cyan-600 text-white hover:bg-cyan-700"
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
                <h3 class="font-semibold text-slate-900 mb-2 text-base"><i class="fa-solid fa-circle-info text-cyan-600 mr-2"></i> รายละเอียด</h3>
                <p class="text-slate-700 whitespace-pre-line leading-relaxed text-sm sm:text-base">{{ $desc }}</p>
              </div>
            @endif

            @if($facilities)
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 text-base"><i class="fa-solid fa-wifi text-cyan-600 mr-2"></i> สิ่งอำนวยความสะดวก</h3>
                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                  @foreach($facilities as $i)
                    <span class="chip">{{ $i }}</span>
                  @endforeach
                </div>
              </div>
            @endif

            @if($styles)
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 text-base"><i class="fa-solid fa-palette text-cyan-600 mr-2"></i> สไตล์คาเฟ่</h3>
                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                  @foreach($styles as $i)
                    <span class="chip">{{ $i }}</span>
                  @endforeach
                </div>
                @if(!empty($cafe->other_style))
                  <p class="mt-2 text-slate-700 text-sm sm:text-base"><strong>สไตล์อื่นๆ:</strong> {{ $cafe->other_style }}</p>
                @endif
              </div>
            @endif

            @if($payments)
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 text-base"><i class="fa-regular fa-credit-card text-cyan-600 mr-2"></i> ช่องทางชำระเงิน</h3>
                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                  @foreach($payments as $i)
                    <span class="chip">{{ $i }}</span>
                  @endforeach
                </div>
              </div>
            @endif

            @if($services)
              <div class="pt-4 border-t">
                <h3 class="font-semibold text-slate-900 mb-2 text-base"><i class="fa-solid fa-bell-concierge text-cyan-600 mr-2"></i> บริการเพิ่มเติม</h3>
                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                  @foreach($services as $i)
                    <span class="chip">{{ $i }}</span>
                  @endforeach
                </div>
              </div>
            @endif
          </div>

          <!-- REVIEWS -->
          <div x-show="ready && tab==='reviews'" x-cloak class="mt-4 sm:mt-6">
            @if($reviews->isEmpty())
              <div class="text-center py-10 border rounded-xl bg-white/60">
                <i class="fa-solid fa-comment-slash text-3xl sm:text-4xl text-slate-400 mb-3"></i>
                <p class="text-slate-500">ยังไม่มีรีวิวสำหรับคาเฟ่นี้</p>
              </div>
            @else
              <div class="space-y-5 sm:space-y-6">
                @foreach($reviews as $review)
                  <div class="border-t pt-5 first:border-t-0 first:pt-0">
                    <div class="flex items-center justify-between gap-3">
                      <p class="font-semibold text-slate-900 text-sm sm:text-base truncate">{{ $review->user_name ?? 'ผู้ใช้ไม่ระบุชื่อ' }}</p>
                      <p class="text-xs sm:text-sm text-slate-500">{{ optional($review->created_at)->format('d/m/Y') }}</p>
                    </div>
                    <p class="mt-1 font-bold text-sm sm:text-base">
                      @for($i=1;$i<=5;$i++)
                        <i class="fa-solid fa-star {{ $i <= (int)($review->rating ?? 0) ? 'text-amber-500' : 'text-slate-300' }}"></i>
                      @endfor
                      <span class="text-xs sm:text-sm ml-1 text-slate-600">({{ $review->rating ?? '-' }}/5)</span>
                    </p>
                    @if(!empty($review->title))
                      <h4 class="font-semibold mt-2 sm:mt-3 text-slate-900">{{ $review->title }}</h4>
                    @endif
                    @if(!empty($review->content))
                      <p class="mt-1 text-slate-700 whitespace-pre-line leading-relaxed text-sm sm:text-base">{{ $review->content }}</p>
                    @endif

                    @php
                      $revImages = is_string($review->images) ? (json_decode($review->images,true) ?: []) : (is_array($review->images) ? $review->images : []);
                    @endphp
                    @if($revImages)
                      <div class="mt-3 sm:mt-4 grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($revImages as $image)
                          <button class="aspect-square rounded-md overflow-hidden border"
                                  @click="lightboxSrc='{{ asset('storage/'.$image) }}'; lightbox=true">
                            <img src="{{ asset('storage/'.$image) }}"
                                 alt="รูปรีวิวของ {{ $review->user_name ?? 'ผู้ใช้' }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"/>
                          </button>
                        @endforeach
                      </div>
                    @endif
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </section>
      </div>

      <!-- RIGHT -->
      <aside class="space-y-4 sm:space-y-7 lg:sticky lg:top-24 h-max">
        <section class="card p-4 sm:p-6">
          <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 flex items-center">
            <i class="fa-solid fa-map-location-dot text-amber-600 mr-2"></i> แผนที่
          </h3>
          <div id="map" class="w-full h-[260px] sm:h-[320px] rounded-lg border"></div>
          @if(!empty($cafe->lat) && !empty($cafe->lng))
            <a href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}"
               target="_blank" rel="noopener"
               class="mt-3 sm:mt-4 inline-flex w-full justify-center items-center gap-2 px-4 py-3 rounded-lg bg-amber-500 text-white font-semibold hover:bg-amber-600">
              <i class="fa-brands fa-google"></i> เปิดด้วย Google Maps
            </a>
          @endif
        </section>

        <section class="card p-4 sm:p-6">
          <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 flex items-center">
            <i class="fa-solid fa-bolt text-emerald-600 mr-2"></i> ด่วน & ติดต่อ
          </h3>
          <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
            <a href="{{ !empty($cafe->phone) ? 'tel:'.preg_replace('/\s+/', '', $cafe->phone) : '#' }}"
               class="px-4 py-3 rounded-lg bg-emerald-600 text-white text-center font-semibold hover:bg-emerald-700 {{ empty($cafe->phone)?'pointer-events-none opacity-50':'' }}">
              <i class="fa-solid fa-phone"></i> โทร
            </a>
            <button class="px-4 py-3 rounded-lg border hover:bg-slate-50"
                    @click="navigator.share ? navigator.share({title:'{{ addslashes($cafe->cafe_name) }}', text:'ชวนไปคาเฟ่นี้กันไหม?', url: location.href}) : (async()=>{try{await navigator.clipboard.writeText(location.href)}finally{copied=true; setTimeout(()=>copied=false,1500)}})()">
              <i class="fa-solid fa-share-nodes"></i> แชร์
            </button>
            @auth
            @endauth
          </div>
        </section>
      </aside>
    </div>
  </main>

  {{-- MOBILE STICKY --}}
  <div class="fixed bottom-3 left-3 right-3 z-40 lg:hidden" style="padding-bottom: var(--safe-bottom);">
    <div class="card p-2 grid grid-cols-4 gap-2 backdrop-blur-sm">
      <a href="{{ !empty($cafe->phone) ? 'tel:'.preg_replace('/\s+/', '', $cafe->phone) : '#' }}"
         class="flex flex-col items-center py-2 rounded-lg border text-sm {{ empty($cafe->phone)?'pointer-events-none opacity-50':'' }}">
        <i class="fa-solid fa-phone"></i><span class="text-[11px] mt-1">โทร</span>
      </a>
      <button class="flex flex-col items-center py-2 rounded-lg border text-sm"
              @click="document.getElementById('map')?.scrollIntoView({behavior:'smooth'})">
        <i class="fa-solid fa-map-location-dot"></i><span class="text-[11px] mt-1">แผนที่</span>
      </button>
      <button class="flex flex-col items-center py-2 rounded-lg border text-sm" @click="tab='reviews'; window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fa-solid fa-star"></i><span class="text-[11px] mt-1">รีวิว</span>
      </button>
      <button class="flex flex-col items-center py-2 rounded-lg bg-amber-500 text-white text-sm"
              @click="navigator.share ? navigator.share({title:'{{ addslashes($cafe->cafe_name) }}', url: location.href}) : (async()=>{try{await navigator.clipboard.writeText(location.href)}finally{copied=true; setTimeout(()=>copied=false,1500)}})()">
        <i class="fa-solid fa-share-nodes"></i><span class="text-[11px] mt-1">แชร์</span>
      </button>
    </div>
  </div>

  {{-- LIGHTBOX --}}
  <div x-show="lightbox" x-cloak x-transition.opacity
       class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4"
       @click.self="lightbox=false">
    <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 text-white hover:bg-white/20"
            @click="lightbox=false" aria-label="Close">
      <i class="fa-solid fa-xmark text-xl"></i>
    </button>
    <img :src="lightboxSrc" alt="preview" class="max-w-full max-h-full rounded-lg shadow-2xl"/>
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
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19 }).addTo(map);
      if (hasPos) L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($cafe->cafe_name ?? 'ตำแหน่ง') }}`);
      setTimeout(() => map.invalidateSize(), 250);
    })();
  </script>
</body>
</html>
