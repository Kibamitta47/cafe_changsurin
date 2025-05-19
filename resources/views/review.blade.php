<title>เพิ่มข้อมูลคาเฟ่</title>
<style>
.rating {
  direction: rtl;
  display: flex;
  gap: 5px;
}
.rating input {
  display: none;
}
.rating label {
  font-size: 2rem;
  color: #ccc;
  cursor: pointer;
  transition: color 0.2s;
}
.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
  color: #ffc107;
}
</style>
<!-- Bootstrap & Font Awesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="container mt-5" style="max-width: 700px;">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h4 class="text-center mb-4"><i class="fas fa-pen"></i> เขียนรีวิวคาเฟ่</h4>
      <form action="/submit-review" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="title" class="form-label">หัวข้อรีวิว</label>
          <input type="text" class="form-control" id="title" name="title" placeholder="เช่น บรรยากาศดี กาแฟอร่อย" required>
        </div>

        <div class="mb-3">
          <label for="content" class="form-label">รายละเอียดรีวิว</label>
          <textarea class="form-control" id="content" name="content" rows="5" placeholder="บอกเล่าประสบการณ์ของคุณ..." required></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">ให้คะแนน</label>
          <div class="rating">
            <input type="radio" name="rating" value="5" id="star5"><label for="star5"><i class="fas fa-star"></i></label>
            <input type="radio" name="rating" value="4" id="star4"><label for="star4"><i class="fas fa-star"></i></label>
            <input type="radio" name="rating" value="3" id="star3"><label for="star3"><i class="fas fa-star"></i></label>
            <input type="radio" name="rating" value="2" id="star2"><label for="star2"><i class="fas fa-star"></i></label>
            <input type="radio" name="rating" value="1" id="star1"><label for="star1"><i class="fas fa-star"></i></label>
          </div>
        </div>

        <div class="mb-3">
          <label for="images" class="form-label">แนบรูปภาพ (ได้หลายรูป)</label>
          <input class="form-control" type="file" id="images" name="images[]" accept="image/*" multiple>
        </div>

        <div class="d-flex justify-content-end gap-2">
  <a href="home-admin" class="btn btn-secondary">
    <i class="fas fa-times me-1"></i> ยกเลิก
  </a>
  <button type="submit" class="btn btn-success">
    <i class="fas fa-paper-plane me-1"></i> ส่งรีวิว
  </button>
</div>

      </form>
    </div>
  </div>
</div>

