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
    :root{
      --shadow: 0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -2px rgb(0 0 0 / .1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / .1), 0 4px 6px -4px rgb(0 0 0 / .1);
      --radius: .75rem;
    }
    html,body{ font-family: 'Kanit', sans-serif; background:#f8f9fa; }
    /* ระยะ top เผื่อ header fixed */
    body{ padding-top: 80px; }

    /* กล่องหลัก */
    .main-container{
      background:#fff; border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      padding: 1.25rem;       /* มือถือ */
    }
    @media (min-width:768px){
      .main-container{ padding: 2rem; }
    }

    .page-title{ font-weight:700; color:#212529; }
    .gradient-text{
      background: linear-gradient(45deg,#0d6efd,#6f42c1);
      -webkit-background-clip:text; background-clip:text; color:transparent;
    }

    /* การ์ด */
    .card{ border:0; border-radius: var(--radius); box-shadow: var(--shadow); }
    .card .ratio{ border-top-left-radius: var(--radius); border-top-right-radius: var(--radius); overflow:hidden; }

    /* ปุ่มในการ์ด: มือถือให้กว้างเต็มบรรทัด เรียงแนวตั้ง */
    .card-actions{ display:grid; gap:.5rem; grid-template-columns: 1fr; }
    @media (min-width:768px){
      .card-actions{ display:flex; justify-content:flex-end; gap:.5rem; }
    }

    /* empty state */
    .empty-state{
      padding: 3rem 1.5rem; text-align:center; background:#f8f9fa;
      border:2px dashed #dee2e6; border-radius: var(--radius);
    }
    .empty-state .icon{ font-size:3rem; color:#ced4da; margin-bottom:.75rem; }
    @media (min-width:768px){
      .empty-state{ padding: 4rem 2rem; }
      .empty-state .icon{ font-size:4rem; }
    }

    /* ปุ่มลอย เพิ่มคาเฟ่ (แสดงเฉพาะมือถือ) */
    .fab-add{
      position: fixed; right: 16px; bottom: 80px; z-index: 1040;
      border-radius: 999px; box-shadow: var(--shadow-lg);
    }
    @media (min-width:768px){ .fab-add{ display:none; } }

    /* ปรับระยะ container บนจอเล็ก */
    .container-responsive{ padding-left: .75rem; padding-right: .75rem; }
    @media (min-width:576px){ .container-responsive{ padding-left: .75rem; padding-right: .75rem; } }
  </style>
</head>
<body>

  <!-- Header -->
  @include('partials.header1')

  <!-- Content -->
  <div class="container container-responsive my-3 my-md-4">
    <div class="main-container">

      <!-- Page Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 pb-3 border-bottom">
        <h1 class="page-title mb-0">คาเฟ่ทั้งหมดของฉัน</h1>
        <span class="badge text-bg-primary rounded-pill fs-6 fw-normal align-self-start align-self-md-center">
          {{ $cafes->total() }} รายการ
        </span>
      </div>

      <!-- Alert -->
      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if($cafes->isNotEmpty())
        <!-- Grid: 1 คอลัมน์มือถือ / 2 ที่ md / 3 ที่ lg -->
        <div class="row g-3 g-md-4">
          @foreach ($cafes as $cafe)
            <div class="col-12 col-sm-6 col-lg-4">
              <div class="card h-100">
                @php
                  $cafeImages = is_array($cafe->images) ? $cafe->images : [];
                  $imageUrl = !empty($cafeImages) ? asset('storage/' . $cafeImages[0]) : 'https://placehold.co/800x600/E2E8F0/64748B?text=No+Image';
                @endphp

                <!-- รูป: ใช้สัดส่วน 4x3 + cover -->
                <div class="ratio ratio-4x3">
                  <img src="{{ $imageUrl }}" class="w-100 h-100 object-fit-cover" alt="{{ $cafe->cafe_name }}">
                </div>

                <div class="card-body d-flex flex-column">
                  <h5 class="card-title fw-bold mb-1 text-truncate">{{ $cafe->cafe_name }}</h5>
                  <p class="card-text text-muted small mb-3">
                    {{ Str::limit($cafe->address, 80) }}
                  </p>

                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="badge
                      @if($cafe->status == 'approved') text-bg-success
                      @elseif($cafe->status == 'pending') text-bg-warning
                      @else text-bg-danger @endif">
                      สถานะ: {{ ucfirst($cafe->status) }}
                    </span>
                    <small class="text-muted">{{ $cafe->updated_at?->diffForHumans() }}</small>
                  </div>
                </div>

                <div class="card-footer bg-white border-0 pt-0 pb-3">
                  <div class="card-actions">
                    <a href="{{ route('cafes.show', $cafe) }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">
                      ดูร้าน
                    </a>
                    <a href="{{ route('user.cafes.edit', $cafe) }}" class="btn btn-outline-primary btn-sm w-100 w-md-auto">
                      แก้ไข
                    </a>
                    <form action="{{ route('user.cafes.destroy', $cafe) }}" method="POST"
                          onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคาเฟ่นี้?');" class="w-100 w-md-auto">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        ลบ
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="empty-state mt-3">
          <div class="icon"><i class="bi bi-cup-hot"></i></div>
          <h3 class="fw-bold mb-1">ยังไม่มีคาเฟ่</h3>
          <p class="text-muted mb-3">คุณยังไม่ได้เพิ่มคาเฟ่ของคุณเลย ลองเพิ่มคาเฟ่แรกของคุณดูสิ</p>
          <a href="{{ route('user.cafes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>เพิ่มคาเฟ่ใหม่
          </a>
        </div>
      @endif

      @if ($cafes->hasPages())
        <div class="mt-4 mt-md-5 d-flex justify-content-center">
          {{ $cafes->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- FAB เพิ่มคาเฟ่ (มือถือ) -->
  <a href="{{ route('user.cafes.create') }}" class="btn btn-primary fab-add">
    <i class="bi bi-plus-circle me-1"></i> เพิ่มคาเฟ่
  </a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
