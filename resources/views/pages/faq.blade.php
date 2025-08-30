<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>หน้าแนะนำคาเฟ่</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <style>
    body {
      margin: 0;
      font-family: "Prompt", sans-serif;
      background: linear-gradient(135deg, #fdfbfb, #ebedee);
      color: #333;
      scroll-behavior: smooth;
    }

    h1.title {
      text-align: center;
      font-size: 2rem;
      font-weight: 600;
      margin: 40px 0 10px;
      color: #444;
    }
    h1.title span { color: #e63946; }

    .update-time {
      text-align: center;
      font-size: 0.95rem;
      color: #666;
      margin-bottom: 25px;
    }

    /* ปุ่มทางลัดแบบแถวด้านบน */
    .faq-shortcuts {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
      max-width: 1000px;
      margin: 0 auto 30px;
    }
    .faq-shortcuts button {
      background: #e63946;
      color: #fff;
      border: none;
      border-radius: 20px;
      padding: 10px 16px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    .faq-shortcuts button:hover {
      background: #d62839;
    }

    /* FAQ */
    .faq-item {
      max-width: 1200px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      display: none;
    }
    .faq-item.active { display: block; }
    .faq-item h3 {
      margin: 0 0 15px;
      color: #e63946;
      font-size: 1.6rem;
    }

    /* Swiper รูปใหญ่ */
    .swiper {
      width: 100%;
      height: 600px;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      margin-top: 15px;
    }
    .swiper-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .swiper-button-next,
    .swiper-button-prev {
      color: #fff;
      text-shadow: 0 2px 6px rgba(0,0,0,0.6);
    }
    .swiper-pagination-bullet {
      background: #fff;
      opacity: 0.8;
    }
    .swiper-pagination-bullet-active {
      background: #e63946;
    }

    @media(max-width:768px){
      .faq-shortcuts {
        gap: 8px;
      }
      .faq-shortcuts button {
        padding: 8px 12px;
        font-size: 12px;
      }
      .swiper { height: 400px; }
      .faq-item h3 { font-size: 1.3rem; }
    }
  </style>
</head>
<body>

  <h1 class="title">✨ <span>❓ คำถามที่พบบ่อย (FAQ)</span></h1>
  <p class="update-time">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>

  <!-- ปุ่มทางลัด -->
  <div class="faq-shortcuts">
    <button onclick="showFAQ('wifi')">💻 Free WiFi</button>
    <button onclick="showFAQ('meeting')">🏢 ห้องประชุม ทำงานได้</button>
    <button onclick="showFAQ('price')">💰 ราคาย่อมเยา</button>
    <button onclick="showFAQ('kids')">🧸 โซนเด็กเล่น</button>
    <button onclick="showFAQ('parking')">🚗 ที่จอดรถ</button>
    <button onclick="showFAQ('style')">🎨 สไตล์มินิมอล</button>
  </div>

  <!-- FAQ -->
  <div id="wifi" class="faq-item">
    <h3>💻 Free WiFi</h3>
    <p>ทุกคาเฟ่ในรายการมีบริการ WiFi ฟรี ความเร็วเพียงพอสำหรับทำงานหรือประชุมออนไลน์</p>

    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <a href="http://127.0.0.1:8000/cafes/1">
            <img src="{{ asset('/images/Top10_/3.png') }}" alt="Follow the sun.home cafe">
          </a>
        </div>
        <div class="swiper-slide">
          <a href="http://127.0.0.1:8000/cafes/2">
            <img src="{{ asset('/images/Top10_/2.png') }}" alt="Little Elephant patisserie & special coffee Bar">
          </a>
        </div>
        <div class="swiper-slide">
          <a href="http://127.0.0.1:8000/cafes/3">
            <img src="{{ asset('/images/Top10_/1.png') }}" alt="ธนสาร แกลเลอรี่">
          </a>
        </div>
        <div class="swiper-slide">
          <a href="http://127.0.0.1:8000/cafes/4">
            <img src="{{ asset('/images/Top10_/9.png') }}" alt="ดัมมะชาติ Eatery & Coffee by Jaokao Vol.3">
          </a>
        </div>
        <div class="swiper-slide">
          <a href="http://127.0.0.1:8000/cafes/5">
            <img src="{{ asset('/images/Top10_/10.png') }}" alt="Journe’y Roastery & Special Coffee">
          </a>
        </div>
      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination"></div>
    </div>
  </div>

  <div id="meeting" class="faq-item">
    <h3>🏢 ห้องประชุม ทำงานได้</h3>
    <p>คาเฟ่บางแห่งมีห้องประชุมหรือโซนสงบ เหมาะสำหรับทำงานและประชุมกลุ่มเล็ก</p>
  </div>

  <div id="price" class="faq-item">
    <h3>💰 ราคาย่อมเยา</h3>
    <p>คาเฟ่มีราคากาแฟและอาหารว่างที่เข้าถึงได้ ไม่แพงจนเกินไป</p>
  </div>

  <div id="kids" class="faq-item">
    <h3>🧸 โซนเด็กเล่น</h3>
    <p>คาเฟ่หลายแห่งมีโซนสำหรับเด็กเล่น ทำให้พ่อแม่สามารถนั่งพักผ่อนได้อย่างสบาย</p>
  </div>

  <div id="parking" class="faq-item">
    <h3>🚗 ที่จอดรถ</h3>
    <p>มีพื้นที่จอดรถสำหรับลูกค้า บางแห่งจอดได้ทั้งรถยนต์และจักรยานยนต์</p>
  </div>

  <div id="style" class="faq-item">
    <h3>🎨 สไตล์มินิมอล</h3>
    <p>คาเฟ่ส่วนใหญ่ตกแต่งสไตล์มินิมอล สวยงามเหมาะสำหรับถ่ายรูปและนั่งชิล</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    function showFAQ(id){
      document.querySelectorAll('.faq-item').forEach(item=>item.classList.remove('active'));
      const target = document.getElementById(id);
      target.classList.add('active');
      target.scrollIntoView({behavior:'smooth', block:'start'});

      if(target.querySelector('.mySwiper')){
        new Swiper(target.querySelector('.mySwiper'), {
          loop:true,
          pagination:{ el: target.querySelector('.swiper-pagination'), clickable:true },
          navigation:{ nextEl: target.querySelector('.swiper-button-next'), prevEl: target.querySelector('.swiper-button-prev') },
        });
      }
    }
  </script>
</body>
</html>
