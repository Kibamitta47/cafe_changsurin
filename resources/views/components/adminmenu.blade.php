<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Admin Dashboard (Light Tone)</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS ของคุณถูกต้องดีแล้ว ไม่ต้องแก้ไข */
        :root {
            --primary-light: #4a90e2; /* A nice light blue */
            --secondary-light: #6dd5ed; /* Lighter blue/cyan */
            --accent-light: #8e44ad; /* A subtle purple for accents */
            --background-light: #f4f7f6; /* Very light grey/off-white */
            --sidebar-light-bg: #ffffff; /* Pure white sidebar */
            --card-light-bg: #ffffff; /* White cards */
            --text-dark: #333333; /* Dark text for contrast */
            --text-secondary-dark: #666666; /* Slightly lighter dark text */
            --text-primary: #ffffff; /* White text for headers */
            --border-light-color: #e0e0e0; /* Light grey border */
            --shadow-light-mild: 0 4px 15px rgba(0, 0, 0, 0.08); /* Soft shadow */
            --shadow-light-strong: 0 8px 25px rgba(0, 0, 0, 0.15); /* Stronger shadow on hover */
            --transition-smooth: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Kanit', sans-serif; background: var(--background-light); color: var(--text-dark); transition: var(--transition-smooth); overflow-x: hidden; }
        .top-bar { position: fixed; top: 0; left: 0; width: 100%; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); z-index: 999; display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; box-shadow: var(--shadow-light-mild); transition: var(--transition-smooth); }
        body.sidebar-open .top-bar { left: 320px; width: calc(100% - 320px); }
        .top-bar-title { font-size: 24px; font-weight: 600; color: var(--text-dark); }
        .sidebar-toggle { position: fixed; top: 20px; right: 30px; z-index: 1010; background: var(--primary-light); color: var(--sidebar-light-bg); border: none; padding: 12px 18px; border-radius: 8px; font-size: 15px; cursor: pointer; box-shadow: var(--shadow-light-mild); transition: var(--transition-smooth); display: flex; align-items: center; gap: 8px; transform-origin: center; }
        .sidebar-toggle:hover { background: var(--secondary-light); transform: scale(1.05) translateY(-2px); box-shadow: var(--shadow-light-strong); }
        .sidebar-toggle .icon { font-size: 18px; transition: transform 0.3s ease; }
        .sidebar-toggle.active .icon { transform: rotate(90deg); }
        .admin-sidebar { position: fixed; top: 0; left: 0; width: 320px; height: 100vh; background: var(--sidebar-light-bg); border-right: 1px solid var(--border-light-color); box-shadow: var(--shadow-light-strong); padding: 0; display: flex; flex-direction: column; z-index: 1000; transform: translateX(-100%); transition: var(--transition-smooth); overflow-y: auto; }
        .admin-sidebar.show { transform: translateX(0); }
        body.sidebar-open { padding-left: 320px; }
        .sidebar-header { background: var(--primary-light); padding: 30px 25px; color: var(--text-primary); }
        .profile-info { display: flex; align-items: center; gap: 20px; }
        .profile-info img { width: 65px; height: 65px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.5); }
        .profile-details span { display: block; font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 5px; }
        .profile-status { font-size: 12px; color: rgba(255, 255, 255, 0.9); background: rgba(0, 0, 0, 0.1); padding: 4px 12px; border-radius: 20px; display: inline-block; }
        .menu-container { flex-grow: 1; padding: 25px 0; }
        .menu-list { padding: 0 15px; }
        .menu-item { margin-bottom: 8px; }
        .menu-item a, .menu-item button { display: flex; align-items: center; gap: 15px; padding: 18px 20px; font-size: 16px; color: var(--text-secondary-dark); text-decoration: none; border: none; width: 100%; text-align: left; font-weight: 500; cursor: pointer; border-radius: 15px; transition: var(--transition-smooth); }
        .menu-item a .icon, .menu-item button .icon { font-size: 20px; width: 25px; text-align: center; color: var(--primary-light); }
        .menu-item:hover a, .menu-item:hover button { color: var(--text-dark); background: #f0f0f0; transform: translateX(10px); }
        .menu-item.active a { color: var(--primary-light); background: #e6f0fa; }
        .menu-item.logout button { color: #d9534f; }
        .menu-item.logout:hover button { color: var(--sidebar-light-bg); background: #d9534f; }
        .main-content { padding: 100px 30px 30px; transition: var(--transition-smooth); }
        body.sidebar-open .main-content { padding-left: calc(320px + 30px); }
        @media (max-width: 768px) { .admin-sidebar { width: 280px; } body.sidebar-open { padding-left: 0; } .main-content, body.sidebar-open .main-content { padding: 100px 20px 20px; } .sidebar-toggle { top: 15px; right: 15px; padding: 10px 14px; font-size: 14px; } .top-bar { padding: 10px 20px; } .top-bar-title { font-size: 20px; } body.sidebar-open .top-bar { left: 0; width: 100%; } }
    </style>
</head>
<body>
   
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars icon"></i>
        <span>เมนู</span>
    </button>
  
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="profile-info">
                <div class="profile-avatar">
                    @php
                        $adminUser = Auth::guard('admin')->user();
                        $profileImage = $adminUser ? $adminUser->profile_image : null;
                        $userName = $adminUser ? $adminUser->UserName : 'Guest Admin';
                    @endphp
                    @if($profileImage && file_exists(storage_path('app/public/' . $profileImage)))
                        <img src="{{ asset('storage/' . $profileImage) }}" alt="Profile Image">
                    @else
                        <img src="{{ asset('images/default-profile.jpg') }}" alt="Default Profile Image">
                    @endif
                </div>
                <div class="profile-details">
                    <span>{{ $userName }}</span>
                    <div class="profile-status">ผู้ดูแลระบบ</div>
                </div>
            </div>
        </div>

        <div class="menu-container">
            <div class="menu-list">
                <!-- FIX 1: แก้ไขเงื่อนไข active state จาก 'home.admin' เป็น 'admin.home' -->
                <div class="menu-item @if(request()->routeIs('admin.home' )) active @endif">
                    <a href="{{ route('admin.home') }}"><i class="fas fa-tachometer-alt icon"></i> Dashboard</a>
                </div>
                <div class="menu-item @if(request()->routeIs('admin.cafe.create')) active @endif">
                    <a href="{{ route('admin.cafe.create') }}"><i class="fas fa-plus-circle icon"></i> เพิ่มคาเฟ่ใหม่</a>
                </div>
                <!-- การใช้ url() ในที่นี้ยังทำงานได้ แต่แนะนำให้ใช้ route() ถ้ามีชื่อ Route กำหนดไว้ -->
                <div class="menu-item @if(request()->is('admin/news*')) active @endif"> 
                    <a href="{{ route('admin.news.add') }}"><i class="fas fa-newspaper icon"></i> โปรโมชั่น/ข่าวสาร</a>
                </div>
                <!-- FIX 2: แก้ไขเงื่อนไข active state และลิงก์ของหน้าแก้ไขโปรไฟล์ -->
                <div class="menu-item @if(request()->routeIs('admin.profile.edit')) active @endif">
                    <a href="{{ route('admin.profile.edit') }}"><i class="fas fa-user-edit icon"></i> แก้ไขข้อมูลส่วนตัว</a>
                </div>
                <div class="menu-item @if(request()->routeIs('admin.cafe.index')) active @endif">
                    <a href="{{ route('admin.cafe.index') }}"><i class="fas fa-list icon"></i> รายการคาเฟ่</a>
                </div>
                <div class="menu-item @if(request()->routeIs('admin.recommend')) active @endif">
                    <a href="{{ route('admin.recommend') }}"><i class="fas fa-star icon"></i> แนะนำคาเฟ่</a>
                </div>
            </div>
        </div>

        <!-- FIX 3: แก้ไข action ของฟอร์ม logout ให้ใช้ named route ที่ถูกต้อง -->
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <div class="menu-item logout">
                <button type="submit"><i class="fas fa-sign-out-alt icon"></i> ออกจากระบบ</button>
            </div>
        </form>
    </div>

    <div class="main-content">
        {{-- This is where the content of each page will be displayed --}}
        {{-- For example, in home.admin.blade.php, you would extend this layout --}}
        {{-- @yield('content') --}}
        
        <div class="top-bar">
            <h1 class="top-bar-title">ยินดีต้อนรับ, {{ $userName }}</h1>
        </div>

    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('show');
            document.body.classList.toggle('sidebar-open');
            document.querySelector('.sidebar-toggle').classList.toggle('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Logic to keep sidebar open on larger screens if preferred
        });
    </script>
</body>
</html>