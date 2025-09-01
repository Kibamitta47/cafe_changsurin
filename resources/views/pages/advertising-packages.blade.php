<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>แพ็กเกจโฆษณา - น้องช้างสะเร็น</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    body{font-family:'Kanit',sans-serif}
    .glass{background:rgba(255,255,255,.9);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px)}
    .price::before{content:"฿";opacity:.7;margin-right:.125rem}
  </style>
</head>
<body class="bg-slate-900 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-cyan-900/40 via-slate-900 to-slate-900 text-slate-800">

  @guest
    @include('components.1navbar')
  @endguest
  @auth
    @include('components.2navbar')
  @endauth

  @php
    // รองรับทั้ง $lineUrl/$lineId หรือ $lineAddUrl ที่มีอยู่เดิม
    $lineUrlFinal = $lineUrl
      ?? ($lineAddUrl ?? (isset($lineId) ? ('https://line.me/R/ti/p/' . ltrim($lineId, '@')) : '#'));
    $lineLabel = isset($lineId) ? ('@' . ltrim($lineId, '@')) : 'LINE Official';
    $lineOfficialId = $lineOfficialId ?? '@nongchangsurin';
  @endphp

  <div class="relative">
    <div class="pointer-events-none absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>

    <div class="container mx-auto px-4 py-12 sm:py-16 relative">
      <!-- HERO -->
      <header class="max-w-4xl mx-auto text-center">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold bg-cyan-500/20 text-cyan-200 ring-1 ring-cyan-400/30">
          <i class="fa-solid fa-bullhorn"></i> แพ็กเกจโปรโมทธุรกิจ
        </span>
        <h1 class="mt-4 text-4xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight">
          โปรโมทธุรกิจของคุณให้ปังในสุรินทร์
        </h1>
        <p class="mt-4 text-lg sm:text-xl text-slate-300">
          ร้านเปิดใหม่ ร้านอาหาร คาเฟ่ หรืออีเว้นท์ เราช่วยให้คนรู้จักมากขึ้น!
        </p>
      </header>

      <!-- เนื้อหา/ติดต่อ -->
      <section class="mt-10">
        <div class="flex flex-col md:flex-row gap-10 items-start md:items-center">

          <!-- ปุ่มติดขอบล่าง (เฉพาะมือถือ) -->
          <div class="fixed bottom-0 inset-x-0 sm:hidden bg-white/95 backdrop-blur border-t border-slate-200 p-3 z-40">
            <div class="max-w-xl mx-auto px-2">
              <a href="{{ $lineUrlFinal }}" target="_blank" rel="noopener"
                 class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#06C755] px-4 py-3 text-white font-extrabold shadow-lg hover:bg-[#05a646] transition"
                 aria-label="ติดต่อผ่าน LINE">
                <i class="fab fa-line text-lg"></i>
                <span>แชทกับแอดมินทางไลน์</span>
              </a>
            </div>
          </div>

          <!-- กล่อง QR / สำหรับผู้ใช้คอมพิวเตอร์ -->
          <div class="flex-1 text-center mx-auto">
            <h3 class="font-semibold text-slate-200 mb-6 text-lg">สำหรับผู้ใช้คอมพิวเตอร์</h3>
            <div class="flex justify-center mb-6">
              <img src="{{ asset('/images/logoline.png') }}"
                   alt="LINE QR Code - {{ $lineOfficialId }}"
                   class="w-52 h-52 rounded-2xl shadow-2xl border-8 border-white" />
            </div>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
              ใช้มือถือสแกน QR Code เพื่อเพิ่มเพื่อน<br />
              หรือค้นหา ID: <strong class="text-white">{{ $lineOfficialId }}</strong>
            </p>



            <p class="mt-4 text-sm text-slate-400">
              ไลน์ทางการ: <span class="font-semibold text-white">{{ $lineLabel }}</span>
            </p>
            <p class="mt-2 text-xs text-slate-500">ตอบกลับภายใน 24 ชั่วโมง</p>
          </div>

        </div>
      </section>
    </div> <!-- /container -->
  </div> <!-- /relative -->

</body>
</html>
