<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ $newsItem->title }} - น้องช้างสะเร็น</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>

  <style>
    body{font-family:'Kanit',system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial,sans-serif}
    .reading p{line-height:1.9}
    .prose :where(h1,h2,h3,h4){color:#0f172a}
    .prose :where(a){text-decoration:none}
    .prose :where(a:hover){text-decoration:underline}
    .lightbox{position:fixed;inset:0;background:rgba(0,0,0,.8);display:none;align-items:center;justify-content:center;z-index:60}
    .lightbox.open{display:flex}
    .progress{position:fixed;top:0;left:0;height:4px;width:0;background:linear-gradient(90deg,#60a5fa,#22c55e);z-index:50;box-shadow:0 0 12px rgba(34,197,94,.35)}
  </style>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: { 50:'#eff6ff',200:'#bfdbfe',500:'#3b82f6',600:'#2563eb' }
          },
          boxShadow: {
            soft: '0 12px 34px rgba(2,6,23,.08)',
            card: '0 8px 24px rgba(2,6,23,.06)'
          },
          borderRadius: {
            xl2: '1.25rem'
          }
        }
      }
    }
  </script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

  <!-- Progress -->
  <div id="progressBar" class="progress"></div>

  {{-- Navbar --}}
  @guest @include('components.1navbar') @endguest
  @auth  @include('components.2navbar') @endauth

  <!-- Hero -->
  <section class="relative">
    <div class="absolute inset-0 bg-gradient-to-b from-brand-50/80 to-transparent pointer-events-none"></div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
      <nav class="text-sm text-slate-500 mb-4 flex items-center gap-2">
        <span class="opacity-60">/</span>
        <span class="line-clamp-1">{{ $newsItem->title }}</span>
      </nav>

      <header class="bg-white/80 backdrop-blur rounded-2xl shadow-card border border-slate-200 p-5 md:p-7">
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 mb-3">
          {{ $newsItem->title }}
        </h1>

        <div class="flex flex-wrap items-center gap-3 text-sm">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700">
            <i class="fa-regular fa-calendar"></i>
            เผยแพร่: {{ optional($newsItem->start_datetime)->format('d F Y, H:i') ?? 'N/A' }}
          </span>
          <span id="readingTime" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700">
            <i class="fa-regular fa-clock"></i> ใช้อ่านประมาณ: —
          </span>

          <div class="ml-auto flex items-center gap-2">
            <button id="copyBtn" type="button"
              class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
              <i class="fa-solid fa-link"></i> คัดลอกลิงก์
            </button>
            <a target="_blank" rel="noopener"
               href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
              <i class="fa-brands fa-facebook"></i> แชร์
            </a>
            <a target="_blank" rel="noopener"
               href="https://line.me/R/msg/text/?{{ urlencode($newsItem->title.' '.request()->fullUrl()) }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
              <i class="fa-brands fa-line"></i> LINE
            </a>
          </div>
        </div>
      </header>
    </div>
  </section>

  <!-- Main -->
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- Content -->
      <main class="lg:col-span-2">
        <article class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden divide-y divide-slate-100">

          {{-- รูปภาพหลัก --}}
          @if(!empty($newsItem->images))
            <div class="relative">
              <img
                src="{{ asset('storage/' . $newsItem->images[0]) }}"
                alt="{{ $newsItem->title }}"
                class="w-full max-h-[520px] object-cover select-none">
              <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/30 to-transparent"></div>
              <div class="absolute top-4 right-4">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/90 backdrop-blur text-slate-700 shadow">
                  <i class="fa-regular fa-image"></i>
                  {{ count($newsItem->images) }} รูป
                </span>
              </div>
            </div>
          @endif

          <div class="p-6 sm:p-8">
            <div id="articleContent" class="prose prose-slate max-w-none reading text-[1.06rem] leading-relaxed whitespace-pre-wrap">
              {{ $newsItem->content }}
            </div>

            {{-- แกลเลอรี --}}
            @if(isset($newsItem->images) && count($newsItem->images) > 1)
              <div class="mt-10">
                <div class="flex items-center gap-2 mb-4">
                  <span class="inline-block w-2.5 h-2.5 rounded-sm bg-brand-500"></span>
                  <h2 class="text-xl font-semibold">แกลเลอรี</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                  @foreach(array_slice($newsItem->images, 1) as $imageFile)
                    @php $src = asset('storage/' . $imageFile); @endphp
                    <button
                      type="button"
                      class="group aspect-square overflow-hidden rounded-xl border border-slate-200 bg-slate-50 ring-1 ring-transparent hover:ring-brand-200 transition"
                      data-image="{{ $src }}">
                      <img
                        src="{{ $src }}"
                        alt="{{ $newsItem->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300 ease-out">
                    </button>
                  @endforeach
                </div>
              </div>
            @endif
          </div>
        </article>
      </main>

      <!-- Sidebar -->
      <aside class="lg:sticky lg:top-24 self-start space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-200">
          <div class="flex items-center gap-2 mb-4">
            <span class="inline-block w-2.5 h-2.5 rounded-sm bg-brand-500"></span>
            <h2 class="text-xl font-bold text-slate-900">ข่าวสารแนะนำ</h2>
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
                  <div class="w-24 h-20 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                    <img src="{{ $recImageUrl }}" alt="{{ $recItem->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                  </div>
                  <div class="min-w-0">
                    <h3 class="font-semibold text-slate-800 group-hover:text-brand-600 line-clamp-2 leading-snug">
                      {{ $recItem->title }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
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
      </aside>

    </div>
  </div>

  <!-- Lightbox -->
  <div id="lightbox" class="lightbox">
    <div class="relative w-full max-w-5xl px-4">
      <img id="lightboxImg" src="" alt="image" class="w-full max-h-[80vh] object-contain rounded-xl shadow-xl">
      <button
        id="closeLightbox"
        type="button"
        class="absolute -top-3 -right-3 bg-white text-slate-700 rounded-full w-10 h-10 grid place-items-center shadow-lg border">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  </div>

  <script>
    // เวลาอ่าน
    (function(){
      const el = document.getElementById('articleContent');
      const rt = document.getElementById('readingTime');
      if(el && rt){
        const words = el.innerText.trim().split(/\s+/).filter(Boolean).length;
        const mins = Math.max(1, Math.round(words / 250));
        rt.innerHTML = '<i class="fa-regular fa-clock"></i> ใช้อ่านประมาณ: <span class="font-semibold">'+mins+' นาที</span>';
      }
    })();

    // คัดลอกลิงก์
    (function(){
      const btn = document.getElementById('copyBtn');
      if(!btn) return;
      btn.addEventListener('click', async () => {
        try{
          await navigator.clipboard.writeText(location.href);
          const old = btn.innerHTML;
          btn.innerHTML = '<i class="fa-solid fa-check text-green-600"></i> คัดลอกแล้ว';
          setTimeout(()=> btn.innerHTML = old, 1200);
        }catch(e){}
      });
    })();

    // ไลท์บ็อกซ์
    (function(){
      const lb = document.getElementById('lightbox');
      const lbImg = document.getElementById('lightboxImg');
      const closeBtn = document.getElementById('closeLightbox');
      document.querySelectorAll('[data-image]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          lbImg.src = btn.getAttribute('data-image');
          lb.classList.add('open');
          document.body.style.overflow = 'hidden';
        });
      });
      const close = ()=>{ lb.classList.remove('open'); lbImg.src=''; document.body.style.overflow=''; };
      closeBtn.addEventListener('click', close);
      lb.addEventListener('click', (e)=>{ if(e.target === lb) close(); });
      window.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') close(); });
    })();

    // Progress bar
    (function(){
      const bar = document.getElementById('progressBar');
      const article = document.getElementById('articleContent');
      const onScroll = ()=>{
        if(!article) return;
        const rect = article.getBoundingClientRect();
        const total = article.scrollHeight - window.innerHeight + rect.top;
        const scrolled = Math.min(Math.max(window.scrollY - (article.offsetTop - 80), 0), total || 1);
        const percent = Math.max(0, Math.min(100, (scrolled / (total || 1)) * 100));
        bar.style.width = percent + '%';
      };
      window.addEventListener('scroll', onScroll, {passive:true});
      window.addEventListener('resize', onScroll);
      onScroll();
    })();
  </script>
</body>
</html>
