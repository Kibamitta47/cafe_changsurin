<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกี่ยวกับเรา - น้องช้างสะเร็น</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <style> body { font-family: 'Kanit', sans-serif; } </style>
</head>
<body class="bg-slate-50">
    
    
    @guest
        @include('components.1navbar')
    @endguest
    @auth
        @include('components.2navbar')
    @endauth
   

    <div class="container mx-auto px-4 py-12 sm:py-16">

        <!-- ===== Hero Section ===== -->
        <header class="relative bg-white rounded-3xl shadow-lg overflow-hidden mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="p-8 sm:p-12 lg:p-16 self-center">
                    <span class="text-sm font-bold uppercase tracking-widest text-indigo-600">เรื่องราวของเรา</span>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-black text-slate-900">
                        แพลตฟอร์มสำหรับคนรักคาเฟ่<br>ในอำเภอเมืองสุรินทร์ จังหวัดสุรินทร์
                    </h1>
                    <p class="mt-6 text-lg text-slate-600">
                        "น้องช้างสะเร็น" เกิดขึ้นจากความตั้งใจที่จะรวบรวมข้อมูลคาเฟ่ เพื่อสร้างชุมชนที่แข็งแกร่งและสนับสนุนธุรกิจในจังหวัดของเรา
                    </p>
                </div>
                <div class="h-64 lg:h-auto">
                    {{-- *** เปลี่ยนเป็นรูปภาพที่สื่อถึงคาเฟ่หรือบรรยากาศในสุรินทร์ *** --}}
                    <img src="https://images.unsplash.com/photo-1559925393-8be0ec4767c8?q=80&w=2070&auto=format&fit=crop" 
                         alt="บรรยากาศคาเฟ่" 
                         class="w-full h-full object-cover">
                </div>
            </div>
        </header>

        <!-- ===== Mission Section ===== -->
        <section class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-slate-800">ภารกิจของเรา</h2>
            <p class="mt-4 text-lg text-slate-500">เรามุ่งมั่นที่จะเป็นมากกว่าแค่เว็บรีวิว แต่เป็นพื้นที่สำหรับทุกคน</p>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Mission 1 -->
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full bg-cyan-100 text-cyan-600">
                        <i class="fa-solid fa-mug-saucer fa-2x"></i>
                    </div>
                    <h3 class="mt-4 text-xl font-bold text-slate-900">เป็นศูนย์กลางข้อมูล</h3>
                    <p class="mt-2 text-slate-500">รวบรวมข้อมูลคาเฟ่และร้านค้าให้ครบถ้วน ค้นหาง่าย และเป็นปัจจุบันที่สุด</p>
                </div>
                <!-- Mission 2 -->
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fa-solid fa-store fa-2x"></i>
                    </div>
                    <h3 class="mt-4 text-xl font-bold text-slate-900">สนับสนุนธุรกิจท้องถิ่น</h3>
                    <p class="mt-2 text-slate-500">เป็นช่องทางให้ผู้ประกอบการได้โปรโมทธุรกิจและเข้าถึงลูกค้ากลุ่มใหม่ๆ</p>
                </div>
                <!-- Mission 3 -->
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full bg-amber-100 text-amber-600">
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                    <h3 class="mt-4 text-xl font-bold text-slate-900">สร้างชุมชนที่เข้มแข็ง</h3>
                    <p class="mt-2 text-slate-500">เป็นพื้นที่สำหรับแลกเปลี่ยนความคิดเห็น, รีวิว, และสร้างความสัมพันธ์ระหว่างผู้คน</p>
                </div>
            </div>
        </section>

        <!-- ===== Story Section ===== -->
        <section class="mt-20 max-w-5xl mx-auto bg-white p-10 sm:p-16 rounded-3xl shadow-lg border">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-center">
                <div class="lg:col-span-3">
                    <h2 class="text-3xl font-bold text-slate-800">จุดเริ่มต้นของเรา</h2>
                    <p class="mt-6 text-lg text-slate-600 leading-relaxed">
                        โปรเจกต์ "น้องช้างสะเร็น" เริ่มต้นจากทีมงานกลุ่มเล็กๆ ที่อาศัยอยู่ในอำเภอเมืองสุรินทร์ จังหวัดสุรินทร์และหลงใหลในวัฒนธรรมการดื่มกาแฟและการสรรหาร้านอาหารใหม่ๆ เราพบว่าข้อมูลร้านค้ายังคงกระจัดกระจายและไม่เป็นปัจจุบัน ทำให้การค้นพบสถานที่ใหม่ๆ เป็นเรื่องยาก
                    </p>
                    <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                        ด้วยความตั้งใจนี้ เราจึงสร้างแพลตฟอร์มนี้ขึ้นมาเพื่อแก้ปัญหาดังกล่าว โดยหวังว่าจะเป็นประโยชน์ต่อนักท่องเที่ยว, คนในท้องถิ่น, และที่สำคัญที่สุดคือผู้ประกอบการร้านค้าทุกคน ให้เติบโตไปพร้อมๆ กัน
                    </p>
                </div>
                <div class="lg:col-span-2">
                    {{-- *** เปลี่ยนเป็นรูปโลโก้ของคุณ หรือรูปทีมงาน *** --}}
                    <img src="{{ asset('/images/logo.png') }}" alt="โลโก้น้องช้างสะเร็น" class="rounded-2xl mx-auto">
                </div>
            </div>
        </section>

        <!-- ===== Call to Action Section ===== -->
        <footer class="mt-20 text-center">
            <h2 class="text-3xl font-bold text-slate-800">พร้อมออกเดินทางไปกับเราแล้วหรือยัง?</h2>
            <div class="mt-8">
                <a href="{{ route('welcome') }}" {{-- ลิงก์ไปหน้าแรก --}}
                   class="inline-block bg-gradient-to-r from-cyan-500 to-indigo-600 text-white font-bold text-xl px-10 py-4 rounded-lg shadow-xl shadow-cyan-500/30 hover:scale-105 transform transition-all duration-300">
                    <i class="fa-solid fa-search mr-3"></i>
                    เริ่มต้นค้นหาคาเฟ่เลย!
                </a>
            </div>
        </footer>

    </div>
</body>
</html>