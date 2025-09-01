<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</title>

  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    :root{
      --primary:#4A90E2;--primary-dark:#357ABD;--light:#f8f9fa;--border:#dee2e6;--text:#343a40;--white:#fff;--radius:.75rem;--shadow:0 4px 6px rgba(0,0,0,.05),0 1px 3px rgba(0,0,0,.08);--shadow-lg:0 10px 15px -3px rgba(0,0,0,.07),0 4px 6px -2px rgba(0,0,0,.05)
    }
    body{font-family:'Sarabun','Inter',sans-serif;background:#f4f7f9;color:var(--text);padding:1rem}
    .form-container{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:1.25rem;max-width:1200px;margin:auto}
    .form-title{color:var(--primary-dark);font-weight:700;text-align:center;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--border);display:flex;align-items:center;justify-content:center;gap:.6rem}
    .form-section{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-bottom:1rem;box-shadow:var(--shadow)}
    h5.section-header{color:var(--primary-dark);font-weight:600;margin:-1rem -1rem 1rem -1rem;padding:.75rem 1rem;border-bottom:1px solid var(--border);background:var(--light);border-top-left-radius:var(--radius);border-top-right-radius:var(--radius);display:flex;align-items:center;gap:.5rem}
    .form-label{font-weight:600;color:#555;margin-bottom:.4rem}
    .form-control,.form-select{border-radius:.5rem;border-color:var(--border);padding:.7rem 1rem}
    .form-control:focus,.form-select:focus{box-shadow:0 0 0 .25rem rgba(74,144,226,.25);border-color:var(--primary)}
    .form-check-group .form-check{padding:.5rem .75rem;border:1px solid var(--border);border-radius:.5rem;margin:.25rem .4rem .25rem 0}
    #map{height:380px;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:.75rem}
    .map-toolbar{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.5rem}
    .btn-primary{background:var(--primary);border-color:var(--primary)}
    .btn-primary:hover{background:var(--primary-dark);border-color:var(--primary-dark)}
    @media (max-width:768px){
      body{padding:.5rem}
      .form-container{padding:.9rem}
      #map{height:300px}
      .actions .btn{flex:1}
    }
  </style>
</head>
<body>

<div class="container">

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong><i class="bi bi-exclamation-triangle-fill"></i> ข้อผิดพลาด!</strong> กรุณาตรวจสอบข้อมูลที่กรอก:
      <ul class="mb-0 ms-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('db_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-server"></i> {{ session('db_error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="form-container">
    <h2 class="form-title"><i class="fas fa-coffee"></i>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</h2>

    <form action="{{ isset($cafe) ? route('user.cafes.update', $cafe->id) : route('user.cafes.store') }}" method="POST" enctype="multipart/form-data" id="cafeForm">
      @csrf
      @if(isset($cafe)) @method('PUT') @endif

      <div class="row g-3">
        <!-- ซ้าย -->
        <div class="col-lg-6">
          <div class="form-section">
            <h5 class="section-header"><i class="bi bi-shop"></i>ข้อมูลพื้นฐาน</h5>

            <div class="mb-3">
              <label for="cafe_name" class="form-label">ชื่อคาเฟ่ <span class="text-danger">*</span></label>
              <input type="text" id="cafe_name" name="cafe_name" class="form-control @error('cafe_name') is-invalid @enderror" placeholder="ระบุชื่อคาเฟ่" required value="{{ old('cafe_name', $cafe->cafe_name ?? '') }}" />
              @error('cafe_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label for="images" class="form-label">รูปภาพคาเฟ่ <span class="text-muted">(สูงสุด 5 รูป • 5MB/รูป • รวม 20MB)</span></label>
              <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
              <div class="form-text">ระบบจะย่อ & บีบอัดรูปให้อัตโนมัติก่อนอัปโหลด</div>
              @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror

              @if(isset($cafe) && is_array($cafe->images) && count($cafe->images))
              <div class="mt-3 p-3 border rounded">
                <div class="d-flex flex-wrap gap-2">
                  @foreach($cafe->images as $img)
                    <img src="{{ asset('storage/'.$img) }}" style="width:100px;height:100px;object-fit:cover;border-radius:.5rem;border:1px solid #eee" alt="Cafe Image">
                  @endforeach
                </div>
                <small class="text-muted d-block mt-2">อัปโหลดใหม่เพื่อแทนที่รูปเดิม</small>
              </div>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label">สถานะ</label>
              <div class="form-check-group d-flex flex-wrap">
                <div class="form-check">
                  <input type="checkbox" id="new_opening" name="is_new_opening" value="1" class="form-check-input" {{ old('is_new_opening', $cafe->is_new_opening ?? false) ? 'checked' : '' }}>
                  <label for="new_opening" class="form-check-label">🌟 เปิดใหม่</label>
                </div>
              </div>
            </div>

            <div class="mb-1">
              <label class="form-label">ช่วงราคา <span class="text-danger">*</span></label>
              <div class="d-flex flex-wrap gap-2">
                @foreach(['ต่ำกว่า 100'=>'primary','101 - 250'=>'success','251 - 500'=>'warning','501 - 1,000'=>'danger','มากกว่า 1,000'=>'dark'] as $label=>$color)
                  @php $id='price'.$loop->index; @endphp
                  <input class="btn-check" type="radio" name="price_range" id="{{ $id }}" value="{{ $label }}" required {{ old('price_range', $cafe->price_range ?? '')==$label?'checked':'' }}>
                  <label for="{{ $id }}" class="btn btn-outline-{{ $color }} btn-sm"><i class="bi bi-tags-fill"></i> {{ $label }}</label>
                @endforeach
              </div>
              @error('price_range')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-section">
            <h5 class="section-header"><i class="bi bi-palette-fill"></i>สไตล์คาเฟ่</h5>
            <div class="mb-3 form-check-group d-flex flex-wrap">
              @foreach(['มินิมอล','วินเทจ','โมเดิร์น','อินดัสเทรียล','ธรรมชาติ/สวน','โคซี่/อบอุ่น','อาร์ต/แกลเลอรี่','ลอฟท์','ญี่ปุ่น','ยุโรป'] as $style)
                @php $id='style_'.\Illuminate\Support\Str::slug($style); @endphp
                <div class="form-check">
                  <input type="checkbox" id="{{ $id }}" name="cafe_styles[]" value="{{ $style }}" class="form-check-input" {{ in_array($style, old('cafe_styles', $cafe->cafe_styles ?? []))?'checked':'' }}>
                  <label for="{{ $id }}" class="form-check-label">{{ $style }}</label>
                </div>
              @endforeach
            </div>

            <div class="mb-0">
              <label for="other_style" class="form-label">ระบุสไตล์อื่นๆ</label>
              <input type="text" id="other_style" name="other_style" class="form-control @error('other_style') is-invalid @enderror" placeholder="เช่น สไตล์ลึกลับ, สไตล์อนาคต" value="{{ old('other_style', $cafe->other_style ?? '') }}">
              @error('other_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-section">
            <h5 class="section-header"><i class="bi bi-person-lines-fill"></i>ข้อมูลติดต่อ</h5>

            <div class="mb-3">
              <label for="phone" class="form-label">เบอร์ติดต่อ</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" pattern="^\d{9,10}$" placeholder="เช่น 0812345678" value="{{ old('phone', $cafe->phone ?? '') }}">
              </div>
              @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">อีเมล์</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@email.com" value="{{ old('email', $cafe->email ?? '') }}">
              </div>
              @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label class="form-label">เว็บไซต์</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-globe"></i></span>
                <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" placeholder="https://www.yourcafe.com" value="{{ old('website', $cafe->website ?? '') }}">
              </div>
              @error('website')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <hr class="my-3">
            <label class="form-label fw-bold">โซเชียลมีเดีย</label>

            <div class="mb-2">
              <div class="input-group">
                <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                <input type="text" name="facebook_page" class="form-control @error('facebook_page') is-invalid @enderror" placeholder="ลิงก์ Facebook Page" value="{{ old('facebook_page', $cafe->facebook_page ?? '') }}">
              </div>
              @error('facebook_page')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="mb-2">
              <div class="input-group">
                <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                <input type="text" name="instagram_page" class="form-control @error('instagram_page') is-invalid @enderror" placeholder="ชื่อผู้ใช้ Instagram" value="{{ old('instagram_page', $cafe->instagram_page ?? '') }}">
              </div>
              @error('instagram_page')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div>
              <div class="input-group">
                <span class="input-group-text"><i class="fab fa-line"></i></span>
                <input type="text" name="line_id" class="form-control @error('line_id') is-invalid @enderror" placeholder="Line ID หรือ @บัญชี" value="{{ old('line_id', $cafe->line_id ?? '') }}">
              </div>
              @error('line_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <!-- ขวา -->
        <div class="col-lg-6">
          <div class="form-section">
            <h5 class="section-header"><i class="bi bi-geo-alt-fill"></i>ที่ตั้งและแผนที่</h5>

            <div class="mb-3">
              <label for="place_name" class="form-label">ชื่อสถานที่ <span class="text-danger">*</span></label>
              <input type="text" id="place_name" name="place_name" class="form-control @error('place_name') is-invalid @enderror" placeholder="ชื่ออาคาร, ชื่อโครงการ" required value="{{ old('place_name', $cafe->place_name ?? '') }}">
              @error('place_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label for="address" class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
              <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="บ้านเลขที่, ถนน, ตำบล, อำเภอ, จังหวัด, รหัสไปรษณีย์" required>{{ old('address', $cafe->address ?? '') }}</textarea>
              @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- แถบเครื่องมือแผนที่ -->
            <div class="map-toolbar">
              <button type="button" id="locateBtn" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-geo-alt-fill"></i> ใกล้ฉัน
              </button>
            </div>

            <div id="map"></div>

            <div id="duplicateCoordsWarning" class="alert alert-warning d-none mt-2" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>พิกัดนี้มีคาเฟ่อื่นใช้งานอยู่แล้ว!
            </div>
            <div id="outOfBoundsWarning" class="alert alert-danger d-none mt-2" role="alert">
              <i class="bi bi-geo-alt-fill me-2"></i>ตำแหน่งที่เลือกอยู่นอกเขตอำเภอเมืองสุรินทร์
            </div>

            <div class="row g-2 mt-2">
              <div class="col-6">
                <label for="lat" class="form-label">ละติจูด</label>
                <input type="text" id="lat" name="lat" class="form-control @error('lat') is-invalid @enderror" placeholder="จากแผนที่" required value="{{ old('lat', $cafe->lat ?? '') }}">
                @error('lat')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-6">
                <label for="lng" class="form-label">ลองจิจูด</label>
                <input type="text" id="lng" name="lng" class="form-control @error('lng') is-invalid @enderror" placeholder="จากแผนที่" required value="{{ old('lng', $cafe->lng ?? '') }}">
                @error('lng')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="d-grid mt-2">
              <button type="button" class="btn btn-outline-secondary" id="resetBtn">
                <i class="bi bi-arrow-counterclockwise"></i> รีเซ็ตตำแหน่ง
              </button>
            </div>
          </div>

          <div class="form-section">
            <h5 class="section-header"><i class="bi bi-clock-history"></i>เวลาทำการ</h5>
            <div class="row g-2">
              <div class="col-md-6">
                <label for="open_day" class="form-label">วันเปิด</label>
                <select class="form-select @error('open_day') is-invalid @enderror" id="open_day" name="open_day">
                  <option value="">-- เลือกวัน --</option>
                  @php $days=['ทุกวัน','จันทร์-ศุกร์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์']; @endphp
                  @foreach($days as $day)
                    <option value="{{ $day }}" {{ (isset($cafe) && $cafe->open_day==$day)||old('open_day')==$day?'selected':'' }}>{{ $day }}</option>
                  @endforeach
                </select>
                @error('open_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="close_day" class="form-label">วันปิด</label>
                <select class="form-select @error('close_day') is-invalid @enderror" id="close_day" name="close_day">
                  <option value="">ไม่มีวันปิด</option>
                  @php $closeDays=['จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์']; @endphp
                  @foreach($closeDays as $day)
                    <option value="{{ $day }}" {{ (isset($cafe) && $cafe->close_day==$day)||old('close_day')==$day?'selected':'' }}>{{ $day }}</option>
                  @endforeach
                </select>
                @error('close_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="row g-2 mt-1">
              <div class="col-md-6">
                <label for="open_time" class="form-label">⏰ เวลาเปิด</label>
                <div class="input-group">
                  <input type="text" class="form-control @error('open_time') is-invalid @enderror" id="open_time" name="open_time" pattern="([01][0-9]|2[0-3]):[0-5][0-9]" placeholder="HH:MM (09:30)" value="{{ old('open_time', isset($cafe) && $cafe->open_time ? Carbon\Carbon::parse($cafe->open_time)->format('H:i') : '') }}">
                  <span class="input-group-text">น.</span>
                </div>
                @error('open_time')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="close_time" class="form-label">⏰ เวลาปิด</label>
                <div class="input-group">
                  <input type="text" class="form-control @error('close_time') is-invalid @enderror" id="close_time" name="close_time" pattern="([01][0-9]|2[0-3]):[0-5][0-9]" placeholder="HH:MM (18:00)" value="{{ old('close_time', isset($cafe) && $cafe->close_time ? Carbon\Carbon::parse($cafe->close_time)->format('H:i') : '') }}">
                  <span class="input-group-text">น.</span>
                </div>
                @error('close_time')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="form-section">
            <h5 class="section-header"><i class="bi bi-stars"></i>บริการและสิ่งอำนวยความสะดวก</h5>

            <label class="form-label fw-bold">วิธีชำระเงิน</label>
            <div class="mb-2 form-check-group d-flex flex-wrap">
              @foreach(['เงินสด','บัตรเครดิต','บัตรเดบิต','จ่ายผ่านมือถือ','ไม่ระบุ'] as $payment)
                @php $id='pay_'.\Illuminate\Support\Str::slug($payment); @endphp
                <div class="form-check">
                  <input type="checkbox" id="{{ $id }}" name="payment_methods[]" value="{{ $payment }}" class="form-check-input" {{ in_array($payment, old('payment_methods', $cafe->payment_methods ?? []))?'checked':'' }}>
                  <label for="{{ $id }}" class="form-check-label">{{ $payment }}</label>
                </div>
              @endforeach
            </div>

            <label class="form-label fw-bold mt-2">สิ่งอำนวยความสะดวก</label>
            <div class="mb-2 form-check-group d-flex flex-wrap">
              @foreach(['ห้องประชุม','โซนเด็กเล่น','ที่จอดรถ','เครื่องปรับอากาศ','Wi-Fi'] as $facility)
                @php $id='facility_'.\Illuminate\Support\Str::slug($facility); @endphp
                <div class="form-check">
                  <input type="checkbox" id="{{ $id }}" name="facilities[]" value="{{ $facility }}" class="form-check-input" {{ in_array($facility, old('facilities', $cafe->facilities ?? []))?'checked':'' }}>
                  <label for="{{ $id }}" class="form-check-label">{{ $facility }}</label>
                </div>
              @endforeach
            </div>

            <label class="form-label fw-bold mt-2">บริการเพิ่มเติม</label>
            <div class="form-check-group d-flex flex-wrap">
              @foreach(['ส่งเดลิเวอรี่','รับจัดงาน','ซื้อกลับบ้าน','รับจองโต๊ะ'] as $service)
                @php $id='service_'.\Illuminate\Support\Str::slug($service); @endphp
                <div class="form-check">
                  <input type="checkbox" id="{{ $id }}" name="other_services[]" value="{{ $service }}" class="form-check-input" {{ in_array($service, old('other_services', $cafe->other_services ?? []))?'checked':'' }}>
                  <label for="{{ $id }}" class="form-check-label">{{ $service }}</label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <div class="actions d-flex justify-content-center gap-3 mt-3 pt-3 border-top">
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary px-4">
          <i class="fas fa-times me-2"></i>ยกเลิก
        </a>
        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
          <i class="fas fa-save me-2"></i>บันทึกข้อมูล
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // ---------- ค่าควบคุมการอัปโหลด ----------
  const MAX_FILES = 5;
  const MAX_PER_FILE = 5 * 1024 * 1024;       // 5MB/ไฟล์ (ตรวจของเดิม)
  const TARGET_PER_FILE = 1.5 * 1024 * 1024;  // บีบอัดให้เล็กกว่าประมาณนี้
  const MAX_TOTAL = 20 * 1024 * 1024;         // รวมทั้งหมด ≤ 20MB หลังบีบอัด
  const MAX_DIM = 1600;                        // ย่อลงให้ด้านยาวสุดไม่เกิน 1600px

  const imageInput = document.getElementById('images');
  const cafeForm = document.getElementById('cafeForm');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // ------------ ตรวจจำนวนไฟล์ทันทีที่เลือก ------------
  imageInput?.addEventListener('change', () => {
    if (imageInput.files.length > MAX_FILES) {
      alert('เลือกได้สูงสุด 5 รูปภาพเท่านั้น');
      imageInput.value = '';
      return;
    }
    for (const f of imageInput.files) {
      if (f.size > 50 * 1024 * 1024) { // กันเผื่อรูปใหญ่มากผิดปกติ
        alert(`ไฟล์ ${f.name} ใหญ่เกิน 50MB ไม่รองรับ`);
        imageInput.value = '';
        return;
      }
    }
  });

  // ------------- ดัก submit เพื่อบีบอัดรูป + ส่งด้วย fetch -------------
  cafeForm.addEventListener('submit', async function(e) {
    // เงื่อนไขอื่น
    if (!duplicateCoordsWarning.classList.contains('d-none') ||
        !outOfBoundsWarning.classList.contains('d-none')) {
      e.preventDefault(); alert('โปรดแก้ไขพิกัดก่อนบันทึก'); return;
    }

    // ถ้าไม่มีไฟล์ ก็ปล่อยให้ submit ปกติ
    if (!imageInput || imageInput.files.length === 0) return;

    e.preventDefault();

    // 1) ตรวจจำนวนไฟล์
    if (imageInput.files.length > MAX_FILES) {
      alert('กรุณาอัปโหลดรูปภาพไม่เกิน 5 รูป');
      return;
    }

    // 2) บีบอัดไฟล์ทั้งหมด
    const files = Array.from(imageInput.files);
    const compressed = [];
    let totalAfter = 0;

    for (const file of files) {
      // ถ้าไฟล์เดิมไม่ใหญ่มาก จะบีบให้สั้น (เพื่อให้แน่ใจหลุด 413)
      const out = await compressImage(file, {maxDim: MAX_DIM, targetBytes: TARGET_PER_FILE});
      compressed.push(out);
      totalAfter += out.blob.size;

      if (out.blob.size > MAX_PER_FILE) {
        alert(`ไฟล์ ${file.name} หลังบีบอัดยังเกิน 5MB กรุณาเลือกไฟล์ที่เล็กกว่านี้`);
        return;
      }
    }

    if (totalAfter > MAX_TOTAL) {
      alert('ขนาดไฟล์รวมหลังบีบอัดเกิน 20MB กรุณาลดจำนวน/ขนาดรูป');
      return;
    }

    // 3) เตรียม FormData: ลบทุกรายการชื่อ images[] เดิม แล้วใส่ไฟล์ที่บีบอัดแทน
    const fd = new FormData(cafeForm);
    fd.delete('images[]');
    compressed.forEach((it, idx) => {
      const safeName = (files[idx].name.replace(/\.[^.]+$/, '') || 'image') + '-compressed.jpg';
      fd.append('images[]', it.blob, safeName);
    });

    // 4) ส่งด้วย fetch (method เป็น POST เสมอ, PUT ใช้ _method)
    const action = cafeForm.getAttribute('action');
    const method = (cafeForm.getAttribute('method') || 'POST').toUpperCase(); // จะเป็น POST อยู่แล้ว
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';

    try {
      const res = await fetch(action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf },
        body: fd,
        redirect: 'follow'
      });
      // ถ้าสำเร็จ Laravel มัก redirect -> ให้ตามไป
      if (res.redirected) {
        window.location.href = res.url;
        return;
      }
      if (res.ok) {
        // อาจเป็น JSON หรือ HTML; พยายามกลับไป dashboard
        try {
          const data = await res.json();
          if (data?.redirect) { window.location.href = data.redirect; return; }
        } catch {}
        window.location.reload();
      } else {
        const text = await res.text();
        console.error('Upload failed:', res.status, text);
        alert('บันทึกไม่สำเร็จ กรุณาลองใหม่หรือเลือกรูปที่เล็กลง');
      }
    } catch (err) {
      console.error(err);
      alert('มีข้อผิดพลาดระหว่างอัปโหลด');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>บันทึกข้อมูล';
    }
  });

  // ---------- ฟังก์ชันบีบอัดรูป ----------
  async function compressImage(file, {maxDim=1600, targetBytes=1.5*1024*1024} = {}) {
    const bitmap = await readImageBitmap(file);
    const {width, height} = fitContain(bitmap.width, bitmap.height, maxDim);

    // วาดลง canvas
    const canvas = document.createElement('canvas');
    canvas.width = width; canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(bitmap, 0, 0, width, height);

    // ลองลดคุณภาพหลายสเต็ปจนได้ขนาดที่ต้องการ
    let quality = 0.9;
    let blob = await canvasToBlob(canvas, 'image/jpeg', quality);
    const steps = [0.85, 0.8, 0.75, 0.7, 0.65, 0.6];
    for (const q of steps) {
      if (blob.size <= targetBytes) break;
      quality = q;
      blob = await canvasToBlob(canvas, 'image/jpeg', quality);
    }
    return { blob };
  }

  function fitContain(w, h, max) {
    if (w <= max && h <= max) return {width:w, height:h};
    const r = Math.min(max / w, max / h);
    return {width: Math.round(w*r), height: Math.round(h*r)};
  }

  function canvasToBlob(canvas, type, quality){
    return new Promise(resolve => canvas.toBlob(b => resolve(b), type, quality));
  }

  async function readImageBitmap(file){
    if ('createImageBitmap' in window) {
      return await createImageBitmap(file);
    }
    // fallback
    const dataUrl = await fileToDataURL(file);
    const img = new Image();
    img.decoding = 'async';
    img.src = dataUrl;
    await img.decode();
    // วาดใส่ canvas เพื่อได้ ImageBitmap-like
    const c = document.createElement('canvas');
    c.width = img.naturalWidth; c.height = img.naturalHeight;
    c.getContext('2d').drawImage(img, 0, 0);
    return { width: c.width, height: c.height, drawImage: (ctx, ...args)=>ctx.drawImage(img, ...args) };
  }

  function fileToDataURL(file){
    return new Promise((resolve,reject)=>{
      const fr = new FileReader();
      fr.onload = ()=> resolve(fr.result);
      fr.onerror = reject;
      fr.readAsDataURL(file);
    });
  }

  // ========== แผนที่ ==========
  const latInput = document.getElementById('lat');
  const lngInput = document.getElementById('lng');
  const duplicateCoordsWarning = document.getElementById('duplicateCoordsWarning');
  const outOfBoundsWarning = document.getElementById('outOfBoundsWarning');
  const submitBtn = document.getElementById('submitBtn');
  const locateBtn = document.getElementById('locateBtn');

  const bounds = L.latLngBounds([[14.75,103.35],[15.00,103.65]]);
  const center = [14.885,103.490];

  const map = L.map('map',{scrollWheelZoom:true,tap:true}).setView(center,12);
  map.setMaxBounds(bounds);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
    maxZoom:19,attribution:'© OpenStreetMap contributors'
  }).addTo(map);

  const geocoder = L.Control.geocoder({
    geocoder: L.Control.Geocoder.nominatim(),
    defaultMarkGeocode: false,
    placeholder: 'ค้นหาสถานที่หรือที่อยู่…',
    errorMessage: 'ไม่พบสถานที่'
  })
  .on('markgeocode', function(e){
    const c = e.geocode.center;
    applyPoint(c.lat, c.lng, true);
  })
  .addTo(map);

  let marker;

  if (latInput.value && lngInput.value) {
    const lat = parseFloat(latInput.value), lng = parseFloat(lngInput.value);
    if (!isNaN(lat) && !isNaN(lng)) {
      marker = L.marker([lat,lng]).addTo(map);
      map.setView([lat,lng], 15);
      if (!bounds.contains([lat,lng])) displayOutOfBounds(true); else checkCoordinates(lat,lng);
    }
  }

  map.on('click', (e)=> applyPoint(e.latlng.lat, e.latlng.lng, true));

  locateBtn.addEventListener('click', ()=>{
    if (!navigator.geolocation){ alert('อุปกรณ์ไม่รองรับการระบุตำแหน่ง'); return; }
    navigator.geolocation.getCurrentPosition(
      (pos)=>{
        const {latitude, longitude} = pos.coords;
        if (!bounds.contains([latitude, longitude])) {
          alert('ตำแหน่งปัจจุบันอยู่นอกเขตอำเภอเมืองสุรินทร์');
          map.setView(center, 13);
          return;
        }
        applyPoint(latitude, longitude, true);
      },
      (err)=>{ console.error(err); alert('ไม่สามารถดึงตำแหน่งปัจจุบันได้'); },
      {enableHighAccuracy:true, timeout:8000}
    );
  });

  document.getElementById('resetBtn').addEventListener('click', function() {
    if (marker){ map.removeLayer(marker); marker=null; }
    latInput.value=''; lngInput.value='';
    displayOutOfBounds(false);
    duplicateCoordsWarning.classList.add('d-none');
    submitBtn.disabled=false;
    map.setView(center,12);
  });

  latInput.addEventListener('input', handleManual);
  lngInput.addEventListener('input', handleManual);

  function handleManual(){
    const lat=parseFloat(latInput.value), lng=parseFloat(lngInput.value);
    if (isNaN(lat)||isNaN(lng)){ submitBtn.disabled=true; return; }
    applyPoint(lat,lng,true);
  }

  function applyPoint(lat,lng,move){
    if (!bounds.contains([lat,lng])){
      displayOutOfBounds(true);
      placeMarker(lat,lng);
      latInput.value=lat.toFixed(6); lngInput.value=lng.toFixed(6);
      submitBtn.disabled=true;
      if (move) map.setView([lat,lng],15);
      return;
    }
    displayOutOfBounds(false);
    placeMarker(lat,lng);
    latInput.value=lat.toFixed(6); lngInput.value=lng.toFixed(6);
    if (move) map.setView([lat,lng],16);
    checkCoordinates(lat,lng);
  }

  function placeMarker(lat,lng){
    if (marker) marker.setLatLng([lat,lng]); else marker=L.marker([lat,lng]).addTo(map);
  }

  function displayOutOfBounds(show){ outOfBoundsWarning.classList.toggle('d-none', !show); }

  async function checkCoordinates(lat,lng){
    if (!lat || !lng){ duplicateCoordsWarning.classList.add('d-none'); submitBtn.disabled=false; return; }
    const cafeId = "{{ $cafe->id ?? 'null' }}";
    try{
      const res = await fetch("{{ route('admin.cafe.check_coordinates') }}",{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({lat,lng,cafe_id:cafeId})
      });
      const data = await res.json();
      const isDup = (data.exists ?? data.is_duplicate ?? false);
      duplicateCoordsWarning.classList.toggle('d-none', !isDup);
      submitBtn.disabled = isDup || !bounds.contains([lat,lng]);
    }catch(err){
      console.error('check_coordinates error:', err);
      submitBtn.disabled=false;
    }
  }

  setTimeout(()=> map.invalidateSize(), 300);
  window.addEventListener('resize', ()=> map.invalidateSize());
});
</script>
</body>
</html>
