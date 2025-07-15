<form action="{{ route('user.reviews.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    <input type="hidden" name="cafe_id" value="{{ $cafe->id }}">
</form>  <div class="mb-3">
        <label for="title" class="form-label">หัวข้อรีวิว</label>
        </div>
      <div class="flex justify-end gap-3 mt-4">
        <a href="{{ route('cafes.show', $cafe->id) }}" class="btn btn-secondary btn-sm px-3 py-1 rounded">
          ยกเลิก
        </a>
        <button type="submit" class="btn btn-success btn-sm px-3 py-1 rounded">ส่งรีวิว</button>
      </div>
    </form> ```
เนื่องจากฟิลด์ `title`, `content`, `rating`, `images` และปุ่ม "ส่งรีวิว" อยู่นอกแท็ก `<form>` เมื่อคุณกดปุ่มส่ง ข้อมูลเหล่านั้นจึงไม่ถูกส่งไปด้วยครับ

---

## วิธีแก้ไข

คุณต้องย้ายแท็กปิด `</form>` ไปไว้ท้ายสุดของฟอร์ม หลังจากฟิลด์ข้อมูลและปุ่ม "ส่งรีวิว" ครับ

### โค้ด `review.blade.php` ที่แก้ไขแล้ว

```html
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>เขียนรีวิวสำหรับ {{ $cafe->cafe_name }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-size: 0.85rem;
      background-image: url('https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1470&q=80');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background-attachment: fixed;
    }

    /* เพิ่ม overlay สีขาวโปร่งแสงด้านหลัง container เพื่อให้ข้อความอ่านง่าย */
    main.container {
      max-width: 360px;
      background: rgba(255 255 255 / 0.92);
      padding: 20px 24px 24px 24px;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgb(0 0 0 / 0.1);
      margin: 24px auto;
      flex-grow: 1;
    }

    .form-label {
      margin-bottom: 0.25rem;
      font-weight: 600;
      color: #333;
      display: block;
      font-size: 0.9rem;
    }
    input.form-control,
    textarea.form-control {
      padding: 0.3rem 0.6rem;
      font-size: 0.85rem;
      border: 1.5px solid #ddd;
      border-radius: 6px;
      transition: border-color 0.3s;
      width: 100%;
      box-sizing: border-box;
      font-family: inherit;
    }
    input.form-control:focus,
    textarea.form-control:focus {
      border-color: #66afe9;
      outline: none;
      box-shadow: 0 0 6px rgb(102 175 233 / 0.6);
    }
    .mb-3 {
      margin-bottom: 0.75rem !important;
    }

    /* Rating Star Styles (เล็กลง) */
    .rating {
      direction: ltr;
      unicode-bidi: normal;
      display: flex;
      gap: 6px;
      justify-content: center;
    }
    .rating input[type="radio"] {
      display: none;
    }
    .rating label {
      font-size: 1.6rem; /* ลดขนาดดาว */
      color: #ccc;
      cursor: pointer;
      position: static;
      transition: color 0.35s ease, transform 0.15s ease;
      filter: drop-shadow(0 0 1px rgba(0, 0, 0, 0.12));
      user-select: none;
    }
    .rating label::before {
      content: "★";
    }
    .rating label:hover,
    .rating label:hover ~ label {
      color: #ffcc33;
      filter: drop-shadow(0 0 5px #ffcc33);
      transform: scale(1.15);
      z-index: 2;
    }
    .rating input[type="radio"]:checked ~ label {
      color: #ffb400;
      filter: drop-shadow(0 0 8px #ffb400);
      animation: pulseStar 1.2s ease-in-out forwards;
    }
    @keyframes pulseStar {
      0%, 100% {
        transform: scale(1);
        filter: drop-shadow(0 0 8px #ffb400);
      }
      50% {
        transform: scale(1.3);
        filter: drop-shadow(0 0 12px #ffd94d);
      }
    }

    /* Buttons (เล็กลง) */
    .btn {
      font-size: 0.85rem;
      padding: 0.28rem 0.6rem;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      user-select: none;
      transition: background 0.3s, box-shadow 0.3s;
    }
    .btn-success {
      background: #c49a6c;
      border: none;
      color: white;
      box-shadow: 0 3px 6px rgb(40 167 69 / 0.3);
    }
    .btn-success:hover {
      background: #fac48a;
      box-shadow: 0 5px 10px rgb(33 136 56 / 0.5);
    }
    .btn-secondary {
      background: #6c757d;
      border: none;
      color: white;
      box-shadow: 0 3px 6px rgb(108 117 125 / 0.3);
    }
    .btn-secondary:hover {
      background: #5a6268;
      box-shadow: 0 5px 10px rgb(90 98 104 / 0.5);
    }

    h3.mb-4 {
      font-weight: 700;
      color: #222;
      font-size: 1.2rem;
      margin-bottom: 1.2rem !important;
      text-align: center;
    }

    small.text-muted {
      font-size: 0.7rem;
      color: #666;
    }
  </style>
</head>
<body>

  @include('components.2navbar')

  <main class="container">
    <h3 class="mb-4">เขียนรีวิวสำหรับ: {{ $cafe->cafe_name }}</h3>

    <form action="{{ route('user.reviews.store') }}" method="POST" enctype="multipart/form-data" novalidate>
      @csrf
      <input type="hidden" name="cafe_id" value="{{ $cafe->id }}">

      {{-- ย้ายฟิลด์และปุ่มทั้งหมดเข้ามาใน form ตรงนี้ --}}
      <div class="mb-3">
        <label for="title" class="form-label">หัวข้อรีวิว</label>
        <input
          type="text"
          class="form-control"
          id="title"
          name="title"
          placeholder="เช่น บรรยากาศดี กาแฟอร่อย"
          required
        />
      </div>

      <div class="mb-3">
        <label for="content" class="form-label">รายละเอียดรีวิว</label>
        <textarea
          class="form-control"
          id="content"
          name="content"
          rows="3"
          placeholder="บอกเล่าประสบการณ์ของคุณ..."
          required
        ></textarea>
      </div>

      <div class="mb-3 text-center">
        <label class="form-label block mb-2">ให้คะแนน</label>
        <div class="rating" role="radiogroup" aria-label="ให้คะแนน">
          <input type="radio" name="rating" value="5" id="star5" />
          <label for="star5" title="5 ดาว"></label>

          <input type="radio" name="rating" value="4" id="star4" />
          <label for="star4" title="4 ดาว"></label>

          <input type="radio" name="rating" value="3" id="star3" />
          <label for="star3" title="3 ดาว"></label>

          <input type="radio" name="rating" value="2" id="star2" />
          <label for="star2" title="2 ดาว"></label>

          <input type="radio" name="rating" value="1" id="star1" />
          <label for="star1" title="1 ดาว"></label>
        </div>
      </div>

      <div class="mb-3">
        <label for="images" class="form-label">แนบรูปภาพ (ได้หลายรูป)</label>
        <input
          class="form-control form-control-sm"
          type="file"
          id="images"
          name="images[]"
          accept="image/*"
          multiple
        />
        <small class="text-muted">สามารถเลือกได้สูงสุด 5 รูป</small>
      </div>

      <div class="flex justify-end gap-3 mt-4">
        <a href="{{ route('cafes.show', $cafe->id) }}" class="btn btn-secondary btn-sm px-3 py-1 rounded">
          ยกเลิก
        </a>
        <button type="submit" class="btn btn-success btn-sm px-3 py-1 rounded">ส่งรีวิว</button>
      </div>
    </form> {{-- <<-- ปิด form ตรงนี้ครับ --}}
  </main>

</body>
</html>