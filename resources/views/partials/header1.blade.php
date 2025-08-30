<!-- resources/views/partials/header.blade.php -->
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand fw-bold gradient-text" href="{{ route('welcome') }}">
      น้องช้างสะเร็น
    </a>

    <!-- Hamburger -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}">หน้าแรก</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('user.dashboard') }}">หน้าบ้านของฉัน</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('user.cafes.my') }}">คาเฟ่ของฉัน</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('user.cafes.myLiked') }}">คาเฟ่ที่ถูกใจ</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('user.reviews.my') }}">รีวิวของฉัน</a></li>
      </ul>

      <!-- ปุ่มเพิ่มคาเฟ่ -->
      <a href="{{ route('user.cafes.create') }}" class="btn btn-primary ms-lg-3">
        <i class="bi bi-plus-circle me-1"></i> เพิ่มคาเฟ่
      </a>

      <!-- Dropdown โปรไฟล์ -->
      <div class="dropdown ms-3">
        <button class="btn btn-light border rounded-circle dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-person"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li class="dropdown-header">
            ลงชื่อเข้าใช้ในชื่อ <br><strong>{{ Auth::user()->name }}</strong>
          </li>
          <li><a class="dropdown-item" href="{{ route('user.profile.show') }}"><i class="bi bi-pencil-square me-2"></i>แก้ไขโปรไฟล์</a></li>
          <li>
            <form method="POST" action="{{ route('user.logout') }}">
              @csrf
              <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
