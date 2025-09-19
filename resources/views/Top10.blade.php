<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Top10 คาเฟ่ยอดนิยม</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

  <style>
    body{font-family:'Kanit',sans-serif;background-color:#F8F9FA}
    .swiper{width:95%;max-width:1100px;height:520px;margin:20px auto 60px;border-radius:20px;overflow:hidden;box-shadow:0 8px 25px rgba(0,0,0,.12);background:#fff}
    .swiper-slide{position:relative}
    .swiper-slide a{display:block;width:100%;height:100%}
    .swiper-slide img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
    .swiper-slide:hover img{transform:scale(1.04)}
    .rank-badge{position:absolute;top:14px;left:14px;background:#111827CC;color:#fff;padding:6px 12px;border-radius:999px;font-weight:600}
    .caption{position:absolute;left:0;right:0;bottom:0;background:linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,.7) 70%);color:#fff;padding:18px}
    .caption h3{margin:0 0 6px;font-size:1.15rem}
    .caption p{margin:0;font-size:.95rem;opacity:.95}
    .swiper-button-next,.swiper-button-prev{color:#fff;background:rgba(0,0,0,.4);border-radius:50%;width:45px;height:45px;display:flex;align-items:center;justify-content:center;transition:background .3s}
    .swiper-button-next:hover,.swiper-button-prev:hover{background:rgba(0,0,0,.65)}
    .swiper-pagination-bullet{background:#bdbdbd;opacity:1;transition:.3s}
    .swiper-pagination-bullet-active{background:#e63946;transform:scale(1.2)}
  </style>
</head>
<body class="min-h-screen">
  @include('components.2navbar')

  <main class="container mx-auto px-4 py-10">
    <h1 class="text-4xl font-bold text-gray-800 mb-2">⭐ 10 อันดับคาเฟ่ยอดนิยม</h1>
    <p class="text-gray-500 mb-6">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>

    @if($topRatedCafes->isEmpty())
      <div class="p-6 rounded-xl bg-white border text-gray-600 shadow">ยังไม่มีข้อมูล Top10</div>
    @else
      <div class="swiper myTop10">
        <div class="swiper-wrapper">
          @foreach($topRatedCafes as $idx => $cafe)
            <div class="swiper-slide">
              <a href="{{ route('cafes.show', $cafe->cafe_id) }}">
                <img src="{{ $cafe->image_url }}" alt="{{ $cafe->cafe_name }}">
                <div class="rank-badge">#{{ $idx + 1 }}</div>
                <div class="caption">
                  <h3 class="line-clamp-1">{{ $cafe->cafe_name }}</h3>
                  <p>
                    ⭐ {{ number_format($cafe->reviews_avg_rating ?? 0, 2) }}
                    · รีวิว {{ $cafe->reviews_count ?? 0 }} ครั้ง
                  </p>
                </div>
              </a>
            </div>
          @endforeach
        </div>

        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
      </div>
    @endif
  </main>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    new Swiper(".myTop10", {
      loop: true,
      autoplay: { delay: 3000, disableOnInteraction: false },
      pagination: { el: ".swiper-pagination", clickable: true },
      navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
      effect: "slide",
      speed: 600,
      // แสดงหลายสไลด์ตามจอ
      slidesPerView: 1,
      breakpoints: {
        640: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024:{ slidesPerView: 2 },
        1280:{ slidesPerView: 3 }
      },
      spaceBetween: 16
    });
  </script>
</body>
</html>
