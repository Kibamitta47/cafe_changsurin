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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- ใช้ Kanit ให้ตรงงานของคุณ (ภาพแนบฟีลหัวหนาดูไทย) -->
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
  /* ===== สี/ธีมแบบตายตัว เพื่อเลียนแบบภาพให้เป๊ะ ===== */
  :root{
    --brand:#2E6BE5;            /* ฟ้าชิปหัวข้อ (สดกว่าเดิม) */
    --brand-ink:#1E335A;        /* สีตัวอักษรหัวข้อ */
    --ink:#2b3445;              /* ตัวอักษรหลัก */
    --muted:#6b7b97;            /* อักษรรอง */
    --line:#e7edf6;             /* เส้นคั่น */
    --bg:#f7f9ff;               /* พื้นหลัง */
    --radius:16px;
    --shadow:0 10px 24px rgba(24, 64, 146, .06);
    --focus:0 0 0 .2rem rgba(46,107,229,.22);
  }

  /* รีเซ็ต margin/line-height ของหัวข้อจาก Bootstrap เพื่อคุมเองทั้งหมด */
  h1,h2,h3,h4,h5,h6{margin:0; line-height:1}

  body{
    font-family:'Kanit',system-ui,Segoe UI,Roboto,Arial,sans-serif;
    background:var(--bg); color:var(--ink);
    padding:24px 0;
  }

  .container{
    background:#fff; border:1px solid var(--line); border-radius:var(--radius);
    box-shadow:var(--shadow); padding:34px 28px; max-width:1200px;
  }

  /* ===== หัวเรื่องใหญ่ (ไอคอน + ขีดคั่น) ===== */
  h2{
    display:flex; align-items:center; gap:10px;
    color:#20407A; font-weight:800; font-size:24px;
    border-bottom:2px solid var(--line); padding-bottom:14px; margin-bottom:22px;
  }
  h2 i{
    width:42px; height:42px; border-radius:12px; display:grid; place-items:center;
    background:#EDF3FF; color:var(--brand); font-size:18px;
  }

  /* ===== คอมโพเนนต์หัวข้อ “ตามภาพแนบ” แบบเป๊ะ =====
     - สี่เหลี่ยมฟ้า 12x12 มุม 2px
     - ระยะห่าง chip ↔ ข้อความ 8px
     - ข้อความ 16px หนัก 800, สีเข้มฟ้า-เทา
     - line-height 1.25 (ชิดสวยสำหรับ baseline form-control)
     - ระยะบน 14px / ล่าง 8px
  */
  .head-label{
    display:flex; align-items:center;
    gap:8px;
    margin:14px 0 8px;
    font-size:16px; line-height:1.25;
    color:var(--brand-ink);
    font-weight:800; letter-spacing:.1px;
  }
  .head-label .chip{
    width:12px; height:12px; border-radius:2px;
    background:var(--brand);
    /* เงาบาง ๆ ให้ดูนูนแบบภาพ */
    box-shadow:0 .5px 0 rgba(0,0,0,.06);
    flex:0 0 12px;
  }
  /* บล็อกเนื้อหาถัดจากหัวข้อ มีเส้นคั่นบาง ๆ ข้างบนเหมือนภาพ */
  .section-block{
    padding:10px 0 2px;
    border-top:1px solid var(--line);
  }

  /* ป้ายกำกับ + อินพุต */
  .form-label{font-weight:600; color:#5a6a88; margin-bottom:6px}
  .form-control,.form-select,.input-group-text{
    border-radius:12px; border-color:#e9eef6; padding:.72rem .95rem;
  }
  .form-control:focus,.form-select:focus{border-color:var(--brand); box-shadow:var(--focus)}
  .input-group-text{background:#f7faff; color:#6e7fa0}

  /* ปุ่มช่วงราคา (แคปซูล) */
  .btn-check + .btn{border-radius:999px; padding:.46rem .85rem; font-weight:700}
  .btn-check:checked + .btn{outline:2px solid var(--brand); outline-offset:1px}

  .btn-outline-primary{background:#ecf3ff; color:#1d5ac2; border-color:#d6e6ff}
  .btn-outline-success{background:#eafaf2; color:#0f7b5c; border-color:#cfeee0}
  .btn-outline-warning{background:#fff6e6; color:#9a6400; border-color:#ffe2b8}
  .btn-outline-danger{background:#ffedf3; color:#b21e5f; border-color:#ffd5e4}
  .btn-outline-dark{background:#eef2f7; color:#2f3b4a; border-color:#e2e8f0}

  /* checkbox เป็นชิป */
  .form-check-input{margin-top:0}
  .form-check-input:checked{background-color:var(--brand); border-color:var(--brand)}
  .form-check-label{font-weight:700; color:#41536e}

  /* แผนที่ */
  #map{height:400px; margin-bottom:12px; border-radius:14px; border:1px solid #e9eef6}
  .leaflet-container{background:#eaf1ff; border-radius:14px}
  #locateBtn,#resetBtn{border-radius:999px}

  /* ปุ่มหลัก */
  .btn-primary{background:var(--brand); border-color:var(--brand); font-weight:700; padding:10px 24px; border-radius:10px}
  .btn-primary:hover{transform:translateY(-1px); background:#2458C7; border-color:#2458C7}
  .btn-outline-secondary{border-radius:10px}

  /* responsive */
  @media (max-width:768px){
    .container{padding:22px}
    h2{font-size:20px}
    #map{height:300px}
  }
  </style>
</head>
<body>

<div class="container">

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px">
      <strong><i class="bi bi-exclamation-triangle-fill"></i> ข้อผิดพลาด!</strong> กรุณาตรวจสอบข้อมูลที่กรอก:
      <ul class="mb-0 ms-2">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('db_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px">
      <i class="bi bi-server"></i> {{ session('db_error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <h2><i class="fas fa-coffee"></i>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</h2>

  <form action="{{ isset($cafe) ? route('user.cafes.update', $cafe->id) : route('user.cafes.store') }}" method="POST" enctype="multipart/form-data" id="cafeForm">
    @csrf
    @if(isset($cafe)) @method('PUT') @endif

    <div class="row g-3">
      <!-- ซ้าย -->
      <div class="col-lg-6">
        <div class="mb-3">
          <div class="head-label"><span class="chip"></span><span>ข้อมูลพื้นฐาน</span></div>
          <div class="section-block">
            <label for="cafe_name" class="form-label">ชื่อคาเฟ่ <span class="text-danger">*</span></label>
            <input type="text" id="cafe_name" name="cafe_name" class="form-control @error('cafe_name') is-invalid @enderror" placeholder="ระบุชื่อคาเฟ่" required value="{{ old('cafe_name', $cafe->cafe_name ?? '') }}" />
            @error('cafe_name')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <div class="mt-3">
              <label for="images" class="form-label">รูปภาพคาเฟ่ <span class="text-muted">(สูงสุด 5 รูป • 5MB/รูป • รวม 20MB)</span></label>
              <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
              <div class="form-text">ระบบจะย่อ & บีบอัดรูปให้อัตโนมัติก่อนอัปโหลด</div>
              @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror

              @if(isset($cafe) && is_array($cafe->images) && count($cafe->images))
                <div class="mt-3 p-3 border rounded" style="border-radius:12px">
                  <div class="d-flex flex-wrap gap-2">
                    @foreach($cafe->images as $img)
                      <img src="{{ asset('storage/'.$img) }}" style="width:100px;height:100px;object-fit:cover;border-radius:12px;border:1px solid #eef3fb" alt="Cafe Image">
                    @endforeach
                  </div>
                  <small class="text-muted d-block mt-2">อัปโหลดใหม่เพื่อแทนที่รูปเดิม</small>
                </div>
              @endif
            </div>

            <div class="mt-3">
              <label class="form-label">สถานะ</label>
              <div class="form-check">
                <input type="checkbox" id="new_opening" name="is_new_opening" value="1" class="form-check-input" {{ old('is_new_opening', $cafe->is_new_opening ?? false) ? 'checked' : '' }}>
                <label for="new_opening" class="form-check-label">🌟 เปิดใหม่</label>
              </div>
            </div>

            <div class="mt-3">
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
        </div>

        <div class="mb-3">
          <div class="head-label"><span class="chip"></span><span>สไตล์คาเฟ่</span></div>
          <div class="section-block">
            <div class="d-flex flex-wrap">
              @foreach(['มินิมอล','วินเทจ','โมเดิร์น','อินดัสเทรียล','ธรรมชาติ/สวน','โคซี่/อบอุ่น','อาร์ต/แกลเลอรี่','ลอฟท์','ญี่ปุ่น','ยุโรป'] as $style)
                @php $id='style_'.\Illuminate\Support\Str::slug($style); @endphp
                <div class="form-check me-3 mb-2">
                  <input type="checkbox" id="{{ $id }}" name="cafe_styles[]" value="{{ $style }}" class="form-check-input" {{ in_array($style, old('cafe_styles', $cafe->cafe_styles ?? []))?'checked':'' }}>
                  <label for="{{ $id }}" class="form-check-label">{{ $style }}</label>
                </div>
              @endforeach
            </div>

            <label for="other_style" class="form-label mt-1">ระบุสไตล์อื่นๆ</label>
            <input type="text" id="other_style" name="other_style" class="form-control @error('other_style') is-invalid @enderror" placeholder="เช่น สไตล์ลึกลับ, สไตล์อนาคต" value="{{ old('other_style', $cafe->other_style ?? '') }}">
            @error('other_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mb-3">
          <div class="head-label"><span class="chip"></span><span>ข้อมูลติดต่อ</span></div>
          <div class="section-block">
            <label for="phone" class="form-label">เบอร์ติดต่อ</label>
            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
              <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" pattern="^\d{9,10}$" placeholder="เช่น 0812345678" value="{{ old('phone', $cafe->phone ?? '') }}">
            </div>
            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

            <label for="email" class="form-label">อีเมล์</label>
            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
              <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@email.com" value="{{ old('email', $cafe->email ?? '') }}">
            </div>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

            <label class="form-label">เว็บไซต์</label>
            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-globe"></i></span>
              <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" placeholder="https://www.yourcafe.com" value="{{ old('website', $cafe->website ?? '') }}">
            </div>
            @error('website')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

            <!-- Social Media: หัวข้อแบบเดียวกับภาพแนบ -->
            <div class="head-label" style="margin-top:16px"><span class="chip"></span><span>Social Media</span></div>
            <div class="section-block">
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
        </div>
      </div>

      <!-- ขวา -->
      <div class="col-lg-6">
        <div class="mb-3">
          <div class="head-label"><span class="chip"></span><span>ที่ตั้งและแผนที่</span></div>
          <div class="section-block">
            <label for="place_name" class="form-label">ชื่อสถานที่ <span class="text-danger">*</span></label>
            <input type="text" id="place_name" name="place_name" class="form-control @error('place_name') is-invalid @enderror" placeholder="ชื่ออาคาร, ชื่อโครงการ" required value="{{ old('place_name', $cafe->place_name ?? '') }}">
            @error('place_name')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <label for="address" class="form-label mt-3">ที่อยู่ <span class="text-danger">*</span></label>
            <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="บ้านเลขที่, ถนน, ตำบล, อำเภอ, จังหวัด, รหัสไปรษณีย์" required>{{ old('address', $cafe->address ?? '') }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <div class="mt-2">
              <button type="button" id="locateBtn" class="btn btn-sm btn-outline-secondary"><i class="bi bi-geo-alt-fill"></i> ใกล้ฉัน</button>
            </div>

            <div id="map"></div>

            <div id="duplicateCoordsWarning" class="alert alert-warning d-none mt-2" role="alert" style="border-radius:12px">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>พิกัดนี้มีคาเฟ่อื่นใช้งานอยู่แล้ว!
            </div>
            <div id="outOfBoundsWarning" class="alert alert-danger d-none mt-2" role="alert" style="border-radius:12px">
              <i class="bi bi-geo-alt-fill me-2"></i>ตำแหน่งที่เลือกอยู่นอกเขตอำเภอเมืองสุรินทร์
            </div>

            <div class="row g-2 mt-1">
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
        </div>

        <div class="mb-3">
          <div class="head-label"><span class="chip"></span><span>เวลาทำการ</span></div>
          <div class="section-block">
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
        </div>

        <div class="mb-3">
          <div class="head-label"><span class="chip"></span><span>บริการและสิ่งอำนวยความสะดวก</span></div>
          <div class="section-block">
            <!-- วิธีชำระเงิน -->
            <div class="head-label" style="margin-top:8px"><span class="chip"></span><span>วิธีชำระเงิน</span></div>
            <div class="section-block d-flex flex-wrap">
              @foreach(['เงินสด','บัตรเครดิต','บัตรเดบิต','จ่ายผ่านมือถือ','ไม่ระบุ'] as $payment)
                @php $id='pay_'.\Illuminate\Support\Str::slug($payment); @endphp
                <div class="form-check me-3 mb-2">
                  <input type="checkbox" id="{{ $id }}" name="payment_methods[]" value="{{ $payment }}" class="form-check-input" {{ in_array($payment, old('payment_methods', $cafe->payment_methods ?? []))?'checked':'' }}>
                  <label for="{{ $id }}" class="form-check-label">{{ $payment }}</label>
                </div>
              @endforeach
            </div>

            <!-- สิ่งอำนวยความสะดวก -->
            <div class="head-label" style="margin-top:10px"><span class="chip"></span><span>สิ่งอำนวยความสะดวก</span></div>
            <div class="section-block d-flex flex-wrap">
              @foreach(['ห้องประชุม','โซนเด็กเล่น','ที่จอดรถ','เครื่องปรับอากาศ','Wi-Fi'] as $facility)
                @php $id='facility_'.\Illuminate\Support\Str::slug($facility); @endphp
                <div class="form-check me-3 mb-2">
                  <input type="checkbox" id="{{ $id }}" name="facilities[]" value="{{ $facility }}" class="form-check-input" {{ in_array($facility, old('facilities', $cafe->facilities ?? []))?'checked':'' }}>
                  <label for="{{ $id }}" class="form-check-label">{{ $facility }}</label>
                </div>
              @endforeach
            </div>

            <!-- บริการเพิ่มเติม -->
            <div class="head-label" style="margin-top:10px"><span class="chip"></span><span>บริการเพิ่มเติม</span></div>
            <div class="section-block d-flex flex-wrap">
              @foreach(['ส่งเดลิเวอรี่','รับจัดงาน','ซื้อกลับบ้าน','รับจองโต๊ะ'] as $service)
                @php $id='service_'.\Illuminate\Support\Str::slug($service); @endphp
                <div class="form-check me-3 mb-2">
                  <input type="checkbox" id="{{ $id }}" name="other_services[]" value="{{ $service }}" class="form-check-input" {{ in_array($service, old('other_services', $cafe->other_services ?? []))?'checked':'' }}>
                  <label for="{{ $id }}" class="form-check-label">{{ $service }}</label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-center gap-3 mt-3 pt-3" style="border-top:1px solid var(--line)">
      <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary px-4"><i class="fas fa-times me-2"></i>ยกเลิก</a>
      <button type="submit" class="btn btn-primary px-4" id="submitBtn"><i class="fas fa-save me-2"></i>บันทึกข้อมูล</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // อัปโหลด/บีบอัดรูป (เหมือนเดิมของคุณ) — ตัดทอนเพื่อความสั้น
  const imageInput=document.getElementById('images'), cafeForm=document.getElementById('cafeForm');
  const MAX_FILES=5, MAX_PER_FILE=5*1024*1024, TARGET_PER_FILE=1.5*1024*1024, MAX_TOTAL=20*1024*1024, MAX_DIM=1600;
  const csrf=document.querySelector('meta[name="csrf-token"]')?.content||'';
  imageInput?.addEventListener('change',()=>{ if(imageInput.files.length>MAX_FILES){alert('เลือกได้สูงสุด 5 รูปภาพเท่านั้น'); imageInput.value='';}});
  cafeForm.addEventListener('submit',async function(e){
    if (!imageInput || imageInput.files.length===0) return;
    e.preventDefault();
    if (imageInput.files.length>MAX_FILES){alert('กรุณาอัปโหลดรูปภาพไม่เกิน 5 รูป'); return;}
    const files=[...imageInput.files]; const compressed=[]; let total=0;
    for (const f of files){ const out=await compress(f,{maxDim:MAX_DIM,targetBytes:TARGET_PER_FILE}); compressed.push(out); total+=out.size; if(out.size>MAX_PER_FILE){alert(`ไฟล์ ${f.name} ยังเกิน 5MB`); return;}}
    if (total>MAX_TOTAL){alert('ขนาดไฟล์รวมเกิน 20MB'); return;}
    const fd=new FormData(cafeForm); fd.delete('images[]');
    compressed.forEach((b,i)=>fd.append('images[]', new Blob([b],{type:'image/jpeg'}), (files[i].name.replace(/\.[^.]+$/,'')||'image')+'-compressed.jpg'));
    const action=cafeForm.getAttribute('action'); const btn=document.getElementById('submitBtn'); btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';
    try{ const res=await fetch(action,{method:'POST',headers:{'X-CSRF-TOKEN':csrf},body:fd,redirect:'follow'}); if(res.redirected){location.href=res.url;return;} if(res.ok){location.reload();} else {alert('บันทึกไม่สำเร็จ');}}
    catch(err){console.error(err); alert('เกิดข้อผิดพลาด');}
    finally{btn.disabled=false; btn.innerHTML='<i class="fas fa-save me-2"></i>บันทึกข้อมูล';}
  });
  function compress(file,{maxDim=1600,targetBytes=1.5*1024*1024}={}){return new Promise(async resolve=>{
    const img=await createImageBitmap(file); const r=Math.min(maxDim/img.width, maxDim/img.height, 1); const w=Math.round(img.width*r), h=Math.round(img.height*r);
    const c=document.createElement('canvas'); c.width=w; c.height=h; const x=c.getContext('2d'); x.drawImage(img,0,0,w,h);
    let q=0.9, b=await blob(c,q); for (const s of [0.85,0.8,0.75,0.7,0.65,0.6]){ if(b.size<=targetBytes) break; q=s; b=await blob(c,q); } const ar=new Uint8Array(await b.arrayBuffer()); resolve(ar);
  })}
  function blob(c,q){return new Promise(r=>c.toBlob(b=>r(b),'image/jpeg',q));}

  // ===== แผนที่ (คงเดิมของคุณ) ย่อเพื่อความสั้น =====
  const lat=document.getElementById('lat'), lng=document.getElementById('lng');
  const bounds=L.latLngBounds([[14.75,103.35],[15.00,103.65]]), center=[14.885,103.490];
  const map=L.map('map',{scrollWheelZoom:true,tap:true}).setView(center,12); map.setMaxBounds(bounds);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap contributors'}).addTo(map);
  const geocoder=L.Control.geocoder({geocoder:L.Control.Geocoder.nominatim(), defaultMarkGeocode:false, placeholder:'ค้นหาสถานที่หรือที่อยู่…'}).on('markgeocode',e=>{ place(e.geocode.center.lat,e.geocode.center.lng,true); }).addTo(map);
  let marker=null; function place(a,b,m){ if(marker) marker.setLatLng([a,b]); else marker=L.marker([a,b]).addTo(map); if(m) map.setView([a,b],16); lat.value=a.toFixed(6); lng.value=b.toFixed(6);}
  map.on('click',e=>place(e.latlng.lat,e.latlng.lng,true));
  document.getElementById('locateBtn').addEventListener('click',()=>navigator.geolocation.getCurrentPosition(p=>place(p.coords.latitude,p.coords.longitude,true)));
  document.getElementById('resetBtn').addEventListener('click',()=>{ if(marker){map.removeLayer(marker); marker=null;} lat.value=''; lng.value=''; map.setView(center,12);});
});
</script>
</body>
</html>
