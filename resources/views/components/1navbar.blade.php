<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>น้องช้างสะเร็น - คาเฟ่และร้านอาหารในสุรินทร์</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --brand-primary: #a87c5f; /* Brownish color */
      --brand-secondary: #c49a6c;
      --brand-bg: #f8f4f0;
      --bs-body-font-family: 'Kanit', sans-serif;
      --bs-body-bg: var(--brand-bg);
      --bs-border-radius: 0.5rem;
      --bs-border-radius-lg: 0.75rem;
      --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.07);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .navbar-custom {
      background-color: white;
      box-shadow: var(--shadow);
    }

    .nav-link, .nav-link:focus {
      color: #5b4636;
      font-weight: 500;
    }
    .nav-link:hover, .nav-link.active {
        color: var(--brand-primary);
    }

    .dropdown-menu {
        border-radius: var(--bs-border-radius-lg);
        border: 1px solid #e5dfd6;
        box-shadow: var(--shadow-lg);
        animation: fadeIn 0.3s ease-in-out;
    }
    .dropdown-item:hover { background-color: #f4ede7; color: var(--brand-primary); }

    .btn-icon {
        color: var(--brand-secondary);
        transition: all 0.2s ease;
    }
    .btn-icon:hover {
        color: var(--brand-primary);
        transform: scale(1.1);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<!-- Navigation Bar (Static Top) -->
<header>
    <nav class="navbar navbar-expand navbar-custom">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
                <img src="{{ asset('/images/logo.png') }}" alt="โลโก้" style="height: 40px; width: 40px;" class="me-2 rounded-circle border">
                <span class="fw-bold" style="color: #5b4636;">น้องช้างสะเร็น</span>
            </a>

            <!-- Main Menu (Will always be visible) -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link active" href="{{ route('welcome') }}">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link" href="#">แนะนำ</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">บริการ</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Line น้องช้างสะเร็น</a></li>
                        <li><a class="dropdown-item" href="#">ติดต่อโฆษณา</a></li>
                        <li><a class="dropdown-item" href="#">แจ้งปัญหา</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">เกี่ยวกับเรา</a></li>
            </ul>
            
            <!-- Right-side Icons & User Menu -->
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="btn-icon fs-5"><i class="bi bi-geo-alt-fill"></i></a>
                <a href="#" class="btn-icon fs-5"><i class="bi bi-search"></i></a>
                
                @auth
                    <!-- User is logged in -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=a87c5f&color=fff' }}"
                                 alt="รูปโปรไฟล์" class="rounded-circle me-2" style="width: 38px; height: 38px; object-fit: cover;">
                            <span class="fw-semibold" style="color: #5b4636;">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="bi bi-grid-1x2-fill me-2"></i>แดชบอร์ด</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.profile.show') }}"><i class="bi bi-person-gear me-2"></i>จัดการโปรไฟล์</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <!-- User is a guest -->
                     <div class="dropdown">
                        <a href="#" class="btn-icon fs-4" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('login') }}">เข้าสู่ระบบ</a></li>
                            <li><a class="dropdown-item" href="{{ route('register') }}">สมัครสมาชิก</a></li>
                            <li><a class="dropdown-item" href="{{ url('/login-admin') }}">สำหรับ Admin Login</a></li>
                          
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
</header>




<!-- Bootstrap JS Bundle (Still needed for dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>