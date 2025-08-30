<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>แจ้งปัญหาการใช้งาน - น้องช้างสะเร็น</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <style>
    body { font-family: 'Kanit', sans-serif; }
  </style>
</head>
<body class="bg-slate-50 min-h-screen pt-4 sm:pt-6 pb-24 sm:pb-8">

  {{-- Navbar --}}
  @guest
    @include('components.1navbar')
  @endguest
  @auth
    @include('components.2navbar')
  @endauth

  <div class="max-w-xl sm:max-w-3xl mx-auto px-4">
    <div class="bg-white p-5 sm:p-8 rounded-3xl shadow-xl w-full border border-slate-200">

      <!-- ===== ส่วนหัว ===== -->
      <header class="text-center">
        <div class="mx-auto mb-4 w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20 ring-4 ring-white">
          <i class="fa-solid fa-wrench text-white text-2xl sm:text-3xl"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">แจ้งปัญหาการใช้งาน</h1>
        <p class="mt-2 text-slate-500 text-sm sm:text-lg">ขอบคุณที่ช่วยให้เราพัฒนาเว็บไซต์ให้ดียิ่งขึ้น</p>
      </header>

      <!-- ===== ส่วนคำแนะนำ ===== -->
      <section class="mt-6 sm:mt-10 pt-6 sm:pt-8 border-t border-slate-200">
        <h2 class="text-lg sm:text-xl font-bold text-slate-800 text-center">เพื่อให้เราแก้ไขได้รวดเร็วที่สุด</h2>
        <p class="text-center text-slate-500 mb-4 sm:mb-6 text-sm sm:text-base">กรุณาใส่ข้อมูลต่อไปนี้ในไลน์ของคุณ:</p>

        <div class="bg-slate-100 p-4 sm:p-6 rounded-2xl border border-slate-200">
          <ul class="space-y-3 sm:space-y-4 text-slate-700">
            <li class="flex items-start">
              <i class="fa-solid fa-check-double text-blue-500 mr-3 sm:mr-4 mt-0.5"></i>
              <div class="text-sm sm:text-base">
                <strong class="font-bold">หัวข้อ</strong>
              </div>
            </li>
            <li class="flex items-start">
              <i class="fa-solid fa-link text-blue-500 mr-3 sm:mr-4 mt-0.5"></i>
              <div class="text-sm sm:text-base">
                <strong class="font-bold">ลิงก์ (URL) ของหน้าที่พบปัญหา</strong>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">เช่น https://nongchangsurin.com/cafes/some-cafe</p>
              </div>
            </li>
            <li class="flex items-start">
              <i class="fa-solid fa-comment-dots text-blue-500 mr-3 sm:mr-4 mt-0.5"></i>
              <div class="text-sm sm:text-base">
                <strong class="font-bold">คำอธิบายปัญหาอย่างละเอียด</strong>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">เกิดอะไรขึ้น? คุณคาดหวังให้เกิดอะไรขึ้น? มีข้อความ error อะไรแสดงหรือไม่?</p>
              </div>
            </li>
            <li class="flex items-start">
              <i class="fa-solid fa-camera text-blue-500 mr-3 sm:mr-4 mt-0.5"></i>
              <div class="text-sm sm:text-base">
                <strong class="font-bold">ภาพหน้าจอ (Screenshot)</strong>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">(แนะนำอย่างยิ่ง) ภาพหนึ่งภาพช่วยอธิบายปัญหาได้ชัดเจน</p>
              </div>
            </li>
          </ul>
        </div>
      </section>

      <!-- ปุ่ม LINE (ในบัตร สำหรับเดสก์ท็อป/แท็บเล็ต และก็ยังแสดงในมือถือได้) -->
      <div class="mt-6 sm:mt-8">
        <a href="https://lin.ee/pc4H6dbo" target="_blank" rel="noopener"
           class="block w-full text-center items-center justify-center gap-2 rounded-2xl bg-[#06C755] px-5 py-3 text-white font-bold text-base sm:text-lg shadow-lg hover:bg-[#05a646] active:scale-[.99] transition">
          <i class="fab fa-line mr-2"></i> แจ้งปัญหาผ่าน LINE น้องช้างสะเร็น
        </a>
      </div>

    </div>
  </div>

  <!-- แถบปุ่มติดขอบล่าง (แสดงเฉพาะบนมือถือ) -->
  <div class="fixed bottom-0 inset-x-0 sm:hidden bg-white/95 backdrop-blur border-t border-slate-200 p-3 z-40">
    <div class="max-w-xl mx-auto px-2">
      <a href="https://lin.ee/pc4H6dbo" target="_blank" rel="noopener"
         class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#06C755] px-4 py-3 text-white font-extrabold shadow-lg hover:bg-[#05a646] transition">
        <i class="fab fa-line text-lg"></i>
        <span>แจ้งปัญหาผ่าน LINE</span>
      </a>
    </div>
  </div>

</body>
</html>
