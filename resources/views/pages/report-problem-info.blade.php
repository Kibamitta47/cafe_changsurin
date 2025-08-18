<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งปัญหาการใช้งาน - น้องช้างสะเร็น</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <style> body { font-family: 'Kanit', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest

    @auth
        @include('components.2navbar')
    @endauth

    <div class="flex items-center justify-center p-4">
        <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl max-w-3xl w-full border border-slate-200">

            <!-- ===== ส่วนหัว ===== -->
            <header class="text-center">
                <div class="mx-auto mb-4 w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20 ring-4 ring-white">
                    <i class="fa-solid fa-wrench fa-2x text-white"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900">แจ้งปัญหาการใช้งาน</h1>
                <p class="mt-2 text-lg text-slate-500">ขอบคุณที่ช่วยให้เราพัฒนาเว็บไซต์ให้ดียิ่งขึ้น</p>
            </header>

            <!-- ===== ส่วนคำแนะนำ ===== -->
            <section class="mt-10 pt-8 border-t border-slate-200">
                <h2 class="text-xl font-bold text-slate-800 text-center">เพื่อให้เราแก้ไขได้รวดเร็วที่สุด</h2>
                <p class="text-center text-slate-500 mb-6">กรุณาใส่ข้อมูลต่อไปนี้ในอีเมลของคุณ:</p>

                <div class="bg-slate-100 p-6 rounded-2xl border border-slate-200">
                    <ul class="space-y-4 text-slate-700">
                        <li class="flex items-start">
                            <i class="fa-solid fa-check-double text-blue-500 mr-4 mt-1"></i>
                            <div>
                                <strong class="font-bold">หัวข้ออีเมล:</strong>
                                <p class="text-slate-500 text-sm">เราได้ใส่หัวข้อให้อัตโนมัติ เพื่อให้เรื่องของคุณถูกส่งไปถึงทีมที่ถูกต้อง</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fa-solid fa-link text-blue-500 mr-4 mt-1"></i>
                            <div>
                                <strong class="font-bold">ลิงก์ (URL) ของหน้าที่พบปัญหา</strong>
                                <p class="text-slate-500 text-sm">เช่น https://nongchangsurin.com/cafes/some-cafe</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fa-solid fa-comment-dots text-blue-500 mr-4 mt-1"></i>
                            <div>
                                <strong class="font-bold">คำอธิบายปัญหาอย่างละเอียด</strong>
                                <p class="text-slate-500 text-sm">เกิดอะไรขึ้น? คุณคาดหวังให้เกิดอะไรขึ้น? มีข้อความ error อะไรแสดงหรือไม่?</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fa-solid fa-camera text-blue-500 mr-4 mt-1"></i>
                            <div>
                                <strong class="font-bold">ภาพหน้าจอ (Screenshot)</strong>
                                <p class="text-slate-500 text-sm">(แนะนำอย่างยิ่ง) ภาพหนึ่งภาพสามารถอธิบายปัญหาได้ดีกว่าคำพูดนับพัน</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- ===== ส่วน Call to Action ===== -->
            <footer class="mt-10 text-center space-y-3">
                <a href="mailto:{{ $problemEmail }}?subject=แจ้งปัญหาการใช้งานเว็บไซต์ น้องช้างสะเร็น&body={{ $emailBodyTemplate }}"
                   class="inline-block bg-blue-600 text-white font-bold text-lg px-8 py-4 rounded-lg shadow-xl shadow-blue-500/30 hover:bg-blue-700 hover:scale-105 transform transition-all duration-300">
                    <i class="fa-solid fa-envelope-open-text mr-3"></i>
                    กดที่นี่เพื่อแจ้งปัญหาทางอีเมล
                </a>

                <p class="text-sm text-slate-400">
                    หรือส่งอีเมลโดยตรงมาที่: <strong class="text-slate-500">{{ $problemEmail }}</strong>
                </p>

                <a href="https://lin.ee/pc4H6dbo" target="_blank"
                   class="inline-flex items-center gap-2 text-green-600 font-semibold hover:underline">
                    <i class="fab fa-line text-2xl"></i>
                    แจ้งผ่าน LINE น้องช้างสะเร็น
                </a>
            </footer>

        </div>
    </div>

</body>
</html>
