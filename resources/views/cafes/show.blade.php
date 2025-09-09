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

  <!-- Swiper (Gallery) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

  <style>
    body{font-family:'Kanit',sans-serif;background:
      radial-gradient(1200px 600px at 20% 0%, #fef3c7 0%, transparent 60%),
      radial-gradient(1200px 600px at 100% 20%, #cffafe 0%, transparent 55%),
      linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%)}
    [x-cloak]{display:none!important}
    .glass{backdrop-filter:blur(12px); background:rgba(255,255,255,.75)}
  </style>
</head>
<body class="min-h-screen text-slate-800" x-data="{ tab:'info', lightbox:false, lightboxSrc:'' }">

  {{-- Navbar (เดิม) --}}
  @guest @include('components.1navbar') @endguest
  @auth  @include('components.2navbar') @endauth

  @php
    $imgs = is_string($cafe->images) ? (json_decode($cafe->images, true) ?: []) : (is_array($cafe->images) ? $cafe->images : []);
    $thumbs = array_slice($imgs, 0, 8);

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

  <!-- HERO (ย่อความสูง & ใช้สัดส่วน 16:9) -->
  <header class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
    <div class="glass rounded-3xl shadow-xl border border-white/60 overflow-hidden">
      <div class="relative">
        <div class="swiper mySwiper aspect-[16/9] max-h-[380px]">
          <div class="swiper-wrapper">
            @forelse($thumbs as $t)
              <div class="swiper-slide">
                <img src="{{ asset('storage/'.$t) }}" alt="ภาพ {{ $cafe->cafe_name }}"
                     class="w-full h-full object-cover cursor-pointer"
                     @click="lightboxSrc='{{ asset('storage/'.$t) }}'; lightbox=true">
              </div>
            @empty
              <div class="swiper-slide bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                <div class="text-center">
                  <i class="fa-regular fa-image text-4xl text-slate-400"></i>
                  <p class="mt-2 text-slate-500">ยังไม่มีรูปภาพ</p>
                </div>
              </div>
            @endforelse
          </div>
          <div class="swiper-pagination"></div>
          <!-- ลูกศรเอาออกเพื่อความเรียบ -->
        </div>

        <!-- Badge เล็กๆ (ไม่ใช่ปุ่ม) -->
        <div class="absolute bottom-3 left-3 right-3 flex flex-wrap gap-2 text-xs">
          @if(!empty($cafe->is_new_opening))
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100/90 text-amber-800 border border-amber-200">
              <i class="fa-solid fa-bolt"></i> เปิดใหม่
            </span>
          @endif
          @if(!empty($cafe->price_range))
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-cyan-50/90 text-cyan-700 border border-cyan-200">
              <i class="fa-solid fa-tags"></i> {{ $cafe->price_range }}
            </span>
          @endif
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full {{ $hasParking?'bg-emerald-50/90 text-emerald-700 border border-emerald-200':'bg-slate-100/90 text-slate-600 border' }}">
            <i class="fa-solid fa-square-parking"></i> จอดรถ {{ $hasParking?'ได้':'-' }}
          </span>
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full {{ $hasCC?'bg-emerald-50/90 text-emerald-700 border border-emerald-200':'bg-slate-100/90 text-slate-600 border' }}">
            <i class="fa-regular fa-credit-card"></i> บัตรเครดิต {{ $hasCC?'รองรับ':'-' }}
          </span>
        </div>
      </div>

      <!-- ชื่อ & คะแนนแบบเรียบ -->
      <div class="p-6 sm:p-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
          {{ $cafe->cafe_name }}
        </h1>
        @if(!empty($cafe->place_name))
          <p class="text-slate-600 mt-1">{{ $cafe->place_name }}</p>
        @endif>

        @if($avgRating)
          <div class="mt-3 text-slate-700">
            <span class="font-bold">{{ number_format($avgRating,1) }}</span>
            @for($i=1;$i<=5;$i++)
              <i class="fa-solid fa-star {{ $i <= floor($avgRating) ? 'text-amber-500' : 'text-slate-300' }}"></i>
            @endfor
            <span class="text-sm text-slate-500">จาก {{ $reviewCount }} รีวิว</span>
          </div>
        @endif
      </div>

      <!-- Tabs (ปุ่มระบบภายในหน้า – ยังจำเป็นสำหรับสลับเนื้อหา) -->
      <div class="px-6 sm:px-8 pb-2">
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

        <!-- INFO -->
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
                @if(!empty($cafe->lat) && !empty($cafe->lng))
                  <p class="mt-1 text-sm">
                    <a class="text-cyan-600 hover:underline break-all"
                       href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}"
                       target="_blank" rel="noopener">
                      เปิดดูบน Google Maps
                    </a>
                  </p>
                @endif
              </div>
            </div>

            <div class="flex items-center">
              <i class="fa-solid fa-phone text-cyan-500 w-5 mr-3 shrink-0"></i>
              <span><strong>โทรศัพท์:</strong> {{ $cafe->phone ?? '-' }}</span>
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

            {{-- เวลาทำการ --}}
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

          {{-- รายละเอียด --}}
          @if($desc !== '')
          <div class="mt-6 pt-6 border-t border-slate-200">
            <h3 class="text-lg font-semibold mb-2 flex items-center">
              <i class="fa-solid fa-circle-info mr-2 text-cyan-500"></i> รายละเอียดเพิ่มเติม
            </h3>
            <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $desc }}</p>
          </div>
          @endif

          {{-- แท็กหมวดต่าง ๆ --}}
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

        <!-- REVIEWS -->
        <section x-show="tab==='reviews'" x-cloak
                 class="glass rounded-3xl shadow-xl border border-white/60 p-6 sm:p-8">
          <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
            <i class="fa-solid fa-star text-amber-500"></i> รีวิวจากผู้ใช้
          </h2>

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
                    <div class="mt-4 grid grid-cols-3 sm:grid-cols-4 gap-2">
                      @foreach($revImages as $image)
                        <div class="aspect-square rounded-md overflow-hidden border border-white/70">
                          <img src="{{ asset('storage/'.$image) }}"
                               alt="รูปรีวิวของ {{ $review->user_name ?? 'ผู้ใช้' }}"
                               class="w-full h-full object-cover transition-transform duration-300 hover:scale-105 cursor-pointer"
                               loading="lazy"
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
        <!-- MAP -->
        <section :class="tab==='map' ? 'ring-2 ring-amber-300' : ''"
                 class="glass rounded-3xl shadow-xl border border-white/60 p-6">
          <h3 class="text-xl font-bold mb-4 flex items-center">
            <i class="fa-solid fa-map-location-dot text-amber-500 mr-2"></i> แผนที่
          </h3>
          <div id="map" class="w-full h-[280px] rounded-xl overflow-hidden shadow-lg"></div>
          {{-- ปุ่มเปิด Maps เอาออก เหลือแค่ลิงก์ด้านบนในที่อยู่ --}}
        </section>
      </aside>
    </div>
  </main>

  {{-- LIGHTBOX --}}
  <div x-show="lightbox" x-cloak
       x-transition.opacity
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

  <!-- SWIPER INIT (ไม่มีลูกศร/auto-play ช้าๆ) -->
  <script>
    new Swiper('.mySwiper', {
      loop: true,
      slidesPerView: 1,
      spaceBetween: 6,
      pagination: { el: '.swiper-pagination', clickable: true },
      autoplay: { delay: 4200, disableOnInteraction: false },
    });
  </script>
</body>
</html>
