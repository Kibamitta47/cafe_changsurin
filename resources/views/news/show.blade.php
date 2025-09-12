<!DOCTYPE html>
<html lang="th" x-data="{ copied:false }">
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>

  <style>
    body{font-family:'Kanit',system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial,sans-serif}
    .reading p{line-height:1.9}
  </style>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',
              400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a'
            }
          },
          boxShadow: {
            soft: '0 8px 24px rgba(2,6,23,.06)'
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

  <!-- HERO -->
  <header class="border-b border-slate-200/70 bg-gradient-to-br from-brand-50 via-white to-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
      <nav class="text-sm text-slate-500 mb-3" aria-label="breadcrumb">
        <ol class="flex flex-wrap items-center gap-1">
          <li><a href="{{ route('news.index') }}" class="hover:text-brand-700 hover:underline">ข่าวสาร</a></li>
          <li class="mx-1 text-slate-400">/</li>
          <li class="text-slate-700 line-clamp-1">{{ $newsItem->title }}</li>
        </ol>
      </nav>

      <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900">
        {{ $newsItem->title }}
      </h1>

      <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
        <span class="inline-flex items-center gap-2 bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
          <i class="fa-regular fa-calendar"></i>
          เผยแพร่: {{ optional($newsItem->start_datetime)->format('d F Y, H:i') ?? 'N/A' }}
        </span>
        <span
          class="inline-flex items-center gap-2 bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm"
          x-data="{ rt:'—' }"
          x-init="
            const el = document.getElementById('article-content');
            if(el){ const w = el.innerText.trim().split(/\s+/).length; rt = Math.max(1, Math.round(w/250)) + ' นาที'; }
          "
        >
          <i class="fa-regular fa-clock"></i> ใช้อ่านประมาณ: <span class="font-semibold" x-text="rt"></span>
        </span>

        <span class="ml-auto"></span>
        <button
          class="inline-flex items-center gap-2 bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm hover:bg-slate-50"
          @click="if(navigator.share){navigator.share({title:'{{ addslashes($newsItem->title) }}', url:location.href});}else{await navigator.clipboard.writeText(location.href); copied=true; setTimeout(()=>copied=false,1500);}">
          <i class="fa-solid fa-share-nodes text-brand-600"></i>
          <span x-show="!copied">แชร์ / คัดลอกลิงก์</span>
          <span x-show="copied" class="text-green-600">คัดลอกแล้ว</span>
        </button>
        <button class="inline-flex items-center gap-2 bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm hover:bg-slate-50" onclick="print()">
          <i class="fa-solid fa-print"></i> พิมพ์
        </button>
      </div>
    </div>
  </header>

  <!-- MAIN -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row lg:gap-8">

      <!-- ซ้าย: เนื้อหาข่าวหลัก (คงเนื้อหาเดิมทุกคำ) -->
      <main class="w-full lg:w-2/3">
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

            {{-- เนื้อหา (เดิม) --}}
            <div id="article-content" class="reading prose prose-slate max-w-none text-lg whitespace-pre-wrap">
              {{ $newsItem->content }}
            </div>

            {{-- แกลเลอรีรูปภาพ (รูปที่เหลือ) --}}
            @if(isset($newsItem->images) && count($newsItem->images) > 1)
              <div class="mt-9">
                <div class="flex items-center gap-2 mb-4">
                  <span class="inline-block w-2.5 h-2.5 rounded-sm bg-brand-500"></span>
                  <h3 class="text-xl font-semibold text-slate-900">แกลเลอรี</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                  @foreach(array_slice($newsItem->images, 1) as $imageFile)
                    <div class="group aspect-square overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                      <img
                        src="{{ asset('storage/' . $imageFile) }}"
                        alt="{{ $newsItem->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition"
                        loading="lazy">
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

            {{-- ลิงก์เพิ่มเติม (เดิม) --}}
            @if($newsItem->link_url)
              <div class="mt-8 pt-6 border-t border-slate-200">
                <a
                  href="{{ $newsItem->link_url }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 text-brand-700 hover:text-brand-900 hover:underline font-semibold">
                  <i class="fas fa-link"></i> อ่านข้อมูลเพิ่มเติมที่ลิงก์ต้นฉบับ
                </a>
              </div>
            @endif
          </div>
        </article>

        <!-- ปุ่มกลับ/ดูทั้งหมด -->
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
            <i class="fa-solid fa-arrow-left-long"></i> กลับ
          </a>
          <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
            <i class="fa-regular fa-newspaper"></i> ดูข่าวทั้งหมด
          </a>
        </div>
      </main>

      <!-- ขวา: ข่าวสารแนะนำ (ข้อมูลเดิม, จัดรูปแบบใหม่ให้อ่านง่าย) -->
      <aside class="w-full lg:w-1/3 mt-8 lg:mt-0">
        <div class="sticky top-24">
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
                  <a href="{{ route('news.show', $recItem) }}"
                     class="group flex items-start gap-3 p-2 rounded-xl border border-transparent hover:border-slate-200 hover:bg-slate-50 transition">
                    <div class="w-24 h-20 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                      <img src="{{ $recImageUrl }}" alt="{{ $recItem->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                    </div>
                    <div class="min-w-0">
                      <h3 class="font-semibold text-slate-800 group-hover:text-brand-700 line-clamp-2 leading-snug">
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
        </div>
      </aside>

    </div>
  </div>

</body>
</html>
