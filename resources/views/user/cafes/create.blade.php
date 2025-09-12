<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>

  <style>
    /* ===== Theme ตามภาพ (ฟ้าอ่อน คลีน มีเงาเบา ๆ) ===== */
    :root{
      --brand:#4A90E2;        /* ฟ้าเฉดหลัก */
      --brand-600:#357ABD;    /* ฟ้าเข้มสำหรับ hover/หัวข้อ */
      --brand-50:#F1F6FF;     /* พื้นหลังหัวข้อ/บล็อกอ่อน */
      --ink:#2c3e50;          /* ตัวอักษรหลัก */
      --muted:#6b7280;        /* ตัวอักษรรอง */
      --line:#e6edf5;         /* เส้นขอบ */
      --bg:#f7faff;           /* พื้นหลังทั้งหน้า */
      --radius:16px;
      --shadow:0 10px 24px rgba(35,78,135,.06);
      --shadow-sm:0 6px 14px rgba(35,78,135,.05);
      --focus:0 0 0 .25rem rgba(74,144,226,.25);
    }
    html,body{height:100%}
    body{background:var(--bg);color:var(--ink);font-family:'Sarabun','Inter',system-ui,Segoe UI,Roboto,Arial,sans-serif}
    .container{max-width:1200px;padding:22px}

    /* กล่องหลัก */
    .card-wrap{
      background:#fff;border:1px solid var(--line);border-radius:var(--radius);
      box-shadow:var(--shadow);padding:18px
    }
    .page-title{
      display:flex;align-items:center;gap:10px;justify-content:flex-start;
      font-weight:800;color:var(--brand-600);letter-spacing:.2px;margin:4px 4px 18px
    }
    .page-title .icon{
      width:42px;height:42px;border-radius:12px;display:grid;place-items:center;
      background:var(--brand-50);color:var(--brand);font-size:20px
    }

    /* กล่อง Section */
    .section{border:1px solid var(--line);border-radius:14px;background:#fff;box-shadow:var(--shadow-sm);margin-bottom:14px}
    .section-head{
      display:flex;align-items:center;gap:8px;font-weight:700;color:var(--brand-600);
      background:var(--brand-50);padding:12px 14px;border-bottom:1px solid var(--line);
      border-top-left-radius:14px;border-top-right-radius:14px
    }
    .section-body{padding:14px}

    /* ฟอร์ม */
    .form-label{font-weight:600;color:var(--muted);margin-bottom:.35rem}
    .form-control,.form-select{border-radius:12px;border-color:var(--line);padding:.72rem .95rem}
    .form-control:focus,.form-select:focus{border-color:var(--brand);box-shadow:var(--focus)}
    .input-group-text{background:#f9fbff;border-color:var(--line);color:var(--muted)}

    /* แคปซูลตัวเลือก (ราคา/เช็คบ็อกซ์) */
    .chips{display:flex;flex-wrap:wrap;gap:.5rem}
    .btn-check+label{
      border-radius:999px;border:1px solid var(--line);box-shadow:var(--shadow-sm);
      padding:.42rem .78rem;font-weight:700
    }
    .btn-check:checked+label{outline:2px solid var(--brand);outline-offset:1px}
    .btn-outline-primary{background:#ecf3ff;color:#1d5ac2;border-color:#d6e6ff}
    .btn-outline-success{background:#eafaf2;color:#0f7b5c;border-color:#cfeee0}
    .btn-outline-warning{background:#fff6e6;color:#9a6400;border-color:#ffe2b8}
    .btn-outline-danger{background:#ffedf3;color:#b21e5f;border-color:#ffd5e4}
    .btn-outline-dark{background:#eef2f7;color:#2f3b4a;border-color:#e2e8f0}

    .chip{
      display:inline-flex;align-items:center;gap:.45rem;background:#f9fbff;border:1px solid var(--line);
      border-radius:999px;padding:.42rem .72rem
    }
    .chip input{margin-top:0}
    .chip span{font-weight:700;color:#41536e}

    /* แผนที่ */
    #map{height:380px;border:1px solid var(--line);border-radius:14px}
    .map-tools{display:flex;gap:.6rem;flex-wrap:wrap;margin:.5rem 0}
    .map-tools .btn{border-radius:999px}
    .alert{border-radius:12px}

    /* แกลเลอรีตัวอย่าง */
    .thumb{width:100px;height:100px;object-fit:cover;border-radius:12px;border:1px solid #eef3fb}

    /* ปุ่มบันทึก */
    .btn-primary{background:var(--brand);border-color:var(--brand)}
    .btn-primary:hover{background:var(--brand-600);border-color:var(--brand-600)}
    .btn-outline-secondary{border-radius:999px}
    .action-bar{background:#f9fbff;border:1px solid var(--line);border-radius:14px;padding:12px}

    @media (max-width:768px){
      .container{padding:14px}
      #map{height:320px}
      .action-bar .btn{flex:1}
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card-wrap">
    <div class="page-title">
      <div class="icon"><i class="bi bi-shop"></i></div>
      <h3 class="m-0">{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</h3>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill"></i> กรุณาตรวจสอบข้อมูล:</strong>
        <ul class="mb-0 ms-2">
          @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <form id="cafeForm" action="{{ isset($cafe) ? route('user.cafes.update',$cafe->id) : route('user.cafes.store') }}" method="POST" enctype="multipart/form-data">
      @csrf @if(isset($cafe)) @method('PUT') @endif

      <div class="row g-3">
        <!-- ซ้าย -->
        <div class="col-lg-6">
          <div class="section">
            <div class="section-head"><i class="bi bi-clipboard2-check"></i>ข้อมูลพื้นฐาน</div>
            <div class="section-body">
              <div class="mb-3">
                <label class="form-label">ชื่อคาเฟ่ <span class="text-danger">*</span></label>
                <input class="form-control @error('cafe_name') is-invalid @enderror" name="cafe_name" required
                       placeholder="เช่น คาเฟ่นม" value="{{ old('cafe_name',$cafe->cafe_name ?? '') }}">
                @error('cafe_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-3">
                <label class="form-label">รูปภาพคาเฟ่ <small class="text-muted">(สูงสุด 5 รูป • 5MB/รูป • รวม 20MB)</small></label>
                <input type="file" id="images" name="images[]" accept="image/*" multiple class="form-control @error('images.*') is-invalid @enderror">
                <div class="form-text">ระบบจะบีบอัด/ย่อรูปอัตโนมัติก่อนอัปโหลด</div>
                @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror>

                @if(isset($cafe) && is_array($cafe->images) && count($cafe->images))
                  <div class="mt-3 p-3" style="background:#f9fbff;border:1px solid var(--line);border-radius:12px">
                    <div class="d-flex flex-wrap gap-2">
                      @foreach($cafe->images as $img)
                        <img class="thumb" src="{{ asset('storage/'.$img) }}" alt="">
                      @endforeach
                    </div>
                    <small class="text-muted d-block mt-2">อัปโหลดใหม่เพื่อแทนที่รูปเดิม</small>
                  </div>
                @endif
              </div>

              <div class="mb-3">
                <label class="form-label">สถานะ</label>
                <div class="chips">
                  <label class="chip">
                    <input type="checkbox" name="is_new_opening" value="1" {{ old('is_new_opening',$cafe->is_new_opening ?? false) ? 'checked':'' }}>
                    <span>🌟 เปิดใหม่</span>
                  </label>
                </div>
              </div>

              <div class="mb-1">
                <label class="form-label">ช่วงราคา <span class="text-danger">*</span></label>
                <div class="chips">
                  @foreach(['ต่ำกว่า 100'=>'primary','101 - 250'=>'success','251 - 500'=>'warning','501 - 1,000'=>'danger','มากกว่า 1,000'=>'dark'] as $label=>$color)
                    @php $id='p'.$loop->index; @endphp
                    <input class="btn-check" id="{{ $id }}" type="radio" name="price_range" value="{{ $label }}"
                           {{ old('price_range',$cafe->price_range ?? '')==$label ? 'checked':'' }} required>
                    <label for="{{ $id }}" class="btn btn-outline-{{ $color }} btn-sm"><i class="bi bi-tags-fill me-1"></i>{{ $label }}</label>
                  @endforeach
                </div>
                @error('price_range')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="section">
            <div class="section-head"><i class="bi bi-palette2"></i>สไตล์คาเฟ่</div>
            <div class="section-body">
              <div class="chips mb-3">
                @foreach(['มินิมอล','วินเทจ','โมเดิร์น','อินดัสเทรียล','ธรรมชาติ/สวน','โคซี่/อบอุ่น','อาร์ต/แกลเลอรี่','ลอฟท์','ญี่ปุ่น','ยุโรป'] as $style)
                  @php $sid='s'.\Illuminate\Support\Str::slug($style); @endphp
                  <label class="chip" for="{{ $sid }}">
                    <input id="{{ $sid }}" type="checkbox" name="cafe_styles[]" value="{{ $style }}"
                           {{ in_array($style, old('cafe_styles',$cafe->cafe_styles ?? [])) ? 'checked':'' }}>
                    <span>{{ $style }}</span>
                  </label>
                @endforeach
              </div>

              <label class="form-label">สไตล์อื่น ๆ</label>
              <input class="form-control" name="other_style" placeholder="เช่น สไตล์ลึกลับ, สไตล์อนาคต"
                     value="{{ old('other_style',$cafe->other_style ?? '') }}">
            </div>
          </div>

          <div class="section">
            <div class="section-head"><i class="bi bi-person-lines-fill"></i>ข้อมูลติดต่อ</div>
            <div class="section-body">
              <div class="mb-3">
                <label class="form-label">เบอร์ติดต่อ</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                  <input class="form-control" name="phone" pattern="^\d{9,10}$" placeholder="เช่น 0812345678"
                         value="{{ old('phone',$cafe->phone ?? '') }}">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">อีเมล์</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                  <input class="form-control" type="email" name="email" placeholder="example@email.com"
                         value="{{ old('email',$cafe->email ?? '') }}">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">เว็บไซต์</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-globe"></i></span>
                  <input class="form-control" type="url" name="website" placeholder="https://www.yourcafe.com"
                         value="{{ old('website',$cafe->website ?? '') }}">
                </div>
              </div>

              <hr class="my-3">
              <label class="form-label fw-bold">Social Media</label>
              <div class="mb-2">
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                  <input class="form-control" name="facebook_page" placeholder="ลิงก์เพจ Facebook"
                         value="{{ old('facebook_page',$cafe->facebook_page ?? '') }}">
                </div>
              </div>
              <div class="mb-2">
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                  <input class="form-control" name="instagram_page" placeholder="ชื่อผู้ใช้ Instagram"
                         value="{{ old('instagram_page',$cafe->instagram_page ?? '') }}">
                </div>
              </div>
              <div>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-chat-dots"></i></span>
                  <input class="form-control" name="line_id" placeholder="Line ID หรือ @บัญชี"
                         value="{{ old('line_id',$cafe->line_id ?? '') }}">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ขวา -->
        <div class="col-lg-6">
          <div class="section">
            <div class="section-head"><i class="bi bi-geo-alt-fill"></i>ที่ตั้งและแผนที่</div>
            <div class="section-body">
              <div class="mb-3">
                <label class="form-label">ชื่อสถานที่ <span class="text-danger">*</span></label>
                <input class="form-control" name="place_name" required placeholder="ชื่ออาคาร, ชื่อโครงการ"
                       value="{{ old('place_name',$cafe->place_name ?? '') }}">
              </div>

              <div class="mb-3">
                <label class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
                <textarea class="form-control" name="address" rows="3" required
                          placeholder="บ้านเลขที่, ถนน, ตำบล, อำเภอ, จังหวัด, รหัสไปรษณีย์">{{ old('address',$cafe->address ?? '') }}</textarea>
              </div>

              <div class="map-tools">
                <button id="locateBtn" type="button" class="btn btn-sm btn-outline-primary"><i class="bi bi-geo-alt-fill me-1"></i>ใกล้ฉัน</button>
              </div>
              <div id="map" class="mb-2"></div>

              <div id="duplicateCoordsWarning" class="alert alert-warning d-none"><i class="bi bi-exclamation-triangle-fill me-1"></i>พิกัดนี้มีคาเฟ่อื่นใช้งานอยู่แล้ว</div>
              <div id="outOfBoundsWarning" class="alert alert-danger d-none"><i class="bi bi-geo-alt-fill me-1"></i>ตำแหน่งอยู่นอกเขตอำเภอเมืองสุรินทร์</div>

              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label">ละติจูด</label>
                  <input id="lat" name="lat" class="form-control" placeholder="จากแผนที่"
                         value="{{ old('lat',$cafe->lat ?? '') }}" required>
                </div>
                <div class="col-6">
                  <label class="form-label">ลองจิจูด</label>
                  <input id="lng" name="lng" class="form-control" placeholder="จากแผนที่"
                         value="{{ old('lng',$cafe->lng ?? '') }}" required>
                </div>
              </div>

              <div class="d-grid mt-2">
                <button id="resetBtn" type="button" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>รีเซ็ตตำแหน่ง</button>
              </div>
            </div>
          </div>

          <div class="section">
            <div class="section-head"><i class="bi bi-clock-history"></i>เวลาทำการ</div>
            <div class="section-body">
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label">วันเปิด</label>
                  <select class="form-select" name="open_day">
                    <option value="">-- เลือกวัน --</option>
                    @php $days=['ทุกวัน','จันทร์-ศุกร์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์']; @endphp
                    @foreach($days as $d)
                      <option value="{{ $d }}" {{ (isset($cafe)&&$cafe->open_day==$d)||old('open_day')==$d?'selected':'' }}>{{ $d }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">วันปิด</label>
                  <select class="form-select" name="close_day">
                    <option value="">ไม่มีวันปิด</option>
                    @foreach(['จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์'] as $d)
                      <option value="{{ $d }}" {{ (isset($cafe)&&$cafe->close_day==$d)||old('close_day')==$d?'selected':'' }}>{{ $d }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row g-2 mt-1">
                <div class="col-md-6">
                  <label class="form-label">⏰ เวลาเปิด</label>
                  <div class="input-group">
                    <input class="form-control" name="open_time" pattern="([01][0-9]|2[0-3]):[0-5][0-9]" placeholder="HH:MM (09:30)"
                           value="{{ old('open_time', isset($cafe)&&$cafe->open_time ? Carbon\Carbon::parse($cafe->open_time)->format('H:i') : '') }}">
                    <span class="input-group-text">น.</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">⏰ เวลาปิด</label>
                  <div class="input-group">
                    <input class="form-control" name="close_time" pattern="([01][0-9]|2[0-3]):[0-5][0-9]" placeholder="HH:MM (18:00)"
                           value="{{ old('close_time', isset($cafe)&&$cafe->close_time ? Carbon\Carbon::parse($cafe->close_time)->format('H:i') : '') }}">
                    <span class="input-group-text">น.</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="section">
            <div class="section-head"><i class="bi bi-stars"></i>บริการ & สิ่งอำนวยความสะดวก</div>
            <div class="section-body">
              <label class="form-label fw-bold">วิธีชำระเงิน</label>
              <div class="chips mb-2">
                @foreach(['เงินสด','บัตรเครดิต','บัตรเดบิต','จ่ายผ่านมือถือ','ไม่ระบุ'] as $p)
                  @php $pid='pay_'.\Illuminate\Support\Str::slug($p); @endphp
                  <label class="chip" for="{{ $pid }}">
                    <input id="{{ $pid }}" type="checkbox" name="payment_methods[]" value="{{ $p }}"
                           {{ in_array($p, old('payment_methods',$cafe->payment_methods ?? [])) ? 'checked':'' }}>
                    <span>{{ $p }}</span>
                  </label>
                @endforeach
              </div>

              <label class="form-label fw-bold mt-2">สิ่งอำนวยความสะดวก</label>
              <div class="chips mb-2">
                @foreach(['ห้องประชุม','โซนเด็กเล่น','ที่จอดรถ','เครื่องปรับอากาศ','Wi-Fi'] as $f)
                  @php $fid='facility_'.\Illuminate\Support\Str::slug($f); @endphp
                  <label class="chip" for="{{ $fid }}">
                    <input id="{{ $fid }}" type="checkbox" name="facilities[]" value="{{ $f }}"
                           {{ in_array($f, old('facilities',$cafe->facilities ?? [])) ? 'checked':'' }}>
                    <span>{{ $f }}</span>
                  </label>
                @endforeach
              </div>

              <label class="form-label fw-bold mt-2">บริการเพิ่มเติม</label>
              <div class="chips">
                @foreach(['ส่งเดลิเวอรี่','รับจัดงาน','ซื้อกลับบ้าน','รับจองโต๊ะ'] as $s)
                  @php $sid='srv_'.\Illuminate\Support\Str::slug($s); @endphp
                  <label class="chip" for="{{ $sid }}">
                    <input id="{{ $sid }}" type="checkbox" name="other_services[]" value="{{ $s }}"
                           {{ in_array($s, old('other_services',$cafe->other_services ?? [])) ? 'checked':'' }}>
                    <span>{{ $s }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="action-bar d-flex justify-content-center gap-3 mt-2">
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary px-4"><i class="bi bi-x-circle me-1"></i>ยกเลิก</a>
        <button id="submitBtn" type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>บันทึกข้อมูล</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
/* ---- บีบอัดรูปก่อนส่ง (5 รูป / รวม 20MB) ---- */
(function(){
  const MAX_FILES=5, MAX_PER=5*1024*1024, TARGET=1.5*1024*1024, MAX_TOTAL=20*1024*1024, MAX_DIM=1600;
  const $form=document.getElementById('cafeForm'), $inp=document.getElementById('images'), CSRF=document.querySelector('meta[name="csrf-token"]').content;

  $inp?.addEventListener('change',()=>{ if($inp.files.length>MAX_FILES){ alert('เลือกได้สูงสุด 5 รูป'); $inp.value=''; } });

  $form.addEventListener('submit', async (e)=>{
    if(!$inp || $inp.files.length===0) return;

    e.preventDefault();
    const files=[...$inp.files]; if(files.length>MAX_FILES){ alert('เลือกได้สูงสุด 5 รูป'); return; }

    const out=[]; let total=0;
    for(const f of files){
      const c=await compress(f,{maxDim:MAX_DIM,target:TARGET}); out.push(c); total+=c.blob.size;
      if(c.blob.size>MAX_PER){ alert(`ไฟล์ ${f.name} ยังเกิน 5MB หลังบีบอัด`); return; }
    }
    if(total>MAX_TOTAL){ alert('ขนาดไฟล์รวมเกิน 20MB'); return; }

    const fd=new FormData($form); fd.delete('images[]');
    out.forEach((o,i)=>{ const name=(files[i].name.replace(/\.[^.]+$/,'')||'image')+'-compressed.jpg'; fd.append('images[]',o.blob,name); });

    const btn=document.getElementById('submitBtn'); btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';
    try{
      const res=await fetch($form.action,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF},body:fd,redirect:'follow'});
      if(res.redirected){ location.href=res.url; return; }
      if(res.ok){ location.reload(); } else { alert('บันทึกไม่สำเร็จ'); }
    }catch{ alert('เกิดข้อผิดพลาดระหว่างอัปโหลด'); }
    finally{ btn.disabled=false; btn.innerHTML='<i class="bi bi-save me-1"></i>บันทึกข้อมูล'; }
  });

  async function compress(file,{maxDim=1600,target=TARGET}={}){
    const bmp=await readBitmap(file);
    const {w,h}=contain(bmp.width,bmp.height,maxDim);
    const c=document.createElement('canvas'); c.width=w; c.height=h;
    c.getContext('2d').drawImage(bmp,0,0,w,h);
    let q=0.9, blob=await toBlob(c,q), steps=[0.85,0.8,0.75,0.7,0.65,0.6];
    for(const s of steps){ if(blob.size<=target) break; q=s; blob=await toBlob(c,q); }
    return {blob};
  }
  function contain(w,h,max){ if(w<=max&&h<=max) return {w,h}; const r=Math.min(max/w,max/h); return {w:Math.round(w*r), h:Math.round(h*r)}; }
  function toBlob(c,q){ return new Promise(r=>c.toBlob(b=>r(b),'image/jpeg',q)); }
  async function readBitmap(file){
    if('createImageBitmap' in window) return await createImageBitmap(file);
    const url=await new Promise((res,rej)=>{ const fr=new FileReader(); fr.onload=()=>res(fr.result); fr.onerror=rej; fr.readAsDataURL(file); });
    const img=new Image(); img.decoding='async'; img.src=url; await img.decode();
    const cn=document.createElement('canvas'); cn.width=img.naturalWidth; cn.height=img.naturalHeight; cn.getContext('2d').drawImage(img,0,0);
    return {width:cn.width,height:cn.height, drawImage:(ctx,...a)=>ctx.drawImage(img,...a)};
  }
})();

/* ---- แผนที่ + ตรวจพิกัด ---- */
(function(){
  const lat=document.getElementById('lat'), lng=document.getElementById('lng');
  const warnDup=document.getElementById('duplicateCoordsWarning');
  const warnOOB=document.getElementById('outOfBoundsWarning');
  const locate=document.getElementById('locateBtn');

  const bounds=L.latLngBounds([[14.75,103.35],[15.00,103.65]]);
  const center=[14.885,103.490];
  const map=L.map('map',{scrollWheelZoom:true}).setView(center,12);
  map.setMaxBounds(bounds);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);

  const geocoder=L.Control.geocoder({geocoder:L.Control.Geocoder.nominatim(),defaultMarkGeocode:false,placeholder:'ค้นหาสถานที่…'})
    .on('markgeocode',e=>setPoint(e.geocode.center.lat,e.geocode.center.lng,true)).addTo(map);

  let marker=null;
  if(lat.value && lng.value){
    const la=parseFloat(lat.value), ln=parseFloat(lng.value);
    if(!isNaN(la)&&!isNaN(ln)){ marker=L.marker([la,ln]).addTo(map); map.setView([la,ln],15); if(!bounds.contains([la,ln])) showOOB(true); else checkDup(la,ln); }
  }

  map.on('click',e=>setPoint(e.latlng.lat,e.latlng.lng,true));
  locate.addEventListener('click',()=>{
    if(!navigator.geolocation) return alert('อุปกรณ์ไม่รองรับการระบุตำแหน่ง');
    navigator.geolocation.getCurrentPosition(p=>{
      const {latitude,longitude}=p.coords;
      if(!bounds.contains([latitude,longitude])){ alert('อยู่นอกเขตอำเภอเมืองสุรินทร์'); return; }
      setPoint(latitude,longitude,true);
    },()=>alert('ดึงตำแหน่งไม่ได้'),{enableHighAccuracy:true,timeout:8000});
  });

  document.getElementById('resetBtn').addEventListener('click',()=>{
    if(marker){ map.removeLayer(marker); marker=null; }
    lat.value=''; lng.value=''; showOOB(false); warnDup.classList.add('d-none'); map.setView(center,12);
  });

  function setPoint(la,ln,move){
    if(marker) marker.setLatLng([la,ln]); else marker=L.marker([la,ln]).addTo(map);
    lat.value=la.toFixed(6); lng.value=ln.toFixed(6);
    if(move) map.setView([la,ln],16);
    if(!bounds.contains([la,ln])){ showOOB(true); return; }
    showOOB(false); checkDup(la,ln);
  }
  function showOOB(s){ warnOOB.classList.toggle('d-none',!s); }
  async function checkDup(la,ln){
    try{
      const res=await fetch("{{ route('admin.cafe.check_coordinates') }}",{
        method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({lat:la,lng:ln,cafe_id:"{{ $cafe->id ?? 'null' }}"}),
      });
      const data=await res.json(); const dup=(data.exists ?? data.is_duplicate ?? false);
      warnDup.classList.toggle('d-none',!dup);
    }catch{}
  }
  setTimeout(()=>map.invalidateSize(),250);
})();
</script>
</body>
</html>
