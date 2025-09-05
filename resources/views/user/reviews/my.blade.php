<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>รีวิวของฉัน | น้องช้างสะเร็น</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --shadow: 0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -2px rgb(0 0 0 / .1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / .1), 0 4px 6px -4px rgb(0 0 0 / .1);
      --radius: .75rem;
    }
    html,body{ font-family:'Kanit',sans-serif; background:#f8f9fa; }
    body{ padding-top:70px; } /* เผื่อ header fixed-top */
    .main{ background:#fff; border-radius:var(--radius); box-shadow:var(--shadow-lg); padding:1rem; }
    @media (min-width:768px){ .main{ padding:2rem; } }
    .review-card{ border:0; border-radius:var(--radius); box-shadow:var(--shadow); }
    .review-card:hover{ box-shadow:var(--shadow-lg); }
    .star{ color:#f59e0b; } /* ดาวสีทอง */
    .empty{ background:#fff; border:1px dashed #dee2e6; border-radius:var(--radius); text-align:center; padding:3rem 1.5rem; }
    .truncate-2{
      display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
    }
  </style>
</head>
<body>

  @include('partials.header1')

  <div class="container my-4 my-md-5">
    <div class="main">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 pb-2 border-bottom">
        <h1 class="h3 mb-2 mb-md-0 fw-bold">รีวิวทั้งหมดของฉัน</h1>
        <span class="badge text-bg-primary">{{ $reviews->total() }} รายการ</span>
      </div>

      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
          <button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if ($reviews->isEmpty())
        <div class="empty">
          <div class="display-6 text-muted mb-2"><i class="bi bi-chat-left-quote"></i></div>
          <h5 class="fw-bold mb-1">ยังไม่มีรีวิว</h5>
          <p class="text-muted mb-3">ไปที่หน้าร้านแล้วเขียนรีวิวแรกของคุณได้เลย</p>
          <a href="{{ route('welcome') }}" class="btn btn-primary"><i class="bi bi-search me-1"></i> ค้นหาร้าน</a>
        </div>
      @else
        <div class="vstack gap-3">
          @foreach ($reviews as $review)
            @php
              $cafe = $review->cafe ?? null;
              $rating = (int)($review->rating ?? 0);
            @endphp

            <div class="card review-card">
              <div class="card-body">
                <div class="d-flex gap-3">
                  <div class="w-100">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                      <div>
                        <div class="small text-muted">รีวิวสำหรับร้าน</div>
                        <h5 class="mb-1">
                          @if($cafe)
                            <a href="{{ route('cafes.show', $cafe) }}" class="link-dark link-underline-opacity-0 link-underline-opacity-100-hover">
                              {{ $cafe->cafe_name }}
                            </a>
                          @else
                            ไม่มีชื่อร้าน
                          @endif
                        </h5>
                      </div>
                      <div class="text-nowrap">
                        @for($i=1;$i<=5;$i++)
                          <i class="bi bi-star{{ $i <= $rating ? '-fill' : '' }} star"></i>
                        @endfor
                        <span class="ms-1 small text-muted">({{ $rating }}/5)</span>
                      </div>
                    </div>

                    <div class="mt-2">
                      <div class="fw-semibold">{{ $review->title }}</div>
                      <p class="mb-2 text-muted truncate-2">{{ $review->content }}</p>
                      <div class="small text-secondary">
                        เขียนเมื่อ: {{ $review->created_at->format('d/m/Y') }}
                      </div>
                    </div>

                    <div class="mt-3 d-grid gap-2 d-sm-flex justify-content-sm-end">
                      <a href="{{ route('user.reviews.edit', $review) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil-square me-1"></i>แก้ไข
                      </a>
                      <form action="{{ route('user.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรีวิวนี้?')" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                          <i class="bi bi-trash3 me-1"></i>ลบ
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
          {{ $reviews->links() }}
        </div>
      @endif
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
