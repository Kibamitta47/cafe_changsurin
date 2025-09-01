{{-- resources/views/partials/cafe-wifi-grid.blade.php (หรือวางตรง ๆ ในหน้า Blade ของคุณได้เลย) --}}
@php
  // ถ้ายังไม่มีฐานข้อมูล ใช้ข้อมูลแบบกำหนดเองก่อน (แก้ชื่อ/รูป/ลิงก์ได้ตามจริง)
  $wifiCafes = [
    ['name' => 'Follow the Sun.Home Cafe’', 'image' => '/images/cafes/follow-sun.jpg', 'url' => '#'],
    ['name' => 'Little Elephant Patisserie & Special Coffee Bar', 'image' => '/images/cafes/little-elephant.jpg', 'url' => '#'],
    ['name' => 'ดัมมะชาติ Eatery & Coffee by Jaokao Vol.3', 'image' => '/images/cafes/dammachat.jpg', 'url' => '#'],
    ['name' => 'Journey Roastery & Special Coffee', 'image' => '/images/cafes/journey.jpg', 'url' => '#'],
    ['name' => 'Craft Cafe Surin', 'image' => '/images/cafes/craft-surin.jpg', 'url' => '#'],
  ];
@endphp

<section class="cafe-wifi">
  <h2 class="cafe-wifi__title">📶 คาเฟ่ที่มี <span>Wi-Fi</span></h2>
  <p class="cafe-wifi__subtitle">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>

  <div class="cafe-wifi__grid">
    @foreach ($wifiCafes as $cafe)
      <article class="cafe-card">
        <div class="cafe-card__media">
          <img
            src="{{ asset($cafe['image']) }}"
            alt="รูปภาพ {{ $cafe['name'] }}"
            onerror="this.src='{{ asset('/images/placeholder-cafe.jpg') }}'; this.onerror=null;"
            loading="lazy"
          />
          <span class="badge badge--wifi" title="มีบริการ Wi-Fi">
            {{-- ไอคอน Wi-Fi (SVG) --}}
            <svg viewBox="0 0 24 24" aria-hidden="true" class="icon">
              <path d="M12 18.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm-3.536-2.536a5 5 0 0 1 7.072 0l1.414-1.414a7 7 0 0 0-9.9 0l1.414 1.414Zm-3.536-3.536a10 10 0 0 1 14.142 0l1.414-1.414a12 12 0 0 0-16.97 0l1.414 1.414Z"></path>
            </svg>
            Wi-Fi
          </span>
        </div>

        <div class="cafe-card__body">
          <h3 class="cafe-card__name">{{ $cafe['name'] }}</h3>

          <div class="cafe-card__meta">
            <span class="chip" title="สิ่งอำนวยความสะดวก">Wi-Fi ฟรี</span>
            <span class="chip chip--soft">ปลั๊กไฟ</span>
            <span class="chip chip--soft">นั่งทำงาน</span>
          </div>

          @if (!empty($cafe['url']) && $cafe['url'] !== '#')
            <a class="btn" href="{{ $cafe['url'] }}">ดูรายละเอียด</a>
          @else
            <button class="btn btn--disabled" disabled>เร็ว ๆ นี้</button>
          @endif
        </div>
      </article>
    @endforeach
  </div>
</section>

<style>
  .cafe-wifi { width: min(1100px, 94%); margin: 32px auto 64px; }
  .cafe-wifi__title { font: 700 1.8rem/1.2 "Prompt", system-ui; color:#333; text-align:center; margin:0 0 6px; }
  .cafe-wifi__title span { color:#e63946; }
  .cafe-wifi__subtitle { text-align:center; color:#667085; margin:0 0 22px; font-size:.95rem; }
  .cafe-wifi__grid {
    display: grid; gap: 18px;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }

  .cafe-card {
    background:#fff; border-radius:16px; overflow:hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.08);
    display:flex; flex-direction:column;
    transition: transform .18s ease, box-shadow .18s ease;
  }
  .cafe-card:hover { transform: translateY(-4px); box-shadow:0 14px 36px rgba(0,0,0,.12); }

  .cafe-card__media { position:relative; aspect-ratio: 16/10; background:#f3f5f7; }
  .cafe-card__media img { width:100%; height:100%; object-fit:cover; display:block; }
  .badge {
    position:absolute; left:12px; top:12px; display:inline-flex; align-items:center; gap:6px;
    font: 600 .85rem/1 "Prompt", system-ui;
    background: #0ea5e9; color:#fff; padding:8px 10px; border-radius:999px;
    box-shadow: 0 6px 18px rgba(14,165,233,.35);
  }
  .badge .icon { width:18px; height:18px; fill:#fff; }
  .badge--wifi { background:#0ea5e9; }

  .cafe-card__body { padding:14px 14px 16px; display:flex; flex-direction:column; gap:10px; }
  .cafe-card__name { margin:0; color:#111827; font: 700 1.05rem/1.35 "Prompt", system-ui; letter-spacing:.1px; }

  .cafe-card__meta { display:flex; flex-wrap:wrap; gap:8px; }
  .chip {
    font: 600 .8rem/1 "Prompt", system-ui; color:#155e75;
    background:#e0f2fe; border:1px solid #bae6fd; padding:6px 8px; border-radius:999px;
  }
  .chip--soft { color:#334155; background:#f1f5f9; border-color:#e2e8f0; }

  .btn {
    margin-top:4px; align-self:flex-start;
    font: 700 .9rem/1 "Prompt", system-ui; letter-spacing:.2px;
    background:#111827; color:#fff; padding:10px 14px; border-radius:10px; text-decoration:none;
    transition: background .15s ease, transform .08s ease;
  }
  .btn:hover { background:#000; transform: translateY(-1px); }
  .btn--disabled { background:#cbd5e1; color:#fff; cursor:not-allowed; }
  @media (max-width: 480px) {
    .cafe-wifi__title { font-size:1.5rem; }
  }
</style>