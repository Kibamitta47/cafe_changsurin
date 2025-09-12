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
        :root {
            --primary-light:#4a90e2; --secondary-light:#6dd5ed; --accent-light:#8e44ad;
            --background-light:#f4f7f6; --card-light-bg:#ffffff; --text-dark:#333333;
            --text-secondary-dark:#666666; --border-light-color:#e0e0e0;
            --shadow-light-mild:0 4px 15px rgba(0,0,0,.08); --transition-smooth:.3s ease;
            --sidebar-width:320px;
        }
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Sarabun',sans-serif;background:var(--background-light);padding-top:80px;color:var(--text-dark);transition:padding-left .3s ease}
        body.sidebar-open{padding-left:var(--sidebar-width)}
        .container{max-width:1200px}
        .top-bar{position:fixed;top:0;left:0;width:100%;background:rgba(255,255,255,.95);backdrop-filter:blur(8px);z-index:999;display:flex;justify-content:space-between;align-items:center;padding:15px 30px;box-shadow:var(--shadow-light-mild);transition:all .3s ease}
        body.sidebar-open .top-bar{left:var(--sidebar-width);width:calc(100% - var(--sidebar-width))}
        .top-bar-title{font-size:24px;font-weight:600;color:var(--text-dark)}
        .sidebar-toggle{position:fixed;top:20px;right:30px;z-index:1010;background:var(--primary-light);color:#fff;border:none;padding:12px 18px;border-radius:8px;font-size:15px;cursor:pointer;box-shadow:var(--shadow-light-mild);transition:var(--transition-smooth);display:flex;align-items:center;gap:8px;transform-origin:center}
        .sidebar-toggle:hover{background:#3a7bd5;border-color:#3a7bd5;transform:scale(1.05) translateY(-2px);box-shadow:0 8px 25px rgba(0,0,0,.15)}
        .sidebar-toggle .icon{font-size:18px;transition:transform .3s ease}
        .sidebar-toggle.active .icon{transform:rotate(90deg)}
        .admin-sidebar{position:fixed;top:0;left:0;width:var(--sidebar-width);height:100vh;background:#fff;border-right:1px solid var(--border-light-color);box-shadow:0 8px 25px rgba(0,0,0,.15);padding:0;display:flex;flex-direction:column;z-index:1000;transform:translateX(-100%);transition:all .3s ease;overflow-y:auto}
        .admin-sidebar.show{transform:translateX(0)}
        .menu-container{flex-grow:1;padding:25px 0}
        .menu-list{padding:0 15px}
        .menu-item{margin-bottom:8px;position:relative;overflow:hidden}
        .menu-item::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:var(--primary-light);transition:var(--transition-smooth);z-index:0;opacity:.1}
        .menu-item:hover::before{left:0}
        .menu-item a,.menu-item button{display:flex;align-items:center;gap:15px;padding:18px 20px;font-size:16px;color:var(--text-secondary-dark);text-decoration:none;background:transparent;border:none;width:100%;text-align:left;font-weight:500;cursor:pointer;font-family:inherit;border-radius:15px;transition:var(--transition-smooth);position:relative;z-index:1}
        .menu-item:hover a,.menu-item:hover button{color:var(--text-dark);background:#f0f0f0;transform:translateX(10px);box-shadow:0 3px 10px rgba(0,0,0,.05)}
        .menu-item.active a,.menu-item.active button{color:var(--primary-light);background:#e6f0fa;border-left:4px solid var(--primary-light);padding-left:16px}
        .main-content-wrapper{padding:30px 0}
        .section-title{color:var(--primary-light);margin-bottom:2.5rem;font-weight:700;font-size:2.2rem;display:flex;align-items:center;justify-content:center;gap:15px}
        .card{border:none;border-radius:12px;box-shadow:var(--shadow-light-mild);background:#fff;transition:var(--transition-smooth)}
        .card-header{background:var(--primary-light);color:#fff;font-weight:600;padding:1rem 1.5rem;border-bottom:none;border-top-left-radius:12px;border-top-right-radius:12px;display:flex;align-items:center;gap:10px}
        .card-body{padding:2rem}
        .form-label{font-weight:600;font-size:1rem;color:var(--text-dark);margin-bottom:.5rem;display:flex;align-items:center;gap:8px}
        .form-control{padding:.75rem 1rem;border-radius:8px;border:1px solid var(--border-light-color);background:#fff;font-size:1rem;transition:border-color .3s ease, box-shadow .3s ease}
        .form-control:focus{border-color:var(--primary-light);box-shadow:0 0 0 .25rem rgba(74,144,226,.25)}
        .mb-3{margin-bottom:1.5rem}
        .btn{padding:.75rem 1.5rem;font-size:1rem;border-radius:8px;display:inline-flex;align-items:center;font-weight:500;gap:8px}
        .btn-primary{background:var(--primary-light);border-color:var(--primary-light);color:#fff}
        .btn-primary:hover{background:#3a7bd5;border-color:#3a7bd5;transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,.1)}
        .btn-secondary{background:var(--border-light-color);border-color:var(--border-light-color);color:var(--text-dark)}
        .btn-secondary:hover{background:#d0d0d0;border-color:#d0d0d0;transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,.05)}
        .profile-image{width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-light);box-shadow:0 4px 10px rgba(0,0,0,.1);margin-bottom:1rem!important}
        @media (max-width:768px){body{padding-left:0!important;padding-top:70px}.admin-sidebar{width:280px}.top-bar{padding:10px 20px;left:0!important;width:100%!important}.section-title{font-size:1.8rem;margin-bottom:2rem}.card-body{padding:1.5rem}}
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
                                $profileImage = Auth::guard('admin')->user()->profile_image; // ตัวอย่าง: 'profile_images/xxx.webp'
                            @endphp

                            @if($profileImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($profileImage))
                                <img src="{{ asset('storage/'.$profileImage) }}" alt="รูปโปรไฟล์" class="profile-image mb-3">
                            @else
                                <p class="text-muted">ยังไม่มีรูปโปรไฟล์</p>
                            @endif

                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-2">ระบบจะย่อเป็น 512×512 และบีบอัดเป็น .webp อัตโนมัติ</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> รหัสผ่านใหม่ (ถ้าต้องการเปลี่ยน)
                            </label>
                            <input type="password" name="password" class="form-control" placeholder="อย่างน้อย 8 ตัวอักษร">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> ยืนยันรหัสผ่านใหม่
                            </label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="พิมพ์รหัสผ่านเดิมอีกครั้ง">
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('admin.home') }}" class="btn btn-secondary me-2">
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
        function toggleSidebar(){
            const s=document.getElementById('adminSidebar');
            const b=document.body;
            const t=document.querySelector('.sidebar-toggle');
            s.classList.toggle('show'); b.classList.toggle('sidebar-open'); t.classList.toggle('active');
            if(s.classList.contains('show')){localStorage.setItem('sidebarState','open');}else{localStorage.removeItem('sidebarState');}
        }
        document.addEventListener('click',function(e){
            const s=document.getElementById('adminSidebar');
            const t=document.querySelector('.sidebar-toggle');
            if(window.innerWidth>768 && !s.contains(e.target) && !t.contains(e.target) && s.classList.contains('show')){toggleSidebar();}
        });
        document.addEventListener('DOMContentLoaded',function(){
            const p=window.location.pathname;
            document.querySelectorAll('.menu-item a').forEach(a=>{
                const lp=new URL(a.href).pathname;
                if(p===lp || (lp!=='/' && p.startsWith(lp))){a.closest('.menu-item').classList.add('active');}
            });
            if(localStorage.getItem('sidebarState')==='open' && window.innerWidth>768){
                const s=document.getElementById('adminSidebar'); const b=document.body; const t=document.querySelector('.sidebar-toggle');
                s.classList.add('show'); b.classList.add('sidebar-open'); t.classList.add('active');
            }
        });
        document.querySelectorAll('.menu-item a, .menu-item button').forEach(i=>{
            i.addEventListener('click',function(){
                document.querySelectorAll('.menu-item').forEach(m=>m.classList.remove('active'));
                this.closest('.menu-item').classList.add('active');
                if(window.innerWidth<=768 && document.getElementById('adminSidebar').classList.contains('show')){toggleSidebar();}
            });
        });
        window.addEventListener('resize',function(){
            const b=document.body, s=document.getElementById('adminSidebar');
            if(window.innerWidth<=768){b.classList.remove('sidebar-open');}else if(s.classList.contains('show')){b.classList.add('sidebar-open');}
        });
    </script>
</body>
</html>
