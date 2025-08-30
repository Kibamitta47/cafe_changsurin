<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
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
    :root{
      --bs-primary:#EC4899; --bs-primary-rgb:236,72,153;
      --bs-body-font-family:'Kanit',sans-serif; --bs-body-bg:#F9FAFB;
      --bs-border-radius:.5rem; --bs-border-radius-lg:.75rem;
      --shadow:0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -2px rgb(0 0 0 / .1);
      --shadow-lg:0 10px 15px -3px rgb(0 0 0 / .1), 0 4px 6px -4px rgb(0 0 0 / .1);
    }
    body{ padding-top:70px; }
    .navbar{ box-shadow:var(--shadow); background:rgba(255,255,255,.95); backdrop-filter:blur(10px); }
    .gradient-text{ background:linear-gradient(45deg,var(--bs-primary),#EF4444); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }

    .page-header .icon{ font-size:2.5rem; line-height:1; }

    .cafe-card{ border:1px solid #E5E7EB; border-radius:var(--bs-border-radius-lg); box-shadow:var(--shadow); transition:transform .3s, box-shadow .3s, opacity .3s, height .3s; display:flex; flex-direction:column; }
    .cafe-card:hover{ transform:translateY(-8px); box-shadow:var(--shadow-lg); }

    .card-img-container{ position:relative; height:200px; overflow:hidden; border-top-left-radius:var(--bs-border-radius-lg); border-top-right-radius:var(--bs-border-radius-lg); }
    .card-img-container img{ width:100%; height:100%; object-fit:cover; transition:transform .3s; }
    .cafe-card:hover .card-img-container img{ transform:scale(1.05); }
    @media (max-width:575.98px){ .card-img-container{ height:160px; } } /* มือถือ */

    .like-button{ position:absolute; top:.75rem; right:.75rem; width:40px; height:40px; background:rgba(0,0,0,.4); backdrop-filter:blur(4px); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; border:none; transition:background-color .2s; z-index:10; }
    .like-button:hover{ background:rgba(0,0,0,.6); }
    .like-button i{ font-size:1.25rem; }
    .like-button .fa-solid{ color:var(--bs-primary); }

    .tag{ font-size:.75rem; font-weight:500; padding:.25rem .75rem; border-radius:50px; }

    .empty-state{ background:#fff; border-radius:var(--bs-border-radius-lg); padding:4rem 2rem; text-align:center; border:1px solid #E5E7EB; }
    .empty-state .icon{ font-size:4rem; color:#D1D5DB; }
  </style>
</head>
<body>

  <!-- Header (Navbar) -->
  @include('partials.header1')

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

            <div class="col-12 col-sm-6 col-lg-4 col-xl-3" id="cafe-card-{{ $cafe->cafe_id }}" x-ref="card{{ $cafe->cafe_id }}">
              <div class="cafe-card h-100">
                <div class="card-img-container">
                  <img src="{{ $imageUrl }}" alt="รูปคาเฟ่ {{ $cafe->cafe_name }}">
                  <a href="{{ route('cafes.show', $cafe) }}" class="card-img-link"></a>
                  <!-- ส่ง $event เข้าไปแก้บั๊กปุ่ม -->
                  <button @click="toggleLike({{ $cafe->cafe_id }}, $event)" class="like-button">
                    <i class="fa-solid fa-heart"></i>
                  </button>
                </div>

                <div class="card-body d-flex flex-column">
                  <h5 class="card-title fw-bold mb-1">
                    <a href="{{ route('cafes.show', $cafe) }}" class="card-title-link">{{ $cafe->cafe_name }}</a>
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

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function likedCafesManager(){
      return {
        async toggleLike(cafeId, ev){
          const card = this.$refs[`card${cafeId}`];
          if(!card){ console.error('Card not found:', cafeId); return; }

          const button = ev.currentTarget;
          if(button.disabled) return;
          button.disabled = true;
          const prevHTML = button.innerHTML;
          button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

          try{
            const res = await fetch(`/cafes/${cafeId}/toggle-like`, {
              method:'POST',
              headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
            });
            if(!res.ok) throw new Error('HTTP '+res.status);
            const data = await res.json();

            // เอาออกจากรายการเมื่อยกเลิกถูกใจสำเร็จ
            if(data.status === 'success' && data.is_liked === false){
              card.style.transition = 'opacity .3s ease, transform .3s ease, margin-top .3s ease';
              card.style.opacity = '0';
              card.style.transform = 'scale(.97)';
              setTimeout(()=>{
                card.remove();
                const row = document.querySelector('#liked-cafes-container .row');
                if(row && row.children.length === 0){
                  window.location.reload();
                }
              }, 300);
            }else{
              throw new Error('Unexpected response: '+JSON.stringify(data));
            }
          }catch(err){
            console.error(err);
            button.disabled = false;
            button.innerHTML = prevHTML;
          }
        }
      }
    }
  </script>
</body>
</html>
