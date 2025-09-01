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
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --brand-primary:#a87c5f;
      --brand-secondary:#c49a6c;
      --brand-bg:#faf7f3;
      --brand-dark:#5b4636;
      --bs-body-font-family:'Kanit',sans-serif;
      --gradient-primary:linear-gradient(135deg,var(--brand-primary) 0%,var(--brand-secondary) 100%);
    }

    body{background:#faf7f3;font-family:var(--bs-body-font-family);line-height:1.55}

    .navbar-custom{
      background:rgba(255,255,255,.95);
      backdrop-filter:blur(16px);
      border-bottom:1px solid rgba(168,124,95,.12);
      box-shadow:0 4px 12px rgba(139,108,87,.12);
      position:relative;z-index:1060;
    }
    .navbar-brand span{
      background:var(--gradient-primary);
      -webkit-background-clip:text;background-clip:text;
      -webkit-text-fill-color:transparent;
      color:#5b4636;
    }

    /* เมนูหลัก (ไม่พับ) */
    .nav-link{
      color:var(--brand-dark)!important;
      font-weight:500;
      padding:.65rem .9rem !important;
      border-radius:.5rem;
      transition:color .2s, background .2s;
      margin:0 .1rem;
    }
    .nav-link:hover,.nav-link.active{
      color:var(--brand-primary)!important;
      background:rgba(168,124,95,.08)
    }

    .dropdown-menu{
      border-radius:.9rem;
      border:1px solid rgba(168,124,95,.18);
      box-shadow:0 18px 28px rgba(139,108,87,.15);
      background:rgba(255,255,255,.98);
      backdrop-filter:blur(16px);
    }
    .dropdown-item:hover{
      background:linear-gradient(90deg,rgba(168,124,95,.08),transparent);
      color:var(--brand-primary);
    }

    .user-avatar{
      width:34px;height:34px;object-fit:cover;border-radius:50%;
      border:2px solid rgba(168,124,95,.18)
    }

    /* ===== ปรับ “ขนาดเล็ก” สำหรับมือถือ (เมนูไม่พับ แต่ย่อทุกอย่าง) ===== */
    @media (max-width: 767.98px){ /* md- */
      .container{padding-left:.75rem;padding-right:.75rem}
      .navbar{padding-top:.25rem!important;padding-bottom:.25rem!important}
      .navbar-brand img{width:28px;height:28px}
      .navbar-brand span{font-size:1rem}
      .nav-link{padding:.45rem .6rem !important;font-size:.9rem}
      .navbar-nav{gap:.1rem}
      .dropdown-menu{font-size:.92rem;min-width:11rem}
      .user-avatar{width:28px;height:28px}
      .user-name{display:none}           /* ซ่อนชื่อบนจอเล็ก เหลือเฉพาะอวาตาร์ */
      .btn-icon{font-size:1.25rem}
    }

    @media (max-width: 575.98px){ /* sm- */
      .navbar-brand img{width:24px;height:24px}
      .navbar-brand span{font-size:.95rem}
      .nav-link{font-size:.85rem;padding:.35rem .5rem !important;margin:0 .05rem}
    }
  </style>
</head>
<body>

<header>
  <!-- ไม่ใช้เมนูพับ: ใช้ .navbar-expand (ขยายตลอด) หรือจะตัดออกเลยก็ได้ -->
  <nav class="navbar navbar-expand navbar-custom">
    <div class="container d-flex align-items-center gap-2">

      <!-- โลโก้ -->
      <a class="navbar-brand d-flex align-items-center text-decoration-none" href="{{ route('welcome') }}" aria-label="ไปหน้าแรก">
        <img src="{{ asset('/images/logo.png') }}" alt="โลโก้" class="me-2 rounded-circle border" style="width:40px;height:40px;object-fit:cover;">
        <span class="fw-bold">น้องช้างสะเร็น</span>
      </a>

      <!-- เมนูหลัก (ไม่พับ) -->
      <ul class="navbar-nav mx-auto align-items-center">
        <li class="nav-item"><a class="nav-link active" href="{{ route('welcome') }}">หน้าแรก</a></li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">บริการ</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('line.add') }}">Line น้องช้างสะเร็น</a></li>
            <li><a class="dropdown-item" href="{{ route('advertising.packages') }}">ติดต่อโฆษณา</a></li>
            <li><a class="dropdown-item" href="{{ route('problem.info') }}">แจ้งปัญหา</a></li>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link" href="{{ route('about.us') }}">เกี่ยวกับเรา</a></li>
      </ul>

      <!-- ผู้ใช้ -->
      <div class="d-flex align-items-center">
        @auth
          <div class="dropdown">
            <a href="#" class="d-inline-flex align-items-center text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
              <img
                src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=a87c5f&color=fff' }}"
                alt="โปรไฟล์" class="user-avatar me-2">
              <span class="fw-semibold user-name" style="color:#5b4636;">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="bi bi-grid-1x2-fill me-2"></i>Dashboard</a></li>
              <li><a class="dropdown-item" href="{{ route('user.profile.show') }}"><i class="bi bi-person-gear me-2"></i>โปรไฟล์</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('user.logout') }}" class="d-inline">
                  @csrf
                  <a href="{{ route('user.logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                  </a>
                </form>
              </li>
            </ul>
          </div>
        @else
          <div class="dropdown">
            <a href="#" class="btn-icon text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" aria-label="เมนูผู้ใช้">
              <i class="bi bi-person-circle" style="color:#5b4636;font-size:1.35rem;"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="{{ route('login') }}">เข้าสู่ระบบ</a></li>
              <li><a class="dropdown-item" href="{{ route('register') }}">สมัคร</a></li>
            </ul>
          </div>
        @endauth
      </div>

    </div>
  </nav>
</header>

<!-- Bootstrap JS (สำหรับ dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
