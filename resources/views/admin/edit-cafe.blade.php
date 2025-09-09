<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ isset($cafe) ? 'แก้ไขข้อมูลคาเฟ่' : 'เพิ่มข้อมูลคาเฟ่ใหม่' }} | ระบบจัดการ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        .form-check-label {
            font-weight: 600;
            color: #333;
        }
        :root {
            --primary-blue: #3b82f6;
            --dark-blue: #2563eb;
            --red-error: #dc2626;
            --red-dark: #b91c1c;
            --gray-light: #e5e7eb;
            --gray-text: #333;
            --white: #ffffff;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --transition-ease: all 0.3s ease-in-out;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            padding-top: 20px;
            padding-bottom: 20px;
            transition: padding-left 0.3s ease;
        }

        body.sidebar-open {
            padding-left: 250px;
        }

        .container.mt-5.mb-5 {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
        }

        h2 {
            color: var(--dark-blue);
            border-bottom: 2px solid var(--gray-light);
            padding-bottom: 15px;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }

        .form-control, .form-select, .input-group-text {
            border-radius: 8px;
            border-color: #ced4da;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
            border-color: var(--primary-blue);
        }

        .form-check-inline {
            margin-right: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .btn-check + .btn {
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.95rem;
        }

        .btn-outline-primary { --bs-btn-color: #0d6efd; --bs-btn-border-color: #0d6efd; --bs-btn-hover-bg: #0d6efd; --bs-btn-hover-border-color: #0d6efd; }
        .btn-outline-success { --bs-btn-color: #198754; --bs-btn-border-color: #198754; --bs-btn-hover-bg: #198754; --bs-btn-hover-border-color: #198754; }
        .btn-outline-warning { --bs-btn-color: #ffc107; --bs-btn-border-color: #ffc107; --bs-btn-hover-bg: #ffc107; --bs-btn-hover-border-color: #ffc107; }
        .btn-outline-danger { --bs-btn-color: #dc3545; --bs-btn-border-color: #dc3545; --bs-btn-hover-bg: #dc3545; --bs-btn-hover-border-color: #dc3545; }
        .btn-outline-dark { --bs-btn-color: #212529; --bs-btn-border-color: #212529; --bs-btn-hover-bg: #212529; --bs-btn-hover-border-color: #212529; }

        #map {
            height: 400px;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .leaflet-container {
            background: #e9ecef;
            border-radius: 8px;
        }

        .form-control[type="file"] {
            padding: 0.75rem 1rem;
        }

        .d-flex.justify-content-end.gap-2.mt-3 {
            padding-top: 20px;
            border-top: 1px solid var(--gray-light);
            margin-top: 30px !important;
        }
        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-primary:hover {
            background-color: var(--dark-blue);
            border-color: var(--dark-blue);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-secondary:hover {
            background-color: #5c636a;
            border-color: #5c636a;
            transform: translateY(-1px);
        }
        .btn-outline-secondary {
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-outline-secondary:hover {
            transform: translateY(-1px);
        }

        .form-text {
            font-size: 0.875em;
            color: #6c757d;
            margin-top: 5px;
        }

        .col-md-6 label {
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            body.sidebar-open {
                padding-left: 0;
            }
            .container.mt-5.mb-5 {
                padding: 20px;
                margin-top: 20px !important;
                margin-bottom: 20px !important;
            }
            h2 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }
            .btn-check + .btn {
                display: block;
                width: 100%;
                margin-right: 0 !important;
                margin-bottom: 0.5rem !important;
            }
            .d-flex.justify-content-end.gap-2.mt-3 {
                flex-direction: column;
                align-items: stretch;
            }
            .d-flex.justify-content-end.gap-2.mt-3 .btn {
                width: 100%;
            }
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
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
        @if(isset($cafe))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cafe_name" class="form-label">ชื่อคาเฟ่ <span class="text-danger">*</span></label>
                    <input type="text" id="cafe_name" name="cafe_name" class="form-control @error('cafe_name') is-invalid @enderror" placeholder="ชื่อคาเฟ่เต็ม" required value="{{ old('cafe_name', $cafe->cafe_name ?? '') }}" />
                    @error('cafe_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">สถานะ</label><br />
                    <div class="form-check form-check-inline">
                        <input type="checkbox" id="new_opening" name="is_new_opening" value="1" class="form-check-input"
                            {{ old('is_new_opening', $cafe->is_new_opening ?? false) ? 'checked' : '' }} />
                        <label for="new_opening" class="form-check-label">เปิดใหม่</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">รูปภาพคาเฟ่ <span class="text-muted">(สูงสุด 5 รูป)</span></label>
                    <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
                    <div class="form-text">เลือกรูปภาพได้หลายรูป (JPG, PNG)</div>
                    @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if(isset($cafe) && !empty($cafe->images) && is_array($cafe->images))
                        <div class="mt-3">
                            <p class="form-label">รูปภาพที่มีอยู่:</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($cafe->images as $index => $imagePath)
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $imagePath) }}" alt="Cafe Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text mt-2">หากอัปโหลดรูปภาพใหม่ รูปภาพที่มีอยู่จะถูกแทนที่</div>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">ช่วงราคา <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach([
                            'ต่ำกว่า 100' => 'primary',
                            '101 - 250' => 'success',
                            '251 - 500' => 'warning',
                            '501 - 1,000' => 'danger',
                            'มากกว่า 1,000' => 'dark'
                        ] as $label => $color)
                            @php $id = 'price' . $loop->index; @endphp
                            <input type="radio" class="btn-check" name="price_range" id="{{ $id }}" value="{{ $label }}" required autocomplete="off"
                                {{ old('price_range', $cafe->price_range ?? '') == $label ? 'checked' : '' }} />
                            <label for="{{ $id }}" class="btn btn-outline-{{ $color }}"><i class="bi bi-currency-bitcoin"></i> {{ $label }}</label>
                        @endforeach
                    </div>
                    @error('price_range')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <h5 class="mt-4 mb-3 text-primary">สไตล์คาเฟ่</h5>
                <div class="mb-3 d-flex flex-wrap gap-2">
                    @foreach([
                        'มินิมอล', 'วินเทจ', 'โมเดิร์น', 'อินดัสเทรียล', 'ธรรมชาติ/สวน',
                        'โคซี่/อบอุ่น', 'อาร์ต/แกลเลอรี่', 'ลอฟท์', 'ญี่ปุ่น', 'ยุโรป'
                    ] as $style)
                        @php $id = 'style_' . \Illuminate\Support\Str::slug($style); @endphp
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{{ $id }}" name="cafe_styles[]" value="{{ $style }}" class="form-check-input"
                                {{ in_array($style, old('cafe_styles', $cafe->cafe_styles ?? [])) ? 'checked' : '' }} />
                            <label for="{{ $id }}" class="form-check-label">{{ $style }}</label>
                        </div>
                    @endforeach
                </div>

                <h5 class="mt-4 mb-3 text-primary">สไตล์อื่นๆ</h5>
                <div class="mb-3">
                    <label for="other_style" class="form-label">ระบุสไตล์อื่นๆ ที่ไม่มีในรายการ</label>
                    <input type="text" id="other_style" name="other_style" class="form-control @error('other_style') is-invalid @enderror" placeholder="เช่น สไตล์ลึกลับ, สไตล์อนาคต"
                        value="{{ old('other_style', $cafe->other_style ?? '') }}" />
                    @error('other_style')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">สามารถระบุสไตล์เพิ่มเติมได้</div>
                </div>

                <h5 class="mt-4 mb-3 text-primary">ข้อมูลติดต่อ</h5>
                <div class="mb-3">
                    <label for="phone" class="form-label">📞 เบอร์ติดต่อ</label>
                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" pattern="^\d{9,10}$" placeholder="เช่น 0812345678"
                        value="{{ old('phone', $cafe->phone ?? '') }}" />
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">📧 อีเมล์</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="เช่น example@email.com"
                        value="{{ old('email', $cafe->email ?? '') }}" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">🌐 เว็บไซต์</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-globe"></i></span>
                        <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" placeholder="เช่น https://www.yourcafe.com"
                            value="{{ old('website', $cafe->website ?? '') }}" />
                    </div>
                    @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <h5 class="mt-4 mb-3 text-primary">Social Media</h5>
                <div class="mb-3">
                    <label class="form-label">📘 Facebook</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                        <input type="text" name="facebook_page" class="form-control @error('facebook_page') is-invalid @enderror" placeholder="ชื่อผู้ใช้ หรือ ลิงก์เพจ"
                            value="{{ old('facebook_page', $cafe->facebook_page ?? '') }}" />
                    </div>
                    @error('facebook_page')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">📸 Instagram</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                        <input type="text" name="instagram_page" class="form-control @error('instagram_page') is-invalid @enderror" placeholder="ชื่อผู้ใช้"
                            value="{{ old('instagram_page', $cafe->instagram_page ?? '') }}" />
                    </div>
                    @error('instagram_page')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">📱 Line</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-line"></i></span>
                        <input type="text" name="line_id" class="form-control @error('line_id') is-invalid @enderror" placeholder="ID Line หรือ @บัญชี"
                            value="{{ old('line_id', $cafe->line_id ?? '') }}" />
                    </div>
                    @error('line_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="col-md-6">
                <h5 class="mb-3 text-primary">ที่ตั้ง</h5>
                <div class="mb-3">
                    <label for="place_name" class="form-label">ชื่อสถานที่ <span class="text-danger">*</span></label>
                    <input type="text" id="place_name" name="place_name" class="form-control @error('place_name') is-invalid @enderror" placeholder="ชื่ออาคาร, ชื่อโครงการ" required
                        value="{{ old('place_name', $cafe->place_name ?? '') }}" />
                        @error('place_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
                    <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="บ้านเลขที่, ถนน, ตำบล, อำเภอ, จังหวัด, รหัสไปรษณีย์" required>{{ old('address', $cafe->address ?? '') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                        <label for="lat" class="form-label">ละติจูด <small class="text-muted">(Latitude)</small></label>
                        <input type="text" id="lat" name="lat" class="form-control @error('lat') is-invalid @enderror" placeholder="คลิกบนแผนที่ หรือ พิมพ์เอง" required
                            value="{{ old('lat', $cafe->lat ?? '') }}" />
                        @error('lat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="lng" class="form-label">ลองจิจูด <small class="text-muted">(Longitude)</small></label>
                        <input type="text" id="lng" name="lng" class="form-control @error('lng') is-invalid @enderror" placeholder="คลิกบนแผนที่ หรือ พิมพ์เอง" required
                            value="{{ old('lng', $cafe->lng ?? '') }}" />
                        @error('lng')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                            @php
                                $days = ['ทุกวัน', 'จันทร์-ศุกร์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'];
                            @endphp
                            @foreach($days as $day)
                                <option value="{{ $day }}" {{ (isset($cafe) && $cafe->open_day == $day) || old('open_day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                        @error('open_day')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="close_day" class="form-label">📅 วันปิด <small class="text-muted">(หากไม่มี ให้เลือก "ไม่มีวันปิด")</small></label>
                        <select class="form-select @error('close_day') is-invalid @enderror" id="close_day" name="close_day">
                            <option value="">ไม่มีวันปิด</option>
                            @php
                                $closeDays = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'];
                            @endphp
                            @foreach($closeDays as $day)
                                <option value="{{ $day }}" {{ (isset($cafe) && $cafe->close_day == $day) || old('close_day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                        @error('close_day')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="open_time" class="form-label">⏰ เวลาเปิด</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control @error('open_time') is-invalid @enderror"
                                   id="open_time"
                                   name="open_time"
                                   pattern="([01][0-9]|2[0-3]):[0-5][0-9]"
                                   placeholder="HH:MM (ตัวอย่าง: 09:30)"
                                   value="{{ old('open_time', isset($cafe) && $cafe->open_time ? Carbon\Carbon::parse($cafe->open_time)->format('H:i') : '') }}">
                            <span class="input-group-text">น.</span>
                        </div>
                        @error('open_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="close_time" class="form-label">⏰ เวลาปิด</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control @error('close_time') is-invalid @enderror"
                                   id="close_time"
                                   name="close_time"
                                   pattern="([01][0-9]|2[0-3]):[0-5][0-9]"
                                   placeholder="HH:MM (ตัวอย่าง: 18:00)"
                                   value="{{ old('close_time', isset($cafe) && $cafe->close_time ? Carbon\Carbon::parse($cafe->close_time)->format('H:i') : '') }}">
                            <span class="input-group-text">น.</span>
                        </div>
                        @error('close_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mt-4 mb-3 text-primary">วิธีชำระเงิน</h5>
                <div class="mb-3 d-flex flex-wrap gap-2">
                    @foreach(['เงินสด', 'บัตรเครดิต', 'บัตรเดบิต', 'จ่ายผ่านมือถือ', 'ไม่ระบุ'] as $payment)
                        @php $id = 'pay_' . \Illuminate\Support\Str::slug($payment); @endphp
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{{ $id }}" name="payment_methods[]" value="{{ $payment }}" class="form-check-input"
                                {{ in_array($payment, old('payment_methods', $cafe->payment_methods ?? [])) ? 'checked' : '' }} />
                            <label for="{{ $id }}" class="form-check-label">{{ $payment }}</label>
                        </div>
                    @endforeach
                </div>

                <h5 class="mt-4 mb-3 text-primary">สิ่งอำนวยความสะดวก</h5>
                <div class="mb-3 d-flex flex-wrap gap-2">
                    @foreach(['ห้องประชุม', 'โซนเด็กเล่น', 'ที่จอดรถ', 'เครื่องปรับอากาศ', 'Wi-Fi'] as $facility)
                        @php $id = 'facility_' . \Illuminate\Support\Str::slug($facility); @endphp
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{{ $id }}" name="facilities[]" value="{{ $facility }}" class="form-check-input"
                                {{ in_array($facility, old('facilities', $cafe->facilities ?? [])) ? 'checked' : '' }} />
                            <label for="{{ $id }}" class="form-check-label">{{ $facility }}</label>
                        </div>
                    @endforeach
                </div>

                <h5 class="mt-4 mb-3 text-primary">บริการเพิ่มเติม</h5>
                <div class="mb-3 d-flex flex-wrap gap-2">
                    @foreach(['ส่งเดลิเวอรี่', 'รับจัดงาน', 'ซื้อกลับบ้าน', 'รับจองโต๊ะ'] as $service)
                        @php $id = 'service_' . \Illuminate\Support\Str::slug($service); @endphp
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{{ $id }}" name="other_services[]" value="{{ $service }}" class="form-check-input"
                                {{ in_array($service, old('other_services', $cafe->other_services ?? [])) ? 'checked' : '' }} />
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
    // จำกัดอัปโหลดรูปไม่เกิน 5 รูป
    const imageInput = document.getElementById('images');
    const cafeForm = document.getElementById('cafeForm');

    imageInput.addEventListener('change', () => {
        if (imageInput.files.length > 5) {
            alert('เลือกได้สูงสุด 5 รูปภาพเท่านั้น');
            imageInput.value = '';
        }
    });

    cafeForm.addEventListener('submit', function(e) {
        if (imageInput.files.length > 5) {
            e.preventDefault();
            alert('กรุณาอัปโหลดรูปภาพไม่เกิน 5 รูปเท่านั้น');
            imageInput.focus();
            return;
        }

        if (!document.getElementById('duplicateCoordsWarning').classList.contains('d-none') ||
            !document.getElementById('outOfBoundsWarning').classList.contains('d-none')) {
            e.preventDefault();
            alert('โปรดแก้ไขข้อผิดพลาดเกี่ยวกับพิกัดก่อนบันทึกข้อมูล');
            return;
        }
    });

    // Leaflet Map Initialization
    const map = L.map('map');
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const duplicateCoordsWarning = document.getElementById('duplicateCoordsWarning');
    const outOfBoundsWarning = document.getElementById('outOfBoundsWarning');
    const submitBtn = document.getElementById('submitBtn');

    // Bounds & center for Amphoe Mueang Surin
    const mueangSurinBounds = L.latLngBounds([[14.75, 103.35], [15.00, 103.65]]);
    const mueangSurinCenter = [14.885, 103.490];

    map.setView(mueangSurinCenter, 12);
    map.setMaxBounds(mueangSurinBounds);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let marker;

    // สำหรับหน้าแก้ไข: ถ้ามีค่า lat/lng เดิม ให้แสดง marker บนแผนที่
    if (latInput.value && lngInput.value) {
        const initial = L.latLng(parseFloat(latInput.value), parseFloat(lngInput.value));
        if (mueangSurinBounds.contains(initial)) {
            marker = L.marker(initial).addTo(map);
            map.setView(initial, 15);
            checkCoordinates(initial.lat, initial.lng);
        } else {
            marker = L.marker(initial).addTo(map);
            map.setView(initial, 15);
            displayOutOfBoundsWarning(true);
        }
    } else {
        map.setView(mueangSurinCenter, 12);
    }

    // เพิ่ม Geocoder สำหรับการค้นหา
    L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "ค้นหาที่อยู่...",
        collapsed: true,
        suggestMinLength: 3,
        geocodingQueryParams: {
            'viewbox': `${mueangSurinBounds.getSouthWest().lng},${mueangSurinBounds.getSouthWest().lat},${mueangSurinBounds.getNorthEast().lng},${mueangSurinBounds.getNorthEast().lat}`,
            'bounded': 1
        }
    })
    .on('markgeocode', function(e) {
        const bbox = e.geocode.bbox;
        const center = e.geocode.center;

        if (mueangSurinBounds.contains(center)) {
            if (marker) map.removeLayer(marker);
            marker = L.marker(center).addTo(map);
            if (bbox && bbox.isValid && typeof bbox.isValid === 'function' && bbox.isValid()) {
                map.fitBounds(bbox);
            } else {
                map.setView(center, 15);
            }

            latInput.value = center.lat.toFixed(6);
            lngInput.value = center.lng.toFixed(6);
            displayOutOfBoundsWarning(false);
            checkCoordinates(center.lat, center.lng);
        } else {
            alert('ตำแหน่งที่ค้นหาอยู่นอกเขตอำเภอเมืองสุรินทร์ กรุณาลองค้นหาใหม่');
            displayOutOfBoundsWarning(true);
        }
    })
    .addTo(map);

    // คลิกบนแผนที่
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        if (mueangSurinBounds.contains(e.latlng)) {
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
            displayOutOfBoundsWarning(false);
            checkCoordinates(lat, lng);
        } else {
            alert('ตำแหน่งที่เลือกอยู่นอกเขตอำเภอเมืองสุรินทร์ กรุณาเลือกภายในขอบเขต');
            displayOutOfBoundsWarning(true);
        }
    });

    // ปุ่มรีเซ็ตตำแหน่ง
    document.getElementById('resetBtn').addEventListener('click', function() {
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
        latInput.value = '';
        lngInput.value = '';
        displayOutOfBoundsWarning(false);
        duplicateCoordsWarning.classList.add('d-none');
        submitBtn.disabled = false;
    });

    // พิมพ์ค่าพิกัดเอง
    latInput.addEventListener('input', validateAndCheckCoordinates);
    lngInput.addEventListener('input', validateAndCheckCoordinates);

    function displayOutOfBoundsWarning(show) {
        if (show) {
            outOfBoundsWarning.classList.remove('d-none');
            submitBtn.disabled = true;
        } else {
            outOfBoundsWarning.classList.add('d-none');
        }
    }

    // ตรวจสอบพิกัดซ้ำกับฐานข้อมูล
    async function checkCoordinates(lat, lng) {
        const currentCafeId = "{{ $cafe->id ?? 'null' }}"; // ใช้ id แทน cafe_id

        if (isNaN(lat) || isNaN(lng)) {
            duplicateCoordsWarning.classList.add('d-none');
            submitBtn.disabled = true;
            return;
        }

        try {
            const response = await fetch('{{ route('admin.cafe.check_coordinates') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ lat: lat, lng: lng, cafe_id: currentCafeId })
            });

            const data = await response.json();

            const isDup = (typeof data.is_duplicate !== 'undefined') ? data.is_duplicate : (data.exists ?? false);

            if (isDup) {
                duplicateCoordsWarning.classList.remove('d-none');
                submitBtn.disabled = true;
            } else {
                duplicateCoordsWarning.classList.add('d-none');
                if (outOfBoundsWarning.classList.contains('d-none')) {
                    submitBtn.disabled = false;
                }
            }
        } catch (error) {
            console.error('Error checking coordinates:', error);
            duplicateCoordsWarning.classList.add('d-none');
            if (outOfBoundsWarning.classList.contains('d-none')) {
                submitBtn.disabled = false;
            }
        }
    }

    function validateAndCheckCoordinates() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);

        if (isNaN(lat) || isNaN(lng)) {
            displayOutOfBoundsWarning(false);
            duplicateCoordsWarning.classList.add('d-none');
            submitBtn.disabled = true;
            return;
        }

        const currentLatLng = L.latLng(lat, lng);

        if (!mueangSurinBounds.contains(currentLatLng)) {
            displayOutOfBoundsWarning(true);
            duplicateCoordsWarning.classList.add('d-none');
            submitBtn.disabled = true;
            return;
        } else {
            displayOutOfBoundsWarning(false);
        }

        if (marker) {
            marker.setLatLng(currentLatLng);
            map.setView(currentLatLng, 15);
        } else {
            marker = L.marker(currentLatLng).addTo(map);
            map.setView(currentLatLng, 15);
        }

        checkCoordinates(lat, lng);
    }
</script>
</body>
</html>
