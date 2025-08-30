<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดผู้ใช้ - ระบบจัดการคาเฟ่</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AlpineJS for Dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #a7bfe8 0%, #6190e8 100%);
            min-height: 100vh;
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .header-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .gradient-text {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .btn-gradient {
            background-image: linear-gradient(to right, #3b82f6, #60a5fa);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }
        .card-base {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-base:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.15);
        }
        .stat-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- ===== ส่วนที่ดึง Header เข้ามาแสดงผล ===== -->
    @include('partials.header')

    <!-- Main Content -->
    <main class="container mx-auto px-6 pt-28 pb-12">
        <!-- Welcome Header -->
        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white text-shadow">สวัสดี, <span class="gradient-text">{{ Auth::user()->name }}</span></h1>
            <p class="text-xl text-white/80 mt-2">ยินดีต้อนรับสู่แดชบอร์ดของคุณ</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <div class="stat-card glass-morphism p-6 rounded-2xl flex items-center gap-6 card-base">
                <div class="stat-card-icon bg-blue-500"><i class="fas fa-coffee"></i></div>
                <div>
                    <p class="text-gray-600">คาเฟ่ทั้งหมดของคุณ</p>
                   <p class="text-3xl font-bold text-gray-800">{{ $totalCafes }}</p>
                </div>
            </div>
            <div class="stat-card glass-morphism p-6 rounded-2xl flex items-center gap-6 card-base">
                <div class="stat-card-icon bg-green-500"><i class="fas fa-check-circle"></i></div>
                <div>
                    <p class="text-gray-600">อนุมัติแล้ว</p>
                  <p class="text-3xl font-bold text-gray-800">{{ $approvedCafes }}</p>
                </div>
            </div>
            <div class="stat-card glass-morphism p-6 rounded-2xl flex items-center gap-6 card-base">
                <div class="stat-card-icon bg-yellow-500"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <p class="text-gray-600">รอการอนุมัติ</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $pendingCafes }}</p>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <a href="{{ route('user.cafes.my') }}" class="action-card glass-morphism p-8 rounded-2xl text-center card-base">
                <i class="fas fa-tasks text-5xl gradient-text mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">จัดการคาเฟ่ของฉัน</h3>
                <p class="text-gray-600">ดู แก้ไข หรือลบคาเฟ่ที่คุณได้เพิ่มไว้ในระบบ</p>
            </a>
            <a href="{{ route('user.cafes.create') }}" class="action-card glass-morphism p-8 rounded-2xl text-center card-base">
                <i class="fas fa-plus-circle text-5xl gradient-text mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">เพิ่มคาเฟ่ใหม่</h3>
                <p class="text-gray-600">แบ่งปันคาเฟ่ดีๆ ที่คุณรู้จักให้ทุกคนได้เห็น</p>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full text-center py-4 text-white/70 text-sm mt-auto">
        © {{ date('Y') }} น้องช้างสะเร็น. All Rights Reserved.
    </footer>

</body>
</html>