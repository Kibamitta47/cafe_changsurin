<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Admin Dashboard (Light Tone)</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Global Styles & Variables (Light Tone) */
        :root {
            --primary-light: #4a90e2; /* A nice light blue */
            --secondary-light: #6dd5ed; /* Lighter blue/cyan */
            --accent-light: #8e44ad; /* A subtle purple for accents */
            --background-light: #f4f7f6; /* Very light grey/off-white */
            --sidebar-light-bg: #ffffff; /* Pure white sidebar */
            --card-light-bg: #ffffff; /* White cards */
            --text-dark: #333333; /* Dark text for contrast */
            --text-secondary-dark: #666666; /* Slightly lighter dark text */
            --text-accent-light: var(--primary-light); /* Accent color matches primary */
            --border-light-color: #e0e0e0; /* Light grey border */
            --shadow-light-mild: 0 4px 15px rgba(0, 0, 0, 0.08); /* Soft shadow */
            --shadow-light-strong: 0 8px 25px rgba(0, 0, 0, 0.15); /* Stronger shadow on hover */
            --transition-smooth: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: var(--background-light);
            color: var(--text-dark);
            transition: var(--transition-smooth);
            overflow-x: hidden;
        }

        /* Top Bar (Header) */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95); /* White, slightly transparent */
            backdrop-filter: blur(8px);
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: var(--shadow-light-mild);
            transition: var(--transition-smooth);
        }

        body.sidebar-open .top-bar {
            left: 320px;
            width: calc(100% - 320px);
        }
        
        .top-bar-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            right: 30px;
            z-index: 1010;
            background: var(--primary-light);
            color: var(--sidebar-light-bg); /* White text on blue button */
            border: none;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            box-shadow: var(--shadow-light-mild);
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            gap: 8px;
            transform-origin: center;
        }

        .sidebar-toggle:hover {
            background: var(--secondary-light);
            transform: scale(1.05) translateY(-2px);
            box-shadow: var(--shadow-light-strong);
        }

        .sidebar-toggle .icon {
            font-size: 18px;
            transition: transform 0.3s ease;
        }

        .sidebar-toggle.active .icon {
            transform: rotate(90deg);
        }

        /* Admin Sidebar */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 320px;
            height: 100vh;
            background: var(--sidebar-light-bg);
            border-right: 1px solid var(--border-light-color);
            box-shadow: var(--shadow-light-strong);
            padding: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transform: translateX(-100%);
            transition: var(--transition-smooth);
            overflow-y: auto;
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1); /* Light scrollbar thumb */
            border-radius: 10px;
        }

        .admin-sidebar.show {
            transform: translateX(0);
        }

        body.sidebar-open {
            padding-left: 320px;
        }

        /* Sidebar Header */
        .sidebar-header {
            background: var(--primary-light); /* Blue header */
            padding: 30px 25px;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-primary); /* White text on blue header */
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        /* Profile Info */
        .profile-info {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 2;
        }

        .profile-avatar {
            position: relative;
            flex-shrink: 0;
        }

        .profile-info img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.5); /* White border */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: var(--transition-smooth);
        }

        .profile-avatar::after {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: rgba(255, 255, 255, 0.3); /* Lighter glow */
            border-radius: 50%;
            z-index: -1;
            opacity: 0;
            transition: var(--transition-smooth);
        }

        .profile-info:hover .profile-avatar::after {
            opacity: 1;
            animation: pulse 2s infinite;
        }

        .profile-details span {
            display: block;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary); /* White text on blue header */
            margin-bottom: 5px;
        }

        .profile-status {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            background: rgba(0, 0, 0, 0.1); /* Darker background for status bubble */
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
        }

        /* Menu Container */
        .menu-container {
            flex-grow: 1;
            padding: 25px 0;
        }

        .menu-list {
            padding: 0 15px;
        }

        /* Menu Items */
        .menu-item {
            margin-bottom: 8px;
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-light); /* Blue highlight */
            transition: var(--transition-smooth);
            z-index: 0;
            opacity: 0.1; /* Subtle overlay */
        }

        .menu-item:hover::before {
            left: 0;
        }

        .menu-item a,
        .menu-item button {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px 20px;
            font-size: 16px;
            color: var(--text-secondary-dark); /* Dark grey text */
            text-decoration: none;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            border-radius: 15px;
            transition: var(--transition-smooth);
            position: relative;
            z-index: 1;
        }

        .menu-item a .icon,
        .menu-item button .icon {
            font-size: 20px;
            width: 25px;
            text-align: center;
            color: var(--primary-light); /* Blue icons */
        }

        .menu-item:hover a,
        .menu-item:hover button {
            color: var(--text-dark); /* Darker text on hover */
            background: #f0f0f0; /* Light grey background on hover */
            transform: translateX(10px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .menu-item.active a,
        .menu-item.active button {
            color: var(--primary-light); /* Primary blue for active text */
            background: #e6f0fa; /* Very light blue background for active */
            border-left: 4px solid var(--primary-light); /* Blue active border */
            padding-left: 16px;
        }

        .menu-item.active a .icon,
        .menu-item.active button .icon {
            color: var(--primary-light); /* Primary blue icon for active */
        }


        /* Logout Button Special Styling */
        .menu-item.logout {
            margin-top: 30px;
            padding: 0 15px;
        }

        .menu-item.logout::before {
            background: #ff4d4f; /* Red highlight */
            opacity: 0.1;
        }

        .menu-item.logout button {
            color: #d9534f; /* Red text */
            border: 1px solid rgba(217, 83, 79, 0.3); /* Red border */
            background: rgba(217, 83, 79, 0.05); /* Very light red background */
        }

        .menu-item.logout:hover button {
            color: var(--sidebar-light-bg); /* White text on hover */
            background: #d9534f; /* Solid red background on hover */
            border-color: transparent;
        }

        /* Main Content Styling */
        .main-content {
            padding: 100px 30px 30px;
            transition: var(--transition-smooth);
            min-height: calc(100vh - 100px);
            background: var(--background-light); /* Match body background */
            color: var(--text-dark);
        }

        body.sidebar-open .main-content {
            padding-left: calc(320px + 30px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 280px;
            }
            
            body.sidebar-open {
                padding-left: 0;
            }

            .main-content {
                padding: 100px 20px 20px;
            }

            body.sidebar-open .main-content {
                padding-left: 20px;
            }
            
            .sidebar-toggle {
                top: 15px;
                right: 15px;
                padding: 10px 14px;
                font-size: 14px;
            }

            .top-bar {
                padding: 10px 20px;
            }

            .top-bar-title {
                font-size: 20px;
            }

            body.sidebar-open .top-bar {
                left: 0;
                width: 100%;
            }
        }
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
                <div class="menu-item @if(request()->routeIs('home.admin')) active @endif">
                    <a href="{{ route('home.admin') }}">
                        <i class="fas fa-home icon"></i>
                        หน้าแรก
                    </a>
                </div>
                <div class="menu-item @if(request()->routeIs('admin.cafe.create')) active @endif">
                    <a href="{{ route('admin.cafe.create') }}">
                        <i class="fas fa-plus-circle icon"></i>
                        เพิ่มคาเฟ่ใหม่
                    </a>
                </div>
                <div class="menu-item @if(request()->is('admin/news*')) active @endif"> 
    <a href="{{ url('admin/news') }}">
        <i class="fas fa-newspaper icon"></i>
        โปรโมชั่น/ข่าวสาร
    </a>
</div>

                <div class="menu-item @if(request()->is('edit-profile*')) active @endif">
                    <a href="{{ url('/edit-profile') }}">
                        <i class="fas fa-user-edit icon"></i>
                        แก้ไขข้อมูลส่วนตัว
                    </a>
                </div>
               <div class="menu-item @if(request()->routeIs('admin.cafe.index')) active @endif">
    <a href="{{ route('admin.cafe.index') }}">
        รายการคาเฟ่
    </a>
</div>

                </div>

            <form action="{{ url('/logout-admin') }}" method="POST">
                @csrf
                <div class="menu-item logout">
                    <button type="submit">
                        <i class="fas fa-sign-out-alt icon"></i>
                        ออกจากระบบ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const body = document.body;
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            sidebar.classList.toggle('show');
            body.classList.toggle('sidebar-open');
            toggleBtn.classList.toggle('active');

            // Save sidebar state to localStorage (optional)
            if (sidebar.classList.contains('show')) {
                localStorage.setItem('sidebarState', 'open');
            } else {
                localStorage.removeItem('sidebarState');
            }
        }

        // Close sidebar when clicking outside (on desktop/tablet)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggleButton = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth > 768 && 
                !sidebar.contains(event.target) && 
                !toggleButton.contains(event.target) && 
                sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        });

        // Initialize sidebar state on load
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.menu-item a').forEach(item => {
                const linkPath = new URL(item.href).pathname;
                if (currentPath === linkPath || 
                    (linkPath !== '/' && currentPath.startsWith(linkPath))) {
                    item.closest('.menu-item').classList.add('active');
                } else {
                    item.closest('.menu-item').classList.remove('active');
                }
            });

            if (localStorage.getItem('sidebarState') === 'open' && window.innerWidth > 768) {
                toggleSidebar();
            }
        });

        // Handle active state for menu items (re-evaluate on click)
        document.querySelectorAll('.menu-item a, .menu-item button').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.menu-item').forEach(menuItem => {
                    menuItem.classList.remove('active');
                });
                this.closest('.menu-item').classList.add('active');

                if (window.innerWidth <= 768 && document.getElementById('adminSidebar').classList.contains('show')) {
                    toggleSidebar();
                }
            });
        });

        // Ensure proper padding on resize
        window.addEventListener('resize', function() {
            const body = document.body;
            const sidebar = document.getElementById('adminSidebar');
            if (window.innerWidth <= 768) {
                body.classList.remove('sidebar-open');
            } else if (sidebar.classList.contains('show')) {
                body.classList.add('sidebar-open');
            }
        });
    </script>
</body>
</html>