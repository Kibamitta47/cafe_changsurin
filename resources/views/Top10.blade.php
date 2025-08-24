<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>หน้าแนะนำคาเฟ่</title>
  <link rel="stylesheet" href="cafe.css"> <!-- CSS ที่ส่งให้ไป -->
</head>
<body>

  <!-- Header -->
  <header class="cafe-header">
    <div class="cafe-header__inner">
      <div class="cafe-logo"><a href="https://nongchangsaren.ddns.net/">น้องช้างสะเร็น</a></div>
      <span class="cafe-badge">TOP-10 คาเฟ่ในอำเภอเมืองสุรินทร์</span>
    </div>
  </header>

  <main class="cafe-container">

    <!-- Hero -->
    <section class="cafe-hero">
      <div class="cafe-hero__card">
        <div class="cafe-hero__media">
          <img src="{{ asset('/images/TOP-10.png') }}" alt="10 อันดับคาเฟ่แนะนำ">
        </div>
        <div class="cafe-hero__body">
          <h2 class="cafe-hero__title">Elephant Café</h2>
          <p class="cafe-hero__desc">คาเฟ่บรรยากาศอบอุ่นในสุรินทร์ มีเมล็ดกาแฟคั่วพิเศษ และเครื่องดื่มซิกเนเจอร์</p>
          <a href="https://maps.google.com" class="cafe-hero__chip">📍 ดูแผนที่</a>
        </div>
      </div>
    </section>

    <!-- Grid -->
    <section class="cafe-grid">
      <!-- Card 1 -->
      <div class="cafe-card">
        <div class="cafe-card__media">
          <img src="https://picsum.photos/400/250?coffee1" alt="Cafe A">
          <span class="cafe-card__tag">ใหม่</span>
        </div>
        <div class="cafe-card__body">
          <h3 class="cafe-card__title">Coffee Time</h3>
          <div class="cafe-card__meta">
            <i>☕ กาแฟพิเศษ</i>
            <i>📍 เมืองสุรินทร์</i>
          </div>
          <p class="text-muted">มีทั้งเครื่องดื่มและขนมโฮมเมด อบสดใหม่ทุกวัน</p>
          <div class="cafe-actions">
            <button class="cafe-like">❤️ ถูกใจ</button>
            <a href="https://example.com" class="cafe-view">🔗 เยี่ยมชม</a>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="cafe-card">
        <div class="cafe-card__media">
          <img src="https://picsum.photos/400/250?coffee2" alt="Cafe B">
          <span class="cafe-card__tag">ฮิต</span>
        </div>
        <div class="cafe-card__body">
          <h3 class="cafe-card__title">Morning Brew</h3>
          <div class="cafe-card__meta">
            <i>🥐 ครัวซองต์</i>
            <i>📍 ใกล้ตลาด</i>
          </div>
          <p class="text-muted">บรรยากาศสบายๆ เหมาะกับการนั่งทำงาน</p>
          <div class="cafe-actions">
            <button class="cafe-like">❤️ ถูกใจ</button>
            <a href="https://example.com" class="cafe-view">🔗 เยี่ยมชม</a>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <footer class="cafe-footer">
    © 2025 Surin Café Guide — <a href="#">ติดต่อเรา</a>
  </footer>

</body>
</html>
