<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดผู้ใช้ - ระบบจัดการคาเฟ่</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #a7bfe8 0%, #6190e8 100%);
            min-height: 100vh;
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-text {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card-base {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-base:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -6px rgba(0, 0, 0, 0.15);
        }
        .stat-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    @include('partials.header')

    <!-- Main Content -->
    <main class="container mx-auto px-4 pt-24 pb-10">
        <!-- Welcome -->
        <div class="mb-8 text-center">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">สวัสดี, <span class="gradient-text">{{ Auth::user()->name }}</span></h1>
            <p class="text-base sm:text-lg text-white/80 mt-2">ยินดีต้อนรับสู่แดชบอร์ดของคุณ</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
            <div class="glass-morphism p-4 rounded-xl flex items-center gap-4 card-base">
                <div class="stat-card-icon bg-blue-500"><i class="fas fa-coffee"></i></div>
                <div>
                    <p class="text-sm text-gray-600">คาเฟ่ทั้งหมดของคุณ</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalCafes }}</p>
                </div>
            </div>
            <div class="glass-morphism p-4 rounded-xl flex items-center gap-4 card-base">
                <div class="stat-card-icon bg-green-500"><i class="fas fa-check-circle"></i></div>
                <div>
                    <p class="text-sm text-gray-600">อนุมัติแล้ว</p>
                    <p class="text-xl font-bold text-gray-800">{{ $approvedCafes }}</p>
                </div>
            </div>
            <div class="glass-morphism p-4 rounded-xl flex items-center gap-4 card-base">
                <div class="stat-card-icon bg-yellow-500"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <p class="text-sm text-gray-600">รอการอนุมัติ</p>
                    <p class="text-xl font-bold text-gray-800">{{ $pendingCafes }}</p>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('user.cafes.my') }}" class="glass-morphism p-6 rounded-xl text-center card-base">
                <i class="fas fa-tasks text-4xl gradient-text mb-3"></i>
                <h3 class="text-lg font-bold text-gray-800 mb-1">จัดการคาเฟ่ของฉัน</h3>
                <p class="text-sm text-gray-600">ดู แก้ไข หรือลบคาเฟ่ที่คุณได้เพิ่มไว้</p>
            </a>
            <a href="{{ route('user.cafes.create') }}" class="glass-morphism p-6 rounded-xl text-center card-base">
                <i class="fas fa-plus-circle text-4xl gradient-text mb-3"></i>
                <h3 class="text-lg font-bold text-gray-800 mb-1">เพิ่มคาเฟ่ใหม่</h3>
                <p class="text-sm text-gray-600">แบ่งปันคาเฟ่ดีๆ ที่คุณรู้จักให้ทุกคนได้เห็น</p>
            </a>
        </div>
    </main>

   {{-- Footer --}}
  @include('components.footer')

</body>
</html>
