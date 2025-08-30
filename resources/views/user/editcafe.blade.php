<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Title จะเปลี่ยนไปตามหน้าว่าเป็นหน้าเพิ่มหรือแก้ไข -->
    <title>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        /* --- Root Variables (Consistent Design) --- */
        :root {
            --primary-color: #4A90E2;
            --primary-dark: #357ABD;
            --secondary-color: #6c757d;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
            --text-color: #343a40;
            --white: #ffffff;
            --error-color: #dc3545;
            --success-color: #198754;
            --font-family: 'Sarabun', 'Inter', sans-serif;
            --border-radius: 0.75rem;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: var(--font-family);
            background-color: #f4f7f9;
            color: var(--text-color);
            padding: 1rem;
        }

        .form-container {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 1.5rem;
            max-width: 1200px;
            margin: auto;
        }

        .form-title {
            color: var(--primary-dark);
            font-weight: 700;
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .form-title .fa-coffee, .form-title .fa-edit { font-size: 1.6rem; }

        .form-section {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }

        h5.section-header {
            color: var(--primary-dark);
            font-weight: 600;
            margin: -1rem -1rem 1rem -1rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-gray);
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label { font-weight: 600; color: #555; margin-bottom: 0.5rem; }
        .form-control, .form-select {
            border-radius: 0.5rem;
            border-color: var(--border-color);
            padding: 0.75rem 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
            border-color: var(--primary-color);
        }

        .input-group-text {
            background-color: var(--light-gray);
            border-color: var(--border-color);
            border-radius: 0.5rem 0 0 0.5rem;
        }
        .input-group > .form-control { border-radius: 0 0.5rem 0.5rem 0 !important; }
        .input-group > .input-group-text:last-child { border-radius: 0 0.5rem 0.5rem 0; }

        .form-check-group .form-check {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            transition: border-color 0.2s, background-color 0.2s;
        }
        .form-check-group .form-check:hover { background-color: var(--light-gray); border-color: var(--primary-color); }
        .form-check-input:checked { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-check:checked+.btn { transform: scale(1.05); box-shadow: 0 0 10px rgba(74, 144, 226, 0.4); }

        /* Map + toolbar */
        .map-toolbar { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:.5rem }
        #map {
            height: 380px;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .image-preview-container {
            background-color: var(--light-gray);
            border-radius: 0.5rem;
            padding: 1rem;
            border: 2px dashed var(--border-color);
        }
        .image-preview-container img {
            border: 2px solid var(--white);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn { font-weight: 600; padding: 0.7rem 1.2rem; border-radius: 0.5rem; display:inline-flex; align-items:center; gap:.5rem }
        .btn:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--primary-dark); border-color: var(--primary-dark); }

        .alert { border-radius: 0.5rem; }

        @media (max-width: 768px) {
            .form-container { padding: 1rem; }
            .form-title { font-size: 1.25rem; margin-bottom: 1rem; }
            .form-section { padding: 0.9rem; }
            h5.section-header { padding: 0.6rem 0.9rem; margin: -0.9rem -0.9rem 0.9rem -0.9rem; }
            #map { height: 300px; }
            .actions .btn { flex:1; }
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
        @if(isset($cafe))
            @method('PUT')
        @endif

            <div class="row">
                <!-- ========== Left Column ========== -->
                <div class="col-lg-6">
                    <div class="form-section">
                        <h5 class="section-header"><i class="bi bi-shop"></i>ข้อมูลพื้นฐาน</h5>
                        <div class="mb-3">
                            <label for="cafe_name" class="form-label">ชื่อคาเฟ่ <span class="text-danger">*</span></label>
                            <input type="text" id="cafe_name" name="cafe_name" class="form-control @error('cafe_name') is-invalid @enderror" placeholder="ระบุชื่อคาเฟ่" required value="{{ old('cafe_name', $cafe->cafe_name ?? '') }}" />
                            @error('cafe_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">รูปภาพคาเฟ่ <span class="text-muted">(สูงสุด 5 รูป)</span></label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
                            <div class="form-text">เลือกรูปภาพเพื่อแทนที่ของเดิม</div>
                            @error('images.*') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            @if(isset($cafe) && !empty($cafe->images) && is_array($cafe->images))
                                <div class="mt-3 image-preview-container">
                                    <p class="form-label mb-2">รูปภาพที่มีอยู่:</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($cafe->images as $imagePath)
                                            <img src="{{ asset('storage/' . $imagePath) }}" alt="Cafe Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.5rem;">
                                        @endforeach
                                    </div>
                                    <div class="form-text mt-2">หากอัปโหลดรูปภาพใหม่ รูปภาพชุดเดิมจะถูกแทนที่</div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <div class="form-check-group d-flex">
                                <div class="form-check">
                                    <input type="checkbox" id="new_opening" name="is_new_opening" value="1" class="form-check-input" {{ old('is_new_opening', $cafe->is_new_opening ?? false) ? 'checked' : '' }} />
                                    <label for="new_opening" class="form-check-label">🌟 เปิดใหม่</label>
                                </div>
                             </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ช่วงราคา <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['ต่ำกว่า 100' => 'primary', '101 - 250' => 'success', '251 - 500' => 'warning', '501 - 1,000' => 'danger', 'มากกว่า 1,000' => 'dark'] as $label => $color)
                                    @php $id = 'price' . $loop->index; @endphp
                                    <input type="radio" class="btn-check" name="price_range" id="{{ $id }}" value="{{ $label }}" required autocomplete="off" {{ old('price_range', $cafe->price_range ?? '') == $label ? 'checked' : '' }} />
                                    <label for="{{ $id }}" class="btn btn-outline-{{ $color }}"><i class="bi bi-tags-fill"></i> {{ $label }}</label>
                                @endforeach
                            </div>
                            @error('price_range') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-header"><i class="bi bi-palette-fill"></i>สไตล์คาเฟ่</h5>
                        <div class="mb-3 form-check-group d-flex flex-wrap">
                            @foreach(['มินิมอล', 'วินเทจ', 'โมเดิร์น', 'อินดัสเทรียล', 'ธรรมชาติ/สวน', 'โคซี่/อบอุ่น', 'อาร์ต/แกลเลอรี่', 'ลอฟท์', 'ญี่ปุ่น', 'ยุโรป'] as $style)
                                @php $id = 'style_' . \Illuminate\Support\Str::slug($style); @endphp
                                <div class="form-check">
                                    <input type="checkbox" id="{{ $id }}" name="cafe_styles[]" value="{{ $style }}" class="form-check-input" {{ in_array($style, old('cafe_styles', $cafe->cafe_styles ?? [])) ? 'checked' : '' }} />
                                    <label for="{{ $id }}" class="form-check-label">{{ $style }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label for="other_style" class="form-label">ระบุสไตล์อื่นๆ</label>
                            <input type="text" id="other_style" name="other_style" class="form-control @error('other_style') is-invalid @enderror" placeholder="เช่น สไตล์ลึกลับ, สไตล์อนาคต" value="{{ old('other_style', $cafe->other_style ?? '') }}" />
                            @error('other_style') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-header"><i class="bi bi-person-lines-fill"></i>ข้อมูลติดต่อ</h5>
                        <div class="mb-3">
                            <label for="phone" class="form-label">เบอร์ติดต่อ</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" pattern="^\d{9,10}$" placeholder="เช่น 0812345678" value="{{ old('phone', $cafe->phone ?? '') }}" />
                            </div>
                            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">อีเมล์</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="เช่น example@email.com" value="{{ old('email', $cafe->email ?? '') }}" />
                            </div>
                             @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">เว็บไซต์</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" placeholder="https://www.yourcafe.com" value="{{ old('website', $cafe->website ?? '') }}" />
                            </div>
                            @error('website')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        
                        <hr class="my-4">
                        <label class="form-label fw-bold">โซเชียลมีเดีย</label>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                <input type="text" name="facebook_page" class="form-control @error('facebook_page') is-invalid @enderror" placeholder="ลิงก์ Facebook Page" value="{{ old('facebook_page', $cafe->facebook_page ?? '') }}" />
                            </div>
                             @error('facebook_page')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                             <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                <input type="text" name="instagram_page" class="form-control @error('instagram_page') is-invalid @enderror" placeholder="ชื่อผู้ใช้ Instagram" value="{{ old('instagram_page', $cafe->instagram_page ?? '') }}" />
                            </div>
                             @error('instagram_page')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-line"></i></span>
                                <input type="text" name="line_id" class="form-control @error('line_id') is-invalid @enderror" placeholder="Line ID หรือ @บัญชี" value="{{ old('line_id', $cafe->line_id ?? '') }}" />
                            </div>
                            @error('line_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- ========== Right Column ========== -->
                <div class="col-lg-6">
                    <div class="form-section">
                        <h5 class="section-header"><i class="bi bi-geo-alt-fill"></i>ที่ตั้งและแผนที่</h5>
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

                        <!-- Toolbar: ค้นหา + ใกล้ฉัน -->
                        <div class="map-toolbar">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="locateBtn">
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

                        <div class="row mt-3">
                            <div class="col-sm-6">
                                <label for="lat" class="form-label">ละติจูด</label>
                                <input type="text" id="lat" name="lat" class="form-control @error('lat') is-invalid @enderror" placeholder="จากแผนที่" required value="{{ old('lat', $cafe->lat ?? '') }}" />
                                @error('lat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="lng" class="form-label">ลองจิจูด</label>
                                <input type="text" id="lng" name="lng" class="form-control @error('lng') is-invalid @enderror" placeholder="จากแผนที่" required value="{{ old('lng', $cafe->lng ?? '') }}" />
                                @error('lng')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-outline-secondary" id="resetBtn">
                                <i class="bi bi-arrow-counterclockwise"></i> รีเซ็ตตำแหน่ง
                            </button>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-header"><i class="bi bi-clock-history"></i>เวลาทำการ</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="open_day" class="form-label">วันเปิด</label>
                                <select class="form-select @error('open_day') is-invalid @enderror" id="open_day" name="open_day">
                                    <option value="">-- เลือกวัน --</option>
                                    @php $days = ['ทุกวัน', 'จันทร์-ศุกร์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์']; @endphp
                                    @foreach($days as $day)<option value="{{ $day }}" {{ old('open_day', $cafe->open_day ?? '') == $day ? 'selected' : '' }}>{{ $day }}</option>@endforeach
                                </select>
                                @error('open_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="close_day" class="form-label">วันปิด</label>
                                <select class="form-select @error('close_day') is-invalid @enderror" id="close_day" name="close_day">
                                    <option value="">ไม่มีวันปิด</option>
                                    @php $closeDays = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์']; @endphp
                                    @foreach($closeDays as $day)<option value="{{ $day }}" {{ old('close_day', $cafe->close_day ?? '') == $day ? 'selected' : '' }}>{{ $day }}</option>@endforeach
                                </select>
                                 @error('close_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
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
                        <div class="mb-3 form-check-group d-flex flex-wrap">
                            @foreach(['เงินสด', 'บัตรเครดิต', 'บัตรเดบิต', 'จ่ายผ่านมือถือ', 'ไม่ระบุ'] as $payment)
                                <div class="form-check">
                                    <input type="checkbox" id="pay_{{ \Illuminate\Support\Str::slug($payment) }}" name="payment_methods[]" value="{{ $payment }}" class="form-check-input" {{ in_array($payment, old('payment_methods', $cafe->payment_methods ?? [])) ? 'checked' : '' }} />
                                    <label for="pay_{{ \Illuminate\Support\Str::slug($payment) }}" class="form-check-label">{{ $payment }}</label>
                                </div>
                            @endforeach
                        </div>
                        <label class="form-label fw-bold mt-2">สิ่งอำนวยความสะดวก</label>
                        <div class="mb-3 form-check-group d-flex flex-wrap">
                            @foreach(['ห้องประชุม', 'โซนเด็กเล่น', 'ที่จอดรถ', 'เครื่องปรับอากาศ', 'Wi-Fi'] as $facility)
                                <div class="form-check">
                                    <input type="checkbox" id="facility_{{ \Illuminate\Support\Str::slug($facility) }}" name="facilities[]" value="{{ $facility }}" class="form-check-input" {{ in_array($facility, old('facilities', $cafe->facilities ?? [])) ? 'checked' : '' }} />
                                    <label for="facility_{{ \Illuminate\Support\Str::slug($facility) }}" class="form-check-label">{{ $facility }}</label>
                                </div>
                            @endforeach
                        </div>
                        <label class="form-label fw-bold mt-2">บริการเพิ่มเติม</label>
                        <div class="mb-3 form-check-group d-flex flex-wrap">
                            @foreach(['ส่งเดลิเวอรี่', 'รับจัดงาน', 'ซื้อกลับบ้าน', 'รับจองโต๊ะ'] as $service)
                                <div class="form-check">
                                    <input type="checkbox" id="service_{{ \Illuminate\Support\Str::slug($service) }}" name="other_services[]" value="{{ $service }}" class="form-check-input" {{ in_array($service, old('other_services', $cafe->other_services ?? [])) ? 'checked' : '' }} />
                                    <label for="service_{{ \Illuminate\Support\Str::slug($service) }}" class="form-check-label">{{ $service }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions d-flex justify-content-center gap-3 mt-4 pt-4 border-top">
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

        const mueangSurinBounds = L.latLngBounds([[14.75, 103.35], [15.00, 103.65]]);
        const mueangSurinCenter = [14.885, 103.490]; 
        const map = L.map('map', { scrollWheelZoom: true, tap: true }).setView(mueangSurinCenter, 12); 
        map.setMaxBounds(mueangSurinBounds); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // ====== ค้นหาบนแผนที่ (Leaflet Control Geocoder) ======
        const geocoderControl = L.Control.geocoder({
            geocoder: L.Control.Geocoder.nominatim(),
            defaultMarkGeocode: false,
            placeholder: 'ค้นหาสถานที่หรือที่อยู่…',
            errorMessage: 'ไม่พบสถานที่'
        })
        .on('markgeocode', function (e) {
            const c = e.geocode.center;
            applyPoint(c.lat, c.lng, true);
        })
        .addTo(map);

        let marker;

        // ปักหมุดจากค่าที่มีอยู่
        if (latInput.value && lngInput.value) {
            const initialLatLng = [parseFloat(latInput.value), parseFloat(lngInput.value)];
            if (!isNaN(initialLatLng[0]) && !isNaN(initialLatLng[1])) {
                marker = L.marker(initialLatLng).addTo(map);
                map.setView(initialLatLng, 15);
                if (mueangSurinBounds.contains(initialLatLng)) {
                    checkCoordinates(initialLatLng[0], initialLatLng[1]);
                } else {
                    displayOutOfBoundsWarning(true);
                }
            }
        }

        // เลือกพิกัดด้วยการคลิก
        map.on('click', function(e) {
            const { lat, lng } = e.latlng;
            applyPoint(lat, lng, true);
        });

        // ปุ่มใกล้ฉัน
        locateBtn.addEventListener('click', () => {
            if (!navigator.geolocation) { alert('อุปกรณ์ไม่รองรับการระบุตำแหน่ง'); return; }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const { latitude, longitude } = pos.coords;
                    if (!mueangSurinBounds.contains([latitude, longitude])) {
                        alert('ตำแหน่งปัจจุบันอยู่นอกเขตอำเภอเมืองสุรินทร์');
                        map.setView(mueangSurinCenter, 13);
                        return;
                    }
                    applyPoint(latitude, longitude, true);
                },
                (err) => { console.error(err); alert('ไม่สามารถดึงตำแหน่งปัจจุบันได้'); },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        });

        document.getElementById('resetBtn').addEventListener('click', function() {
            if (marker) { map.removeLayer(marker); marker = null; }
            latInput.value = '';
            lngInput.value = '';
            displayOutOfBoundsWarning(false);
            duplicateCoordsWarning.classList.add('d-none');
            submitBtn.disabled = false;
            map.setView(mueangSurinCenter, 12);
        });

        latInput.addEventListener('input', handleManual);
        lngInput.addEventListener('input', handleManual);

        function handleManual() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (isNaN(lat) || isNaN(lng)) { submitBtn.disabled = true; return; }
            applyPoint(lat, lng, true);
        }

        function applyPoint(lat, lng, moveMap=false) {
            const inside = mueangSurinBounds.contains([lat, lng]);
            if (marker) marker.setLatLng([lat, lng]); else marker = L.marker([lat, lng]).addTo(map);
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
            displayOutOfBoundsWarning(!inside);
            submitBtn.disabled = !inside;
            if (inside) checkCoordinates(lat, lng);
            if (moveMap) map.setView([lat, lng], inside ? 16 : 15);
        }

        function displayOutOfBoundsWarning(show) {
            outOfBoundsWarning.classList.toggle('d-none', !show);
        }

        async function checkCoordinates(lat, lng) {
            if (!lat || !lng) {
                duplicateCoordsWarning.classList.add('d-none');
                submitBtn.disabled = false;
                return;
            }
            const cafeId = "{{ $cafe->id ?? 'null' }}"; 
            try {
                const response = await fetch("{{ route('admin.cafe.check_coordinates') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ lat: lat, lng: lng, cafe_id: cafeId }) 
                });
                const data = await response.json();
                const isDuplicate = data.exists ?? data.is_duplicate ?? false;
                duplicateCoordsWarning.classList.toggle('d-none', !isDuplicate);
                submitBtn.disabled = isDuplicate || !mueangSurinBounds.contains([lat, lng]);
            } catch (error) {
                console.error('Error checking coordinates:', error);
                submitBtn.disabled = false;
            }
        }

        // ให้แผนที่จัดขนาดตัวเองใหม่เมื่อโหลด/เปลี่ยนขนาดจอ (ช่วยเรื่อง mobile)
        setTimeout(()=> map.invalidateSize(), 300);
        window.addEventListener('resize', ()=> map.invalidateSize());
    });
</script>
</body>
</html>
