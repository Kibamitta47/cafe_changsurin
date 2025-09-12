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
    /* ความกว้างบรรทัดให้อ่านสบาย */
    .reading p{line-height:1.9}
    /* ปรับสีหัวข้อใน prose */
    .prose :where(h1,h2,h3,h4){color:#0f172a}
    .prose :where(a){text-decoration:none}
    .prose :where(a:hover){text-decoration:underline}
    /* ไลท์บ็อกซ์ */
    .lightbox{position:fixed;inset:0;background:rgba(0,0,0,.8);display:none;align-items:center;justify-content:center;z-index:60}
    .lightbox.open{display:flex}
  </style>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: { 50:'#eff6ff',200:'#bfdbfe',500:'#3b82f6',600:'#2563eb' }
          },
          boxShadow: {
            soft: '0 10px 30px rgba(2,6,23,.08)'
          }
        }
      }
    }
  </script>
</head>
<body class="bg-slate-50 text-slate-800">

  {{-- Navbar --}}
  @guest @include('components.1navbar') @endguest
  @auth  @include('components.2navbar') @endauth

  <!-- คอนเทนเนอร์หลัก -->
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- หัวเรื่อง + เมตา -->
    <header class="mb-6">
      <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900">
        {{ $newsItem->title }}
      </h1>
      <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-600">
        <span class="inline-flex items-center gap-2">
          <i class="fa-regular fa-calendar"></i>
          เผยแพร่: {{ optional($newsItem->start_datetime)->format('d F Y, H:i') ?? 'N/A' }}
        </span>

        <!-- เวลาที่ใช้ในการอ่าน (คำนวณแบบง่ายด้วย CSS trick + JS เล็กน้อย) -->
        <span id="readingTime" class="inline-flex items-center gap-2">
          <i class="fa-regular fa-clock"></i> ใช้อ่านประมาณ: —
        </span>

        <!-- ปุ่มคัดลอกลิงก์ -->
        <button
          id="copyBtn"
          class="ml-auto inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm"
          type="button">
          <i class="fa-solid fa-link"></i> คัดลอกลิงก์
        </button>
      </div>
    </header>

    <!-- เลย์เอาต์ 2 คอลัมน์ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- คอลัมน์ซ้าย: เนื้อหาข่าวหลัก -->
      <main class="lg:col-span-2">
        <article class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">

          {{-- รูปภาพหลัก (แสดงแค่รูปแรก) --}}
          @if(!empty($newsItem->images))
            <div class="relative">
              <img
                src="{{ asset('storage/' . $newsItem->images[0]) }}"
                alt="{{ $newsItem->title }}"
                class="w-full max-h-[520px] object-cover"
                loading="lazy">
            </div>
          @endif

          <div class="p-6 sm:p-8">
            <hr class="mb-6 border-slate-200">

            {{-- เนื้อหา (คงเดิมทุกคำ) --}}
            <div id="articleContent" class="prose prose-slate max-w-none reading text-lg whitespace-pre-wrap">
              {{ $newsItem->content }}
            </div>

            {{-- แกลเลอรีรูปภาพ (รูปที่เหลือ) --}}
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
                      class="group aspect-square overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                      data-image="{{ $src }}">
                      <img
                        src="{{ $src }}"
                        alt="{{ $newsItem->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition"
                        loading="lazy">
                    </button>
                  @endforeach
                </div>
              </div>
            @endif

            {{-- ลิงก์เพิ่มเติม --}}
            @if($newsItem->link_url)
              <div class="mt-8 pt-6 border-t border-slate-200">
                <a
                  href="{{ $newsItem->link_url }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-600/90 hover:underline font-semibold">
                  <i class="fas fa-arrow-up-right-from-square"></i>
                  อ่านข้อมูลเพิ่มเติมที่ลิงก์ต้นฉบับ
                </a>
              </div>
            @endif
          </div>
        </article>

        <!-- ปุ่มนำทางสั้นๆ -->
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
            <i class="fa-solid fa-arrow-left-long"></i> กลับ
          </a>
          <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
            <i class="fa-regular fa-newspaper"></i> ดูข่าวทั้งหมด
          </a>
        </div>
      </main>

      <!-- คอลัมน์ขวา: ข่าวสารแนะนำ -->
      <aside>
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

  <!-- Lightbox แบบเรียบง่าย -->
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
    // คำนวณเวลาอ่านโดยประมาณ (~250 คำ/นาที)
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

    // ไลท์บ็อกซ์สำหรับแกลเลอรี
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
  </script>
</body>
</html>
