<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{{ $cafe->cafe_name ?? 'รายละเอียดคาเฟ่' }} | น้องช้างสะเร็น</title>

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

  <!-- Meta -->
  <meta name="description" content="{{ strip_tags($cafe->description ?? ($cafe->place_name ?? 'คาเฟ่ในสุรินทร์')) }}">
  <meta name="theme-color" content="#06b6d4">
  <meta property="og:title" content="{{ $cafe->cafe_name ?? 'คาเฟ่' }} | น้องช้างสะเร็น">
  <meta property="og:description" content="{{ str($cafe->description ?? 'คาเฟ่ในสุรินทร์')->limit(120) }}">
  @php
    $__imgs = is_string($cafe->images) ? (json_decode($cafe->images, true) ?: []) : (is_array($cafe->images) ? $cafe->images : []);
    $__og = $__imgs[0] ?? null;
  @endphp
  @if($__og)
    <meta property="og:image" content="{{ asset('storage/'.$__og) }}">
  @endif

  <style>
    :root { --glass: rgba(255,255,255,.72) }
    body {
      font-family: 'Kanit', sans-serif;
      background:
        radial-gradient(1200px 600px at 10% -10%, #fef9c3 0%, transparent 60%),
        radial-gradient(1200px 600px at 110% 20%, #cffafe 0%, transparent 55%),
        linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    }
    [x-cloak]{display:none!important}
    .glass{backdrop-filter: blur(12px); background: var(--glass)}
    .masonry{column-count:1; column-gap: .75rem}
    @media (min-width:640px){ .masonry{column-count:2} }
    @media (min-width:1024px){ .masonry{column-count:3} }
    .masonry > *{ break-inside: avoid; margin-bottom: .75rem }
  </style>
</head>
<body class="min-h-screen text-slate-800 selection:bg-cyan-200/60"
      x-data="cafePage()"
      x-init="init()">

  {{-- Navbar --}}
  @guest
    @include('components.1navbar')
  @endguest
  @auth
    @include('components.2navbar')
  @endauth

  @php
    // ---------- Helpers ----------
    $toArray = function($v){
      if (is_array($v)) return array_values(array_filter($v,fn($x)=>trim((string)$x)!=''));
      if (is_string($v)){
        $j=json_decode($v,true);
        if(json_last_error()===JSON_ERROR_NONE && is_array($j)) return array_values(array_filter($j,fn($x)=>trim((string)$x)!=''));
        return array_values(array_filter(array_map('trim',explode(',',$v)),fn($x)=>$x!==''));
      }
      return [];
    };

    $imgs        = $__imgs;
    $featured    = $imgs[0] ?? null;
    $thumbs      = array_slice($imgs, 1, 4);
    $restGallery = array_slice($imgs, 0); // ทั้งหมดสำหรับ masonry

    $desc        = trim((string)($cafe->description ?? ''));
    $facilities  = $toArray($cafe->facilities ?? []);
    $styles      = $toArray($cafe->cafe_styles ?? []);
    $payments    = $toArray($cafe->payment_methods ?? []);
    $services    = $toArray($cafe->other_services ?? []);
    $hasParking  = (int)($cafe->parking ?? 0) === 1;
    $hasCC       = (int)($cafe->credit_card ?? 0) === 1;

    $phone       = trim((string)($cafe->phone ?? ''));
    $email       = trim((string)($cafe->email ?? ''));
    $lineId      = trim((string)($cafe->line_id ?? ''));
    $website     = trim((string)($cafe->website ?? ''));
    $lat         = $cafe->lat ?? null;
    $lng         = $cafe->lng ?? null;
    $hasPos      = !empty($lat) && !empty($lng);
  @endphp

  <!-- Sticky Action Bar -->
  <div class="fixed inset-x-0 bottom-0 z-40 lg:inset-auto lg:top-20 lg:right-8 lg:bottom-auto">
    <div class="mx-4 mb-4 lg:mx-0 lg:mb-0 lg:w-[420px] lg:float-right">
      <div class="glass shadow-2xl border border-white/70 rounded-2xl p-2 flex gap-2 items-center justify-between">
        <div class="hidden lg:flex items-center gap-2 px-3">
          <i class="fa-solid fa-mug-saucer text-amber-600"></i>
          <span class="font-semibold truncate max-w-[220px]">{{ $cafe->cafe_name }}</span>
        </div>
        <div class="flex-1 flex items-center justify-around lg:justify-end lg:gap-2">
          @if($phone !== '')
            <a href="tel:{{ preg_replace('/\s+/','',$phone) }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow">
              <i class="fa-solid fa-phone"></i><span class="hidden sm:inline">โทร</span>
            </a>
          @endif
          @if($lineId !== '')
            <a href="https://line.me/R/ti/p/~{{ ltrim($lineId,'@') }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 shadow">
              <i class="fa-brands fa-line"></i><span class="hidden sm:inline">LINE</span>
            </a>
          @endif
          @if($hasPos)
            <a href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lng }}"
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-cyan-600 text-white hover:bg-cyan-700 shadow">
              <i class="fa-solid fa-location-arrow"></i><span class="hidden sm:inline">นำทาง</span>
            </a>
          @endif
          @if(trim((string)($cafe->address ?? '')) !== '')
            <button @click="copyAddress(`{{ addslashes($cafe->address) }}`)"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800 text-white hover:bg-black shadow">
              <i class="fa-regular fa-copy"></i><span class="hidden sm:inline">คัดลอกที่อยู่</span>
            </button>
          @endif
          <button @click="sharePage()"
                  class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-500 text-white hover:bg-amber-600 shadow">
            <i class="fa-solid fa-share-nodes"></i><span class="hidden sm:inline">แชร์</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Header -->
  <header class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6 lg:pt-8">
    <div class="glass rounded-3xl shadow-xl border border-white/60 p-6 sm:p-8">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
            {{ $cafe->cafe_name }}
            @if(!empty($cafe->is_new_opening))
              <span class="ml-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 align-middle">
                <i class="fa-solid fa-bolt"></i> เปิดใหม่
              </span>
            @endif
          </h1>
          @if(!empty($cafe->place_name))
            <p class="text-slate-600 mt-1">{{ $cafe->place_name }}</p>
          @endif
        </div>

        <div class="flex flex-wrap gap-2">
          @if(!empty($cafe->price_range))
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm bg-cyan-50 text-cyan-700 border border-cyan-200">
              <i class="fa-solid fa-tags"></i> {{ $cafe->price_range }}
            </span>
          @endif
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm {{ $hasParking ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border' }}">
            <i class="fa-solid fa-square-parking"></i> ที่จอดรถ: {{ $hasParking ? 'มี' : 'ไม่มี/ไม่ระบุ' }}
          </span>
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm {{ $hasCC ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border' }}">
            <i class="fa-regular fa-credit-card"></i> บัตรเครดิต: {{ $hasCC ? 'รองรับ' : 'ไม่รองรับ/ไม่ระบุ' }}
          </span>
        </div>
      </div>

      {{-- Hero: Featured + Quick thumbs --}}
      @if($featured)
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-3">
          <figure class="lg:col-span-2 relative overflow-hidden rounded-2xl shadow-2xl">
            <img src="{{ asset('storage/'.$featured) }}"
                 alt="ภาพ {{ $cafe->cafe_name }}"
                 class="w-full h-[280px] sm:h-[360px] lg:h-[420px] object-cover transition-transform duration-500 hover:scale-[1.03] cursor-pointer"
                 @click="openLightbox('{{ asset('storage/'.$featured) }}')">
            <figcaption class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/45 to-transparent p-3 text-white text-sm">
              ภาพหลัก
            </figcaption>
          </figure>

          <div class="grid grid-cols-4 lg:grid-cols-1 lg:grid-rows-4 gap-3 overflow-x-auto lg:overflow-visible py-1">
            @forelse($thumbs as $t)
              <div class="relative rounded-xl overflow-hidden shadow-xl min-w-[140px] lg:min-w-0">
                <img src="{{ asset('storage/'.$t) }}"
                     alt="ภาพ {{ $cafe->cafe_name }}"
                     class="w-full h-[120px] sm:h-[150px] lg:h-[95px] object-cover transition-transform duration-300 hover:scale-105 cursor-pointer"
                     @click="openLightbox('{{ asset('storage/'.$t) }}')">
              </div>
            @empty
              <div class="rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-slate-400">ไม่มีรูปเพิ่มเติม</div>
            @endforelse
          </div>
        </div>
      @endif
    </div>
  </header>

  <!-- Main -->
  <main class="py-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-7 px-4 sm:px-6 lg:px-8">

      <!-- Left -->
      <div class="lg:col-span-2 space-y-7">

        <!-- Info -->
        <section class="glass rounded-3xl shadow-xl border border-white/60 p-6 sm:p-8">
          <h2 class="text-xl font-bold mb-5 flex items-center gap-2 text-slate-900">
            <i class="fa-solid fa-circle-info text-cyan-500"></i> ข้อมูลคาเฟ่
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-slate-700">
            @if(!empty($cafe->address))
            <div class="flex items-start">
              <i class="fa-solid fa-location-dot text-cyan-500 w-5 mt-1 mr-3 shrink-0"></i>
              <span class="leading-relaxed">
                <strong>ที่อยู่:</strong> {{ $cafe->address }}
              </span>
            </div>
            @endif

            @if($phone !== '')
            <div class="flex items-center">
              <i class="fa-solid fa-phone text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>โทรศัพท์:</strong> <a href="tel:{{ preg_replace('/\s+/','',$phone) }}" class="text-cyan-600 hover:underline">{{ $phone }}</a></span>
            </div>
            @endif

            @if($email !== '')
            <div class="flex items-center">
              <i class="fa-solid fa-envelope text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>อีเมล:</strong> <a href="mailto:{{ $email }}" class="text-cyan-600 hover:underline break-all">{{ $email }}</a></span>
            </div>
            @endif

            @if($website !== '')
            <div class="flex items-center">
              <i class="fa-solid fa-globe text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>เว็บไซต์:</strong>
                <a href="{{ $website }}" target="_blank" rel="noopener" class="text-cyan-600 hover:underline break-all">{{ $website }}</a>
              </span>
            </div>
            @endif

            @if(!empty($cafe->facebook_page))
            <div class="flex items-center">
              <i class="fa-brands fa-facebook text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>Facebook:</strong>
                <a href="{{ str_starts_with($cafe->facebook_page,'http') ? $cafe->facebook_page : 'https://facebook.com/'.$cafe->facebook_page }}"
                   target="_blank" rel="noopener" class="text-cyan-600 hover:underline break-all">
                  {{ $cafe->facebook_page }}
                </a>
              </span>
            </div>
            @endif

            @if(!empty($cafe->instagram_page))
            <div class="flex items-center">
              <i class="fa-brands fa-instagram text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>Instagram:</strong>
                <a href="{{ str_starts_with($cafe->instagram_page,'http') ? $cafe->instagram_page : 'https://instagram.com/'.$cafe->instagram_page }}"
                   target="_blank" rel="noopener" class="text-cyan-600 hover:underline break-all">
                  {{ $cafe->instagram_page }}
                </a>
              </span>
            </div>
            @endif

            @if($lineId !== '')
            <div class="flex items-center">
              <i class="fa-brands fa-line text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>LINE:</strong> {{ $lineId }}</span>
            </div>
            @endif

            {{-- เวลาทำการ --}}
            <div class="flex items-start md:col-span-2">
              <i class="fa-solid fa-clock text-cyan-500 w-5 mt-1 mr-3 shrink-0"></i>
              <span class="leading-relaxed">
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

          {{-- รายละเอียดเพิ่มเติม --}}
          @if($desc !== '')
            <div class="mt-6 pt-6 border-t border-slate-200">
              <h3 class="text-lg font-semibold mb-2 flex items-center">
                <i class="fa-solid fa-circle-info mr-2 text-cyan-500"></i> รายละเอียดเพิ่มเติม
              </h3>
              <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $desc }}</p>
            </div>
          @endif

          {{-- แท็ก --}}
          @if($facilities)
            <div class="mt-6 pt-6 border-t border-slate-200">
              <h3 class="text-lg font-semibold mb-3 flex items-center"><i class="fa-solid fa-wifi mr-2 text-cyan-500"></i> สิ่งอำนวยความสะดวก</h3>
              <div class="flex flex-wrap gap-2">
                @foreach($facilities as $i)
                  <span class="px-3 py-1 rounded-full text-sm bg-cyan-50 text-cyan-700 border border-cyan-200">{{ $i }}</span>
                @endforeach
              </div>
            </div>
          @endif

          @if($styles)
            <div class="mt-6 pt-6 border-t border-slate-200">
              <h3 class="text-lg font-semibold mb-3 flex items-center"><i class="fa-solid fa-palette mr-2 text-purple-500"></i> สไตล์คาเฟ่</h3>
              <div class="flex flex-wrap gap-2">
                @foreach($styles as $i)
                  <span class="px-3 py-1 rounded-full text-sm bg-purple-50 text-purple-700 border border-purple-200">{{ $i }}</span>
                @endforeach
              </div>
              @if(!empty($cafe->other_style))
                <div class="mt-3 text-slate-700"><strong>สไตล์อื่นๆ:</strong> {{ $cafe->other_style }}</div>
              @endif
            </div>
          @endif

          @if($payments)
            <div class="mt-6 pt-6 border-t border-slate-200">
              <h3 class="text-lg font-semibold mb-3 flex items-center"><i class="fa-regular fa-credit-card mr-2 text-green-600"></i> ช่องทางชำระเงิน</h3>
              <div class="flex flex-wrap gap-2">
                @foreach($payments as $i)
                  <span class="px-3 py-1 rounded-full text-sm bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $i }}</span>
                @endforeach
              </div>
            </div>
          @endif

          @if($services)
            <div class="mt-6 pt-6 border-t border-slate-200">
              <h3 class="text-lg font-semibold mb-3 flex items-center"><i class="fa-solid fa-bell-concierge mr-2 text-indigo-500"></i> บริการเพิ่มเติม</h3>
              <div class="flex flex-wrap gap-2">
                @foreach($services as $i)
                  <span class="px-3 py-1 rounded-full text-sm bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $i }}</span>
                @endforeach
              </div>
            </div>
          @endif
        </section>

        {{-- แกลเลอรี Masonry --}}
        @if(count($restGallery) > 0)
        <section class="glass rounded-3xl shadow-xl border border-white/60 p-6 sm:p-8">
          <h2 class="text-xl font-bold mb-5 flex items-center gap-2">
            <i class="fa-solid fa-images text-amber-500"></i> แกลเลอรีรูปภาพ
          </h2>
          <div class="masonry">
            @foreach($restGallery as $img)
              <div class="overflow-hidden rounded-xl shadow-lg">
                <img src="{{ asset('storage/'.$img) }}"
                     alt="ภาพ {{ $cafe->cafe_name }}"
                     class="w-full h-auto object-cover transition-transform duration-300 hover:scale-105 cursor-pointer"
                     loading="lazy"
                     @click="openLightbox('{{ asset('storage/'.$img) }}')">
              </div>
            @endforeach
          </div>
        </section>
        @endif

      </div>

      <!-- Right -->
      <aside class="space-y-7 lg:sticky lg:top-28 h-max">

        {{-- Map --}}
        <section class="glass rounded-3xl shadow-xl border border-white/60 p-6">
          <h3 class="text-xl font-bold mb-4 flex items-center">
            <i class="fa-solid fa-map-location-dot text-amber-500 mr-2"></i> แผนที่
          </h3>
          <div id="map" class="w-full h-[320px] rounded-xl overflow-hidden shadow-lg"></div>
          @if($hasPos)
            <a href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lng }}"
               target="_blank" rel="noopener"
               class="mt-4 inline-flex w-full justify-center items-center gap-2 px-4 py-3 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 shadow-lg">
              <i class="fa-brands fa-google"></i> เปิดด้วย Google Maps
            </a>
          @endif
        </section>

        {{-- Reviews --}}
        <section class="glass rounded-3xl shadow-xl border border-white/60 p-6 sm:p-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold flex items-center gap-2">
              <i class="fa-solid fa-star text-amber-500"></i> รีวิวจากผู้ใช้
            </h2>
            @auth
              <a href="{{ route('user.reviews.create', ['cafe_id' => $cafe->cafe_id ?? $cafe->id]) }}"
                 class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow">
                <i class="fa-solid fa-pen-to-square"></i> เขียนรีวิว
              </a>
            @endauth
          </div>

          @if($reviews->isEmpty())
            <div class="text-center py-8 rounded-xl border border-dashed border-slate-300 bg-white/60">
              <i class="fa-solid fa-comment-slash text-4xl text-slate-400 mb-3"></i>
              <p class="text-slate-500">ยังไม่มีรีวิวสำหรับคาเฟ่นี้</p>
            </div>
          @else
            <div class="space-y-6">
              @foreach($reviews as $review)
                <div class="border-t border-slate-200 pt-6 first:border-t-0 first:pt-0">
                  <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-800">{{ $review->user_name ?? 'ผู้ใช้ไม่ระบุชื่อ' }}</p>
                    <p class="text-sm text-slate-500">{{ optional($review->created_at)->format('d/m/Y') }}</p>
                  </div>
                  <p class="mt-1 font-bold">
                    @for($i=1;$i<=5;$i++)
                      <i class="fa-solid fa-star {{ $i <= (int)($review->rating ?? 0) ? 'text-amber-500' : 'text-slate-300' }}"></i>
                    @endfor
                    <span class="text-sm ml-1 text-slate-600">({{ $review->rating ?? '-' }}/5)</span>
                  </p>
                  @if(!empty($review->title))
                    <h4 class="font-semibold text-lg mt-3 text-slate-800">{{ $review->title }}</h4>
                  @endif
                  @if(!empty($review->content))
                    <p class="mt-1 text-slate-700 whitespace-pre-line leading-relaxed">{{ $review->content }}</p>
                  @endif

                  @php
                    $revImages = is_string($review->images) ? (json_decode($review->images,true) ?: []) : (is_array($review->images) ? $review->images : []);
                  @endphp
                  @if($revImages)
                    <div class="mt-4 grid grid-cols-3 sm:grid-cols-4 gap-2">
                      @foreach($revImages as $image)
                        <div class="aspect-square rounded-md overflow-hidden border border-white/70">
                          <img src="{{ asset('storage/'.$image) }}"
                               alt="รูปรีวิวของ {{ $review->user_name ?? 'ผู้ใช้' }}"
                               class="w-full h-full object-cover transition-transform duration-300 hover:scale-105 cursor-pointer"
                               loading="lazy"
                               @click="openLightbox('{{ asset('storage/'.$image) }}')">
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @endif
        </section>
      </aside>
    </div>
  </main>

  {{-- LIGHTBOX --}}
  <div x-show="lightbox.open" x-cloak x-transition.opacity
       class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4"
       @keydown.escape.window="lightbox.open=false" @click.self="lightbox.open=false">
    <button class="absolute top-4 right-4 w-12 h-12 rounded-full bg-white/10 text-white hover:bg-white/20"
            @click="lightbox.open=false" aria-label="Close">
      <i class="fa-solid fa-xmark text-2xl"></i>
    </button>
    <img :src="lightbox.src" alt="preview" class="max-w-full max-h-full rounded-lg shadow-2xl"/>
  </div>

  <footer class="py-10 text-center text-slate-500">
    © {{ date('Y') }} ระบบคาเฟ่น้องช้างสะเร็น • ทำด้วย ❤️ ในสุรินทร์
  </footer>

  {{-- MAP & Page Scripts --}}
  <script>
    function cafePage(){
      return {
        lightbox: { open:false, src:'' },
        init(){
          // Map
          const lat = Number('{{ $lat ?? '' }}') || 14.885;
          const lng = Number('{{ $lng ?? '' }}') || 103.490;
          const hasPos = Boolean('{{ $lat }}' && '{{ $lng }}');
          const map = L.map('map', { scrollWheelZoom:true, tap:true }).setView([lat, lng], hasPos ? 16 : 12);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19 }).addTo(map);
          if (hasPos) L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($cafe->cafe_name ?? 'ตำแหน่ง') }}`);
          setTimeout(() => map.invalidateSize(), 250);
        },
        openLightbox(src){ this.lightbox.src = src; this.lightbox.open = true; },
        async copyAddress(text){
          try{
            await navigator.clipboard.writeText(text);
            alert('คัดลอกที่อยู่แล้ว');
          }catch(e){ alert('คัดลอกไม่สำเร็จ'); }
        },
        async sharePage(){
          const shareData = {
            title: document.title,
            text: '{{ $cafe->cafe_name }}',
            url: window.location.href
          };
          if (navigator.share) {
            try { await navigator.share(shareData); } catch(e){}
          } else {
            try{
              await navigator.clipboard.writeText(window.location.href);
              alert('คัดลอกลิงก์แล้ว');
            }catch(e){ alert('แชร์ไม่สำเร็จ'); }
          }
        }
      }
    }
  </script>

  {{-- JSON-LD LocalBusiness (ปรับใช้เฉพาะถ้ามีข้อมูลสำคัญพอ) --}}
  @php
    $jsonLd = [
      '@context' => 'https://schema.org',
      '@type' => 'CafeOrCoffeeShop',
      'name' => $cafe->cafe_name ?? null,
      'description' => $desc ?: null,
      'address' => !empty($cafe->address) ? ['@type'=>'PostalAddress','streetAddress'=>$cafe->address] : null,
      'url' => config('app.url') ? url()->current() : null,
      'telephone' => $phone ?: null,
    ];
    if($hasPos){
      $jsonLd['geo'] = ['@type'=>'GeoCoordinates','latitude'=>$lat,'longitude'=>$lng];
    }
    if(!empty($website)) $jsonLd['sameAs'][] = $website;
    if(!empty($cafe->facebook_page)) $jsonLd['sameAs'][] = str_starts_with($cafe->facebook_page,'http') ? $cafe->facebook_page : 'https://facebook.com/'.$cafe->facebook_page;
    if(!empty($cafe->instagram_page)) $jsonLd['sameAs'][] = str_starts_with($cafe->instagram_page,'http') ? $cafe->instagram_page : 'https://instagram.com/'.$cafe->instagram_page;
    // ลบ key ที่ว่าง
    $jsonLd = array_filter($jsonLd, fn($v)=>!empty($v));
  @endphp
  @if(!empty($jsonLd))
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
  @endif
</body>
</html>
