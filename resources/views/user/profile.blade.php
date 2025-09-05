<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>จัดการโปรไฟล์ - CafeFinder</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #0D6EFD;
            --bs-primary-rgb: 13, 110, 253;
            --bs-secondary: #6c757d;
            --bs-light: #F8F9FA;
            --bs-body-font-family: 'Kanit', sans-serif;
            --bs-body-bg: var(--bs-light);
            --bs-border-radius: 0.5rem;
            --bs-border-radius-lg: 0.75rem;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        body { padding-top: 90px; }

        .navbar {
            box-shadow: var(--shadow);
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .profile-container {
            max-width: 960px;
        }

        .profile-card {
            background-color: white;
            border-radius: var(--bs-border-radius-lg);
            box-shadow: var(--shadow-lg);
        }
        
        .profile-avatar {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem auto;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: var(--shadow);
        }
        .profile-avatar .avatar-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: var(--bs-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #ced4da;
            border: 4px solid white;
            box-shadow: var(--shadow);
        }

        .avatar-upload-button {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--bs-primary);
            color: white;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .avatar-upload-button:hover { transform: scale(1.1); }
        
        .accordion-button:not(.collapsed) {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            color: var(--bs-dark);
            box-shadow: none;
        }
        .accordion-button:focus { box-shadow: none; }
    </style>
</head>
<body>

<!-- Header (Bootstrap 5) -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="{{ route('user.dashboard') }}" style="background: linear-gradient(45deg, #0ea5e9, #6366f1);-webkit-background-clip: text;-webkit-text-fill-color: transparent;background-clip: text;">CafeFinder</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">แดชบอร์ด</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.cafes.my') ? 'active' : '' }}" href="{{ route('user.cafes.my') }}">คาเฟ่ของฉัน</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.cafes.myLiked') ? 'active' : '' }}" href="{{ route('user.cafes.myLiked') }}">คาเฟ่ที่ถูกใจ</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('user.cafes.create') }}" class="btn btn-primary rounded-pill d-none d-lg-flex align-items-center gap-2" style="background-color: #0ea5e9; border:none;"><i class="bi bi-plus-circle-fill"></i> เพิ่มคาเฟ่</a>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle fs-4"></i> {{ Auth::user()->name }}</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item active" href="{{ route('user.profile.show') }}"><i class="bi bi-person-gear me-2"></i>แก้ไขโปรไฟล์</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><form action="{{ route('user.logout') }}" method="POST">@csrf<button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</button></form></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container profile-container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">จัดการโปรไฟล์</h1>
        <p class="lead text-secondary">อัปเดตข้อมูลส่วนตัวและรหัสผ่านของคุณ</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="profile-card p-4 p-md-5">
        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-5">
                <!-- Left Column: Avatar & Info -->
                <div class="col-md-4 text-center">
                    <div class="profile-avatar">
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="รูปโปรไฟล์" id="imagePreview">
                        @else
                            <div class="avatar-placeholder" id="imagePreviewPlaceholder"><i class="bi bi-person-fill"></i></div>
                            <img src="" alt="รูปโปรไฟล์" id="imagePreview" class="d-none">
                        @endif
                        <label for="profile_image" class="avatar-upload-button" title="เปลี่ยนรูปโปรไฟล์">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                        <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                    </div>
                    <h4 class="fw-bold mt-3">{{ Auth::user()->name }}</h4>
                    <p class="text-secondary">{{ Auth::user()->email }}</p>
                </div>

                <!-- Right Column: Form Fields -->
                <div class="col-md-8">
                    <h5 class="fw-bold mb-4">ข้อมูลส่วนตัว</h5>
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">อีเมล</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr class="my-4">

                    <!-- Change Password Accordion -->
                    <div class="accordion" id="passwordAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePassword">
                                    <i class="bi bi-key-fill me-2"></i>เปลี่ยนรหัสผ่าน
                                </button>
                            </h2>
                            <div id="collapsePassword" class="accordion-collapse collapse" data-bs-parent="#passwordAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">รหัสผ่านปัจจุบัน</label>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">รหัสผ่านใหม่</label>
                                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                                        @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill">
                            <i class="bi bi-save-fill me-2"></i>บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileImageInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewPlaceholder = document.getElementById('imagePreviewPlaceholder');

    profileImageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if(imagePreviewPlaceholder) {
                    imagePreviewPlaceholder.classList.add('d-none');
                }
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>

</body>
</html>