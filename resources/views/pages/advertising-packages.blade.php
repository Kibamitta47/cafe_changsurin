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
          เลือกแพ็กเกจที่เหมาะกับคุณ — ร้านเปิดใหม่ ร้านอาหาร คาเฟ่ หรืออีเว้นท์ เราช่วยให้คนรู้จักมากขึ้น!
        </p>

        <div class="mt-8 flex flex-col items-center gap-3">
          <a href="{{ $lineUrlFinal }}" target="_blank" rel="noopener"
             class="flex w-full max-w-md items-center justify-center gap-3 rounded-xl bg-[#06C755] px-6 py-3 text-white font-extrabold text-lg shadow-lg shadow-green-500/40 hover:bg-[#05a646] transition-all duration-300 transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-green-400">
            <i class="fa-brands fa-line fa-lg"></i>
            <span>ติดต่อผ่านไลน์</span>
          </a>
          <p class="text-sm text-slate-400">ไลน์ทางการ: <span class="font-semibold text-white">{{ $lineLabel }}</span></p>
        </div>
      </header>

      

      
        <p class="mt-3 text-xs text-slate-500">ตอบกลับภายใน 24 ชั่วโมง</p>
    

</body>
</html>
