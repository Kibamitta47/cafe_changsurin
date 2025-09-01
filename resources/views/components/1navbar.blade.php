@once
<style>
  :root{
    --brand-primary:#a87c5f; --brand-secondary:#c49a6c; --brand-dark:#5b4636;
    --gradient-primary:linear-gradient(135deg,var(--brand-primary) 0%,var(--brand-secondary) 100%);
  }
  .navbar-custom{
    background:rgba(255,255,255,.95); backdrop-filter:blur(16px);
    border-bottom:1px solid rgba(168,124,95,.12);
    box-shadow:0 6px 18px rgba(139,108,87,.10); position:relative; z-index:1060;
  }
  .navbar-brand span{
    background:var(--gradient-primary); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent;
  }
  /* เมนูแนวนอนเลื่อนได้ (มือถือ) */
  .nav-scroll{display:flex; flex-wrap:nowrap; overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width:none;}
  .nav-scroll::-webkit-scrollbar{display:none;}
  .nav-scroll .nav-link{
    white-space:nowrap; padding:.45rem .65rem!important; margin:0 .1rem; font-weight:500;
    color:var(--brand-dark)!important; border-radius:.5rem; transition:background .2s,color .2s;
  }
  .nav-link:hover,.nav-link.active{background:rgba(168,124,95,.08); color:var(--brand-primary)!important}
  .dropdown-menu{border-radius:.8rem; border:1px solid rgba(168,124,95,.18); box-shadow:0 18px 28px rgba(139,108,87,.15)}
  .user-avatar{width:34px;height:34px;object-fit:cover;border:2px solid rgba(168,124,95,.18);border-radius:50%}
  @media (max-width:575.98px){
    .navbar{padding-top:.25rem!important; padding-bottom:.25rem!important}
    .navbar-brand img{width:28px;height:28px}
    .navbar-brand span{font-size:.95rem}
    .nav-scroll .nav-link{font-size:.9rem; padding:.4rem .55rem!important}
    .user-avatar{width:28px;height:28px}
    .user-name{display:none}
    .btn-icon{font-size:1.25rem}
  }
</style>
@endonce

<header>
  <nav class="navbar navbar-custom py-2" role="navigation" aria-label="เมนูหลัก">
    <div class="container d-flex align-items-center gap-2">

      <!-- โลโก้ -->
      <a class="navbar-brand d-flex align-items-center me-2 text-decoration-none" href="{{ route('welcome') }}" aria-label="ไปหน้าแรก">
        <img src="{{ asset('/images/logo.png') }}" alt="โลโก้" class="me-2 rounded-circle border" style="width:32px;height:32px;object-fit:cover;">
        <span class="fw-bold" style="color:#5b4636;">น้องช้างสะเร็น</span>
      </a>

      <!-- เมนูกลาง (เลื่อนซ้าย-ขวาได้บนมือถือ) -->
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

      <!-- ผู้ใช้ยังไม่ล็อกอิน -->
      <div class="d-flex align-items-center">
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
      </div>

    </div>
  </nav>
</header>
