<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $cafe->cafe_name ?? 'รายละเอียดคาเฟ่' }}</title>

  <!-- Tailwind & Alpine -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

  <!-- Leaflet (แผนที่) -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <style>
    body { font-family:'Kanit',system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,'Apple Color Emoji','Segoe UI Emoji'; }
    [x-cloak]{display:none!important}
  </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800" x-data="{ lightbox:false, lightboxSrc:'' }">

  {{-- นำทางของโปรเจกต์ --}}
  @auth
    @include('components.2navbar')
  @else
    @include('components.1navbar')
  @endauth

  <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

    {{-- HEADER สะอาดตา --}}
    <header class="mb-6 rounded-2xl bg-white border px-5 py-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
          <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
            {{ $cafe->cafe_name ?? '-' }}
            @if(!empty($cafe->is_new_opening))
              <span class="align-middle ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                เปิดใหม่
              </span>
            @endif
          </h1>
          @if(!empty($cafe->place_name))
            <p class="text-slate-500 mt-1">{{ $cafe->place_name }}</p>
          @endif
        </div>

        @if(!empty($cafe->price_range))
          <span class="inline-flex items-center gap-2 self-start md:self-auto px-3 py-1 rounded-full bg-cyan-50 text-cyan-700 border border-cyan-200">
            <i class="fa-solid fa-tags"></i> ช่วงราคา: <strong class="ml-1">{{ $cafe->price_range }}</strong>
          </span>
        @endif
      </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- LEFT: ข้อมูล + แกลเลอรี --}}
      <section class="lg:col-span-2 space-y-6">

        {{-- การ์ดข้อมูลหลัก --}}
        <div class="bg-white rounded-2xl border p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-cyan-500"></i> ข้อมูลคาเฟ่
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            {{-- ที่อยู่ --}}
            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">ที่อยู่</div>
              <div class="font-semibold leading-relaxed">{{ $cafe->address ?? '-' }}</div>
            </div>

            {{-- ติดต่อ --}}
            <div>
              <div class="text-sm text-slate-500">โทรศัพท์</div>
              <div class="font-semibold">{{ $cafe->phone ?? '-' }}</div>
            </div>
            <div>
              <div class="text-sm text-slate-500">อีเมล</div>
              <div class="font-semibold">
                @if(!empty($cafe->email))
                  <a href="mailto:{{ $cafe->email }}" class="text-cyan-600 hover:underline">{{ $cafe->email }}</a>
                @else - @endif
              </div>
            </div>

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">เว็บไซต์</div>
              <div class="font-semibold break-words">
                @if(!empty($cafe->website))
                  <a href="{{ $cafe->website }}" target="_blank" rel="noopener" class="text-cyan-600 hover:underline">
                    {{ $cafe->website }}
                  </a>
                @else - @endif
              </div>
            </div>

            {{-- โซเชียล --}}
            <div>
              <div class="text-sm text-slate-500 flex items-center gap-2"><i class="fa-brands fa-facebook"></i> Facebook</div>
              <div class="font-semibold break-words">
                @if(!empty($cafe->facebook_page))
                  <a href="{{ str_starts_with($cafe->facebook_page,'http') ? $cafe->facebook_page : 'https://facebook.com/'.$cafe->facebook_page }}"
                     target="_blank" rel="noopener" class="text-cyan-600 hover:underline">
                    {{ $cafe->facebook_page }}
                  </a>
                @else - @endif
              </div>
            </div>
            <div>
              <div class="text-sm text-slate-500 flex items-center gap-2"><i class="fa-brands fa-instagram"></i> Instagram</div>
              <div class="font-semibold break-words">
                @if(!empty($cafe->instagram_page))
                  <a href="{{ str_starts_with($cafe->instagram_page,'http') ? $cafe->instagram_page : 'https://instagram.com/'.$cafe->instagram_page }}"
                     target="_blank" rel="noopener" class="text-cyan-600 hover:underline">
                    {{ $cafe->instagram_page }}
                  </a>
                @else - @endif
              </div>
            </div>
            <div class="md:col-span-2">
              <div class="text-sm text-slate-500 flex items-center gap-2"><i class="fa-brands fa-line"></i> LINE</div>
              <div class="font-semibold">{{ $cafe->line_id ?? '-' }}</div>
            </div>

            {{-- เวลาทำการ --}}
            @php
              $open = $cafe->open_time ? \Carbon\Carbon::parse($cafe->open_time)->format('H:i') : null;
              $close = $cafe->close_time ? \Carbon\Carbon::parse($cafe->close_time)->format('H:i') : null;
            @endphp
            <div>
              <div class="text-sm text-slate-500">วันเปิด</div>
              <div class="font-semibold">{{ $cafe->open_day ?? '-' }}</div>
            </div>
            <div>
              <div class="text-sm text-slate-500">วันปิด</div>
              <div class="font-semibold">{{ $cafe->close_day ?? 'ไม่มีวันปิด' }}</div>
            </div>
            <div>
              <div class="text-sm text-slate-500">เวลาเปิด</div>
              <div class="font-semibold">{{ $open ?? '-' }}</div>
            </div>
            <div>
              <div class="text-sm text-slate-500">เวลาปิด</div>
              <div class="font-semibold">{{ $close ?? '-' }}</div>
            </div>

            {{-- Methods/Facilities/Services/Styles --}}
            @php
              $toArray = function($v){
                if (is_array($v)) return array_values(array_filter($v, fn($x)=>trim((string)$x) !== ''));
                if (is_string($v)) {
                  $j = json_decode($v, true);
                  if (json_last_error() === JSON_ERROR_NONE && is_array($j)) return array_values(array_filter($j, fn($x)=>trim((string)$x) !== ''));
                  return array_values(array_filter(array_map('trim', explode(',', $v)), fn($x)=>$x!==''));
                }
                return [];
              };
              $payments  = $toArray($cafe->payment_methods);
              $facils    = $toArray($cafe->facilities);
              $services  = $toArray($cafe->other_services);
              $styles    = $toArray($cafe->cafe_styles);
            @endphp

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">วิธีชำระเงิน</div>
              <div class="mt-1 flex flex-wrap gap-2">
                @forelse($payments as $p)
                  <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">{{ $p }}</span>
                @empty <span class="text-slate-400">-</span> @endforelse
              </div>
            </div>

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">สิ่งอำนวยความสะดวก</div>
              <div class="mt-1 flex flex-wrap gap-2">
                @forelse($facils as $f)
                  <span class="px-3 py-1 rounded-full text-sm bg-cyan-100 text-cyan-800">{{ $f }}</span>
                @empty <span class="text-slate-400">-</span> @endforelse
              </div>
            </div>

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">บริการเพิ่มเติม</div>
              <div class="mt-1 flex flex-wrap gap-2">
                @forelse($services as $s)
                  <span class="px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800">{{ $s }}</span>
                @empty <span class="text-slate-400">-</span> @endforelse
              </div>
            </div>

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">สไตล์คาเฟ่</div>
              <div class="mt-1 flex flex-wrap gap-2">
                @forelse($styles as $stl)
                  <span class="px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">{{ $stl }}</span>
                @empty <span class="text-slate-400">-</span> @endforelse
              </div>
              @if(!empty($cafe->other_style))
                <div class="mt-2 text-sm">
                  <span class="text-slate-500">สไตล์อื่นๆ:</span>
                  <span class="font-semibold">{{ $cafe->other_style }}</span>
                </div>
              @endif
            </div>

            {{-- Booleans --}}
            <div>
              <div class="text-sm text-slate-500">ที่จอดรถ</div>
              @php $flag = (int)($cafe->parking ?? 0) === 1; @endphp
              <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-sm {{ $flag ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid {{ $flag ? 'fa-check-circle' : 'fa-minus-circle' }}"></i>
                {{ $flag ? 'มี' : 'ไม่มี/ไม่ระบุ' }}
              </span>
            </div>

            <div>
              <div class="text-sm text-slate-500">รับบัตรเครดิต</div>
              @php $flag = (int)($cafe->credit_card ?? 0) === 1; @endphp
              <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-sm {{ $flag ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid {{ $flag ? 'fa-check-circle' : 'fa-minus-circle' }}"></i>
                {{ $flag ? 'รองรับ' : 'ไม่รองรับ/ไม่ระบุ' }}
              </span>
            </div>
          </div>
        </div>

        {{-- แกลเลอรีรูปภาพ --}}
        @php
          $images = is_string($cafe->images)
            ? (json_decode($cafe->images, true) ?: [])
            : (is_array($cafe->images) ? $cafe->images : []);
        @endphp

        @if(!empty($images))
          <div class="bg-white rounded-2xl border p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
              <i class="fa-solid fa-images text-amber-500"></i> แกลเลอรีรูปภาพ
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
              @foreach($images as $img)
                <div class="aspect-square rounded-lg overflow-hidden border group">
                  <img
                    src="{{ asset('storage/'.$img) }}"
                    alt="รูป {{ $cafe->cafe_name }}"
                    class="w-full h-full object-cover cursor-pointer transition-transform duration-300 group-hover:scale-110"
                    @click="lightboxSrc='{{ asset('storage/'.$img) }}'; lightbox=true"
                    loading="lazy"
                  />
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </section>

      {{-- RIGHT: แผนที่เท่านั้น --}}
      <aside class="space-y-6">
        <div class="bg-white rounded-2xl border p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-map-location-dot text-rose-500"></i> แผนที่
          </h2>

          <div id="map" class="w-full h-[340px] rounded-xl overflow-hidden border"></div>

          @if(!empty($cafe->lat) && !empty($cafe->lng))
            <a href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}"
               target="_blank" rel="noopener"
               class="mt-4 inline-flex w-full justify-center items-center gap-2 rounded-lg bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 font-semibold">
              <i class="fa-brands fa-google"></i> นำทางด้วย Google Maps
            </a>
          @endif
        </div>
      </aside>
    </div>
  </main>

  {{-- LIGHTBOX --}}
  <div x-show="lightbox" x-cloak
       x-transition.opacity
       class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4"
       @click.self="lightbox=false">
    <button class="absolute top-4 right-4 w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/20"
            @click="lightbox=false" aria-label="Close">
      <i class="fa-solid fa-xmark text-2xl"></i>
    </button>
    <img :src="lightboxSrc" alt="preview" class="max-w-full max-h-full rounded-lg shadow-2xl"/>
  </div>

  {{-- สคริปต์แผนที่ --}}
  <script>
    (function () {
      const lat = Number('{{ $cafe->lat ?? '' }}') || 14.885;
      const lng = Number('{{ $cafe->lng ?? '' }}') || 103.490;
      const map = L.map('map', { scrollWheelZoom: true, tap: true }).setView([lat, lng], ({{ !empty($cafe->lat) && !empty($cafe->lng) ? 16 : 12 }}));
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
      L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($cafe->cafe_name ?? 'ตำแหน่ง') }}`).openPopup();
      setTimeout(() => map.invalidateSize(), 250);
    })();
  </script>

</body>
</html>
