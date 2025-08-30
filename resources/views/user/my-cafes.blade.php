<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>คาเฟ่ของฉัน | น้องช้างสะเร็น</title>
    
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
            --bs-primary-rgb: 13, 110, 253;
            --bs-body-font-family: 'Kanit', sans-serif;
            --bs-body-bg: #f8f9fa;
            --bs-border-radius: 0.5rem;
            --bs-border-radius-lg: 0.75rem;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        body {
            padding-top: 80px;
        }
        .navbar {
            box-shadow: var(--shadow);
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .main-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: var(--bs-border-radius-lg);
            box-shadow: var(--shadow-lg);
            margin-top: 1.5rem;
            margin-bottom: 2rem;
        }
        .page-title {
            font-weight: 700;
            color: #212529;
        }
        .gradient-text {
            background: linear-gradient(45deg, #0d6efd, #6f42c1);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .empty-state-card {
            padding: 4rem 2rem;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: var(--bs-border-radius-lg);
            border: 2px dashed #dee2e6;
        }
        .empty-state-card .icon {
            font-size: 4rem;
            color: #ced4da;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<!-- Header (Navbar) -->
<nav class="navbar navbar-expand-lg fixed-top py-2">
    <div class="container">
        <a class="navbar-brand fw-bold gradient-text fs-4" href="{{ route('user.dashboard') }}">น้องช้างสะเร็น</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('user.dashboard') }}">แดชบอร์ด</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.cafes.my') ? 'active fw-semibold' : '' }}" href="{{ route('user.cafes.my') }}">คาเฟ่ของฉัน</a></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.reviews.my*') ? 'active fw-semibold' : '' }}" href="{{ route('user.reviews.my') }}">รีวิวของฉัน</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('user.cafes.create') }}" class="btn btn-primary rounded-pill d-none d-lg-flex align-items-center gap-2 px-3">
                    <i class="bi bi-plus-circle-fill"></i> เพิ่มคาเฟ่
                </a>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                        <li><a class="dropdown-item" href="{{ route('user.profile.show') }}"><i class="bi bi-person-gear me-2"></i>แก้ไขโปรไฟล์</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('user.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>


<!-- Content -->
<div class="container my-5">
    <div class="main-container">

        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h1 class="page-title mb-2 me-3">คาเฟ่ทั้งหมดของฉัน</h1>
            <span class="badge bg-primary rounded-pill fs-6 fw-normal">{{ $cafes->total() }} รายการ</span>
        </div>

        <!-- Alert Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($cafes->isNotEmpty())
            <div class="row g-4">
                @foreach ($cafes as $cafe)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            @php
                                $cafeImages = is_array($cafe->images) ? $cafe->images : [];
                                $imageUrl = !empty($cafeImages) ? asset('storage/' . $cafeImages[0]) : 'https://placehold.co/400x300/E2E8F0/64748B?text=No+Image';
                            @endphp
                            <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $cafe->cafe_name }}" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">{{ $cafe->cafe_name }}</h5>
                                <p class="card-text text-muted small mb-3">{{ Str::limit($cafe->address, 50) }}</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="badge 
                                        @if($cafe->status == 'approved') bg-success-subtle text-success-emphasis
                                        @elseif($cafe->status == 'pending') bg-warning-subtle text-warning-emphasis
                                        @else bg-danger-subtle text-danger-emphasis @endif">
                                        สถานะ: {{ ucfirst($cafe->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pb-3">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('cafes.show', $cafe) }}" class="btn btn-sm btn-outline-secondary">ดูร้าน</a>
                                    <a href="{{ route('user.cafes.edit', $cafe) }}" class="btn btn-sm btn-outline-primary">แก้ไข</a>
                                    <form action="{{ route('user.cafes.destroy', $cafe) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคาเฟ่นี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">ลบ</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state-card">
                <div class="icon"><i class="bi bi-cup-hot"></i></div>
                <h3 class="fw-bold">ยังไม่มีคาเฟ่</h3>
                <p class="text-muted">คุณยังไม่ได้เพิ่มคาเฟ่ของคุณเลย ลองเพิ่มคาเฟ่แรกของคุณดูสิ</p>
                <a href="{{ route('user.cafes.create') }}" class="btn btn-primary mt-3"><i class="bi bi-plus-circle me-2"></i>เพิ่มคาเฟ่ใหม่</a>
            </div>
        @endif

        @if ($cafes->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $cafes->links() }}
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
