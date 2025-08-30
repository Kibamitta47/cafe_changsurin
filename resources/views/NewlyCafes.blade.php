<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>หน้าแนะนำคาเฟ่</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <style>
    body {
      font-family: "Prompt", sans-serif;
      background: linear-gradient(135deg, #fdfbfb, #ebedee);
      margin: 0;
      padding: 0;
      color: #333;
    }

    /* หัวข้อ */
    .title {
      text-align: center;
      font-size: 2rem;
      font-weight: 600;
      margin-top: 40px;
      margin-bottom: 10px;
      color: #444;
    }
    .title span {
      color: #e63946;
    }

    /* วันเวลาอัปเดต */
    .update-time {
      text-align: center;
      font-size: 0.95rem;
      color: #666;
      margin-bottom: 25px;
    }

    /* Swiper */
    .swiper {
      width: 95%;
      max-width: 900px;
      height: 480px;
      margin: 20px auto 60px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      background: #fff;
    }

    .swiper-slide a {
      display: block;
      width: 100%;
      height: 100%;
    }

    .swiper-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .swiper-slide:hover img {
      transform: scale(1.05);
    }

    /* ปุ่มเลื่อน */
    .swiper-button-next,
    .swiper-button-prev {
      color: #fff;
      background: rgba(0,0,0,0.4);
      border-radius: 50%;
      width: 45px;
      height: 45px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s ease;
    }

    .swiper-button-next:hover,
    .swiper-button-prev:hover {
      background: rgba(0,0,0,0.65);
    }

    /* จุดบอกตำแหน่ง */
    .swiper-pagination-bullet {
      background: #bbb;
      opacity: 1;
      transition: 0.3s;
    }

    .swiper-pagination-bullet-active {
      background: #e63946;
      transform: scale(1.2);
    }
  </style>
</head>
<body>
  {{-- Navbar --}}
  @guest
      @include('components.1navbar')
  @endguest

  @auth
      @include('components.2navbar')
  @endauth

  <h1 class="title">✨ <span>พิกัดคาเฟ่เปิดใหม่</span> ที่ต้องไปลอง</h1>
  <p class="update-time">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>

  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <img src="{{ asset('/images/คาเฟ่เปิดใหม่.png') }}" alt="คาเฟ่เปิดใหม่" >
      </div>
       <div class="swiper-slide">
        <a href="http://127.0.0.1:8000/cafes/5">
          <img src="{{ asset('/images/Top10_/10.png') }}" alt="คาเฟ่อันดับ 10">
        </a>
      </div>
    </div>
    <!-- ปุ่มเลื่อน -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <!-- จุดบอกตำแหน่ง -->
    <div class="swiper-pagination"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    var swiper = new Swiper(".mySwiper", {
      loop: true,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      effect: "slide",
      speed: 600,
    });
  </script>
</body>
</html>
