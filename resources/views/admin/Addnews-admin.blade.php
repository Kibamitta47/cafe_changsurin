@include('components.adminmenu')

<div class="container py-4">

  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- ปุ่มข่าวสาร ติดขวาบน -->
  <button id="btnNews" class="btn btn-primary position-fixed" 
    style="top: 20px; right: 20px; z-index: 1050;">
    <i class="fas fa-bullhorn me-2"></i>ข่าวสารที่เพิ่มแล้ว
  </button>

  {{-- ฟอร์มเพิ่มข่าวสาร --}}
  <section class="mb-5">
    <div class="card shadow-sm rounded-4 border-0 mx-auto" style="max-width: 600px;">
      <div class="card-body p-4">
        <h3 class="text-primary mb-4 fw-bold text-center">
          <i class="fas fa-bullhorn me-2"></i>เพิ่มข่าวสารหรือโปรโมชัน
        </h3>
        <form action="/submit-promotion" method="POST" enctype="multipart/form-data" novalidate>
          @csrf
          <div class="mb-3">
            <label for="title" class="form-label fw-semibold">หัวข้อ</label>
            <input type="text" id="title" name="title" class="form-control form-control-lg shadow-sm" placeholder="กรอกหัวข้อข่าวสาร..." required>
          </div>

          <div class="mb-3">
            <label for="content" class="form-label fw-semibold">รายละเอียด</label>
            <textarea id="content" name="content" rows="4" class="form-control form-control-lg shadow-sm" placeholder="กรอกรายละเอียดข่าวสารหรือโปรโมชัน..." required></textarea>
          </div>

          <div class="mb-4">
            <label for="images" class="form-label fw-semibold">แนบรูปภาพ</label>
            <input type="file" id="images" name="images[]" class="form-control" multiple accept="image/*">
          </div>

          <div class="d-flex justify-content-center gap-3">
            <a href="/home-admin" class="btn btn-outline-secondary btn-lg px-4">ยกเลิก</a>
            <button type="submit" class="btn btn-primary btn-lg px-4">บันทึก</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  {{-- ตารางข่าวสาร --}}
<section id="newsSection">
  <div class="card shadow-sm rounded-4 border-0 bg-white">
    <div class="card-body p-4">
      <h4 class="mb-4 text-primary fw-bold">
        <i class="fas fa-list me-2"></i>ข่าวสารที่เพิ่มแล้ว
      </h4>

        @if($news->isEmpty())
          <p class="text-muted text-center fs-5">ยังไม่มีข่าวสาร</p>
        @else
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light text-center">
                <tr>
                  <th style="width: 18%;">หัวข้อ</th>
                  <th style="width: 38%;">รายละเอียด</th>
                  <th style="width: 20%;">รูปภาพ</th>
                  <th style="width: 14%;">วันที่เพิ่ม</th>
                  <th style="width: 10%;">จัดการ</th>
                </tr>
              </thead>
              <tbody>
                @foreach($news as $item)
                <tr>
                  <td class="fw-semibold">{{ $item->title }}</td>
                  <td>{{ Str::limit($item->content, 100) }}</td>
                  <td>
                    @if($item->images)
                      @foreach(json_decode($item->images) as $img)
                        <img src="{{ asset('storage/' . $img) }}" alt="news image" class="rounded shadow-sm me-1 mb-1" style="max-height: 60px; object-fit: cover;">
                      @endforeach
                    @endif
                  </td>
                  <td class="text-center text-secondary">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                  <td class="text-center">
                    <a href="{{ route('news.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1" title="แก้ไข">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('news.delete', $item->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบ?')" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" title="ลบ">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </section>

</div>

<script>
  document.getElementById('btnNews').addEventListener('click', function() {
    const newsSection = document.getElementById('newsSection');
    if (newsSection) {
      newsSection.scrollIntoView({ behavior: 'smooth' });
    }
  });
</script>

<style>
  /* ฟอนต์หลัก */
  body {
    font-family: 'Sarabun', sans-serif;
    background-color: #f7f9fc;
  }

  /* ฟอร์ม */
  .form-label {
    color: #334155;
  }

  /* ปุ่มบันทึก */
  .btn-primary {
    background-color: #1877f2;
    border-color: #1877f2;
    transition: background-color 0.3s ease;
  }
  .btn-primary:hover {
    background-color: #155db2;
    border-color: #155db2;
  }

  /* ปุ่มยกเลิก */
  .btn-outline-secondary {
    color: #6b7280;
    border-color: #d1d5db;
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  .btn-outline-secondary:hover {
    background-color: #e2e8f0;
    color: #374151;
  }

  /* ตารางหัวข้อ */
  .table thead th {
    border-bottom: 2px solid #d1d5db;
    font-weight: 600;
    color: #1e293b;
  }

  /* ตารางแถว hover */
  .table tbody tr:hover {
    background-color: #f0f4f8;
  }

  /* รูปภาพในตาราง */
  img.rounded {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
  }

  .card-body {
    padding: 24px !important; /* ลด padding ลงนิดหน่อยให้พอดี */
  }
</style>
