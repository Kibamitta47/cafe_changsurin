<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>น้องช้างสะเร็น</title>

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
      --brand-primary: #a87c5f;
      --brand-secondary: #c49a6c;
      --brand-tertiary: #d4b896;
      --brand-bg: #faf7f3;
      --brand-dark: #5b4636;
      --brand-light: #f8f4f0;
      --bs-body-font-family: 'Kanit', sans-serif;
      --bs-body-bg: var(--brand-bg);
      --bs-border-radius: 0.75rem;
      --bs-border-radius-lg: 1rem;
      --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.08);
      --shadow: 0 4px 12px rgba(139, 108, 87, 0.15);
      --shadow-lg: 0 20px 25px -5px rgba(139, 108, 87, 0.15), 0 10px 10px -5px rgba(139, 108, 87, 0.08);
      --gradient-primary: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
      --gradient-bg: linear-gradient(135deg, #faf7f3 0%, #f5f0ea 100%);
    }

    body {
      background: var(--gradient-bg);
      font-family: var(--bs-body-font-family);
      line-height: 1.6;
    }

    .navbar-custom {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(168, 124, 95, 0.1);
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
      position: relative;
      z-index: 1060;
    }

    .navbar-brand {
      transition: all 0.3s ease;
    }

    .navbar-brand span {
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-link {
      color: var(--brand-dark) !important;
      font-weight: 500;
      transition: all 0.3s ease;
      padding: 0.75rem 1rem !important;
      margin: 0 0.25rem;
    }

    .nav-link:hover,
    .nav-link.active {
      color: var(--brand-primary) !important;
      background: rgba(168, 124, 95, 0.08);
    }

    .dropdown-menu {
      z-index: 1055 !important;
      border-radius: var(--bs-border-radius-lg);
      border: 1px solid rgba(168, 124, 95, 0.15);
      box-shadow: var(--shadow-lg);
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(20px);
      margin-top: 0.5rem;
      position: absolute;
    }

    .dropdown-item:hover {
      background: linear-gradient(90deg, rgba(168, 124, 95, 0.08), transparent);
      color: var(--brand-primary);
    }

    .user-avatar {
      border: 2px solid rgba(168, 124, 95, 0.2);
      transition: all 0.3s ease;
    }

    .dropdown-toggle:hover .user-avatar {
      border-color: var(--brand-primary);
      transform: scale(1.05);
    }

    /* ป้องกันการถูกบัง */
    .hero-image, .banner, .cover-section {
      position: relative;
      z-index: 1;
      overflow: visible !important;
    }
  </style>
</head>
<body>

<header>
  <nav class="navbar navbar-expand navbar-custom">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
        <img src="{{ asset('/images/logo.png') }}" alt="โลโก้" style="height: 40px; width: 40px;" class="me-2 rounded-circle border">
        <span class="fw-bold" style="color: #5b4636;">น้องช้างสะเร็น</span>
      </a>
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link active" href="{{ route('welcome') }}">หน้าแรก</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">บริการ</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('line.add') }}">Line น้องช้างสะเร็น</a></li>
            <li><a class="dropdown-item" href="{{ route('advertising.packages') }}">ติดต่อโฆษณา</a></li>
            <li><a class="dropdown-item" href="{{ route('problem.info') }}">แจ้งปัญหา</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="{{ route('about.us') }}">เกี่ยวกับเรา</a></li>
      </ul>
      <div class="d-flex align-items-center gap-3">
        @auth
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=a87c5f&color=fff' }}"
                 alt="รูปโปรไฟล์" class="rounded-circle me-2 user-avatar" style="width: 38px; height: 38px; object-fit: cover;">
            <span class="fw-semibold user-name" style="color: #5b4636;">{{ Auth::user()->name }}</span>
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
       <div class="dropdown">
  <a href="#" class="btn-icon fs-4" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-person-circle" style="color: #5b4636;"></i>
  </a>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
