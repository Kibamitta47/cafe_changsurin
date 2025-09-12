<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>แก้ไขโปรไฟล์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles & Variables (Light Tone for consistency) */
        :root {
            --primary-light: #4a90e2; /* A nice light blue */
            --secondary-light: #6dd5ed; /* Lighter blue/cyan */
            --accent-light: #8e44ad; /* A subtle purple for accents */
            --background-light: #f4f7f6; /* Very light grey/off-white */
            --card-light-bg: #ffffff; /* Pure white cards */
            --text-dark: #333333; /* Dark text for contrast */
            --text-secondary-dark: #666666; /* Slightly lighter dark text */
            --border-light-color: #e0e0e0; /* Light grey border */
            --shadow-light-mild: 0 4px 15px rgba(0, 0, 0, 0.08); /* Soft shadow */
            --transition-smooth: all 0.3s ease;

            /* Admin Sidebar Specific Variables */
            --sidebar-width: 320px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--background-light); /* Light background */
            padding-top: 80px; /* Space for fixed top-bar */
            color: var(--text-dark); /* Dark text for readability */
            transition: padding-left 0.3s ease; /* Smooth transition for sidebar open/close */
        }
        
        /* Apply padding to body when sidebar is open (for desktop) */
        body.sidebar-open {
            padding-left: var(--sidebar-width);
        }

        .container { max-width: 1200px; }
        
        /* Top Bar / Header */
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
            transition: all 0.3s ease; /* Smooth transition for top-bar on sidebar open/close */
        }

        body.sidebar-open .top-bar {
            left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }
        
        .top-bar-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            position: fixed; /* Keep it fixed */
            top: 20px;
            right: 30px;
            z-index: 1010;
            background: var(--primary-light);
            color: var(--card-light-bg); /* White text on blue button */
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
            background: #3a7bd5; /* Slightly darker blue on hover */
            border-color: #3a7bd5; /* Ensure border also changes */
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); /* Stronger shadow on hover */
        }

        .sidebar-toggle .icon {
            font-size: 18px;
            transition: transform 0.3s ease;
        }

        .sidebar-toggle.active .icon {
            transform: rotate(90deg);
        }

        /* Admin Sidebar (from components/adminmenu.blade.php) */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--card-light-bg); /* White sidebar */
            border-right: 1px solid var(--border-light-color);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); /* Stronger shadow */
            padding: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transform: translateX(-100%);
            transition: all 0.3s ease; /* Smooth transition for sidebar itself */
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

        /* Sidebar Header */
        .sidebar-header {
            background: var(--primary-light); /* Blue header */
            padding: 30px 25px;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff; /* White text on blue header */
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
            color: #ffffff; /* White text on blue header */
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
            color: var(--card-light-bg); /* White text on hover */
            background: #d9534f; /* Solid red background on hover */
            border-color: transparent;
        }


        /* Main Content Area */
        .main-content-wrapper {
            padding: 30px 0; /* Padding for the container */
            transition: padding-left 0.3s ease; /* Smooth transition for content shift */
            /* The 'container' class also applies max-width and horizontal centering */
        }
        
        /* Section Titles */
        .section-title {
            color: var(--primary-light); /* Primary blue for section titles */
            margin-bottom: 2.5rem; /* More spacing below title */
            font-weight: 700;
            font-size: 2.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .section-title .fas {
            color: var(--text-dark); /* Darker icon color for contrast */
        }

        /* Card Styling */
        .card {
            border: none; /* Remove default Bootstrap border */
            border-radius: 12px; /* More rounded corners */
            box-shadow: var(--shadow-light-mild); /* Soft shadow for depth */
            background-color: var(--card-light-bg); /* White card background */
            transition: var(--transition-smooth);
        }
        .card-header {
            background-color: var(--primary-light); /* Primary blue header for cards */
            color: #ffffff; /* White text for header */
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: none;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-header .fa-icon {
            font-size: 1.25rem;
        }
        .card-body {
            padding: 2rem; /* More internal padding */
        }
        
        /* Form Control Styling (adjusted for consistency) */
        .form-label {
            font-weight: 600; /* Bold from original, slightly less bold than 700 */
            font-size: 1rem; /* Adjusted to 1rem for better readability and consistency */
            color: var(--text-dark); /* Dark text for contrast */
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px; /* Spacing for icon if added */
        }

        .form-control {
            padding: 0.75rem 1rem; /* Consistent padding with other forms */
            border-radius: 8px; /* Consistent border-radius */
            border: 1px solid var(--border-light-color); /* Consistent border color */
            background-color: var(--card-light-bg); /* White background */
            font-size: 1rem; /* Consistent font size */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-light); /* Primary blue on focus */
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25); /* Primary light blue focus ring */
        }

        .mb-3 {
            margin-bottom: 1.5rem; /* Consistent spacing */
        }

        .mt-3 {
            margin-top: 1.5rem; /* Consistent spacing */
        }

        /* Button Styling (consistent with other pages) */
        .btn {
            padding: 0.75rem 1.5rem; /* Consistent padding */
            font-size: 1rem; /* Consistent font size */
            border-radius: 8px; /* Consistent border-radius */
            display: inline-flex;
            align-items: center;
            font-weight: 500; /* Adjusted to 500 for consistency */
            gap: 8px; /* Spacing for icon */
        }

        .btn-primary {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            color: #fff;
            transition: var(--transition-smooth);
        }

        .btn-primary:hover {
            background-color: #3a7bd5; /* Slightly darker blue on hover */
            border-color: #3a7bd5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: var(--border-light-color); /* Lighter grey for secondary */
            border-color: var(--border-light-color);
            color: var(--text-dark); /* Dark text on secondary button */
            transition: var(--transition-smooth);
        }

        .btn-secondary:hover {
            background-color: #d0d0d0; /* Darker grey on hover */
            border-color: #d0d0d0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .text-muted {
            color: var(--text-secondary-dark) !important; /* Consistent muted text color */
        }

        .d-block {
            display: block;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        input[type="file"] {
            /* Keep padding and border consistent with form-control */
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-light-color);
            background-color: var(--card-light-bg);
            font-size: 1rem;
        }

        .profile-image {
            width: 120px; /* Increased size for better display */
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-light); /* Primary color border */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
            margin-bottom: 1rem !important; /* Adjust margin */
        }

        /* Responsive Design for smaller screens */
        @media (max-width: 768px) {
            body {
                padding-left: 0 !important; /* Reset padding for mobile */
                padding-top: 70px; /* Adjust for smaller top bar */
            }
            .admin-sidebar {
                width: 280px; /* Smaller sidebar on mobile */
            }
            .top-bar {
                padding: 10px 20px;
                left: 0 !important; /* Reset left position on mobile */
                width: 100% !important; /* Full width on mobile */
            }
            .top-bar-title {
                font-size: 20px;
            }
            .sidebar-toggle {
                top: 15px;
                right: 15px;
                padding: 10px 14px;
                font-size: 14px;
            }
            .section-title {
                font-size: 1.8rem;
                margin-bottom: 2rem;
            }
            .card-body {
                padding: 1.5rem;
            }
        }

        /* Keyframe for floating animation */
        @keyframes float {
            0% { transform: translate(0, 0); }
            50% { transform: translate(0px, 10px); }
            100% { transform: translate(0, 0); }
        }

        /* Keyframe for pulse animation */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.3); }
            70% { box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }
    </style>
</head>
<body>
    @include('components.adminmenu')
    <div class="main-content-wrapper container">
        <section class="mb-5">
            <h1 class="section-title text-center">
                <i class="fas fa-user-circle"></i>แก้ไขโปรไฟล์
            </h1>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.update.profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                         @method('PUT')
                        {{-- Laravel automatically handles PUT/PATCH for forms if you use @method('PUT') or @method('PATCH') --}}
                        {{-- In this case, your route is POST, so @method is not strictly needed unless you change the route to PUT/PATCH --}}
                        {{-- If your route was Route::put('/edit-profile', ...), you'd add @method('PUT') here --}}

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user"></i> ชื่อผู้ใช้
                            </label>
                            <input type="text" name="name" value="{{ Auth::guard('admin')->user()->UserName }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> อีเมล
                            </label>
                            <input type="email" name="email" value="{{ Auth::guard('admin')->user()->Email }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-image"></i> รูปโปรไฟล์
                            </label><br>
                            
                            @php
                                $profileImage = Auth::guard('admin')->user()->profile_image;
                            @endphp

                            @if($profileImage && file_exists(public_path('storage/profile_images/' . $profileImage)))
                                <img src="{{ asset('storage/profile_images/' . $profileImage) }}" alt="รูปโปรไฟล์" class="profile-image mb-3">
                            @else
                                <p class="text-muted">ยังไม่มีรูปโปรไฟล์</p>
                            @endif

                            <input type="file" name="profile_image" class="form-control">
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('admin.home') }}" class="btn btn-secondary me-2"> {{-- Changed to correct named route 'home.admin' --}}
                                <i class="fas fa-times me-2"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> บันทึก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle & Active State Logic
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
            
            // Check if click is outside sidebar AND outside toggle button AND sidebar is open
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
                // Basic check: if current path matches or starts with the link path
                if (currentPath === linkPath || 
                    (linkPath !== '/' && currentPath.startsWith(linkPath))) {
                    item.closest('.menu-item').classList.add('active');
                } else {
                    item.closest('.menu-item').classList.remove('active');
                }
            });

            // Restore sidebar state from localStorage (only for desktop initially)
            if (localStorage.getItem('sidebarState') === 'open' && window.innerWidth > 768) {
                const sidebar = document.getElementById('adminSidebar');
                const body = document.body;
                const toggleBtn = document.querySelector('.sidebar-toggle');
                
                sidebar.classList.add('show');
                body.classList.add('sidebar-open');
                toggleBtn.classList.add('active');
            }
        });

        // Handle active state for menu items (re-evaluate on click)
        document.querySelectorAll('.menu-item a, .menu-item button').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active from all items first
                document.querySelectorAll('.menu-item').forEach(menuItem => {
                    menuItem.classList.remove('active');
                });
                // Add active to the clicked item's parent
                this.closest('.menu-item').classList.add('active');

                // Close sidebar automatically on mobile after clicking a menu item
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
                // On mobile, always reset padding-left as sidebar will overlay
                body.classList.remove('sidebar-open');
            } else if (sidebar.classList.contains('show')) {
                // On desktop, if sidebar is open, apply padding
                body.classList.add('sidebar-open');
            }
        });
    </script>
</body>
</html>
