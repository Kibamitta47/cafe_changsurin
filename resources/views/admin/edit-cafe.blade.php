<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }} | ระบบจัดการ</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
  <style>
    .form-check-label{font-weight:600;color:#333}
    :root{--primary-blue:#3b82f6;--dark-blue:#2563eb;--gray-light:#e5e7eb}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif;background:#f0f2f5;color:#333;padding:20px 0}
    .container.mt-5.mb-5{background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.05);padding:40px}
    h2{color:var(--dark-blue);border-bottom:2px solid var(--gray-light);padding-bottom:15px;margin-bottom:30px;font-weight:700}
    .form-label{font-weight:600;color:#555;margin-bottom:8px}
    .form-control,.form-select,.input-group-text{border-radius:8px}
    .form-control:focus,.form-select:focus{box-shadow:0 0 0 .25rem rgba(59,130,246,.25);border-color:var(--primary-blue)}
    .btn-check+.btn{border-radius:8px;padding:8px 15px;font-size:.95rem}
    .map-toolbar{display:flex;gap:.5rem;flex-wrap:wrap;margin:.5rem 0 10px}
    #map{height:400px;margin-bottom:1.5rem;border-radius:8px;border:1px solid #dee2e6}
    .d-flex.justify-content-end.gap-2.mt-3{padding-top:20px;border-top:1px solid var(--gray-light);margin-top:30px!important}
    .btn-primary{background:var(--primary-blue);border-color:var(--primary-blue);font-weight:600;padding:10px 25px;border-radius:8px}
    .btn-primary:hover{background:var(--dark-blue);border-color:var(--dark-blue)}
    @media (max-width:768px){
      .container.mt-5.mb-5{padding:20px}
      .btn-check+.btn{display:block;width:100%;margin-bottom:.5rem!important}
      .d-flex.justify-content-end.gap-2.mt-3{flex-direction:column}
      .d-flex.justify-content-end.gap-2.mt-3 .btn{width:100%}
      #map{height:320px}
    }
  </style>
</head>
<body>

@include('components.adminmenu')

<div class="container mt-5 mb-5">
  <h2 class="mb-4"><i class="bi bi-shop me-2"></i> {{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</h2>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>ข้อผิดพลาด!</strong> กรุณาตรวจสอบข้อมูลที่กรอก:
      <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('db_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('db_error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <form action="{{ isset($cafe) ? route('admin.cafe.update', $cafe) : route('admin.cafe.store') }}" method="POST" enctype="multipart/form-data" class="p-4 rounded" id="cafeForm">
    @csrf
    @if(isset($cafe)) @method('PUT') @endif

    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="cafe_name" class="form-label">ชื่อคาเฟ่ <span class="text-danger">*</span></label>
          <input type="text" id="cafe_name" name="cafe_name" class="form-control @error('cafe_name') is-invalid @enderror" placeholder="ชื่อคาเฟ่เต็ม" required value="{{ old('cafe_name', $cafe->cafe_name ?? '') }}" />
          @error('cafe_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label">สถานะ</label><br/>
          <div class="form-check form-check-inline">
            <input type="checkbox" id="new_opening" name="is_new_opening" value="1" class="form-check-input" {{ old('is_new_opening', $cafe->is_new_opening ?? false) ? 'checked' : '' }} />
            <label for="new_opening" class="form-check-label">เปิดใหม่</label>
          </div>
        </div>

        <div class="mb-3">
          <label for="images" class="form-label">รูปภาพคาเฟ่ <span class="text-muted">(สูงสุด 5 รูป)</span></label>
          <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
          <div class="form-text">เลือกรูปภาพได้หลายรูป (JPG, PNG, WebP)</div>
          @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror>

          @if(isset($cafe) && !empty($cafe->images) && is_array($cafe->images))
            <div class="mt-3">
              <p class="form-label">รูปภาพที่มีอยู่:</p>
              <div class="d-flex flex-wrap gap-2">
                @foreach($cafe->images as $imagePath)
                  <img src="{{ asset('storage/' . $imagePath) }}" alt="Cafe Image" class="img-thumbnail" style="width:100px;height:100px;object-fit:cover;">
                @endforeach
              </div>
              <div class="form-text mt-2">หากอัปโหลดรูปภาพใหม่ รูปภาพที่มีอยู่จะถูกแทนที่</div>
            </div>
          @endif
        </div>

        <div class="mb-3">
          <label class="form-label">ช่วงราคา <span class="text-danger">*</span></label>
          <div class="d-flex flex-wrap gap-2">
            @foreach(['ต่ำกว่า 100'=>'primary','101 - 250'=>'success','251 - 500'=>'warning','501 - 1,000'=>'danger','มากกว่า 1,000'=>'dark'] as $label=>$color)
              @php $id='price'.$loop->index; @endphp
              <input type="radio" class="btn-check" name="price_range" id="{{ $id }}" value="{{ $label }}" required autocomplete="off" {{ old('price_range', $cafe->price_range ?? '') == $label ? 'checked' : '' }} />
              <label for="{{ $id }}" class="btn btn-outline-{{ $color }}"><i class="bi bi-currency-bitcoin"></i> {{ $label }}</label>
            @endforeach
          </div>
          @error('price_range')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <h5 class="mt-4 mb-3 text-primary">สไตล์คาเฟ่</h5>
        <div class="mb-3 d-flex flex-wrap gap-2">
          @foreach(['มินิมอล','วินเทจ','โมเดิร์น','อินดัสเทรียล','ธรรมชาติ/สวน','โคซี่/อบอุ่น','อาร์ต/แกลเลอรี่','ลอฟท์','ญี่ปุ่น','ยุโรป'] as $style)
            @php $id='style_'.\Illuminate\Support\Str::slug($style); @endphp
            <div class="form-check form-check-inline">
              <input type="checkbox" id="{{ $id }}" name="cafe_styles[]" value="{{ $style }}" class="form-check-input" {{ in_array($style, old('cafe_styles', $cafe->cafe_styles ?? [])) ? 'checked' : '' }} />
              <label for="{{ $id }}" class="form-check-label">{{ $style }}</label>
            </div>
          @endforeach
        </div>

        <h5 class="mt-4 mb-3 text-primary">สไตล์อื่นๆ</h5>
        <div class="mb-3">
          <label for="other_style" class="form-label">ระบุสไตล์อื่นๆ ที่ไม่มีในรายการ</label>
          <input type="text" id="other_style" name="other_style" class="form-control @error('other_style') is-invalid @enderror" placeholder="เช่น สไตล์ลึกลับ, สไตล์อนาคต" value="{{ old('other_style', $cafe->other_style ?? '') }}" />
          @error('other_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="form-text">สามารถระบุสไตล์เพิ่มเติมได้</div>
        </div>

        <h5 class="mt-4 mb-3 text-primary">ข้อมูลติดต่อ</h5>
        <div class="mb-3">
          <label for="phone" class="form-label">📞 เบอร์ติดต่อ</label>
          <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" pattern="^\d{9,10}$" placeholder="เช่น 0812345678" value="{{ old('phone', $cafe->phone ?? '') }}" />
          @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">📧 อีเมล์</label>
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="เช่น example@email.com" value="{{ old('email', $cafe->email ?? '') }}" />
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label">🌐 เว็บไซต์</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-globe"></i></span>
            <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" placeholder="เช่น https://www.yourcafe.com" value="{{ old('website', $cafe->website ?? '') }}" />
          </div>
          @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <h5 class="mt-4 mb-3 text-primary">Social Media</h5>
        <div class="mb-3">
          <label class="form-label">📘 Facebook</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
            <input type="text" name="facebook_page" class="form-control @error('facebook_page') is-invalid @enderror" placeholder="ชื่อผู้ใช้ หรือ ลิงก์เพจ" value="{{ old('facebook_page', $cafe->facebook_page ?? '') }}" />
          </div>
          @error('facebook_page')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">📸 Instagram</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
            <input type="text" name="instagram_page" class="form-control @error('instagram_page') is-invalid @enderror" placeholder="ชื่อผู้ใช้" value="{{ old('instagram_page', $cafe->instagram_page ?? '') }}" />
          </div>
          @error('instagram_page')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">📱 Line</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fab fa-line"></i></span>
            <input type="text" name="line_id" class="form-control @error('line_id') is-invalid @enderror" placeholder="ID Line หรือ @บัญชี" value="{{ old('line_id', $cafe->line_id ?? '') }}" />
          </div>
          @error('line_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="col-md-6">
        <h5 class="mb-3 text-primary">ที่ตั้ง</h5>
        <div class="mb-3">
          <label for="place_name" class="form-label">ชื่อสถานที่ <span class="text-danger">*</span></label>
          <input type="text" id="place_name" name="place_name" class="form-control @error('place_name') is-invalid @enderror" placeholder="ชื่ออาคาร, ชื่อโครงการ" required value="{{ old('place_name', $cafe->place_name ?? '') }}" />
          @error('place_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="address" class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
          <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="บ้านเลขที่, ถนน, ตำบล, อำเภอ, จังหวัด, รหัสไปรษณีย์" required>{{ old('address', $cafe->address ?? '') }}</textarea>
          @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <label class="form-label">เลือกตำแหน่งบนแผนที่ <span class="text-danger">*</span></label>
        <div class="map-toolbar">
          <button type="button" class="btn btn-outline-primary" id="locateBtn">
            <i class="bi bi-geo-alt"></i> ปักหมุดที่ฉันอยู่
          </button>
          <button type="button" class="btn btn-outline-secondary" id="resetBtn">
            <i class="bi bi-arrow-counterclockwise"></i> รีเซ็ตตำแหน่งบนแผนที่
          </button>
        </div>
        <div id="map" class="mb-3 position-relative"></div>

        <div class="row mb-4">
          <div class="col">
            <label for="lat" class="form-label">ละติจูด</label>
            <input type="number" step="any" id="lat" name="lat" class="form-control @error('lat') is-invalid @enderror" required value="{{ old('lat', $cafe->lat ?? '') }}" />
            @error('lat')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col">
            <label for="lng" class="form-label">ลองจิจูด</label>
            <input type="number" step="any" id="lng" name="lng" class="form-control @error('lng') is-invalid @enderror" required value="{{ old('lng', $cafe->lng ?? '') }}" />
            @error('lng')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div id="duplicateCoordsWarning" class="alert alert-warning d-none" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>พิกัดนี้มีคาเฟ่อื่นใช้งานอยู่แล้ว! กรุณาเลือกพิกัดอื่น
        </div>
        <div id="outOfBoundsWarning" class="alert alert-danger d-none" role="alert">
          <i class="bi bi-geo-alt-fill me-2"></i>ตำแหน่งที่เลือกอยู่นอกเขตอำเภอเมืองสุรินทร์ กรุณาเลือกภายในขอบเขต
        </div>

        <h5 class="mt-4 mb-3 text-primary">เวลาทำการ</h5>
        <div class="row mb-3">
          <div class="col-md-6 mb-3 mb-md-0">
            <label for="open_day" class="form-label">📅 วันเปิด</label>
            <select class="form-select @error('open_day') is-invalid @enderror" id="open_day" name="open_day">
              <option value="">-- เลือกวัน --</option>
              @php $days=['ทุกวัน','จันทร์-ศุกร์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์']; @endphp
              @foreach($days as $day)
                <option value="{{ $day }}" {{ (isset($cafe) && $cafe->open_day == $day) || old('open_day') == $day ? 'selected' : '' }}>{{ $day }}</option>
              @endforeach
            </select>
            @error('open_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="close_day" class="form-label">📅 วันปิด</label>
            <select class="form-select @error('close_day') is-invalid @enderror" id="close_day" name="close_day">
              <option value="">ไม่มีวันปิด</option>
              @php $closeDays=['จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์']; @endphp
              @foreach($closeDays as $day)
                <option value="{{ $day }}" {{ (isset($cafe) && $cafe->close_day == $day) || old('close_day') == $day ? 'selected' : '' }}>{{ $day }}</option>
              @endforeach
            </select>
            @error('close_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6 mb-3 mb-md-0">
            <label for="open_time" class="form-label">⏰ เวลาเปิด</label>
            <div class="input-group">
              <input type="text" class="form-control @error('open_time') is-invalid @enderror" id="open_time" name="open_time" pattern="([01][0-9]|2[0-3]):[0-5][0-9]" placeholder="HH:MM (ตัวอย่าง: 09:30)" value="{{ old('open_time', isset($cafe) && $cafe->open_time ? Carbon\Carbon::parse($cafe->open_time)->format('H:i') : '') }}">
              <span class="input-group-text">น.</span>
            </div>
            @error('open_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="close_time" class="form-label">⏰ เวลาปิด</label>
            <div class="input-group">
              <input type="text" class="form-control @error('close_time') is-invalid @enderror" id="close_time" name="close_time" pattern="([01][0-9]|2[0-3]):[0-5][0-9]" placeholder="HH:MM (ตัวอย่าง: 18:00)" value="{{ old('close_time', isset($cafe) && $cafe->close_time ? Carbon\Carbon::parse($cafe->close_time)->format('H:i') : '') }}">
              <span class="input-group-text">น.</span>
            </div>
            @error('close_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <h5 class="mt-4 mb-3 text-primary">วิธีชำระเงิน</h5>
        <div class="mb-3 d-flex flex-wrap gap-2">
          @foreach(['เงินสด','บัตรเครดิต','บัตรเดบิต','จ่ายผ่านมือถือ','ไม่ระบุ'] as $payment)
            @php $id='pay_'.\Illuminate\Support\Str::slug($payment); @endphp
            <div class="form-check form-check-inline">
              <input type="checkbox" id="{{ $id }}" name="payment_methods[]" value="{{ $payment }}" class="form-check-input" {{ in_array($payment, old('payment_methods', $cafe->payment_methods ?? [])) ? 'checked' : '' }} />
              <label for="{{ $id }}" class="form-check-label">{{ $payment }}</label>
            </div>
          @endforeach
        </div>

        <h5 class="mt-4 mb-3 text-primary">สิ่งอำนวยความสะดวก</h5>
        <div class="mb-3 d-flex flex-wrap gap-2">
          @foreach(['ห้องประชุม','โซนเด็กเล่น','ที่จอดรถ','เครื่องปรับอากาศ','Wi-Fi'] as $facility)
            @php $id='facility_'.\Illuminate\Support\Str::slug($facility); @endphp
            <div class="form-check form-check-inline">
              <input type="checkbox" id="{{ $id }}" name="facilities[]" value="{{ $facility }}" class="form-check-input" {{ in_array($facility, old('facilities', $cafe->facilities ?? [])) ? 'checked' : '' }} />
              <label for="{{ $id }}" class="form-check-label">{{ $facility }}</label>
            </div>
          @endforeach
        </div>

        <h5 class="mt-4 mb-3 text-primary">บริการเพิ่มเติม</h5>
        <div class="mb-3 d-flex flex-wrap gap-2">
          @foreach(['ส่งเดลิเวอรี่','รับจัดงาน','ซื้อกลับบ้าน','รับจองโต๊ะ'] as $service)
            @php $id='service_'.\Illuminate\Support\Str::slug($service); @endphp
            <div class="form-check form-check-inline">
              <input type="checkbox" id="{{ $id }}" name="other_services[]" value="{{ $service }}" class="form-check-input" {{ in_array($service, old('other_services', $cafe->other_services ?? [])) ? 'checked' : '' }} />
              <label for="{{ $id }}" class="form-check-label">{{ $service }}</label>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
      <a href="{{ route('admin.cafe.index') }}" class="btn btn-secondary d-flex align-items-center">
        <i class="fas fa-times me-1"></i> ยกเลิก
      </a>
      <button type="submit" class="btn btn-primary px-4" id="submitBtn">
        <i class="fas fa-save me-1"></i> บันทึกข้อมูล
      </button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
/* ---------- รูปภาพ: จำกัดจำนวน ---------- */
const imageInput = document.getElementById('images');
const cafeForm   = document.getElementById('cafeForm');
imageInput?.addEventListener('change', () => {
  if (imageInput.files.length > 5) { alert('เลือกได้สูงสุด 5 รูปภาพเท่านั้น'); imageInput.value = ''; }
});
cafeForm.addEventListener('submit', function(e) {
  if (imageInput && imageInput.files.length > 5) { e.preventDefault(); alert('กรุณาอัปโหลดรูปภาพไม่เกิน 5 รูปเท่านั้น'); return; }
  if (!document.getElementById('duplicateCoordsWarning').classList.contains('d-none') ||
      !document.getElementById('outOfBoundsWarning').classList.contains('d-none')) {
    e.preventDefault(); alert('โปรดแก้ไขข้อผิดพลาดเกี่ยวกับพิกัดก่อนบันทึกข้อมูล'); return;
  }
});

/* ---------- แผนที่: เลือก/รีเซ็ต/ปักหมุดที่ฉันอยู่ + พิมพ์พิกัดแล้วเลื่อน ---------- */
const latInput = document.getElementById('lat');
const lngInput = document.getElementById('lng');
const duplicateCoordsWarning = document.getElementById('duplicateCoordsWarning');
const outOfBoundsWarning = document.getElementById('outOfBoundsWarning');
const submitBtn = document.getElementById('submitBtn');
const locateBtn = document.getElementById('locateBtn');
const resetBtn  = document.getElementById('resetBtn');

const mueangSurinBounds = L.latLngBounds([[14.75,103.35],[15.00,103.65]]);
const mueangSurinCenter = [14.885,103.490];

const map = L.map('map',{scrollWheelZoom:true,tap:true}).setView(mueangSurinCenter,12);
map.setMaxBounds(mueangSurinBounds);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom:19, attribution:'© OpenStreetMap contributors'
}).addTo(map);

const geocoder = L.Control.geocoder({
  geocoder: L.Control.Geocoder.nominatim(),
  defaultMarkGeocode:false,
  placeholder:'ค้นหาที่อยู่...',
  errorMessage:'ไม่พบสถานที่'
}).on('markgeocode', function(e){
  const c = e.geocode.center;
  applyPoint(c.lat, c.lng, true);
}).addTo(map);

let marker;

/* เริ่มต้นจากค่าที่มี */
if (latInput.value && lngInput.value) {
  const init = [parseFloat(latInput.value), parseFloat(lngInput.value)];
  if (!isNaN(init[0]) && !isNaN(init[1])) {
    marker = L.marker(init).addTo(map);
    map.setView(init, 15);
    if (mueangSurinBounds.contains(init)) checkCoordinates(init[0], init[1]);
    else displayOutOfBoundsWarning(true);
  }
}

/* คลิกบนแผนที่ */
map.on('click', e => applyPoint(e.latlng.lat, e.latlng.lng, true));

/* ปักหมุดที่ฉันอยู่ */
locateBtn?.addEventListener('click', ()=>{
  if (!navigator.geolocation){ alert('อุปกรณ์ไม่รองรับการระบุตำแหน่ง'); return; }
  navigator.geolocation.getCurrentPosition(
    pos=>{
      const {latitude, longitude} = pos.coords;
      if (!mueangSurinBounds.contains([latitude, longitude])) {
        alert('ตำแหน่งปัจจุบันอยู่นอกเขตอำเภอเมืองสุรินทร์');
        map.setView(mueangSurinCenter, 13); return;
      }
      applyPoint(latitude, longitude, true);
    },
    err=>{ console.error(err); alert('ไม่สามารถดึงตำแหน่งปัจจุบันได้'); },
    {enableHighAccuracy:true, timeout:8000}
  );
});

/* รีเซ็ตแผนที่ */
resetBtn?.addEventListener('click', ()=>{
  if (marker){ map.removeLayer(marker); marker=null; }
  latInput.value=''; lngInput.value='';
  displayOutOfBoundsWarning(false);
  duplicateCoordsWarning.classList.add('d-none');
  submitBtn.disabled=false;
  map.setView(mueangSurinCenter,12);
});

/* พิมพ์พิกัดด้วยมือให้แผนที่เลื่อนไป */
latInput.addEventListener('input', handleManual);
lngInput.addEventListener('input', handleManual);
function handleManual(){
  const lat=parseFloat(latInput.value), lng=parseFloat(lngInput.value);
  if (Number.isFinite(lat) && Number.isFinite(lng)) applyPoint(lat,lng,true);
}

/* ฟังก์ชันหลัก */
function applyPoint(lat,lng,move=false){
  const inside = mueangSurinBounds.contains([lat,lng]);
  if (marker) marker.setLatLng([lat,lng]); else marker = L.marker([lat,lng]).addTo(map);
  latInput.value=(+lat).toFixed(6); lngInput.value=(+lng).toFixed(6);
  displayOutOfBoundsWarning(!inside);
  submitBtn.disabled=!inside;
  if (inside) checkCoordinates(lat,lng);
  if (move) map.setView([lat,lng], inside?16:15);
}
function displayOutOfBoundsWarning(show){
  outOfBoundsWarning.classList.toggle('d-none', !show);
}

/* ตรวจซ้ำพิกัดในระบบ */
async function checkCoordinates(lat,lng){
  if (!lat || !lng){ duplicateCoordsWarning.classList.add('d-none'); submitBtn.disabled=false; return; }
  const currentCafeId = "{{ $cafe->id ?? 'null' }}";
  try{
    const resp = await fetch('{{ route('admin.cafe.check_coordinates') }}', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body: JSON.stringify({lat,lng,cafe_id:currentCafeId})
    });
    const data = await resp.json();
    const isDup = data.is_duplicate ?? data.exists ?? false;
    duplicateCoordsWarning.classList.toggle('d-none', !isDup);
    submitBtn.disabled = isDup || !mueangSurinBounds.contains([lat, lng]);
  }catch(e){
    console.error('check_coordinates error', e);
    duplicateCoordsWarning.classList.add('d-none');
    if (outOfBoundsWarning.classList.contains('d-none')) submitBtn.disabled=false;
  }
}

setTimeout(()=>map.invalidateSize(),300);
window.addEventListener('resize', ()=>map.invalidateSize());
</script>

<!-- (ออปชัน) บีบอัดรูปก่อนอัปโหลด -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const MAX_FILES=5, MAX_BYTES=2.5*1024*1024, MAX_DIM=1600, MIN_QUALITY=0.5;
  const input=document.getElementById('images');
  function supportWebP(){try{const c=document.createElement('canvas');return c.toDataURL('image/webp').startsWith('data:image/webp')}catch{return false}}
  const TARGET_TYPE=supportWebP()?'image/webp':'image/jpeg';
  function fileToDataURL(f){return new Promise((res,rej)=>{const r=new FileReader();r.onload=e=>res(e.target.result);r.onerror=rej;r.readAsDataURL(f)})}
  function canvasToBlob(canvas,type,q){return new Promise(r=>canvas.toBlob(b=>r(b),type,q))}
  async function compressImage(file){
    if (file.size<=MAX_BYTES) return file;
    const img=new Image(); img.src=await fileToDataURL(file); await img.decode();
    let {width,height}=img; const scale=Math.min(1, MAX_DIM/Math.max(width,height));
    width=Math.max(1,Math.round(width*scale)); height=Math.max(1,Math.round(height*scale));
    const cv=document.createElement('canvas'); cv.width=width; cv.height=height; cv.getContext('2d').drawImage(img,0,0,width,height);
    let q=.85, blob=await canvasToBlob(cv,TARGET_TYPE,q);
    while(blob.size>MAX_BYTES && q>MIN_QUALITY){ q=Math.max(MIN_QUALITY,q-.08); blob=await canvasToBlob(cv,TARGET_TYPE,q); }
    const base=(file.name||'image').replace(/\.[^.]+$/,''); const ext=TARGET_TYPE==='image/webp'?'webp':'jpg';
    return new File([blob],`${base}.${ext}`,{type:TARGET_TYPE,lastModified:Date.now()});
  }
  async function compressAllIfNeeded(list){const dt=new DataTransfer(); for(const f of list){dt.items.add(await compressImage(f));} input.files=dt.files;}
  input?.addEventListener('change', async ()=>{
    if (input.files.length>MAX_FILES){alert(`เลือกได้สูงสุด ${MAX_FILES} รูปภาพเท่านั้น`); input.value=''; return;}
    if (input.files.length>0) await compressAllIfNeeded(input.files);
  });
  cafeForm.addEventListener('submit', async (e)=>{
    if (input && input.files.length>MAX_FILES){e.preventDefault(); alert(`กรุณาอัปโหลดรูปภาพไม่เกิน ${MAX_FILES} รูป`); return;}
    const need = input && Array.from(input.files).some(f=>f.size>MAX_BYTES);
    if (need){ e.preventDefault(); await compressAllIfNeeded(input.files); cafeForm.submit(); }
  });
});
</script>
</body>
</html>
