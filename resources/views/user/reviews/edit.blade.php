<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
     <title>แก้ไขรีวิวสำหรับ: {{ $review->cafe->cafe_name }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-body-font-family: 'Kanit', sans-serif;
            --bs-body-bg: #f8f9fa;
            --bs-border-radius-lg: 0.75rem;
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        body {
            background-color: var(--bs-body-bg);
        }

        .main-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: var(--bs-border-radius-lg);
            box-shadow: var(--shadow-lg);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .page-title {
            font-weight: 700;
        }

        /* Interactive Star Rating */
        .interactive-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        .interactive-rating input {
            display: none;
        }
        .interactive-rating label {
            font-size: 2.5rem;
            color: #e4e5e9;
            cursor: pointer;
            transition: color 0.2s;
        }
        .interactive-rating label:hover,
        .interactive-rating label:hover ~ label,
        .interactive-rating input:checked ~ label {
            color: #ffc107;
        }

        .image-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid #dee2e6;
        }
    </style>
</head>
<body>

<div class="container my-4">
    <div class="main-container">

        <!-- ส่วนหัวของฟอร์ม -->
        <div class="text-center mb-4">
            <h1 class="page-title">แก้ไขรีวิว</h1>
             <p class="text-muted fs-5">
                สำหรับคาเฟ่: 
                {{-- ใช้ $review->cafe (Object) แทน $review->cafe->id --}}
                <a href="{{ route('cafes.show', $review->cafe) }}" class="text-decoration-none">
                    {{-- ใช้ cafe_name ตามที่คุณมีในฐานข้อมูล --}}
                    {{ $review->cafe->cafe_name }}
                </a>
            </p>
        </div>

      <form action="{{ route('user.reviews.update', $review) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- คอลัมน์ซ้าย: รายละเอียดและคะแนน -->
        <div class="col-lg-7">
            <div class="mb-4">
                <label for="title" class="form-label fw-semibold">หัวข้อรีวิว</label>
                <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ old('title', $review->title) }}" placeholder="เช่น บรรยากาศดี กาแฟอร่อย" required>
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-semibold">รายละเอียดรีวิวของคุณ</label>
                <textarea class="form-control" id="content" name="content" rows="8" placeholder="เล่าประสบการณ์และความประทับใจของคุณที่นี่..." required>{{ old('content', $review->content) }}</textarea>
            </div>

            <div class="card bg-light border-0">
                <div class="card-body text-center">
                    <label class="form-label fw-semibold mb-2">ให้คะแนนความประทับใจ</label>
                    <div class="interactive-rating">
                        <input type="radio" id="star5" name="rating" value="5" {{ old('rating', $review->rating) == 5 ? 'checked' : '' }} /><label for="star5" title="5 ดาว">★</label>
                        <input type="radio" id="star4" name="rating" value="4" {{ old('rating', $review->rating) == 4 ? 'checked' : '' }} /><label for="star4" title="4 ดาว">★</label>
                        <input type="radio" id="star3" name="rating" value="3" {{ old('rating', $review->rating) == 3 ? 'checked' : '' }} /><label for="star3" title="3 ดาว">★</label>
                        <input type="radio" id="star2" name="rating" value="2" {{ old('rating', $review->rating) == 2 ? 'checked' : '' }} /><label for="star2" title="2 ดาว">★</label>
                        <input type="radio" id="star1" name="rating" value="1" {{ old('rating', $review->rating) == 1 ? 'checked' : '' }} /><label for="star1" title="1 ดาว">★</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- คอลัมน์ขวา: รูปภาพ -->
        <div class="col-lg-5">
            @php
                $images = is_string($review->images) ? json_decode($review->images, true) : (is_array($review->images) ? $review->images : []);
            @endphp

            @if(!empty($images))
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach($images as $image)
                        <img src="{{ asset('storage/' . $image) }}" alt="Review Image" class="image-preview">
                    @endforeach
                </div>
            @else
                <div class="text-center p-3 bg-light rounded mb-3">
                    <i class="bi bi-image-alt fs-3 text-muted"></i>
                    <p class="mb-0 text-muted">ไม่มีรูปภาพในรีวิวนี้</p>
                </div>
            @endif

            <div>
                <label for="images" class="form-label fw-semibold">อัปโหลดรูปภาพใหม่ (ถ้าต้องการ)</label>
                <input class="form-control" type="file" id="images" name="images[]" multiple>
                <div class="form-text">การอัปโหลดรูปภาพใหม่จะลบและแทนที่รูปภาพเดิมทั้งหมด</div>
            </div>
        </div>
    </div>

    <!-- ส่วนปุ่มส่งฟอร์ม -->
    <hr class="my-4">
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('user.reviews.my') }}" class="btn btn-secondary">
            <i class="bi bi-x-circle me-1"></i> ยกเลิก
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle-fill me-1"></i> บันทึกการเปลี่ยนแปลง
        </button>
    </div>
</form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>