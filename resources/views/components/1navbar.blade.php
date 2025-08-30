<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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
    :root{
      --brand-primary:#a87c5f;
      --brand-secondary:#c49a6c;
      --brand-bg:#faf7f3;
      --brand-dark:#5b4636;
      --bs-body-font-family:'Kanit',sans-serif;
      --gradient-primary:linear-gradient(135deg,var(--brand-primary) 0%,var(--brand-secondary) 100%);
    }

    body{font-family:var(--bs-body-font-family); background:#faf7f3; line-height:1.55}

    .navbar-custom{
      background:rgba(255,255,255,.95);
      backdrop-filter:blur(16px);
      border-bottom:1px solid rgba(168,124,95,.12);
      box-shadow:0 6px 18px rgba(139,108,87,.10);
      position:relative; z-index:1060;
    }

    .navbar-brand span{
      background:var(--gradient-primary);
      -webkit-background-clip:text; background-clip:text;
      -webkit-text-fill-color:transparent;
    }

    /* เมนูแนวนอนเลื่อนได้ */
    .nav-scroll{overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width:none;}
    .nav-scroll::-webkit-scrollbar{display:none;}
    .nav-scroll .nav-link{
      white-space:nowrap;
      padding:.45rem .65rem!important;
      margin:0 .1rem;
      font-weight:500;
      color:var(--brand-dark)!important;
      transition:background .2s,color .2s;
      border-radius:.5rem;
    }
    .nav-link:hover,.nav-link.active{background:rgba(168,124,95,.08); color:var(--brand-primary)!important}

    .dropdown-menu{
      border-radius:.8rem;
      border:1px solid rgba(168,124,95,.18);
      box-shadow:0 18px 28px rgba(139,108,87,.15);
    }

    .user-avatar{width:34px;height:34px;object-fit:cover;border:2px solid rgba(168,124,95,.18);border-radius:50%}

    /* ย่อทุกอย่างบนจอเล็ก */
    @media (max-width: 575.98px){
      .navbar{padding-top:.25rem!important; padding-bottom:.25rem!important}
      .navbar-brand img{width:28px;height:28px}
      .navbar-brand span{font-size:.95rem}
      .nav-scroll .nav-link{font-size:.9rem; padding:.4rem .55rem!important}
      .user-avatar{width:28px;height:28px}
      .user-name{display:none}           /* ซ่อนชื่อผู้ใช้บนจอเล็ก คงเหลือเฉพาะไอคอน/อวาตาร์ */
      .btn-icon{font-size:1.25rem}
    }

    /* จอกลางขึ้นไป: จัดกึ่งกลางให้สวย */
    @media (min-width: 576px){
      .nav-scroll{justify-content:center}
    }
  </style>
</head>
<body>

<header>
  <!-- ไม่มีเมนูพับ / ไม่มีปุ่มแฮมเบอร์เกอร์ -->
  <nav class="navbar navbar-custom py-2">
    <div class="container d-flex align-items-center gap-2">

      <!-- โลโก้ซ้าย -->
      <a class="navbar-brand d-flex align-items-center me-2 text-decoration-none" href="{{ route('welcome') }}" aria-label="ไปหน้าแรก">
        <img src="{{ asset('/images/logo.png') }}" alt="โลโก้" class="me-2 rounded-circle border" style="width:32px;height:32px;object-fit:cover;">
        <span class="fw-bold" style="color:#5b4636;">น้องช้างสะเร็น</span>
      </a>

      <!-- เมนูกลาง: แนวนอนเลื่อนซ้าย-ขวาได้ -->
      <ul class="navbar-nav nav-scroll flex-row flex-nowrap flex-grow-1 mx-1">
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

      <!-- ขวา: ผู้ใช้/เข้าสู่ระบบ (ขนาดย่อ) -->
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
            <a href="#" class="btn-icon text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" aria-label="เมนูผู้ใช้">
              <i class="bi bi-person-circle" style="color:#5b4636; font-size:1.35rem;"></i>
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

<!-- Bootstrap JS (จำเป็นสำหรับ dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
