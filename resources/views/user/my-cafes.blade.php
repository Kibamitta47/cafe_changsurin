<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>คาเฟ่ของฉัน | My Cafes</title>
    
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
            --bs-success: #198754;
            --bs-warning: #FFC107;
            --bs-danger: #DC3545;
            --bs-secondary: #6c757d;
            --bs-light: #F8F9FA;
            --bs-dark: #212529;
            --bs-body-font-family: 'Kanit', sans-serif;
            --bs-body-bg: var(--bs-light);
            --bs-border-radius: 0.5rem;
            --bs-border-radius-lg: 0.75rem;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        body { padding-top: 80px; }

        .navbar {
            box-shadow: var(--shadow);
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .main-container {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: var(--bs-border-radius-lg);
            box-shadow: var(--shadow-lg);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .page-title { font-weight: 700; color: var(--bs-dark); }
        .page-subtitle { color: var(--bs-secondary); }

        .nav-pills .nav-link {
            font-weight: 500;
            color: var(--bs-dark);
            border: 1px solid transparent;
        }
        .nav-pills .nav-link.active {
            background-color: var(--bs-primary);
            color: white;
            box-shadow: var(--shadow);
        }

        .cafe-card {
            border: 1px solid #E5E7EB;
            border-radius: var(--bs-border-radius-lg);
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .cafe-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .card-img-top { aspect-ratio: 16 / 10; object-fit: cover; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .card-stats {
            display: flex;
            justify-content: space-between;
            background-color: var(--bs-light);
            padding: 0.75rem 1.25rem;
            border-top: 1px solid #E5E7EB;
        }
        .card-stats .stat-item {
            font-size: 0.9rem;
            color: var(--bs-secondary);
        }
        
        .empty-state-card {
            padding: 4rem 2rem;
            text-align: center;
            background-color: var(--bs-light);
            border-radius: var(--bs-border-radius-lg);
            border: 2px dashed #dee2e6;
        }
        .empty-state-card .icon {
            font-size: 4rem;
            color: #ced4da;
            margin-bottom: 1.5rem;
        }

        .dropdown-menu {
             box-shadow: var(--shadow-lg);
        }

        .gradient-text {
            background: linear-gradient(45deg, #0d6efd, #6f42c1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>

<!-- Header (Bootstrap 5) -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold gradient-text fs-4" href="{{ route('user.dashboard') }}">น้องช้างสะเร็น</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">แดชบอร์ด</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.cafes.my') ? 'active' : '' }}" href="{{ route('user.cafes.my') }}">คาเฟ่ของฉัน</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.cafes.myLiked') ? 'active' : '' }}" href="{{ route('user.cafes.myLiked') }}">คาเฟ่ที่ถูกใจ</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('user.cafes.create') }}" class="btn btn-primary rounded-pill d-none d-lg-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> เพิ่มคาเฟ่
                </a>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
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

<div class="container">
    <div class="main-container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="page-title"><i class="bi bi-journal-richtext me-2"></i>คาเฟ่ของฉัน</h1>
                <p class="page-subtitle mb-0">จัดการและดูแลคาเฟ่ที่คุณสร้าง</p>
            </div>
            <a href="{{ route('user.cafes.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
                <i class="bi bi-plus-lg me-1"></i>เพิ่มคาเฟ่ใหม่
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <ul class="nav nav-pills mb-4" id="statusFilter" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-status="all">ทั้งหมด</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-status="pending"><i class="bi bi-hourglass-split me-1"></i>รออนุมัติ</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-status="approved"><i class="bi bi-patch-check-fill me-1"></i>อนุมัติแล้ว</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-status="rejected"><i class="bi bi-x-octagon-fill me-1"></i>ถูกปฏิเสธ</button></li>
        </ul>
        
        <div class="row g-4" id="cafe-list">
            @forelse ($cafes as $cafe)
                <div class="col-lg-4 col-md-6 cafe-item" data-status="{{ $cafe->status }}">
                    <div class="card cafe-card h-100">
                        <div class="position-relative">
                            <img src="{{ !empty($cafe->images) ? Storage::url($cafe->images[0]) : 'https://placehold.co/400x300/E2E8F0/64748B?text=No+Image' }}" class="card-img-top" alt="{{ $cafe->cafe_name }}">
                        </div>
                        
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title fw-bold mb-1">{{ $cafe->cafe_name }}</h5>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('user.cafes.edit', $cafe->id) }}"><i class="bi bi-pencil-square me-2"></i>แก้ไข</a></li>
                                        <li><button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $cafe->id }}"><i class="bi bi-trash3-fill me-2"></i>ลบ</button></li>
                                    </ul>
                                </div>
                            </div>
                            <p class="card-text text-secondary small mb-3">{{ Str::limit($cafe->address, 50) }}</p>

                            @if($cafe->status === 'approved')
                                <span class="badge rounded-pill text-bg-success status-badge"><i class="bi bi-check-circle-fill"></i> อนุมัติแล้ว</span>
                            @elseif($cafe->status === 'rejected')
                                <span class="badge rounded-pill text-bg-danger status-badge"><i class="bi bi-x-circle-fill"></i> ถูกปฏิเสธ</span>
                            @else
                                <span class="badge rounded-pill text-bg-warning status-badge"><i class="bi bi-hourglass-split"></i> รอการอนุมัติ</span>
                            @endif
                        </div>
                        
                        <!-- *** START: Updated Stats Section *** -->
                        <div class="card-stats mt-3">
                            <span class="stat-item" title="ยอดเข้าชมทั้งหมด">
                                <i class="bi bi-eye-fill me-1"></i>
                                {{ number_format($cafe->view_count) }}
                            </span>
                            <span class="stat-item" title="จำนวนคนกดถูกใจ">
                                <i class="bi bi-heart-fill me-1 text-danger"></i>
                                {{ number_format($cafe->likers_count) }}
                            </span>
                        </div>
                        <!-- *** END: Updated Stats Section *** -->
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal{{ $cafe->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0"><h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>ยืนยันการลบ</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">คุณแน่ใจหรือไม่ว่าต้องการลบคาเฟ่ "<strong>{{ $cafe->cafe_name }}</strong>"?</div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                <form method="POST" action="{{ route('user.cafes.destroy', $cafe->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                     <div class="empty-state-card" id="empty-state">
                        <div class="icon"><i class="bi bi-journal-x"></i></div>
                        <h3 class="fw-bold mb-3">ยังไม่มีคาเฟ่ในรายการ</h3>
                        <p class="text-secondary mb-4">มาเริ่มต้นสร้างคาเฟ่แรกของคุณกันเลย!</p>
                        <a href="{{ route('user.cafes.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2"><i class="bi bi-plus-lg me-1"></i>เพิ่มคาเฟ่แรกของคุณ</a>
                    </div>
                </div>
            @endforelse
        </div>
        
        <div class="col-12 d-none" id="empty-filter-state">
            <div class="empty-state-card">
                <div class="icon"><i class="bi bi-search"></i></div>
                <h3 class="fw-bold mb-3">ไม่พบคาเฟ่ในสถานะนี้</h3>
                <p class="text-secondary mb-4">ไม่มีคาเฟ่ที่ตรงกับสถานะที่คุณเลือกในขณะนี้</p>
            </div>
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $cafes->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript for filtering remains the same
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('#statusFilter .nav-link');
    const cafeItems = document.querySelectorAll('.cafe-item');
    const emptyFilterState = document.getElementById('empty-filter-state');
    const mainEmptyState = document.getElementById('empty-state');
    const pagination = document.querySelector('.pagination');

    filterButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const status = this.getAttribute('data-status');
            let visibleCount = 0;

            cafeItems.forEach(item => {
                if (status === 'all' || item.getAttribute('data-status') === status) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            if (mainEmptyState) mainEmptyState.classList.add('d-none');
            
            emptyFilterState.classList.toggle('d-none', visibleCount > 0 || mainEmptyState);
            
            if (pagination) {
                 pagination.style.display = (status === 'all') ? 'flex' : 'none';
            }
        });
    });
});
</script>
</body>
</html>