<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แพ็กเกจโฆษณา - น้องช้างสะเร็น</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style> body { font-family: 'Kanit', sans-serif; } </style>
</head>
<body class="bg-slate-50">
<body class="bg-slate-100 min-h-screen">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest

    @auth
        @include('components.2navbar')
    @endauth
    <div class="container mx-auto px-4 py-12 sm:py-20">

        <!-- ===== Hero Section ===== -->
        <header class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-indigo-600">
                โปรโมทธุรกิจของคุณ
            </h1>
            <p class="mt-4 text-xl sm:text-2xl font-bold text-slate-800">เข้าถึงกลุ่มลูกค้าชาวสุรินทร์โดยตรงกับ "น้องช้างสะเร็น"</p>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-slate-500">
                เลือกแพ็กเกจที่เหมาะสมกับธุรกิจของคุณ ไม่ว่าจะเป็นร้านกาแฟเปิดใหม่, ร้านอาหาร, หรือกิจกรรมอีเว้นท์ต่างๆ เราพร้อมช่วยให้คุณเป็นที่รู้จักมากขึ้น
            </p>
        </header>

        <!-- ===== Pricing Table Section ===== -->
        <main class="mt-16 sm:mt-24">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">

                <!-- Package 1: Basic -->
                <div class="bg-white border border-slate-200 rounded-2xl p-8 flex flex-col transform hover:-translate-y-2 transition-transform duration-300">
                    <h3 class="text-2xl font-bold text-slate-800">Basic Post</h3>
                    <p class="mt-2 text-slate-500">สำหรับโปรโมททั่วไป</p>
                    <div class="mt-6">
                        <span class="text-5xl font-black text-slate-900">฿900</span>
                        <span class="text-lg font-semibold text-slate-500">/ โพสต์</span>
                    </div>
                    <ul class="mt-8 space-y-4 text-slate-600 flex-grow">
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>รีวิวบนหน้า Facebook Fanpage</li>
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>รูปภาพประกอบ 4-6 รูป</li>
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>แชร์ลง 1 กลุ่มเป้าหมาย</li>
                    </ul>
                    <a href="mailto:{{ $contactEmail }}?subject=สนใจแพ็กเกจ Basic Post" class="mt-8 block w-full text-center px-6 py-3 font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                        เลือกแพ็กเกจนี้
                    </a>
                </div>

                <!-- Package 2: Standard (Recommended) -->
                <div class="bg-white border-2 border-indigo-600 rounded-2xl p-8 flex flex-col relative transform scale-105 shadow-2xl shadow-indigo-200">
                    <span class="absolute top-0 -translate-y-1/2 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-sm font-bold px-4 py-1 rounded-full">แนะนำ</span>
                    <h3 class="text-2xl font-bold text-indigo-600">Standard Review</h3>
                    <p class="mt-2 text-slate-500">ครบเครื่อง คุ้มค่าที่สุด</p>
                    <div class="mt-6">
                        <span class="text-5xl font-black text-slate-900">฿1,500</span>
                        <span class="text-lg font-semibold text-slate-500">/ รีวิว</span>
                    </div>
                    <ul class="mt-8 space-y-4 text-slate-600 flex-grow">
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i><strong>ทุกอย่างในแพ็กเกจ Basic</strong></li>
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>รีวิวบนเว็บไซต์ Nongchangsurin.com</li>
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>วิดีโอสั้น (Reels/Shorts) 1 คลิป</li>
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>แชร์ลง 3 กลุ่มเป้าหมาย</li>
                    </ul>
                    <a href="mailto:{{ $contactEmail }}?subject=สนใจแพ็กเกจ Standard Review" class="mt-8 block w-full text-center px-6 py-3 font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-300 transition">
                        เลือกแพ็กเกจนี้
                    </a>
                </div>

                <!-- Package 3: Premium -->
                <div class="bg-white border border-slate-200 rounded-2xl p-8 flex flex-col transform hover:-translate-y-2 transition-transform duration-300">
                    <h3 class="text-2xl font-bold text-slate-800">Premium Partner</h3>
                    <p class="mt-2 text-slate-500">โปรโมทเต็มรูปแบบ</p>
                    <div class="mt-6">
                        <span class="text-5xl font-black text-slate-900">฿3,200</span>
                        <span class="text-lg font-semibold text-slate-500">/ เดือน</span>
                    </div>
                    <ul class="mt-8 space-y-4 text-slate-600 flex-grow">
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i><strong>ทุกอย่างในแพ็กเกจ Standard</strong></li>
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>ปักหมุดบนหน้า Facebook 3 วัน</li>
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>Live สดแนะนำร้าน 1 ครั้ง</li>
                        <li class="flex items-center"><i class="fa-solid fa-check-circle text-green-500 mr-3"></i>โปรโมทผ่าน Banner บนเว็บไซต์</li>
                    </ul>
                    <a href="mailto:{{ $contactEmail }}?subject=สนใจแพ็กเกจ Premium Partner" class="mt-8 block w-full text-center px-6 py-3 font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                        เลือกแพ็กเกจนี้
                    </a>
                </div>
            </div>
        </main>

        <!-- ===== Call to Action Section ===== -->
        <footer class="mt-16 sm:mt-24 text-center max-w-2xl mx-auto">
            <h3 class="text-2xl font-bold text-slate-800">มีคำถามเพิ่มเติม หรือต้องการแพ็กเกจพิเศษ?</h3>
            <p class="mt-4 text-lg text-slate-500">
                ทีมงานของเราพร้อมให้คำปรึกษาและออกแบบแพ็กเกจที่เหมาะสมกับงบประมาณและเป้าหมายของธุรกิจคุณโดยเฉพาะ
            </p>
            <div class="mt-8">
                <a href="mailto:{{ $contactEmail }}?subject=สอบถามเกี่ยวกับแพ็กเกจโฆษณา"
                   class="inline-block bg-gradient-to-r from-cyan-500 to-indigo-600 text-white font-bold text-xl px-10 py-4 rounded-lg shadow-xl shadow-cyan-500/30 hover:scale-105 transform transition-all duration-300">
                    <i class="fa-solid fa-envelope mr-3"></i>
                    ติดต่อทีมงานทางอีเมล
                </a>
            </div>
            <p class="mt-6 text-sm text-slate-400">
                เราจะตอบกลับภายใน 24 ชั่วโมง
            </p>
        </footer>

    </div>

</body>
</html>