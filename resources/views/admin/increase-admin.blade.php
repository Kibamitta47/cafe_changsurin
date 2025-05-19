@include('components.adminmenu')
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลคาเฟ่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

 
    
    <style>
    body {
        background-color: #f5f7fa;
        font-family: 'Sarabun', sans-serif;
        font-size: 0.9rem;
    }

    .form-section {
        background-color: #ffffff;
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .form-section h2 {
        border-left: 5px solid #0d6efd;
        padding-left: 15px;
        font-size: 1.2rem;
        margin-bottom: 15px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
        color: #444;
    }

    input.form-control,
    select.form-control,
    textarea.form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        box-shadow: none;
        transition: all 0.2s ease-in-out;
        font-size: 0.9rem;
        padding: 6px 10px;
    }

    input.form-control:focus,
    select.form-control:focus,
    textarea.form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .input-group .input-group-text {
        background-color: #e9ecef;
        border-radius: 8px 0 0 8px;
        font-size: 0.9rem;
    }

    .btn-primary {
        padding: 8px 18px;
        border-radius: 10px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .form-check-inline {
        margin-right: 20px;
    }

    .map-container {
        width: 100%;
        height: 200px;
        border-radius: 10px;
        border: 1px solid #ccc;
        margin-top: 10px;
    }

    @media (min-width: 768px) {
        .form-group.row {
            display: flex;
            gap: 20px;
        }
        .form-group.row > div {
            flex: 1;
        }
    }

    .style-options {
        border: 1px solid #ffffff;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .style-options h5 {
        color: #5c3d2e;
        font-weight: bold;
        margin-bottom: 12px;
        font-size: 1rem;
    }
</style>

    
</head>

<body>
    <div class="container mt-5">
        <h2>เพิ่มข้อมูลคาเฟ่</h2>
        <p>กรุณากรอกข้อมูลคาเฟ่ของคุณให้ครบถ้วนและถูกต้องเพื่อให้ผู้ใช้งานเว็บสามารถหาคุณเจอได้ง่ายขึ้น</p>

        <!-- ฟอร์มข้อมูลคาเฟ่ -->
        <form action="#" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- ข้อมูลพื้นฐาน -->
            <div class="form-section">
                <h2>ข้อมูลพื้นฐาน</h2>
                <div class="form-group">
                    <label for="cafe_name">ชื่อคาเฟ่</label>
                    <input type="text" class="form-control" id="cafe_name" name="cafe_name" required>
                </div>
                <div class="form-group">
                    <label for="cafe_image">อัพโหลดรูปภาพ</label>
                    <input type="file" class="form-control" id="cafe_image" name="cafe_image[]" accept="image/*" multiple>
                </div>
            
            <div class="form-group">
                <label class="mb-2">ช่วงราคา (บาท)</label><br>
                <div class="d-flex flex-wrap gap-2">
                <input type="radio" class="btn-check" name="price_range" id="price1" value="ต่ำกว่า 100" required autocomplete="off">
                <label class="btn btn-outline-primary" for="price1" data-price="ต่ำกว่า 100 บาท"><i class="bi bi-currency-bitcoin"></i></label>
                <input type="radio" class="btn-check" name="price_range" id="price2" value="101 - 250" required autocomplete="off">
                <label class="btn btn-outline-success" for="price2" data-price="101 - 250 บาท"><i class="bi bi-currency-bitcoin"></i></label>
                <input type="radio" class="btn-check" name="price_range" id="price3" value="251 - 500" required autocomplete="off">
                <label class="btn btn-outline-warning" for="price3" data-price="251 - 500 บาท"><i class="bi bi-currency-bitcoin"></i></label>
                <input type="radio" class="btn-check" name="price_range" id="price4" value="501 - 1,000" required autocomplete="off">
                <label class="btn btn-outline-danger" for="price4" data-price="501 - 1,000 บาท"><i class="bi bi-currency-bitcoin"></i></label>
                <input type="radio" class="btn-check" name="price_range" id="price5" value="มากกว่า 1,000" required autocomplete="off">
                <label class="btn btn-outline-dark" for="price5" data-price="มากกว่า 1,000 บาท"><i class="bi bi-currency-bitcoin"></i></label>
            </div>
                <div id="selected-price" class="mt-3 text-primary fw-bold"></div>
            </div>
                <script>
                    const labels = document.querySelectorAll('label');
            
                    labels.forEach(label => {
                    label.addEventListener('mouseenter', () => {
                        const price = label.getAttribute('data-price');
                        const display = document.getElementById('selected-price');
                        display.innerHTML = `<i class="bi bi-cash-coin me-1"></i>ราคาช่วง: ${price}`;});
                    });
                </script>
            </div>

           <!-- ที่อยู่ -->
    <div class="form-section">
        <h2>ที่อยู่</h2>
        <div class="form-group">
            <label for="address">ที่อยู่</label>
            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="province">จังหวัด</label>
            <select class="form-control" id="province" name="province" required>
                <option value="">-- เลือกจังหวัด --</option>
            </select>
        </div>
        <div class="form-group">
            <label for="district">เขต / อำเภอ</label>
            <select class="form-control" id="district" name="district" required>
                <option value="">-- เลือกเขต/อำเภอ --</option>
            </select>
        </div>
        <div class="form-group">
            <label for="subdistrict">แขวง / ตำบล</label>
            <select class="form-control" id="subdistrict" name="subdistrict" required>
                <option value="">-- เลือกแขวง/ตำบล --</option>
            </select>
        </div>
            <script>
                const data = {
                    "กรุงเทพมหานคร": {
                        "บางรัก": ["มหาพฤฒาราม", "สีลม", "สุริยวงศ์"],
                        "ปทุมวัน": ["รองเมือง", "วังใหม่"]
                    },
                    "สุรินทร์": {
                        "เมืองสุรินทร์": ["ในเมือง", "ตั้งใจ"],
                        "รัตนบุรี": ["รัตนบุรี", "หนองเทพ"]
                    }
                };

                const provinceSelect = document.getElementById("province");
                const districtSelect = document.getElementById("district");
                const subdistrictSelect = document.getElementById("subdistrict");

                window.onload = function () {
                    for (let province in data) {
                        provinceSelect.innerHTML += `<option value="${province}">${province}</option>`;
                    }
                };

                provinceSelect.addEventListener("change", function () {
                    const districts = data[this.value];
                    districtSelect.innerHTML = '<option value="">-- เลือกเขต/อำเภอ --</option>';
                    subdistrictSelect.innerHTML = '<option value="">-- เลือกแขวง/ตำบล --</option>';
                    if (districts) {
                        for (let district in districts) {
                            districtSelect.innerHTML += `<option value="${district}">${district}</option>`;
                        }
                    }
                });

                districtSelect.addEventListener("change", function () {
                    const subdistricts = data[provinceSelect.value][this.value];
                    subdistrictSelect.innerHTML = '<option value="">-- เลือกแขวง/ตำบล --</option>';
                    if (subdistricts) {
                        for (let sub of subdistricts) {
                            subdistrictSelect.innerHTML += `<option value="${sub}">${sub}</option>`;
                        }
                    }
                });
            </script>

    <!-- แผนที่ -->
    <div class="form-group">
        <label for="search-location">ค้นหาชื่อสถานที่</label>
        <input type="text" class="form-control" id="search-location" placeholder="ค้นหาชื่อสถานที่" required>
    </div>
        <div class="map-container" id="map"></div>
        <small>กรุณาเลื่อนหมุดในแผนที่เพื่อเลือกตำแหน่งก่อน submit</small>
    </div>

            <!-- ข้อมูลติดต่อ -->
            <div class="form-section">
                <h2>ข้อมูลติดต่อ</h2>
<div class="form-group row">
    <div>
        <label for="phone">📞 เบอร์ติดต่อ</label>
        <input type="text" class="form-control" id="phone" name="phone" pattern="^\d{10}$">
    </div>
    <div>
        <label for="email">📧 อีเมล์</label>
        <input type="email" class="form-control" id="email" name="email">
    </div>
</div>

<div class="form-group">
    <label for="website">🌐 Website</label>
    <input type="text" class="form-control" id="website" name="website">
</div>
<div class="form-group">
    <label for="facebook_page">📘Facebook Page</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">https://www.facebook.com/</span>
        </div>
        <input type="text" class="form-control" id="facebook_page" name="facebook_page" placeholder="ชื่อเพจ">
    </div>
</div>
<div class="form-group">
    <label for="instagram_page">📸 Instagram</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">https://www.instagram.com/</span>
        </div>
        <input type="text" class="form-control" id="instagram_page" name="instagram_page" placeholder="ชื่อบัญชี Instagram">
    </div>
</div>
<div class="form-group">
    <label for="line">💬LINE@</label>
    <input type="text" class="form-control" id="line" name="line">
</div>

<!-- ข้อมูลเพิ่มเติม -->
<div class="form-section">
    <h2>ข้อมูลเพิ่มเติม</h2>
    <div class="form-group">
        <label for="open_day">📅 วันที่เปิดให้บริการ</label>
        <select class="form-control" id="open_day" name="open_day">
            <option value="">-- เลือกวัน --</option>
            <option value="ทุกวัน">ทุกวัน</option>
            <option value="จันทร์-ศุกร์">จันทร์-ศุกร์</option>
            <option value="จันทร์">จันทร์</option>
            <option value="อังคาร">อังคาร</option>
            <option value="พุธ">พุธ</option>
            <option value="พฤหัสบดี">พฤหัสบดี</option>
            <option value="ศุกร์">ศุกร์</option>
            <option value="เสาร์">เสาร์</option>
            <option value="อาทิตย์">อาทิตย์</option>
        </select>
    </div>
    <div class="form-group">
        <label for="open_time">⏰ เวลาเปิด</label>
        <input type="time" class="form-control" id="open_time" name="open_time">
    </div>
    <div class="form-group">
        <label for="close_time">⏰ เวลาปิด</label>
        <input type="time" class="form-control" id="close_time" name="close_time">
    </div>
    <div class="style-options">
        <h5>🎨 สไตล์ของร้าน</h5>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_minimal" name="cafe_style[]" value="มินิมอล">
            <label class="form-check-label" for="style_minimal">มินิมอล</label>
        </div>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_loft" name="cafe_style[]" value="ลอฟท์">
            <label class="form-check-label" for="style_loft">ลอฟท์</label>
        </div>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_vintage" name="cafe_style[]" value="วินเทจ">
            <label class="form-check-label" for="style_vintage">วินเทจ</label>
        </div>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_nature" name="cafe_style[]" value="ธรรมชาติ">
            <label class="form-check-label" for="style_nature">ธรรมชาติ</label>
        </div>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_japanese" name="cafe_style[]" value="ญี่ปุ่น">
            <label class="form-check-label" for="style_japanese">ญี่ปุ่น</label>
        </div>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_korean" name="cafe_style[]" value="เกาหลี">
            <label class="form-check-label" for="style_korean">เกาหลี</label>
        </div>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_moroccan" name="cafe_style[]" value="โมรอคโค">
            <label class="form-check-label" for="style_moroccan">โมรอคโค</label>
        </div>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_colorful" name="cafe_style[]" value="สดใส / หวาน ๆ">
            <label class="form-check-label" for="style_colorful">สดใส / หวาน ๆ</label>
        </div>
    
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="style_modern" name="cafe_style[]" value="โมเดิร์น">
            <label class="form-check-label" for="style_modern">โมเดิร์น</label>
        </div>
    
        <!-- ช่องกรอกข้อมูลเพิ่มเติม -->
        <div class="form-group mt-3">
            <label for="other_style">สไตล์คาเฟ่เพิ่มเติม (ถ้ามี)</label>
            <input type="text" class="form-control" id="other_style" name="other_style" placeholder="กรุณาระบุสไตล์เพิ่มเติม">
        </div>
    </div>
    
    <div class="form-group">
        <label class="mb-2">🚗 ที่จอดรถ</label>
        <div class="form-check form-check-inline">
            <input type="checkbox" id="parking_on_site" name="parking[]" value="มีในร้าน" class="form-check-input">
            <label for="parking_on_site" class="form-check-label">มีในร้าน</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="checkbox" id="parking_nearby" name="parking[]" value="มีใกล้เคียง" class="form-check-input">
            <label for="parking_nearby" class="form-check-label">มีใกล้เคียง</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="checkbox" id="parking_none" name="parking[]" value="ไม่มี" class="form-check-input">
            <label for="parking_none" class="form-check-label">ไม่มี</label>
        </div>
    </div>
    <div class="form-group">
        <label class="mb-2">💳 รับบัตรเครดิต</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="credit_unspecified" name="credit_card" value="ไม่ระบุ">
            <label class="form-check-label" for="credit_unspecified">ไม่ระบุ</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="credit_yes" name="credit_card" value="รับ">
            <label class="form-check-label" for="credit_yes">ใช่</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="credit_no" name="credit_card" value="ไม่รับ">
            <label class="form-check-label" for="credit_no">ไม่ใช่</label>
        </div>
    </div>
    
    <div class="form-group">
        <label class="mb-2">👥 สำหรับกลุ่ม</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="group_unspecified" name="for_group" value="ไม่ระบุ">
            <label class="form-check-label" for="group_unspecified">ไม่ระบุ</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="group_yes" name="for_group" value="ใช่">
            <label class="form-check-label" for="group_yes">ใช่</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="group_no" name="for_group" value="ไม่ใช่">
            <label class="form-check-label" for="group_no">ไม่ใช่</label>
        </div>
    </div>
    
    <div class="form-group">
        <label class="mb-2">🧒 สำหรับเด็ก</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="kids_unspecified" name="for_kids" value="ไม่ระบุ">
            <label class="form-check-label" for="kids_unspecified">ไม่ระบุ</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="kids_yes" name="for_kids" value="ใช่">
            <label class="form-check-label" for="kids_yes">ใช่</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="kids_no" name="for_kids" value="ไม่ใช่">
            <label class="form-check-label" for="kids_no">ไม่ใช่</label>
        </div>
    </div>
    
    <div class="form-group">
        <label class="mb-2">🐶 สัตว์เลี้ยงเข้าได้</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="pet_unspecified" name="pet_allowed" value="ไม่ระบุ">
            <label class="form-check-label" for="pet_unspecified">ไม่ระบุ</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="pet_yes" name="pet_allowed" value="ใช่">
            <label class="form-check-label" for="pet_yes">ใช่</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="pet_no" name="pet_allowed" value="ไม่ใช่">
            <label class="form-check-label" for="pet_no">ไม่ใช่</label>
        </div>
    </div>    
</div>

                
         <div class="d-flex justify-content-end gap-2 mt-3">
  <a href="/home-admin" class="btn btn-secondary">
    <i class="fas fa-times me-1"></i> ยกเลิก
  </a>
  <button type="submit" class="btn btn-primary">
    เพิ่มคาเฟ่
  </button>
</div>

            </div>
        </form>
    </div>

    <!-- ใส่ Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places&callback=initMap" async defer></script>
    
    <script>
        let map;
        let marker;
        let geocoder;
        let searchBox;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 13.736717, lng: 100.523186 }, // กำหนดตำแหน่งเริ่มต้น (กรุงเทพฯ)
                zoom: 12,
            });

            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: map.getCenter(),
            });

            geocoder = new google.maps.Geocoder();
            searchBox = new google.maps.places.SearchBox(document.getElementById("search-location"));

            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length === 0) return;
                map.setCenter(places[0].geometry.location);
                map.setZoom(15);
                marker.setPosition(places[0].geometry.location);
            });

            google.maps.event.addListener(marker, 'dragend', function () {
                geocoder.geocode({ 'location': marker.getPosition() }, function (results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            document.getElementById("address").value = results[0].formatted_address;
                        }
                    }
                });
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
</div>