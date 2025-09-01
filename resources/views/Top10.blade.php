<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>หน้าแนะนำคาเฟ่</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <style>
    body { font-family: "Prompt", sans-serif; background: linear-gradient(135deg, #fdfbfb, #ebedee); margin: 0; padding: 0; color: #333; }
    .title { text-align: center; font-size: 2rem; font-weight: 600; margin-top: 40px; margin-bottom: 10px; color: #444; }
    .title span { color: #e63946; }
    .update-time { text-align: center; font-size: 0.95rem; color: #666; margin-bottom: 25px; }
    .swiper { width: 95%; max-width: 900px; height: 480px; margin: 20px auto 60px; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.15); background: #fff; }
    .swiper-slide a { display: block; width: 100%; height: 100%; }
    .swiper-slide img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
    .swiper-slide:hover img { transform: scale(1.05); }
    .swiper-button-next, .swiper-button-prev { color: #fff; background: rgba(0,0,0,0.4); border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; transition: background 0.3s ease; }
    .swiper-button-next:hover, .swiper-button-prev:hover { background: rgba(0,0,0,0.65); }
    .swiper-pagination-bullet { background: #bbb; opacity: 1; transition: 0.3s; }
    .swiper-pagination-bullet-active { background: #e63946; transform: scale(1.2); }
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

  <h1 class="title">✨ <span>10 คาเฟ่แนะนำ</span> ที่ต้องไปลอง</h1>
  <p class="update-time">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>

  @php
    // กำหนดสไลด์แบบเดียวจบ ดูแลง่าย/ลดความซ้ำซ้อน
    $slides = [
      // [id ในระบบ, path รูป, alt]
      [2,  'Top10_/1.png',  'Little Elephant patisserie & special coffee bar'],
      [2,  'Top10_/2.png',  'Follow the Sun Home Cafe'],
      [1,  'Top10_/3.png',  'Follow the Sun Home Cafe'],
      [11, 'Top10_/4.png',  'Healing Cafe'],
      [14, 'Top10_/5.png',  'Kind Cafe'],
      [9,  'Top10_/6.png',  'CHAROENSUK Cafe — เจริญสุข คาเฟ่'],
      [15, 'Top10_/7.png',  'For Foo Cafe'],
      [12, 'Top10_/8.png',  'สติ คาเฟ่ — SATI Cafe'],
      [4,  'Top10_/9.png',  'ดัมมะชาติ Eatery & Coffee by Jaokao Vol.3'],
      [5,  'Top10_/10.png', 'Journey Roastery & Special Coffee'],
    ];
  @endphp

  <div class="swiper mySwiper" aria-label="สไลด์คาเฟ่แนะนำ">
    <div class="swiper-wrapper">
      {{-- สไลด์ปก Top 10 --}}
      <div class="swiper-slide" aria-roledescription="slide">
        <img src="{{ asset('/images/TOP-10.png') }}" alt="10 อันดับคาเฟ่แนะนำ" />
      </div>

      {{-- วนลูปสร้างสไลด์ที่เหลือ --}}
      @foreach ($slides as [$id, $img, $alt])
        <div class="swiper-slide" aria-roledescription="slide">
          <a href="{{ url('/cafes/'.$id) }}">
            <img src="{{ asset('/images/'.$img) }}" alt="{{ $alt }}" />
          </a>
        </div>
      @endforeach
    </div>

    {{-- ปุ่มเลื่อน --}}
    <div class="swiper-button-next" aria-label="ถัดไป"></div>
    <div class="swiper-button-prev" aria-label="ก่อนหน้า"></div>
    {{-- จุดบอกตำแหน่ง --}}
    <div class="swiper-pagination" aria-hidden="true"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    const swiper = new Swiper(".mySwiper", {
      loop: true,
      autoplay: { delay: 3000, disableOnInteraction: false },
      pagination: { el: ".swiper-pagination", clickable: true },
      navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
      effect: "slide",
      speed: 600,
    });
  </script>
</body>
</html>
