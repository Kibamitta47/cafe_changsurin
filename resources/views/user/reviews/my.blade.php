<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>รีวิวของฉัน | น้องช้างสะเร็น</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* === Custom Properties & Base Styles === */
        :root {
            --bs-primary-rgb: 13, 110, 253;
            --bs-body-font-family: 'Kanit', sans-serif;
            --bs-body-bg: #f8f9fa;
            --bs-border-radius: 0.5rem;
            --bs-border-radius-lg: 0.75rem;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        body {
            padding-top: 70px; /* Space for fixed navbar */
            background-color: var(--bs-body-bg);
        }

        /* === Components Styling === */
        .navbar {
            box-shadow: var(--shadow);
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
        
        /* Review Item Card */
        .review-card {
            transition: background-color 0.2s ease-in-out;
        }
        .review-card:hover {
            background-color: #f8f9fa;
        }

        .star-rating i {
            font-size: 1.1rem;
            margin-right: 0.1rem;
        }
        
        /* Empty State Card */
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
        
        /* Pagination */
        .pagination .page-link {
            transition: all 0.2s ease;
        }
        .pagination .page-item.active .page-link {
            box-shadow: var(--shadow);
        }
        
        /* Alert */
        .alert-dismissible {
            box-shadow: var(--shadow);
        }
    </style>
</head>
<body>

<!-- Header (Bootstrap 5) -->
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
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.cafes.myLiked') ? 'active fw-semibold' : '' }}" href="{{ route('user.cafes.myLiked') }}">คาเฟ่ที่ถูกใจ</a></li>
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

        <!-- ตกแต่งส่วนหัว -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h1 class="page-title mb-2 me-3">รีวิวทั้งหมดของฉัน</h1>
            <span class="badge bg-primary rounded-pill fs-6 fw-normal">{{ $reviews->total() }} รายการ</span>
        </div>

        <!-- ข้อความแจ้งเตือน (Alert) -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($reviews->isNotEmpty())
            <!-- การ์ดแสดงรีวิว (ถ้ามีข้อมูล) -->
            <div class="list-group list-group-flush">
    @foreach ($reviews as $review)
        <div class="list-group-item px-1 py-4 review-card">
            <div class="d-flex flex-column flex-md-row w-100">
                <!-- ส่วนเนื้อหารีวิว (ซ้าย) -->
                 <div class="flex-grow-1 mb-3 mb-md-0">

                                {{-- ========================================== --}}
                                {{-- ✅✅✅ จุดที่แก้ไข อยู่ตรงนี้ ✅✅✅ --}}
                                {{-- ========================================== --}}
                                @if ($review->cafe) {{-- ตรวจสอบว่ารีวิวนี้มีความสัมพันธ์กับคาเฟ่ --}}
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <i class="bi bi-geo-alt-fill text-danger"></i>
                                        <span class="text-muted">รีวิวสำหรับ:</span>
                                        {{-- ใช้ $review->cafe (Object) แทน $review->cafe->id --}}
                                        <a href="{{ route('cafes.show', $review->cafe) }}" class="fw-bold text-decoration-none">
                                            {{-- แก้ไข: ใช้ cafe_name ตามที่คุณมีในฐานข้อมูล --}}
                                            {{ $review->cafe->cafe_name }}
                                        </a>
                                    </div>
                                @endif
                                {{-- ========================================== --}}
                                
                                <h5 class="mb-1 fw-bold">{{ $review->title }}</h5>
                                
                                <div class="d-flex align-items-center mb-2 star-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2 text-muted fw-light">({{ $review->rating }} เต็ม 5)</span>
                                </div>
                                
                                <p class="mb-0 text-secondary">{{ $review->content }}</p>

                            </div>
                            
                            <!-- ส่วนปุ่มและเวลา (ขวา) -->
                            <div class="flex-shrink-0 ms-md-4 text-start text-md-end">
                                <small class="text-muted d-block mb-2">{{ $review->created_at->isoFormat('LL') }}</small>
                                <div class="d-flex gap-2 justify-content-start justify-content-md-end">
                                    <a href="{{ route('user.reviews.edit', $review) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i> แก้ไข</a>
                                    <form action="{{ route('user.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรีวิวนี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3 me-1"></i> ลบ</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- แสดงผลเมื่อไม่มีรีวิว -->
            <div class="empty-state-card">
                <div class="icon"><i class="bi bi-chat-square-text"></i></div>
                <h3 class="fw-bold">ยังไม่มีรีวิว</h3>
                <p class="text-muted">ดูเหมือนว่าคุณยังไม่เคยเขียนรีวิวให้กับคาเฟ่ไหนเลย</p>
                <a href="{{ route('welcome') }}" class="btn btn-primary mt-3">ไปหน้าค้นหาคาเฟ่</a>
            </div>
        @endif

        <!-- Pagination -->
        @if ($reviews->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>