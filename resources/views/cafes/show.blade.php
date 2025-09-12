<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>{{ $cafe->cafe_name }}</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <style>
    :root{
      --safe-bottom: env(safe-area-inset-bottom, 0px);
      /* ระยะกันพื้นที่ด้านล่างให้คอนเทนต์ (จะถูกอัปเดตด้วย JS) */
      --mobile-bar-space: 0px;
    }
    body{font-family:'Kanit',sans-serif;background:#f5f7fb}
    [x-cloak]{display:none!important}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:1rem;box-shadow:0 4px 16px rgba(0,0,0,.04)}
    .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .65rem;border-radius:999px;border:1px solid #e5e7eb;background:#fff;font-size:.85rem}
    .no-scrollbar::-webkit-scrollbar{display:none}
    .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
    /* กันพื้นที่ด้านล่างอัตโนมัติสำหรับมือถือ */
    .content-safe{ padding-bottom: var(--mobile-bar-space); }
    /* แถบลอยมือถือ รองรับ safe-area */
    .mobile-bar{ position:fixed; left:12px; right:12px; bottom: calc(var(--safe-bottom) + 12px); z-index:40; }
    @media (min-width: 1024px){
      .content-safe{ padding-bottom: 0 !important; }
    }
  </style>
</head>
<body class="min-h-screen text-slate-800 content-safe"
      x-data="{ tab:'info', lightbox:false, lightboxSrc:'', copied:false, ready:false }"
      x-init="ready=true">

  {{-- Navbar --}}
  @guest @include('components.1navbar') @endguest
  @auth  @include('components.2navbar') @endauth

  @php
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
            <!-- … (ส่วนข้อมูลตามเดิมทั้งหมด) … -->
            <!-- ยกเว้นตัดออกเพื่อย่อ; ใช้ของเดิมที่ให้มาได้เลย -->
          </div>

          <!-- REVIEWS -->
          <div x-show="ready && tab==='reviews'" x-cloak class="mt-4 sm:mt-6">
            <!-- … (ส่วนรีวิวตามเดิมทั้งหมด) … -->
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
            @auth
            @endauth
          </div>
        </section>
      </aside>
    </div>
  </main>

  {{-- MOBILE STICKY --}}
  <div id="mobileBar" class="mobile-bar lg:hidden">
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

  <!-- MAP INIT + กันพื้นที่เมนูมือถือ -->
  <script>
    (function () {
      // Leaflet
      const lat = Number('{{ $cafe->lat ?? '' }}') || 14.885;
      const lng = Number('{{ $cafe->lng ?? '' }}') || 103.490;
      const hasPos = Boolean('{{ $cafe->lat }}' && '{{ $cafe->lng }}');
      const mapEl = document.getElementById('map');
      if (mapEl) {
        const map = L.map('map', { scrollWheelZoom:true, tap:true }).setView([lat, lng], hasPos ? 16 : 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19 }).addTo(map);
        if (hasPos) L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($cafe->cafe_name ?? 'ตำแหน่ง') }}`);
        setTimeout(() => map.invalidateSize(), 250);
      }

      // กันพื้นที่ให้คอนเทนต์ไม่โดนเมนูบัง
      const root = document.documentElement;
      const bar = document.getElementById('mobileBar');
      function updateMobileSpace(){
        const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
        if (!bar || isDesktop) {
          root.style.setProperty('--mobile-bar-space', '0px');
          return;
        }
        // ความสูงจริงของแถบ + safe-area + margin bottom 12px
        const rect = bar.getBoundingClientRect();
        const safe = parseFloat(getComputedStyle(root).getPropertyValue('--safe-bottom')) || 0;
        const space = rect.height + safe + 16; // กันเพิ่มอีกนิด
        root.style.setProperty('--mobile-bar-space', space + 'px');
      }
      updateMobileSpace();
      window.addEventListener('resize', updateMobileSpace);
      window.addEventListener('orientationchange', updateMobileSpace);
      // บางเครื่องต้องรอ layout เสถียร
      setTimeout(updateMobileSpace, 250);
    })();
  </script>
</body>
</html>
