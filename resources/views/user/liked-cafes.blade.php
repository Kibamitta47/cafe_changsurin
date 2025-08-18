<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คาเฟ่ที่ถูกใจ - น้องช้างสะเร็น</title>

    <!-- Frameworks & Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root { --bs-primary: #EC4899; --bs-primary-rgb: 236, 72, 153; --bs-secondary: #6B7280; --bs-light: #F9FAFB; --bs-dark: #1F2937; --bs-body-font-family: 'Kanit', sans-serif; --bs-body-bg: #F9FAFB; --bs-border-radius: 0.5rem; --bs-border-radius-lg: 0.75rem; --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        body { padding-top: 80px; }
        .navbar { box-shadow: var(--shadow); background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .gradient-text { background: linear-gradient(45deg, var(--bs-primary), #EF4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .page-header .icon { font-size: 2.5rem; line-height: 1; }
        .cafe-card { border: 1px solid #E5E7EB; border-radius: var(--bs-border-radius-lg); box-shadow: var(--shadow); transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.5s ease; display: flex; flex-direction: column; }
        .cafe-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); }
        .card-img-container { position: relative; height: 200px; overflow: hidden; border-top-left-radius: var(--bs-border-radius-lg); border-top-right-radius: var(--bs-border-radius-lg); }
        .card-img-container img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
        .cafe-card:hover .card-img-container img { transform: scale(1.05); }
        .like-button {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            width: 40px;
            height: 40px;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: none;
            transition: background-color 0.2s ease;
            z-index: 10; /* [MODIFIED] เพิ่ม z-index เพื่อให้แน่ใจว่าปุ่มอยู่ชั้นบนสุด */
        }
        .like-button:hover { background-color: rgba(0, 0, 0, 0.6); }
        .like-button i { font-size: 1.25rem; transition: all 0.2s ease; }
        .like-button .fa-solid { color: var(--bs-primary); }
        .tag { font-size: 0.75rem; font-weight: 500; padding: 0.25rem 0.75rem; border-radius: 50px; }
        .empty-state { background-color: white; border-radius: var(--bs-border-radius-lg); padding: 4rem 2rem; text-align: center; border: 1px solid #E5E7EB; }
        .empty-state .icon { font-size: 4rem; color: #D1D5DB; }
        /* [MODIFIED] เพิ่มสไตล์สำหรับลิงก์ที่คลุมรูปภาพ */
        .card-img-link {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1; /* ทำให้ลิงก์อยู่ใต้ปุ่มหัวใจ */
        }
        .card-title-link {
            text-decoration: none;
            color: var(--bs-dark);
            position: relative;
            z-index: 1; /* แยก z-index กับปุ่ม */
        }
        .card-title-link:hover {
            color: var(--bs-primary);
        }
        .card-title-link::after {
             content: '';
             position: absolute;
             left: 0;
             right: 0;
             top: 0;
             bottom: 0;
        }

    </style>
</head>
<body>

<!-- Header (เหมือนเดิม) -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="{{ route('welcome') }}" style="background: linear-gradient(45deg, #0ea5e9, #6366f1);-webkit-background-clip: text;-webkit-text-fill-color: transparent;background-clip: text;">น้องช้างสะเร็น</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">แดชบอร์ด</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.cafes.my') ? 'active' : '' }}" href="{{ route('user.cafes.my') }}">คาเฟ่ของฉัน</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.cafes.myLiked') ? 'active' : '' }}" href="{{ route('user.cafes.myLiked') }}">คาเฟ่ที่ถูกใจ</a></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.reviews.my*') ? 'active bg-blue-500 text-white' : '' }}" href="{{ route('user.reviews.my') }}">รีวิวของฉัน</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('user.cafes.create') }}" class="btn btn-primary rounded-pill d-none d-lg-flex align-items-center gap-2" style="background-color: #0ea5e9; border:none;">
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

<!-- Main Content -->
<div class="container my-5" x-data="likedCafesManager()">
    <div class="page-header text-center mb-5">
        <div class="icon gradient-text"><i class="bi bi-heart-fill"></i></div>
        <h1 class="display-5 fw-bold text-dark mt-3">คาเฟ่ที่ถูกใจ</h1>
        <p class="lead text-secondary">รายการคาเฟ่ที่คุณบันทึกไว้ กลับมาดูเมื่อไหร่ก็ได้!</p>
    </div>

    <div id="liked-cafes-container">
        @if ($likedCafes->isEmpty())
            <div class="empty-state">
                <div class="icon"><i class="bi bi-journal-x"></i></div>
                <h3 class="mt-4 fw-bold">ยังไม่มีรายการโปรด</h3>
                <p class="text-secondary mt-2 mb-4">ลองค้นหาคาเฟ่ที่ใช่ แล้วกดรูปหัวใจเพื่อบันทึกไว้ที่นี่</p>
                <a href="{{ route('welcome') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-search me-2"></i> ค้นหาคาเฟ่เลย
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach ($likedCafes as $cafe)
                    @php
                        $cafeImages = is_array($cafe->images) ? $cafe->images : [];
                        $imageUrl = !empty($cafeImages) ? asset('storage/' . $cafeImages[0]) : 'https://placehold.co/400x300/E2E8F0/64748B?text=No+Image';
                        $cafeStyles = is_array($cafe->cafe_styles) ? $cafe->cafe_styles : [];
                    @endphp
                    <div class="col-sm-6 col-lg-4 col-xl-3" id="cafe-card-{{ $cafe->id }}">
                        <div class="cafe-card h-100">
                            <div class="card-img-container">
                                <img src="{{ $imageUrl }}" alt="รูปคาเฟ่ {{ $cafe->cafe_name }}">
                                <!-- [MODIFIED] ทำให้ลิงก์คลุมแค่รูปภาพ และอยู่ใต้ปุ่ม -->
                                <a href="{{ route('cafes.show', $cafe->id) }}" class="card-img-link"></a>
                                <!-- [MODIFIED] ไม่ต้องใช้ .stop หรือ .prevent แล้ว เพราะปุ่มแยกขาดจากลิงก์โดยสิ้นเชิง -->
                                <button @click="toggleLike({{ $cafe->id }})" class="like-button">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold mb-1">
                                    <!-- [MODIFIED] เอา stretched-link ออก และใช้คลาสใหม่แทน -->
                                    <a href="{{ route('cafes.show', $cafe->id) }}" class="card-title-link">{{ $cafe->cafe_name }}</a>
                                </h5>
                                <p class="card-text text-secondary small mb-3">
                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ Str::limit($cafe->address, 40) }}
                                </p>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @foreach (array_slice($cafeStyles, 0, 2) as $style)
                                        <span class="tag bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $style }}</span>
                                    @endforeach
                                </div>
                                <div class="mt-auto d-flex align-items-center justify-content-between text-secondary small">
                                    <div><i class="bi bi-tags-fill text-success me-1"></i>{{ $cafe->price_range ?? 'N/A' }}</div>
                                    <div class="text-end"><i class="bi bi-clock-fill text-info me-1"></i>{{ $cafe->open_time ? \Carbon\Carbon::parse($cafe->open_time)->format('H:i') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $likedCafes->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Scripts (เหมือนเดิม) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function likedCafesManager() {
        return {
            async toggleLike(cafeId) {
                // event.stopPropagation(); // ไม่จำเป็นแล้ว แต่เก็บไว้เผื่อดีบัก
                const button = event.currentTarget;
                const card = document.getElementById(`cafe-card-${cafeId}`);
                if (!card) return;

                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                const url = `/cafes/${cafeId}/toggle-like`;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Server responded with an error.');
                    }

                    const data = await response.json();

                    if (data.status === 'success' && data.is_liked === false) {
                        console.log('Unlike successful. Removing card.');
                        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease, margin-bottom 0.5s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.marginBottom = `-${card.offsetHeight}px`;

                        setTimeout(() => {
                            card.remove();
                            const container = document.querySelector('.row.g-4');
                            if (container && container.childElementCount === 0) {
                                window.location.reload();
                            }
                        }, 500);

                    } else {
                        throw new Error('Server logic error: Cafe is still liked.');
                    }

                } catch (error) {
                    console.error('Failed to toggle like:', error);
                    alert('เกิดข้อผิดพลาดในการยกเลิกการถูกใจ กรุณาลองใหม่');
                    
                    button.disabled = false;
                    button.innerHTML = '<i class="fa-solid fa-heart"></i>';
                }
            }
        };
    }
</script>
</body>
</html>