<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>คาเฟ่เปิดใหม่</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <style>
    body{font-family:"Prompt",sans-serif;background:linear-gradient(135deg,#fdfbfb,#ebedee);margin:0;color:#333}
    .title{text-align:center;font-size:2rem;font-weight:600;margin:40px 0 10px;color:#444}
    .title span{color:#1d976c}
    .update-time{text-align:center;font-size:.95rem;color:#666;margin-bottom:25px}
    .swiper{width:95%;max-width:900px;height:520px;margin:20px auto 60px;border-radius:20px;overflow:hidden;box-shadow:0 8px 25px rgba(0,0,0,.15);background:#fff}
    .swiper-slide{position:relative}
    .swiper-slide a{display:block;width:100%;height:100%}
    .swiper-slide img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
    .swiper-slide:hover img{transform:scale(1.05)}
    .badge{position:absolute;top:14px;left:14px;background:#1d976c;color:#fff;padding:6px 10px;border-radius:999px;font-size:.85rem}
    .caption{position:absolute;left:0;right:0;bottom:0;background:linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,.65) 60%);color:#fff;padding:18px}
    .caption h3{margin:0 0 6px;font-size:1.15rem}
    .caption p{margin:0;font-size:.9rem;opacity:.95}
    .swiper-button-next,.swiper-button-prev{color:#fff;background:rgba(0,0,0,.4);border-radius:50%;width:45px;height:45px;display:flex;align-items:center;justify-content:center;transition:background .3s}
    .swiper-button-next:hover,.swiper-button-prev:hover{background:rgba(0,0,0,.65)}
    .swiper-pagination-bullet{background:#bbb;opacity:1;transition:.3s}
    .swiper-pagination-bullet-active{background:#1d976c;transform:scale(1.2)}
  </style>
</head>
<body>
  {{-- Navbar --}}
  @guest @include('components.1navbar') @endguest
  @auth  @include('components.2navbar') @endauth

  <h1 class="title">☕ <span>คาเฟ่เปิดใหม่</span> ที่น่าลองในสุรินทร์</h1>
  <p class="update-time">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>

  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      @forelse($newCafes as $cafe)
        <div class="swiper-slide">
          <a href="{{ route('cafes.show', $cafe->cafe_id) }}">
            <img src="{{ $cafe->image_url }}" alt="{{ $cafe->cafe_name }}">
            <div class="badge">✨ เปิดใหม่</div>
            <div class="caption">
              <h3>{{ $cafe->cafe_name }}</h3>
              <p>เพิ่มเมื่อ {{ optional($cafe->created_at)->format('d/m/Y') }}</p>
            </div>
          </a>
        </div>
      @empty
        <div class="swiper-slide">
          <img src="{{ asset('images/placeholder-cafe.jpg') }}" alt="ไม่มีคาเฟ่ใหม่">
          <div class="caption"><h3>ยังไม่มีคาเฟ่เปิดใหม่</h3></div>
        </div>
      @endforelse
    </div>

    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    new Swiper(".mySwiper", {
      loop: true,
      autoplay: { delay: 3000, disableOnInteraction: false },
      pagination: { el: ".swiper-pagination", clickable: true },
      navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
      effect: "slide",
      speed: 600
    });
  </script>
</body>
</html>
