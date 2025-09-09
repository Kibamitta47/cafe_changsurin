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
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

  <style>
    body { font-family:'Kanit',system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,'Apple Color Emoji','Segoe UI Emoji'; }
    [x-cloak]{display:none!important}
  </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800" x-data="{ lightbox:false, lightboxSrc:'' }">

  {{-- ถ้ามี navbar ของโปรเจกต์ --}}
  @auth
    @include('components.2navbar')
  @else
    @include('components.1navbar')
  @endauth

  <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

    {{-- HEADER --}}
    <div class="mb-6 flex items-start justify-between gap-4">
      <div>
        <h1 class="text-3xl sm:text-4xl font-bold">
          {{ $cafe->cafe_name ?? '-' }}
          @if(!empty($cafe->is_new_opening))
            <span class="align-middle ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
              เปิดใหม่
            </span>
          @endif
        </h1>
        <p class="text-slate-500 mt-1">{{ $cafe->place_name ?? '' }}</p>
      </div>

      {{-- Status badge + ปุ่มกลับ --}}
      <div class="flex items-center gap-2">
        @php
          $status = $cafe->status ?? 'inactive';
          $statusMap = [
            'active'   => ['bg'=>'bg-green-100', 'text'=>'text-green-800', 'label'=>'เผยแพร่'],
            'inactive' => ['bg'=>'bg-slate-100','text'=>'text-slate-700','label'=>'ซ่อน'],
            'pending'  => ['bg'=>'bg-amber-100','text'=>'text-amber-800','label'=>'รอตรวจ'],
            'rejected' => ['bg'=>'bg-rose-100','text'=>'text-rose-800','label'=>'ไม่อนุมัติ'],
          ];
          $st = $statusMap[$status] ?? $statusMap['inactive'];
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $st['bg'] }} {{ $st['text'] }}">
          สถานะ: {{ $st['label'] }}
        </span>

        <a href="{{ route('admin.cafe.index') }}"
           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-slate-50 text-slate-700">
          <i class="fa-solid fa-arrow-left"></i>
          กลับสู่รายการ
        </a>
      </div>
    </div>

    {{-- META IDs --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="bg-white rounded-xl border p-4">
        <div class="text-xs text-slate-500">User ID</div>
        <div class="font-semibold">{{ $cafe->user_id ?? '-' }}</div>
      </div>
      <div class="bg-white rounded-xl border p-4">
        <div class="text-xs text-slate-500">Admin ID</div>
        <div class="font-semibold">{{ $cafe->admin_id ?? '-' }}</div>
      </div>
      <div class="bg-white rounded-xl border p-4">
        <div class="text-xs text-slate-500">Cafe Primary Key</div>
        <div class="font-semibold">{{ $cafe->id ?? ($cafe->cafe_id ?? '-') }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- LEFT: ข้อมูลหลัก + แกลเลอรี --}}
      <div class="lg:col-span-2 space-y-6">

        {{-- การ์ดข้อมูลหลักทั้งหมด --}}
        <section class="bg-white rounded-2xl border p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-cyan-500"></i> ข้อมูลคาเฟ่
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

            {{-- ชื่อ/สถานที่/ที่อยู่ --}}
            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">ชื่อคาเฟ่</div>
              <div class="font-semibold">{{ $cafe->cafe_name ?? '-' }}</div>
            </div>
            <div>
              <div class="text-sm text-slate-500">ชื่อสถานที่</div>
              <div class="font-semibold">{{ $cafe->place_name ?? '-' }}</div>
            </div>
            <div>
              <div class="text-sm text-slate-500">ช่วงราคา</div>
              <div class="font-semibold">{{ $cafe->price_range ?? '-' }}</div>
            </div>
            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">ที่อยู่</div>
              <div class="font-semibold leading-relaxed">{{ $cafe->address ?? '-' }}</div>
            </div>

            {{-- พิกัด --}}
            <div>
              <div class="text-sm text-slate-500">Latitude</div>
              <div class="font-semibold">{{ $cafe->lat ?? '-' }}</div>
            </div>
            <div>
              <div class="text-sm text-slate-500">Longitude</div>
              <div class="font-semibold">{{ $cafe->lng ?? '-' }}</div>
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
                @else -
                @endif
              </div>
            </div>

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">เว็บไซต์</div>
              <div class="font-semibold break-words">
                @if(!empty($cafe->website))
                  <a href="{{ $cafe->website }}" target="_blank" rel="noopener" class="text-cyan-600 hover:underline">
                    {{ $cafe->website }}
                  </a>
                @else -
                @endif
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
                @else -
                @endif
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
                @else -
                @endif
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

            {{-- ค่าความสะดวก/การชำระเงิน/บริการ/สไตล์/อื่น ๆ --}}
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
                @empty
                  <span class="text-slate-400">-</span>
                @endforelse
              </div>
            </div>

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">สิ่งอำนวยความสะดวก</div>
              <div class="mt-1 flex flex-wrap gap-2">
                @forelse($facils as $f)
                  <span class="px-3 py-1 rounded-full text-sm bg-cyan-100 text-cyan-800">{{ $f }}</span>
                @empty
                  <span class="text-slate-400">-</span>
                @endforelse
              </div>
            </div>

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">บริการเพิ่มเติม</div>
              <div class="mt-1 flex flex-wrap gap-2">
                @forelse($services as $s)
                  <span class="px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800">{{ $s }}</span>
                @empty
                  <span class="text-slate-400">-</span>
                @endforelse
              </div>
            </div>

            <div class="md:col-span-2">
              <div class="text-sm text-slate-500">สไตล์คาเฟ่</div>
              <div class="mt-1 flex flex-wrap gap-2">
                @forelse($styles as $stl)
                  <span class="px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">{{ $stl }}</span>
                @empty
                  <span class="text-slate-400">-</span>
                @endforelse
              </div>
              @if(!empty($cafe->other_style))
                <div class="mt-2 text-sm">
                  <span class="text-slate-500">สไตล์อื่นๆ:</span>
                  <span class="font-semibold">{{ $cafe->other_style }}</span>
                </div>
              @endif
            </div>

            {{-- ฟิลด์ boolean/option เพิ่มเติม --}}
            <div>
              <div class="text-sm text-slate-500">ที่จอดรถ (parking)</div>
              @php $flag = (int)($cafe->parking ?? 0) === 1; @endphp
              <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-sm {{ $flag ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid {{ $flag ? 'fa-check-circle' : 'fa-minus-circle' }}"></i>
                {{ $flag ? 'มี' : 'ไม่มี/ไม่ระบุ' }}
              </span>
            </div>

            <div>
              <div class="text-sm text-slate-500">รับบัตรเครดิต (credit_card)</div>
              @php $flag = (int)($cafe->credit_card ?? 0) === 1; @endphp
              <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-sm {{ $flag ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid {{ $flag ? 'fa-check-circle' : 'fa-minus-circle' }}"></i>
                {{ $flag ? 'รองรับ' : 'ไม่รองรับ/ไม่ระบุ' }}
              </span>
            </div>

          </div>
        </section>

        {{-- แกลเลอรีรูปภาพ --}}
        @php
          $images = is_string($cafe->images)
            ? (json_decode($cafe->images, true) ?: [])
            : (is_array($cafe->images) ? $cafe->images : []);
        @endphp

        @if(!empty($images))
          <section class="bg-white rounded-2xl border p-6">
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
          </section>
        @endif

      </div>

      {{-- RIGHT: แผนที่ / ปุ่ม Google Maps / สรุปสั้น --}}
      <aside class="space-y-6">

        <section class="bg-white rounded-2xl border p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-map-location-dot text-rose-500"></i> แผนที่และการนำทาง
          </h2>

          <div class="space-y-3">
            <div class="text-sm text-slate-500">พิกัด</div>
            <div class="font-semibold">
              {{ $cafe->lat ?? '-' }}, {{ $cafe->lng ?? '-' }}
            </div>

            @if(!empty($cafe->lat) && !empty($cafe->lng))
              <a href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}"
                 target="_blank" rel="noopener"
                 class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 font-semibold">
                <i class="fa-brands fa-google"></i> เปิดด้วย Google Maps
              </a>
            @else
              <div class="text-slate-400 text-sm">ยังไม่มีพิกัด</div>
            @endif
          </div>
        </section>

        {{-- สรุปเปิดใหม่/สถานะอื่น ๆ --}}
        <section class="bg-white rounded-2xl border p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-indigo-500"></i> สรุปสถานะ
          </h2>
          <ul class="space-y-2 text-sm">
            <li class="flex items-center gap-2">
              <i class="fa-solid fa-bolt text-amber-500"></i>
              เปิดใหม่: <span class="font-semibold ml-1">{{ !empty($cafe->is_new_opening) ? 'ใช่' : 'ไม่ใช่' }}</span>
            </li>
            <li class="flex items-center gap-2">
              <i class="fa-regular fa-credit-card text-green-600"></i>
              รับบัตรเครดิต: <span class="font-semibold ml-1">{{ (int)($cafe->credit_card ?? 0) === 1 ? 'รองรับ' : 'ไม่รองรับ/ไม่ระบุ' }}</span>
            </li>
            <li class="flex items-center gap-2">
              <i class="fa-solid fa-square-parking text-sky-600"></i>
              ที่จอดรถ: <span class="font-semibold ml-1">{{ (int)($cafe->parking ?? 0) === 1 ? 'มี' : 'ไม่มี/ไม่ระบุ' }}</span>
            </li>
          </ul>
        </section>

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

</body>
</html>
