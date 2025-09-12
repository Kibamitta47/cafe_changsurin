<!DOCTYPE html>
<html lang="th" x-data="{ openLightbox:false, lightboxIndex:0, gallery: [] }" x-init="
  // เก็บรูปในแกลเลอรีไว้เปิดดูแบบ Lightbox
  gallery = [...document.querySelectorAll('[data-gallery] img')].map((img)=>img.src);
">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ $newsItem->title }} - น้องช้างสะเร็น</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />

  <style>
    body { font-family:'Kanit', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, sans-serif; }
    /* ปรับความกว้างของบรรทัดให้อ่านสบาย */
    .reading-content p { line-height: 1.9; }
    /* ซ่อนสกรอลบาร์ใน Lightbox */
    .no-scroll { overflow: hidden; }
  </style>
  <script>
    // Tailwind config (optional minor tweaks)
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50:  '#eff6ff',
              100: '#dbeafe',
              200: '#bfdbfe',
              300: '#93c5fd',
              400: '#60a5fa',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
              800: '#1e40af',
              900: '#1e3a8a'
            }
          },
          boxShadow: {
            soft: '0 8px 24px rgba(2, 6, 23, .06)'
          }
        }
      }
    }
  </script>
</head>
<body class="bg-slate-50 text-slate-800" x-bind:class="openLightbox ? 'no-scroll' : ''">

  <!-- Navbar -->
  @guest
    @include('components.1navbar')
  @endguest
  @auth
    @include('components.2navbar')
  @endauth

  <!-- แถบความคืบหน้าในการอ่าน -->
  <div x-data="{ progress: 0 }" x-init="
    const onScroll = () => {
      const h = document.documentElement;
      const scrolled = h.scrollTop || document.body.scrollTop;
      const height = h.scrollHeight - h.clientHeight;
      progress = height ? Math.round((scrolled / height) * 100) : 0;
    };
    window.addEventListener('scroll', onScroll, { passive:true });
    onScroll();
  " class="fixed left-0 right-0 top-0 z-[60] h-1 bg-slate-200/60">
    <div class="h-1 bg-brand-500 transition-all" :style="`width:${progress}%;`"></div>
  </div>

  <!-- HERO -->
  <section class="relative bg-gradient-to-br from-brand-50 via-white to-slate-50 border-b border-slate-200/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-10">
      <!-- breadcrumb -->
      <nav class="text-sm text-slate-500 mb-4" aria-label="Breadcrumb">
        <ol class="flex items-center gap-1 flex-wrap">
          <li><a href="{{ route('home') }}" class="hover:text-brand-700 hover:underline">หน้าแรก</a></li>
          <li class="mx-1 text-slate-400">/</li>
          <li><a href="{{ route('news.index') }}" class="hover:text-brand-700 hover:underline">ข่าวสาร</a></li>
          <li class="mx-1 text-slate-400">/</li>
          <li class="text-slate-700 line-clamp-1">{{ $newsItem->title }}</li>
        </ol>
      </nav>

      <!-- หัวเรื่อง -->
      <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900">
        {{ $newsItem->title }}
      </h1>

      <!-- meta bar -->
      <div
        x-data="{ rt: '—' }"
        x-init="
          const article = document.getElementById('article-content');
          if (article) {
            const words = article.innerText.trim().split(/\s+/).length;
            const mins = Math.max(1, Math.round(words / 250)); // อ่าน ~250 คำ/นาที
            rt = `${mins} นาที`;
          }
        "
        class="mt-4 flex flex-wrap items-center gap-3 text-sm text-slate-600"
      >
        <span class="inline-flex items-center gap-2 bg-white/80 backdrop-blur px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
          <i class="fa-regular fa-calendar"></i>
          เผยแพร่: {{ optional($newsItem->start_datetime)->format('d F Y, H:i') ?? 'N/A' }}
        </span>
        <span class="inline-flex items-center gap-2 bg-white/80 backdrop-blur px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
          <i class="fa-regular fa-clock"></i>
          ใช้อ่านประมาณ: <span class="font-semibold" x-text="rt"></span>
        </span>

        <!-- ปุ่มแชร์ -->
        <div class="flex items-center gap-2 ml-auto">
          <button
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 shadow-sm transition"
            @click="if (navigator.share) { navigator.share({title: '{{ addslashes($newsItem->title) }}', url: window.location.href}); } else { navigator.clipboard.writeText(window.location.href); $el.innerText='คัดลอกลิงก์แล้ว'; setTimeout(()=>{$el.innerText='แชร์ / คัดลอกลิงก์';},1500); }"
          >
            <i class="fa-solid fa-share-nodes text-brand-600"></i>
            แชร์ / คัดลอกลิงก์
          </button>
          <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 shadow-sm transition" onclick="window.print()">
            <i class="fa-solid fa-print text-slate-600"></i> พิมพ์
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- MAIN -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row lg:gap-8">

      <!-- คอลัมน์ซ้าย -->
      <main class="w-full lg:w-2/3">
        <article class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
          <!-- รูปหลัก -->
          @php $hasHero = !empty($newsItem->images) && isset($newsItem->images[0]); @endphp
          <div class="relative">
            @if($hasHero)
              <img
                src="{{ asset('storage/' . $newsItem->images[0]) }}"
                alt="{{ $newsItem->title }}"
                class="w-full max-h-[520px] object-cover"
                loading="lazy"
              >
            @else
              <div class="w-full aspect-[16/9] bg-slate-100 grid place-items-center text-slate-400">
                <i class="fa-regular fa-image text-4xl"></i>
              </div>
            @endif
            <div class="absolute bottom-3 right-3">
              <span class="inline-flex items-center gap-2 bg-black/60 text-white text-xs px-2.5 py-1 rounded-full">
                <i class="fa-solid fa-camera"></i> ภาพประกอบข่าว
              </span>
            </div>
          </div>

          <!-- เนื้อหา -->
          <div class="p-6 sm:p-8">
            <div id="article-content" class="reading-content text-lg text-slate-800 whitespace-pre-wrap">
              {{ $newsItem->content }}
            </div>

            <!-- ลิงก์เพิ่มเติม -->
            @if($newsItem->link_url)
              <div class="mt-8 pt-6 border-t border-slate-200">
                <a
                  href="{{ $newsItem->link_url }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 text-brand-700 hover:text-brand-900 hover:underline font-semibold"
                >
                  <i class="fas fa-link"></i> อ่านข้อมูลเพิ่มเติมจากแหล่งที่มา
                </a>
              </div>
            @endif

            <!-- แกลเลอรี -->
            @if(isset($newsItem->images) && count($newsItem->images) > 1)
              <div class="mt-10">
                <div class="flex items-center gap-2 mb-4">
                  <span class="inline-block w-2.5 h-2.5 rounded-sm bg-brand-500"></span>
                  <h2 class="text-xl font-bold">แกลเลอรี</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4" data-gallery>
                  @foreach(array_slice($newsItem->images, 1) as $idx => $imageFile)
                    @php $src = asset('storage/' . $imageFile); @endphp
                    <button
                      type="button"
                      class="group relative aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-50"
                      @click="openLightbox=true; lightboxIndex={{ $idx }} + 1;"
                    >
                      <img
                        src="{{ $src }}"
                        alt="{{ $newsItem->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition"
                        loading="lazy"
                      >
                      <span class="absolute inset-0 ring-0 ring-brand-500/0 group-hover:ring-4 group-hover:ring-brand-500/30 transition"></span>
                      <span class="absolute bottom-2 right-2 bg-black/60 text-white text-[10px] px-2 py-1 rounded-full">คลิกเพื่อขยาย</span>
                    </button>
                  @endforeach
                </div>
              </div>
            @endif
          </div>
        </article>

        <!-- ปุ่มย้อนกลับ / ถัดไป (ตัวอย่างเรียบง่าย) -->
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
            <i class="fa-solid fa-arrow-left-long"></i> กลับ
          </a>
          <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
            <i class="fa-regular fa-newspaper"></i> ดูข่าวทั้งหมด
          </a>
        </div>
      </main>

      <!-- คอลัมน์ขวา -->
      <aside class="w-full lg:w-1/3 mt-8 lg:mt-0">
        <div class="sticky top-24 space-y-6">
          <!-- กล่องข่าวแนะนำ -->
          <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-200">
            <div class="flex items-center gap-2 mb-4">
              <span class="inline-block w-2.5 h-2.5 rounded-sm bg-brand-500"></span>
              <h2 class="text-xl font-bold">ข่าวสารแนะนำ</h2>
            </div>

            @if($recommendedNews->isNotEmpty())
              <div class="space-y-3">
                @foreach($recommendedNews as $recItem)
                  @php
                    $recImages = is_array($recItem->images) ? $recItem->images : [];
                    $recImageUrl = !empty($recImages) ? asset('storage/' . $recImages[0]) : asset('images/no-image.png');
                  @endphp
                  <a
                    href="{{ route('news.show', $recItem) }}"
                    class="group flex items-start gap-3 p-2 rounded-xl border border-transparent hover:border-slate-200 hover:bg-slate-50 transition"
                  >
                    <div class="w-24 h-20 shrink-0 rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                      <img src="{{ $recImageUrl }}" alt="{{ $recItem->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                    </div>
                    <div class="min-w-0">
                      <h3 class="font-semibold text-slate-800 group-hover:text-brand-700 line-clamp-2 leading-snug">
                        {{ $recItem->title }}
                      </h3>
                      <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                        <i class="fa-regular fa-calendar"></i>
                        {{ optional($recItem->start_datetime)->format('d M Y') }}
                      </p>
                    </div>
                  </a>
                @endforeach
              </div>
            @else
              <p class="text-sm text-slate-500">ไม่มีข่าวสารแนะนำในขณะนี้</p>
            @endif
          </div>

          <!-- กล่องติดตามโซเชียล (ตัวเลือกเสริม) -->
          <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-200">
            <div class="flex items-center gap-2 mb-4">
              <span class="inline-block w-2.5 h-2.5 rounded-sm bg-brand-500"></span>
              <h2 class="text-base font-bold">ติดตามเรา</h2>
            </div>
            <div class="flex items-center gap-3">
              <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-slate-200 hover:bg-slate-50">
                <i class="fa-brands fa-facebook-f"></i>
              </a>
              <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-slate-200 hover:bg-slate-50">
                <i class="fa-brands fa-line"></i>
              </a>
              <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-slate-200 hover:bg-slate-50">
                <i class="fa-brands fa-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </aside>

    </div>
  </div>

  <!-- LIGHTBOX -->
  <div
    x-show="openLightbox"
    x-transition.opacity
    class="fixed inset-0 z-[70] bg-black/80"
    @keydown.escape.window="openLightbox=false"
    @click.self="openLightbox=false"
  >
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="relative w-full max-w-5xl">
        <img
          :src="gallery[lightboxIndex]"
          alt="gallery image"
          class="w-full max-h-[80vh] object-contain rounded-xl shadow-xl"
        >
        <!-- ปุ่มปิด -->
        <button
          class="absolute -top-3 -right-3 bg-white text-slate-700 rounded-full w-10 h-10 grid place-items-center shadow-lg border"
          @click="openLightbox=false"
          aria-label="ปิด"
        >
          <i class="fa-solid fa-xmark"></i>
        </button>
        <!-- ก่อนหน้า / ถัดไป -->
        <div class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-2">
          <button
            class="bg-white/90 hover:bg-white text-slate-700 rounded-full w-10 h-10 grid place-items-center shadow border"
            @click.stop="lightboxIndex = (lightboxIndex - 1 + gallery.length) % gallery.length"
            aria-label="ก่อนหน้า"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </button>
          <button
            class="bg-white/90 hover:bg-white text-slate-700 rounded-full w-10 h-10 grid place-items-center shadow border"
            @click.stop="lightboxIndex = (lightboxIndex + 1) % gallery.length"
            aria-label="ถัดไป"
          >
            <i class="fa-solid fa-chevron-right"></i>
          </button>
        </div>
        <!-- ดัชนี -->
        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 text-white text-xs">
          <span x-text="(lightboxIndex+1)"></span>/<span x-text="gallery.length"></span>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
