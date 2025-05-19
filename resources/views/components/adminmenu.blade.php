<style>
    .sidebar-toggle {
        position: fixed;
        top: 5px;
        left: 10px;
        z-index: 101;
        background-color: #3b82f6;
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }

    .admin-sidebar {
        position: fixed;
        top: 5;
        left: 0;
        width: 220px;
        height: 100vh;
        background-color: #ffffff;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        font-family: 'Inter', sans-serif;
        z-index: 100;
        transition: transform 0.3s ease;
    }

    .admin-sidebar.hide {
        transform: translateX(-100%);
    }

    body.sidebar-open {
        padding-left: 220px;
        transition: padding 0.5s;
    }

    .profile-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        margin-top: 20px;
    }

    .profile-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .profile-info span {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .menu-item {
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .menu-item a, .menu-item button {
        font-size: 14px;
        color: #333;
        text-decoration: none;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        font-weight: 500;
        cursor: pointer;
        font-family: inherit;
        padding: 0;
    }

    .menu-item a:hover,
    .menu-item button:hover {
        color: #2563eb;
    }

    .menu-item.logout button {
        color: #dc2626;
    }

    .menu-item.logout button:hover {
        color: #b91c1c;
    }
</style>

<button class="sidebar-toggle" onclick="toggleSidebar()">☰ เมนู</button>

<div class="admin-sidebar hide" id="adminSidebar">
    <div class="profile-info">
        @php
            $profileImage = Auth::guard('admin')->user()->profile_image;
            $userName = Auth::guard('admin')->user()->UserName;
        @endphp
        @if($profileImage && file_exists(public_path('storage/profile_images/' . $profileImage)))
            <img src="{{ asset('storage/profile_images/' . $profileImage) }}" alt="Profile Image">
        @else
            <img src="{{ asset('default-profile.jpg') }}" alt="Default Profile Image">
        @endif
        <span>{{ $userName }}</span>
    </div>

    <div class="menu-item">
        <a href="{{ url('/increase-admin') }}">Increase</a>
    </div>
    <div class="menu-item">
        <a href="{{ url('/addnews-admin') }}">Add News</a>
    </div>
    <div class="menu-item">
        <a href="{{ url('/review') }}">Review</a>
    </div>
    <div class="menu-item">
        <a href="{{ url('/edit-profile') }}">แก้ไขข้อมูลส่วนตัว</a>
    </div>
    <form action="{{ url('/logout-admin') }}" method="POST">
        @csrf
        <div class="menu-item logout">
            <button type="submit">ออกจากระบบ</button>
        </div>
    </form>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const body = document.body;
        sidebar.classList.toggle('hide');
        body.classList.toggle('sidebar-open');
    }
</script>
