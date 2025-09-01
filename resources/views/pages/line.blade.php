<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>เพิ่มเพื่อนกับ น้องช้างสะเร็น</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <style> body { font-family: 'Kanit', sans-serif; } </style>
</head>
<body class="bg-slate-100 min-h-screen">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest

    @auth
        @include('components.2navbar')
    @endauth

    <div class="min-h-screen flex items-center justify-center p-4">

        <div class="bg-white/80 backdrop-blur-sm p-8 md:p-10 rounded-3xl shadow-2xl max-w-4xl w-full border border-white">

            <!-- โลโก้และหัวข้อ -->
            <div class="text-center mb-8">
                <div class="mx-auto mb-4 w-20 h-20 rounded-full bg-green-600 flex items-center justify-center shadow-xl shadow-green-500/30 ring-4 ring-white/50">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12c0 5.52 4.48 10 10 10 1.99 0 3.84-.68 5.29-1.82l4.23 1.11-1.11-4.23A9.947 9.947 0 0 0 22 12c0-5.52-4.48-10-10-10zM8 14h-2v-4h2v4zm6 0h-2v-4h2v4z"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-extrabold text-slate-900">เพิ่มเพื่อนกับ "น้องช้างสะเร็น"</h1>
                <p class="text-slate-500 mt-2 text-base sm:text-lg">รับข่าวสารและโปรโมชั่นพิเศษก่อนใคร!</p>
            </div>

            <!-- แบ่งซ้ายขวา -->
            <div class="flex flex-col md:flex-row gap-10">

                
                <!-- แถบปุ่มติดขอบล่าง (แสดงเฉพาะบนมือถือ) -->
  <div class="fixed bottom-0 inset-x-0 sm:hidden bg-white/95 backdrop-blur border-t border-slate-200 p-3 z-40">
    <div class="max-w-xl mx-auto px-2">
      <a href="https://lin.ee/pc4H6dbo" target="_blank" rel="noopener"
         class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#06C755] px-4 py-3 text-white font-extrabold shadow-lg hover:bg-[#05a646] transition">
        <i class="fab fa-line text-lg"></i>
        <span>แตะเพื่อเพิ่มเพื่อน</span>
      </a>
    </div>
  </div>

                <!-- ขวา: สำหรับผู้ใช้คอมพิวเตอร์ -->
                <div class="flex-1 text-center">
                    <h3 class="font-semibold text-slate-700 mb-6 text-lg">สำหรับผู้ใช้คอมพิวเตอร์</h3>
                    <div class="flex justify-center mb-6">
                        <img src="{{ asset('/images/logoline.png') }}" alt="LINE QR Code" class="w-52 h-52 rounded-2xl shadow-2xl border-8 border-white" />
                    </div>
                    <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                        ใช้มือถือสแกน QR Code เพื่อเพิ่มเพื่อน<br />
                        หรือค้นหา ID: <strong class="text-slate-800">{{ $lineOfficialId ?? '@nongchangsurin' }}</strong>
                    </p>
                </div>

            </div>

        </div>

    </div>

</body>
</html>
