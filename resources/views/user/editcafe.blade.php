<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root{
            --primary-color:#4A90E2;
            --primary-dark:#357ABD;
            --secondary-color:#6c757d;
            --light-gray:#f8f9fa;
            --border-color:#dee2e6;
            --text-color:#1f3b65;
            --ink:#343a40;
            --white:#fff;
            --font-family:'Sarabun','Inter',sans-serif;
            --radius:.75rem;
            --shadow:0 4px 6px rgba(0,0,0,.05),0 1px 3px rgba(0,0,0,.08);
            --shadow-lg:0 10px 15px -3px rgba(0,0,0,.07),0 4px 6px -2px rgba(0,0,0,.05);
        }

        body{font-family:var(--font-family);background:#f4f7f9;color:var(--ink);padding:1rem}
        .form-container{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:1.5rem;max-width:1200px;margin:auto}

        .form-title{
            color:var(--primary-dark);font-weight:700;text-align:center;margin-bottom:1.25rem;
            padding-bottom:.75rem;border-bottom:2px solid var(--border-color);display:flex;align-items:center;justify-content:center;gap:.75rem
        }
        .form-title .fa-edit{font-size:1.6rem}

        /* ======= หัวข้อแบบภาพแนบ (สี่เหลี่ยมฟ้า + ข้อความ) ======= */
        .head-label{
            display:flex;align-items:center;gap:8px;
            margin:14px 0 10px;font-size:1rem;font-weight:800;color:var(--text-color);letter-spacing:.1px
        }
        .head-label .chip{
            width:12px;height:12px;border-radius:2px;background:var(--primary-color);
            box-shadow:0 .5px 0 rgba(0,0,0,.06);flex:0 0 12px
        }
        .section-block{padding:6px 0 2px;border-top:1px solid var(--border-color)}

        /* กล่องส่วนฟอร์ม */
        .form-section{background:var(--white);border:1px solid var(--border-color);border-radius:var(--radius);padding:1rem;margin-bottom:1rem;box-shadow:var(--shadow)}

        /* ฟอร์มคอนโทรล */
        .form-label{font-weight:600;color:#555;margin-bottom:.5rem}
        .form-control,.form-select{border-radius:.5rem;border-color:var(--border-color);padding:.75rem 1rem;transition:border-color .2s,box-shadow .2s}
        .form-control:focus,.form-select:focus{box-shadow:0 0 0 .25rem rgba(74,144,226,.25);border-color:var(--primary-color)}
        .input-group-text{background:var(--light-gray);border-color:var(--border-color)}
        .input-group>.form-control{border-radius:0 .5rem .5rem 0!important}

        /* กลุ่ม checkbox เป็นชิป */
        .form-check-group .form-check{
            padding:.5rem 1rem;border:1px solid var(--border-color);border-radius:.5rem;margin-right:.5rem;margin-bottom:.5rem;transition:background-color .2s,border-color .2s
        }
        .form-check-group .form-check:hover{background:var(--light-gray);border-color:var(--primary-color)}
        .form-check-input:checked{background-color:var(--primary-color);border-color:var(--primary-color)}
        .btn-check:checked+.btn{transform:scale(1.05);box-shadow:0 0 10px rgba(74,144,226,.4)}

        /* แผนที่ */
        .map-toolbar{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.5rem}
        #map{height:380px;border-radius:var(--radius);border:1px solid var(--border-color);margin-bottom:1rem}

        .image-preview-container{background:var(--light-gray);border-radius:.5rem;padding:1rem;border:2px dashed var(--border-color)}
        .image-preview-container img{border:2px solid var(--white);box-shadow:0 2px 4px rgba(0,0,0,.1)}

        .btn{font-weight:600;padding:.7rem 1.2rem;border-radius:.5rem;display:inline-flex;align-items:center;gap:.5rem}
        .btn:hover{transform:translateY(-2px);box-shadow:var(--shadow)}
        .btn-primary{background:var(--primary-color);border-color:var(--primary-color)}
        .btn-primary:hover{background:var(--primary-dark);border-color:var(--primary-dark)}
        .alert{border-radius:.5rem}

        @media (max-width:768px){
            .form-container{padding:1rem}
            .form-title{font-size:1.25rem;margin-bottom:1rem}
            .form-section{padding:.9rem}
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
        <h2 class="form-title"><i class="fas fa-edit"></i>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</h2>

        <form action="{{ isset($cafe) ? route('user.cafes.update', $cafe) : route('user.cafes.store') }}" method="POST" enctype="multipart/form-data" class="p-4 rounded" id="cafeForm">
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
                    @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror

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
                <div id="map" class="mb-3 position-relative"></div>

                <div class="d-grid mb-3">
                    <button type="button" class="btn btn-outline-secondary" id="resetBtn">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> รีเซ็ตตำแหน่งบนแผนที่
                    </button>
                </div>

                <div class="row mb-4">
                    <div class="col">
                        <label for="lat" class="form-label">ละติจูด</label>
                        <input type="text" id="lat" name="lat" class="form-control @error('lat') is-invalid @enderror" required value="{{ old('lat', $cafe->lat ?? '') }}" />
                        @error('lat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col">
                        <label for="lng" class="form-label">ลองจิจูด</label>
                        <input type="text" id="lng" name="lng" class="form-control @error('lng') is-invalid @enderror" required value="{{ old('lng', $cafe->lng ?? '') }}" />
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

            <div class="actions d-flex justify-content-center gap-3 mt-4 pt-4 border-top">
                <a href="{{ route('user.cafes.my') }}" class="btn btn-outline-secondary px-4">
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
        const imageInput = document.getElementById('images');
        const cafeForm = document.getElementById('cafeForm');

        imageInput?.addEventListener('change', () => {
            if (imageInput.files.length > 5) {
                alert('เลือกได้สูงสุด 5 รูปภาพเท่านั้น');
                imageInput.value = '';
            }
        });

        cafeForm.addEventListener('submit', function(e) {
            if (imageInput && imageInput.files.length > 5) {
                e.preventDefault();
                alert('กรุณาอัปโหลดรูปภาพไม่เกิน 5 รูปเท่านั้น');
                return;
            }
            if (!document.getElementById('duplicateCoordsWarning').classList.contains('d-none') ||
                !document.getElementById('outOfBoundsWarning').classList.contains('d-none')) {
                e.preventDefault();
                alert('โปรดแก้ไขข้อผิดพลาดเกี่ยวกับพิกัดก่อนบันทึกข้อมูล');
                return;
            }
        });

        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');
        const duplicateCoordsWarning = document.getElementById('duplicateCoordsWarning');
        const outOfBoundsWarning = document.getElementById('outOfBoundsWarning');
        const submitBtn = document.getElementById('submitBtn');
        const locateBtn = document.getElementById('locateBtn');

        const mueangSurinBounds = L.latLngBounds([[14.75,103.35],[15.00,103.65]]);
        const mueangSurinCenter = [14.885,103.490];
        const map = L.map('map',{scrollWheelZoom:true,tap:true}).setView(mueangSurinCenter,12);
        map.setMaxBounds(mueangSurinBounds);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap contributors'}).addTo(map);

        const geocoderControl = L.Control.geocoder({
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
            const initial = [parseFloat(latInput.value), parseFloat(lngInput.value)];
            if (!isNaN(initial[0]) && !isNaN(initial[1])) {
                marker = L.marker(initial).addTo(map);
                map.setView(initial, 15);
                if (mueangSurinBounds.contains(initial)) { checkCoordinates(initial[0], initial[1]); }
                else { displayOutOfBoundsWarning(true); }
            }
        }

        map.on('click', e => applyPoint(e.latlng.lat, e.latlng.lng, true));

        locateBtn.addEventListener('click', ()=>{
            if (!navigator.geolocation){ alert('อุปกรณ์ไม่รองรับการระบุตำแหน่ง'); return; }
            navigator.geolocation.getCurrentPosition(
                pos=>{
                    const {latitude, longitude} = pos.coords;
                    if (!mueangSurinBounds.contains([latitude, longitude])) {
                        alert('ตำแหน่งปัจจุบันอยู่นอกเขตอำเภอเมืองสุรินทร์');
                        map.setView(mueangSurinCenter, 13);
                        return;
                    }
                    applyPoint(latitude, longitude, true);
                },
                err=>{ console.error(err); alert('ไม่สามารถดึงตำแหน่งปัจจุบันได้'); },
                {enableHighAccuracy:true, timeout:8000}
            );
        });

        document.getElementById('resetBtn')?.addEventListener('click', ()=>{
            if (marker){ map.removeLayer(marker); marker=null; }
            latInput.value=''; lngInput.value='';
            displayOutOfBoundsWarning(false);
            duplicateCoordsWarning.classList.add('d-none');
            submitBtn.disabled=false;
            map.setView(mueangSurinCenter,12);
        });

        latInput.addEventListener('input', handleManual);
        lngInput.addEventListener('input', handleManual);

        function handleManual(){
            const lat=parseFloat(latInput.value), lng=parseFloat(lngInput.value);
            if (isNaN(lat)||isNaN(lng)){ submitBtn.disabled=true; return; }
            applyPoint(lat,lng,true);
        }

        function applyPoint(lat,lng,move=false){
            const inside = mueangSurinBounds.contains([lat,lng]);
            if (marker) marker.setLatLng([lat,lng]); else marker = L.marker([lat,lng]).addTo(map);
            latInput.value = lat.toFixed(6); lngInput.value = lng.toFixed(6);
            displayOutOfBoundsWarning(!inside);
            submitBtn.disabled = !inside;
            if (inside) checkCoordinates(lat,lng);
            if (move) map.setView([lat,lng], inside?16:15);
        }

        function displayOutOfBoundsWarning(show){ outOfBoundsWarning.classList.toggle('d-none', !show); }

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
                const isDup = data.exists ?? data.is_duplicate ?? false;
                duplicateCoordsWarning.classList.toggle('d-none', !isDup);
                submitBtn.disabled = isDup || !mueangSurinBounds.contains([lat, lng]);
            }catch(err){
                console.error('Error checking coordinates:', err);
                submitBtn.disabled=false;
            }
        }

        setTimeout(()=> map.invalidateSize(), 300);
        window.addEventListener('resize', ()=> map.invalidateSize());
    });
</script>
</body>
</html>
