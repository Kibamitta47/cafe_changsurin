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
    body{
      font-family:'Kanit',sans-serif;
      background:
        radial-gradient(1200px 600px at 20% 0%, #fef3c7 0%, transparent 60%),
        radial-gradient(1200px 600px at 100% 20%, #cffafe 0%, transparent 55%),
        linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    }
    [x-cloak]{display:none!important}
    .glass{backdrop-filter:blur(12px); background:rgba(255,255,255,.75)}
    .chip{ @apply inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm border; }
    .no-scrollbar::-webkit-scrollbar{display:none}
    .no-scrollbar{ -ms-overflow-style:none; scrollbar-width:none }
  </style>
</head>
<body class="min-h-screen text-slate-800"
      x-data="{ tab:'info', lightbox:false, lightboxSrc:'', copied:false }"
      x-init="setTimeout(()=>copied=false,2000)">

  @guest @include('components.1navbar') @endguest
  @auth  @include('components.2navbar') @endauth

  @php
    // รูปภาพ
    $imgs = is_string($cafe->images) ? (json_decode($cafe->images, true) ?: []) : (is_array($cafe->images) ? $cafe->images : []);
    $thumbs = array_slice($imgs, 0, 8);
    $featured = $thumbs[0] ?? null;

    // helper array/string -> array
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

  <!-- HERO: Collage Layout (ไม่มีสไลด์เดอร์) -->
  <header class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
    <div class="glass rounded-3xl shadow-xl border border-white/60 overflow-hidden">
      <div class="relative p-2 sm:p-3">
        @php
          $side1 = $thumbs[1] ?? null;
          $side2 = $thumbs[2] ?? null;
        @endphp

        <!-- Desktop/Tablet -->
        <div class="hidden sm:grid grid-cols-3 gap-2 sm:gap-3 h-[300px] lg:h-[420px]">
          <!-- รูปใหญ่ซ้าย -->
          <div class="col-span-2 rounded-2xl overflow-hidden shadow-lg">
            @if($featured)
              <img src="{{ asset('storage/'.$featured) }}" alt="ภาพหลัก {{ $cafe->cafe_name }}"
                   class="w-full h-full object-cover cursor-pointer"
                   loading="lazy" decoding="async"
                   @click="lightboxSrc='{{ asset('storage/'.$featured) }}'; lightbox=true">
            @else
              <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                <i class="fa-regular fa-image text-4xl text-slate-400"></i>
              </div>
            @endif
          </div>

          <!-- คอลัมน์ขวา: สองรูปซ้อน -->
          <div class="grid grid-rows-2 gap-2 sm:gap-3">
            <div class="rounded-2xl overflow-hidden shadow">
              @if($side1)
                <img src="{{ asset('storage/'.$side1) }}" alt="ภาพเสริม 1"
                     class="w-full h-full object-cover cursor-pointer"
                     loading="lazy" decoding="async"
                     @click="lightboxSrc='{{ asset('storage/'.$side1) }}'; lightbox=true">
              @else
                <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                  <i class="fa-regular fa-image text-2xl text-slate-400"></i>
                </div>
              @endif
            </div>
            <div class="rounded-2xl overflow-hidden shadow">
              @if($side2)
                <img src="{{ asset('storage/'.$side2) }}" alt="ภาพเสริม 2"
                     class="w-full h-full object-cover cursor-pointer"
                     loading="lazy" decoding="async"
                     @click="lightboxSrc='{{ asset('storage/'.$side2) }}'; lightbox=true">
              @else
                <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                  <i class="fa-regular fa-image text-2xl text-slate-400"></i>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Mobile -->
        <div class="sm:hidden space-y-2">
          <div class="h-[220px] rounded-2xl overflow-hidden shadow-lg">
            @if($featured)
              <img src="{{ asset('storage/'.$featured) }}" alt="ภาพหลัก {{ $cafe->cafe_name }}"
                   class="w-full h-full object-cover cursor-pointer"
                   loading="lazy" decoding="async"
                   @click="lightboxSrc='{{ asset('storage/'.$featured) }}'; lightbox=true">
            @else
              <div class="w-full h-full bg-slate-100 flex items-center justify-center rounded-2xl">
                <i class="fa-regular fa-image text-3xl text-slate-400"></i>
              </div>
            @endif
          </div>

          @if(count($thumbs) > 1)
            <div class="flex gap-2 overflow-x-auto no-scrollbar">
              @foreach(array_slice($thumbs,1,8) as $t)
                <div class="min-w-[96px] h-[72px] rounded-xl overflow-hidden border border-white/70 flex-shrink-0">
                  <img src="{{ asset('storage/'.$t) }}" alt="thumb"
                       class="w-full h-full object-cover cursor-pointer"
                       loading="lazy" decoding="async"
                       @click="lightboxSrc='{{ asset('storage/'.$t) }}'; lightbox=true">
                </div>
              @endforeach
            </div>
          @endif
        </div>

        <!-- Badges -->
        <div class="absolute bottom-3 left-3 right-3 flex flex-wrap gap-2 pointer-events-none">
          @if(!empty($cafe->is_new_opening))
            <span class="chip bg-amber-100/90 border-amber-200 text-amber-800 pointer-events-auto">
              <i class="fa-solid fa-bolt"></i> เปิดใหม่
            </span>
          @endif
          @if(!empty($cafe->price_range))
            <span class="chip bg-cyan-50/90 border-cyan-200 text-cyan-700 pointer-events-auto">
              <i class="fa-solid fa-tags"></i> {{ $cafe->price_range }}
            </span>
          @endif
          <span class="chip {{ $hasParking?'bg-emerald-50/90 border-emerald-200 text-emerald-700':'bg-slate-100/90 border-slate-300 text-slate-600' }} pointer-events-auto">
            <i class="fa-solid fa-square-parking"></i> จอดรถ {{ $hasParking?'ได้':'-' }}
          </span>
          <span class="chip {{ $hasCC?'bg-emerald-50/90 border-emerald-200 text-emerald-700':'bg-slate-100/90 border-slate-300 text-slate-600' }} pointer-events-auto">
            <i class="fa-regular fa-credit-card"></i> บัตรเครดิต {{ $hasCC?'รองรับ':'-' }}
          </span>
        </div>
      </div>

      <!-- Title + Rating + Action -->
      <div class="p-6 sm:p-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
              {{ $cafe->cafe_name }}
            </h1>
            @if(!empty($cafe->place_name))
              <p class="text-slate-600 mt-1">{{ $cafe->place_name }}</p>
            @endif
          </div>

          <div class="flex items-center gap-3">
            <button class="px-4 py-2 rounded-xl bg-white shadow border text-slate-700 hover:bg-slate-50"
                    @click="navigator.share ? navigator.share({title:'{{ addslashes($cafe->cafe_name) }}', url: window.location.href}) : (async()=>{await navigator.clipboard.writeText(window.location.href); copied=true; setTimeout(()=>copied=false,1500)})()">
              <i class="fa-solid fa-share-nodes"></i> แชร์
            </button>
            @auth
              <a href="{{ route('user.reviews.create', ['cafe_id' => $cafe->cafe_id ?? $cafe->id]) }}"
                 class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow">
                <i class="fa-solid fa-pen-to-square"></i> เขียนรีวิว
              </a>
            @endauth
          </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-3">
          @if($avgRating)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
              <span class="font-bold text-lg">{{ number_format($avgRating,1) }}</span>
              <span>
                @for($i=1;$i<=5;$i++)
                  <i class="fa-solid fa-star {{ $i <= floor($avgRating) ? 'text-amber-500' : 'text-slate-300' }}"></i>
                @endfor
              </span>
              <span class="text-sm text-slate-600">จาก {{ $reviewCount }} รีวิว</span>
            </div>
          @else
            <div class="text-slate-500">ยังไม่มีรีวิว</div>
          @endif
          <div x-show="copied" class="text-emerald-700 text-sm"><i class="fa-regular fa-circle-check"></i> คัดลอกลิงก์แล้ว</div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="px-6 sm:px-8">
        <div class="border-b border-slate-200 flex gap-6 text-slate-600">
          <button class="py-3 -mb-px border-b-2"
                  :class="tab==='info' ? 'border-cyan-500 text-cyan-700 font-semibold' : 'border-transparent hover:text-slate-800'"
                  @click="tab='info'">ข้อมูลคาเฟ่</button>
          <button class="py-3 -mb-px border-b-2"
                  :class="tab==='reviews' ? 'border-cyan-500 text-cyan-700 font-semibold' : 'border-transparent hover:text-slate-800'"
                  @click="tab='reviews'">รีวิวผู้ใช้ ({{ $reviewCount }})</button>
          <button class="py-3 -mb-px border-b-2"
                  :class="tab==='map' ? 'border-cyan-500 text-cyan-700 font-semibold' : 'border-transparent hover:text-slate-800'"
                  @click="tab='map'">แผนที่</button>
        </div>
      </div>
    </div>
  </header>

  <!-- BODY -->
  <main class="py-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-7 px-4 sm:px-6 lg:px-8">
      <!-- LEFT -->
      <div class="lg:col-span-2 space-y-7">
        <!-- INFO TAB -->
        <section x-show="tab==='info'" x-cloak
                 class="glass rounded-3xl shadow-xl border border-white/60 p-6 sm:p-8">
          <h2 class="text-xl font-bold mb-5 flex items-center gap-2 text-slate-900">
            <i class="fa-solid fa-circle-info text-cyan-500"></i> ข้อมูลคาเฟ่
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-slate-700">
            <div class="flex items-start">
              <i class="fa-solid fa-location-dot text-cyan-500 w-5 mt-1 mr-3 shrink-0"></i>
              <div class="min-w-0">
                <strong>ที่อยู่:</strong>
                <p class="break-words">{{ $cafe->address }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                  <button class="px-3 py-1 rounded-lg bg-white border shadow text-slate-700 hover:bg-slate-50"
                          @click="await navigator.clipboard.writeText(`{{ trim(preg_replace('/\s+/', ' ', $cafe->address)) }}`); copied=true; setTimeout(()=>copied=false,1500)">
                    <i class="fa-regular fa-copy"></i> คัดลอกที่อยู่
                  </button>
                  @if(!empty($cafe->lat) && !empty($cafe->lng))
                  <a class="px-3 py-1 rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow"
                     href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}" target="_blank" rel="noopener">
                    <i class="fa-brands fa-google"></i> เปิดใน Google Maps
                  </a>
                  @endif
                </div>
              </div>
            </div>

            <div class="flex items-center">
              <i class="fa-solid fa-phone text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>โทรศัพท์:</strong>
                @if(!empty($cafe->phone))
                  <a href="tel:{{ preg_replace('/\s+/', '', $cafe->phone) }}" class="text-cyan-600 hover:underline">{{ $cafe->phone }}</a>
                @else - @endif
              </span>
            </div>

            <div class="flex items-center">
              <i class="fa-solid fa-envelope text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>อีเมล:</strong>
                @if(!empty($cafe->email))
                  <a href="mailto:{{ $cafe->email }}" class="text-cyan-600 hover:underline">{{ $cafe->email }}</a>
                @else - @endif
              </span>
            </div>

            <div class="flex items-center">
              <i class="fa-solid fa-globe text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>เว็บไซต์:</strong>
                @if(!empty($cafe->website))
                  <a href="{{ $cafe->website }}" target="_blank" rel="noopener" class="text-cyan-600 hover:underline break-all">
                    {{ $cafe->website }}
                  </a>
                @else - @endif
              </span>
            </div>

            <div class="flex items-center">
              <i class="fa-brands fa-facebook text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>Facebook:</strong>
                @if(!empty($cafe->facebook_page))
                  <a href="{{ str_starts_with($cafe->facebook_page,'http') ? $cafe->facebook_page : 'https://facebook.com/'.$cafe->facebook_page }}"
                     target="_blank" rel="noopener" class="text-cyan-600 hover:underline break-all">
                    {{ $cafe->facebook_page }}
                  </a>
                @else - @endif
              </span>
            </div>

            <div class="flex items-center">
              <i class="fa-brands fa-instagram text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>Instagram:</strong>
                @if(!empty($cafe->instagram_page))
                  <a href="{{ str_starts_with($cafe->instagram_page,'http') ? $cafe->instagram_page : 'https://instagram.com/'.$cafe->instagram_page }}"
                     target="_blank" rel="noopener" class="text-cyan-600 hover:underline break-all">
                    {{ $cafe->instagram_page }}
                  </a>
                @else - @endif
              </span>
            </div>

            <div class="flex items-center">
              <i class="fa-brands fa-line text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>LINE:</strong> {{ $cafe->line_id ?? '-' }}</span>
            </div>

            <div class="flex items-start md:col-span-2">
              <i class="fa-solid fa-clock text-cyan-500 w-5 mt-1 mr-3 shrink-0"></i>
              <span>
                <strong>เวลาทำการ:</strong>
                @php
                  $hasStructured = $cafe->open_day && $cafe->open_time && $cafe->close_time;
                  $closeDayText = $cafe->close_day ? ' - ' . $cafe->close_day : '';
                @endphp
                @if($hasStructured)
                  {{ $cafe->open_day }}{{ $closeDayText }},
                  {{ \Carbon\Carbon::parse($cafe->open_time)->format('H:i') }}
                  -
                  {{ \Carbon\Carbon::parse($cafe->close_time)->format('H:i') }}
                @else
                  {{ $cafe->opening_hours ?? '-' }}
                @endif
              </span>
            </div>
          </div>

          @if($desc !== '')
          <div class="mt-6 pt-6 border-t border-slate-200">
            <h3 class="text-lg font-semibold mb-2 flex items-center">
              <i class="fa-solid fa-circle-info mr-2 text-cyan-500"></i> รายละเอียดเพิ่มเติม
            </h3>
            <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $desc }}</p>
          </div>
          @endif

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

        <!-- REVIEWS TAB -->
        <section x-show="tab==='reviews'" x-cloak
                 class="glass rounded-3xl shadow-xl border border-white/60 p-6 sm:p-8">
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
            <div class="text-center py-12 rounded-xl border border-dashed border-slate-300 bg-white/60">
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
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                      @foreach($revImages as $image)
                        <div class="aspect-square rounded-md overflow-hidden border border-white/70">
                          <img src="{{ asset('storage/'.$image) }}"
                               alt="รูปรีวิวของ {{ $review->user_name ?? 'ผู้ใช้' }}"
                               class="w-full h-full object-cover transition-transform duration-300 hover:scale-105 cursor-pointer"
                               loading="lazy" decoding="async"
                               @click="lightboxSrc='{{ asset('storage/'.$image) }}'; lightbox=true">
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @endif
        </section>
      </div>

      <!-- RIGHT -->
      <aside class="space-y-7 lg:sticky lg:top-24 h-max">
        <section :class="tab==='map' ? 'ring-2 ring-amber-300' : ''"
                 class="glass rounded-3xl shadow-xl border border-white/60 p-6">
          <h3 class="text-xl font-bold mb-4 flex items-center">
            <i class="fa-solid fa-map-location-dot text-amber-500 mr-2"></i> แผนที่
          </h3>
          <div id="map" class="w-full h-[300px] rounded-xl overflow-hidden shadow-lg"></div>
          @if(!empty($cafe->lat) && !empty($cafe->lng))
            <a href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}"
               target="_blank" rel="noopener"
               class="mt-4 inline-flex w-full justify-center items-center gap-2 px-4 py-3 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 shadow-lg">
              <i class="fa-brands fa-google"></i> เปิดด้วย Google Maps
            </a>
          @endif
        </section>

        <section class="glass rounded-3xl shadow-xl border border-white/60 p-6">
          <h3 class="text-xl font-bold mb-4 flex items-center">
            <i class="fa-solid fa-bolt text-emerald-600 mr-2"></i> ด่วน & ติดต่อ
          </h3>
          <div class="grid grid-cols-2 gap-3">
            <a href="{{ !empty($cafe->phone) ? 'tel:'.preg_replace('/\s+/', '', $cafe->phone) : '#' }}"
               class="px-4 py-3 rounded-xl bg-emerald-600 text-white text-center font-semibold shadow hover:bg-emerald-700 {{ empty($cafe->phone)?'pointer-events-none opacity-50':'' }}">
              <i class="fa-solid fa-phone"></i> โทร
            </a>
            <button class="px-4 py-3 rounded-xl bg-white text-slate-700 border shadow hover:bg-slate-50"
                    @click="navigator.share ? navigator.share({title:'{{ addslashes($cafe->cafe_name) }}', text:'ชวนไปคาเฟ่นี้กันไหม?', url: location.href}) : (async()=>{await navigator.clipboard.writeText(location.href); copied=true; setTimeout(()=>copied=false,1500)})()">
              <i class="fa-solid fa-share-nodes"></i> แชร์
            </button>
            <a href="{{ !empty($cafe->website) ? $cafe->website : '#' }}" target="_blank" rel="noopener"
               class="px-4 py-3 rounded-xl bg-cyan-600 text-white text-center font-semibold shadow hover:bg-cyan-700 {{ empty($cafe->website)?'pointer-events-none opacity-50':'' }}">
              <i class="fa-solid fa-globe"></i> เว็บไซต์
            </a>
            @auth
            <a href="{{ route('user.reviews.create', ['cafe_id' => $cafe->cafe_id ?? $cafe->id]) }}"
               class="px-4 py-3 rounded-xl bg-amber-500 text-white text-center font-semibold hover:bg-amber-600">
              <i class="fa-solid fa-pen-to-square"></i> รีวิว
            </a>
            @endauth
          </div>
        </section>
      </aside>
    </div>
  </main>

  <!-- MOBILE STICKY BAR -->
  <div class="fixed bottom-3 left-3 right-3 z-40 lg:hidden">
    <div class="glass rounded-2xl shadow-2xl border border-white/60 p-2 grid grid-cols-4 gap-2">
      <a href="{{ !empty($cafe->phone) ? 'tel:'.preg_replace('/\s+/', '', $cafe->phone) : '#' }}"
         class="flex flex-col items-center py-2 rounded-xl bg-white shadow border {{ empty($cafe->phone)?'pointer-events-none opacity-50':'' }}">
        <i class="fa-solid fa-phone"></i><span class="text-xs mt-1">โทร</span>
      </a>
      <button class="flex flex-col items-center py-2 rounded-xl bg-white shadow border"
              @click="tab='map'; window.scrollTo({top:0, behavior:'smooth'})">
        <i class="fa-solid fa-map-location-dot"></i><span class="text-xs mt-1">แผนที่</span>
      </button>
      <button class="flex flex-col items-center py-2 rounded-xl bg-white shadow border"
              @click="tab='reviews'; document.querySelector('main').scrollIntoView({behavior:'smooth'})">
        <i class="fa-solid fa-star"></i><span class="text-xs mt-1">รีวิว</span>
      </button>
      <button class="flex flex-col items-center py-2 rounded-xl bg-amber-500 text-white shadow border border-amber-300"
              @click="navigator.share ? navigator.share({title:'{{ addslashes($cafe->cafe_name) }}', url: location.href}) : (async()=>{await navigator.clipboard.writeText(location.href); copied=true; setTimeout(()=>copied=false,1500)})()">
        <i class="fa-solid fa-share-nodes"></i><span class="text-xs mt-1">แชร์</span>
      </button>
    </div>
  </div>

  <!-- LIGHTBOX -->
  <div x-show="lightbox" x-cloak x-transition.opacity
       class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4"
       @click.self="lightbox=false">
    <button class="absolute top-4 right-4 w-12 h-12 rounded-full bg-white/10 text-white hover:bg-white/20"
            @click="lightbox=false" aria-label="Close">
      <i class="fa-solid fa-xmark text-2xl"></i>
    </button>
    <img :src="lightboxSrc" alt="preview" class="max-w-full max-h-full rounded-lg shadow-2xl"/>
  </div>

  <footer class="py-6 text-center text-slate-500">
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
