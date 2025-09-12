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
    /* ====== โทนสีตามภาพแนบ (ฟ้าอ่อน สะอาด สบายตา) ====== */
    :root{
      --primary:#2b7de9;          /* ฟ้าหลัก */
      --primary-600:#1f6ad0;      /* ฟ้าเข้ม */
      --primary-100:#e9f2ff;      /* ฟ้าอ่อนพื้นหลังกล่อง */
      --teal:#10b981;             /* เขียวมิ้นต์ */
      --amber:#f59e0b;            /* เหลืองพาสเทล */
      --pink:#ec4899;             /* ชมพูพาสเทล */
      --slate:#334155;            /* ตัวอักษรเข้มอ่านง่าย */
      --muted:#6b7280;            /* ตัวอักษรรอง */
      --border:#e6edf5;
      --bg:#f4f8ff;               /* พื้นหลังทั้งหน้า */
      --white:#fff;
      --radius:16px;
      --shadow:0 6px 16px rgba(23,53,97,.06);
      --shadow-sm:0 2px 8px rgba(23,53,97,.06);
      --focus:0 0 0 .25rem rgba(43,125,233,.25);
    }

    body{font-family:'Sarabun','Inter',system-ui,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:var(--slate);padding:18px}
    .container{max-width:1200px}

    /* เฟรมรวม */
    .form-container{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:18px}
    .form-title{color:var(--primary-600);font-weight:800;letter-spacing:.2px;text-align:center;margin:6px 0 18px;display:flex;gap:10px;align-items:center;justify-content:center}
    .form-title i{background:var(--primary-100);color:var(--primary);padding:10px;border-radius:12px}

    /* กล่อง Section */
    .form-section{border:1px solid var(--border);border-radius:14px;background:var(--white);box-shadow:var(--shadow-sm);padding:14px;margin-bottom:14px}
    .section-header{display:flex;align-items:center;gap:8px;margin:-14px -14px 12px;padding:10px 14px;border-bottom:1px solid var(--border);background:var(--primary-100);color:var(--primary-600);border-top-left-radius:14px;border-top-right-radius:14px;font-weight:700}
    .section-header i{color:var(--primary)}

    /* ฟอร์ม */
    .form-label{font-weight:600;color:var(--muted)}
    .form-control,.form-select{border-radius:12px;border-color:var(--border);padding:.7rem .95rem}
    .form-control:focus,.form-select:focus{box-shadow:var(--focus);border-color:var(--primary)}
    .input-group-text{background:#f8fbff;border-color:var(--border);color:var(--muted)}

    /* Pills: ราคา/สไตล์/บริการ ให้เป็นแคปซูลสีอ่อนเหมือนภาพ */
    .btn-check+label{border-radius:999px;padding:.4rem .75rem;border:1px solid var(--border);box-shadow:var(--shadow-sm);font-weight:600}
    /* สีช่วงราคา */
    .btn-outline-primary{background:#ecf3ff;color:#1f5fbf;border-color:#d5e6ff}
    .btn-outline-success{background:#e9fbf4;color:#0f7b5c;border-color:#ccefe2}
    .btn-outline-warning{background:#fff6e6;color:#a86400;border-color:#ffe6bf}
    .btn-outline-danger{background:#ffeaf2;color:#b21e5f;border-color:#ffd0e2}
    .btn-outline-dark{background:#eef1f5;color:#333;border-color:#dfe5ee}
    .btn-check:checked+label{outline:2px solid var(--primary); outline-offset:1px}

    /* กลุ่มเช็คบ็อกซ์แบบชิป */
    .form-check-group{display:flex;flex-wrap:wrap;gap:.5rem}
    .form-check{display:inline-flex;align-items:center;gap:.4rem;background:#f8fbff;border:1px solid var(--border);border-radius:999px;padding:.38rem .7rem}
    .form-check-input{margin-top:0}
    .form-check-input:checked{background-color:var(--primary);border-color:var(--primary)}
    .form-check-label{font-weight:600;color:#42526d}

    /* แผนที่ */
    #map{height:380px;border:1px solid var(--border);border-radius:14px}
    .map-toolbar{display:flex;gap:.6rem;flex-wrap:wrap;margin:.25rem 0 .75rem}
    .map-toolbar .btn{border-radius:999px}

    /* ปุ่มหลัก */
    .btn-primary{background:var(--primary);border-color:var(--primary)}
    .btn-primary:hover{background:var(--primary-600);border-color:var(--primary-600)}
    .btn-outline-secondary{border-radius:999px}
    .actions{background:#f8fbff;border:1px solid var(--border);border-radius:14px;padding:12px}

    /* รูปตัวอย่าง */
    .thumb{width:100px;height:100px;object-fit:cover;border-radius:12px;border:1px solid #eef3fb}

    @media (max-width:768px){
      body{padding:10px}
      .form-container{padding:12px}
      #map{height:320px}
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
            <div class="section-header"><i class="bi bi-shop"></i>ข้อมูล</div>

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
              <div class="mt-3 p-3 border rounded-3" style="background:#f8fbff;border-color:var(--border)">
                <div class="d-flex flex-wrap gap-2">
                  @foreach($cafe->images as $img)
                    <img src="{{ asset('storage/'.$img) }}" class="thumb" alt="Cafe Image">
                  @endforeach
                </div>
                <small class="text-muted d-block mt-2">อัปโหลดใหม่เพื่อแทนที่รูปเดิม</small>
              </div>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label">สถานะ</label>
              <div class="form-check-group">
                <label class="form-check">
                  <input type="checkbox" class="form-check-input" id="new_opening" name="is_new_opening" value="1" {{ old('is_new_opening', $cafe->is_new_opening ?? false) ? 'checked' : '' }}>
                  <span>🌟 เปิดใหม่</span>
                </label>
              </div>
            </div>

            <div class="mb-2">
              <label class="form-label">ช่วงราคา <span class="text-danger">*</span></label>
              <div class="d-flex flex-wrap gap-2">
                @foreach(['ต่ำกว่า 100'=>'primary','101 - 250'=>'success','251 - 500'=>'warning','501 - 1,000'=>'danger','มากกว่า 1,000'=>'dark'] as $label=>$color)
                  @php $id='price'.$loop->index; @endphp
                  <input class="btn-check" type="radio" name="price_range" id="{{ $id }}" value="{{ $label }}" required {{ old('price_range', $cafe->price_range ?? '')==$label?'checked':'' }}>
                  <label for="{{ $id }}" class="btn btn-outline-{{ $color }} btn-sm"><i class="bi bi-tags-fill me-1"></i>{{ $label }}</label>
                @endforeach
              </div>
              @error('price_range')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-section">
            <div class="section-header"><i class="bi bi-palette-fill"></i>สไตล์คาเฟ่</div>
            <div class="mb-3 form-check-group">
              @foreach(['มินิมอล','วินเทจ','โมเดิร์น','อินดัสเทรียล','ธรรมชาติ/สวน','โคซี่/อบอุ่น','อาร์ต/แกลเลอรี่','ลอฟท์','ญี่ปุ่น','ยุโรป'] as $style)
                @php $id='style_'.\Illuminate\Support\Str::slug($style); @endphp
                <label class="form-check" for="{{ $id }}">
                  <input type="checkbox" id="{{ $id }}" name="cafe_styles[]" value="{{ $style }}" class="form-check-input" {{ in_array($style, old('cafe_styles', $cafe->cafe_styles ?? []))?'checked':'' }}>
                  <span>{{ $style }}</span>
                </label>
              @endforeach
            </div>

            <div class="mb-0">
              <label for="other_style" class="form-label">ระบุสไตล์อื่นๆ</label>
              <input type="text" id="other_style" name="other_style" class="form-control @error('other_style') is-invalid @enderror" placeholder="เช่น สไตล์ลึกลับ, สไตล์อนาคต" value="{{ old('other_style', $cafe->other_style ?? '') }}">
              @error('other_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-section">
            <div class="section-header"><i class="bi bi-person-lines-fill"></i>ข้อมูลติดต่อ</div>

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
            <div class="section-header"><i class="bi bi-geo-alt-fill"></i>ที่ตั้งและแผนที่</div>

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

            <div class="map-toolbar">
              <button type="button" id="locateBtn" class="btn btn-sm btn-outline-primary"><i class="bi bi-geo-alt-fill me-1"></i>ใกล้ฉัน</button>
            </div>

            <div id="map" class="mb-2"></div>

            <div id="duplicateCoordsWarning" class="alert alert-warning d-none" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>พิกัดนี้มีคาเฟ่อื่นใช้งานอยู่แล้ว!
            </div>
            <div id="outOfBoundsWarning" class="alert alert-danger d-none" role="alert">
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
              <button type="button" class="btn btn-outline-secondary" id="resetBtn"><i class="bi bi-arrow-counterclockwise me-1"></i>รีเซ็ตตำแหน่ง</button>
            </div>
          </div>

          <div class="form-section">
            <div class="section-header"><i class="bi bi-clock-history"></i>เวลาทำการ</div>
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
            <div class="section-header"><i class="bi bi-stars"></i>บริการและสิ่งอำนวยความสะดวก</div>

            <label class="form-label fw-bold">วิธีชำระเงิน</label>
            <div class="mb-2 form-check-group">
              @foreach(['เงินสด','บัตรเครดิต','บัตรเดบิต','จ่ายผ่านมือถือ','ไม่ระบุ'] as $payment)
                @php $id='pay_'.\Illuminate\Support\Str::slug($payment); @endphp
                <label class="form-check" for="{{ $id }}">
                  <input type="checkbox" id="{{ $id }}" name="payment_methods[]" value="{{ $payment }}" class="form-check-input" {{ in_array($payment, old('payment_methods', $cafe->payment_methods ?? []))?'checked':'' }}>
                  <span>{{ $payment }}</span>
                </label>
              @endforeach
            </div>

            <label class="form-label fw-bold mt-2">สิ่งอำนวยความสะดวก</label>
            <div class="mb-2 form-check-group">
              @foreach(['ห้องประชุม','โซนเด็กเล่น','ที่จอดรถ','เครื่องปรับอากาศ','Wi-Fi'] as $facility)
                @php $id='facility_'.\Illuminate\Support\Str::slug($facility); @endphp
                <label class="form-check" for="{{ $id }}">
                  <input type="checkbox" id="{{ $id }}" name="facilities[]" value="{{ $facility }}" class="form-check-input" {{ in_array($facility, old('facilities', $cafe->facilities ?? []))?'checked':'' }}>
                  <span>{{ $facility }}</span>
                </label>
              @endforeach
            </div>

            <label class="form-label fw-bold mt-2">บริการเพิ่มเติม</label>
            <div class="form-check-group">
              @foreach(['ส่งเดลิเวอรี่','รับจัดงาน','ซื้อกลับบ้าน','รับจองโต๊ะ'] as $service)
                @php $id='service_'.\Illuminate\Support\Str::slug($service); @endphp
                <label class="form-check" for="{{ $id }}">
                  <input type="checkbox" id="{{ $id }}" name="other_services[]" value="{{ $service }}" class="form-check-input" {{ in_array($service, old('other_services', $cafe->other_services ?? []))?'checked':'' }}>
                  <span>{{ $service }}</span>
                </label>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <div class="actions d-flex justify-content-center gap-3 mt-3">
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
  /* ====== อัปโหลดรูป: บีบอัดก่อนส่ง ====== */
  const MAX_FILES = 5;
  const MAX_PER_FILE = 5 * 1024 * 1024;
  const TARGET_PER_FILE = 1.5 * 1024 * 1024;
  const MAX_TOTAL = 20 * 1024 * 1024;
  const MAX_DIM = 1600;

  const imageInput = document.getElementById('images');
  const cafeForm = document.getElementById('cafeForm');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  imageInput?.addEventListener('change', () => {
    if (imageInput.files.length > MAX_FILES) { alert('เลือกได้สูงสุด 5 รูปภาพเท่านั้น'); imageInput.value=''; }
  });

  cafeForm.addEventListener('submit', async function(e) {
    if (!duplicateCoordsWarning.classList.contains('d-none') || !outOfBoundsWarning.classList.contains('d-none')) {
      e.preventDefault(); alert('โปรดแก้ไขพิกัดก่อนบันทึก'); return;
    }
    if (!imageInput || imageInput.files.length===0) return;

    e.preventDefault();
    const files = Array.from(imageInput.files);
    if (files.length>MAX_FILES){ alert('กรุณาอัปโหลดรูปไม่เกิน 5 รูป'); return; }

    const compressed=[]; let totalAfter=0;
    for (const f of files){
      const out = await compressImage(f,{maxDim:MAX_DIM,targetBytes:TARGET_PER_FILE});
      compressed.push(out); totalAfter += out.blob.size;
      if (out.blob.size>MAX_PER_FILE){ alert(`ไฟล์ ${f.name} หลังบีบอัดยังเกิน 5MB`); return; }
    }
    if (totalAfter>MAX_TOTAL){ alert('ขนาดไฟล์รวมเกิน 20MB'); return; }

    const fd = new FormData(cafeForm);
    fd.delete('images[]');
    compressed.forEach((it,idx)=>{
      const safeName=(files[idx].name.replace(/\.[^.]+$/, '')||'image')+'-compressed.jpg';
      fd.append('images[]', it.blob, safeName);
    });

    const action=cafeForm.getAttribute('action');
    const submitBtn=document.getElementById('submitBtn');
    submitBtn.disabled=true;
    submitBtn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';

    try{
      const res=await fetch(action,{method:'POST',headers:{'X-CSRF-TOKEN':csrf},body:fd,redirect:'follow'});
      if (res.redirected){ window.location.href=res.url; return; }
      if (res.ok){ window.location.reload(); }
      else { const t=await res.text(); console.error(t); alert('บันทึกไม่สำเร็จ'); }
    }catch(err){ console.error(err); alert('เกิดข้อผิดพลาดระหว่างอัปโหลด'); }
    finally{ submitBtn.disabled=false; submitBtn.innerHTML='<i class="fas fa-save me-2"></i>บันทึกข้อมูล'; }
  });

  async function compressImage(file,{maxDim=1600,targetBytes=1.5*1024*1024}={}){
    const bitmap=await readImageBitmap(file);
    const {width,height}=fitContain(bitmap.width,bitmap.height,maxDim);
    const canvas=document.createElement('canvas'); canvas.width=width; canvas.height=height;
    const ctx=canvas.getContext('2d'); ctx.drawImage(bitmap,0,0,width,height);
    let q=0.9, blob=await canvasToBlob(canvas,'image/jpeg',q), steps=[0.85,0.8,0.75,0.7,0.65,0.6];
    for (const s of steps){ if (blob.size<=targetBytes) break; q=s; blob=await canvasToBlob(canvas,'image/jpeg',q); }
    return {blob};
  }
  function fitContain(w,h,max){ if (w<=max&&h<=max) return {width:w,height:h}; const r=Math.min(max/w,max/h); return {width:Math.round(w*r),height:Math.round(h*r)}; }
  function canvasToBlob(c,t,q){ return new Promise(r=>c.toBlob(b=>r(b),t,q)); }
  async function readImageBitmap(file){
    if ('createImageBitmap' in window) return await createImageBitmap(file);
    const url=await new Promise((res,rej)=>{ const fr=new FileReader(); fr.onload=()=>res(fr.result); fr.onerror=rej; fr.readAsDataURL(file); });
    const img=new Image(); img.decoding='async'; img.src=url; await img.decode();
    const c=document.createElement('canvas'); c.width=img.naturalWidth; c.height=img.naturalHeight; c.getContext('2d').drawImage(img,0,0);
    return { width:c.width, height:c.height, drawImage:(ctx,...args)=>ctx.drawImage(img,...args) };
  }

  /* ====== แผนที่ ====== */
  const latInput=document.getElementById('lat');
  const lngInput=document.getElementById('lng');
  const duplicateCoordsWarning=document.getElementById('duplicateCoordsWarning');
  const outOfBoundsWarning=document.getElementById('outOfBoundsWarning');
  const locateBtn=document.getElementById('locateBtn');

  const bounds=L.latLngBounds([[14.75,103.35],[15.00,103.65]]);
  const center=[14.885,103.490];

  const map=L.map('map',{scrollWheelZoom:true,tap:true}).setView(center,12);
  map.setMaxBounds(bounds);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap contributors'}).addTo(map);

  const geocoder=L.Control.geocoder({
    geocoder:L.Control.Geocoder.nominatim(),
    defaultMarkGeocode:false,
    placeholder:'ค้นหาสถานที่หรือที่อยู่…',
    errorMessage:'ไม่พบสถานที่'
  }).on('markgeocode',function(e){ const c=e.geocode.center; applyPoint(c.lat,c.lng,true); }).addTo(map);

  let marker;
  if (latInput.value && lngInput.value){
    const lat=parseFloat(latInput.value), lng=parseFloat(lngInput.value);
    if(!isNaN(lat)&&!isNaN(lng)){ marker=L.marker([lat,lng]).addTo(map); map.setView([lat,lng],15); if(!bounds.contains([lat,lng])) showOOB(true); else checkCoordinates(lat,lng); }
  }

  map.on('click',(e)=>applyPoint(e.latlng.lat,e.latlng.lng,true));
  locateBtn.addEventListener('click',()=>{
    if(!navigator.geolocation){ alert('อุปกรณ์ไม่รองรับการระบุตำแหน่ง'); return; }
    navigator.geolocation.getCurrentPosition(
      (pos)=>{ const {latitude,longitude}=pos.coords; if(!bounds.contains([latitude,longitude])){ alert('ตำแหน่งปัจจุบันอยู่นอกอำเภอเมืองสุรินทร์'); map.setView(center,13); return; } applyPoint(latitude,longitude,true); },
      (err)=>{ console.error(err); alert('ไม่สามารถดึงตำแหน่งปัจจุบันได้'); },
      {enableHighAccuracy:true,timeout:8000}
    );
  });
  document.getElementById('resetBtn').addEventListener('click',function(){ if(marker){map.removeLayer(marker); marker=null;} latInput.value=''; lngInput.value=''; showOOB(false); duplicateCoordsWarning.classList.add('d-none'); map.setView(center,12); });

  latInput.addEventListener('input', handleManual); lngInput.addEventListener('input', handleManual);
  function handleManual(){ const lat=parseFloat(latInput.value), lng=parseFloat(lngInput.value); if(isNaN(lat)||isNaN(lng)) return; applyPoint(lat,lng,true); }

  function applyPoint(lat,lng,move){
    placeMarker(lat,lng);
    latInput.value=lat.toFixed(6); lngInput.value=lng.toFixed(6);
    if(move) map.setView([lat,lng],16);
    if(!bounds.contains([lat,lng])){ showOOB(true); return; }
    showOOB(false); checkCoordinates(lat,lng);
  }
  function placeMarker(lat,lng){ if(marker) marker.setLatLng([lat,lng]); else marker=L.marker([lat,lng]).addTo(map); }
  function showOOB(show){ outOfBoundsWarning.classList.toggle('d-none',!show); }

  async function checkCoordinates(lat,lng){
    const cafeId="{{ $cafe->id ?? 'null' }}";
    try{
      const res=await fetch("{{ route('admin.cafe.check_coordinates') }}",{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({lat,lng,cafe_id:cafeId})});
      const data=await res.json(); const isDup=(data.exists ?? data.is_duplicate ?? false);
      duplicateCoordsWarning.classList.toggle('d-none',!isDup);
    }catch(err){ console.error('check_coordinates error:',err); }
  }

  setTimeout(()=>map.invalidateSize(),300);
  window.addEventListener('resize',()=>map.invalidateSize());
});
</script>
</body>
</html>
