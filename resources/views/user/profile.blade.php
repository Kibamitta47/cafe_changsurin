<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>จัดการโปรไฟล์ - CafeFinder</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Kanit', sans-serif; padding-top: 90px; background:#F8F9FA; }
    .profile-card { background:#fff; border-radius:.75rem; box-shadow:0 10px 15px -3px rgb(0 0 0 / .1), 0 4px 6px -4px rgb(0 0 0 / .1); }
    .profile-avatar { position:relative; width:150px; height:150px; margin:0 auto 1rem; }
    .profile-avatar img, .profile-avatar .avatar-placeholder {
      width:100%; height:100%; border-radius:50%; object-fit:cover;
      border:4px solid #fff; box-shadow:0 4px 6px -1px rgb(0 0 0 / .1);
    }
    .avatar-placeholder { display:flex; align-items:center; justify-content:center; background:#eef2f7; color:#c0c7d1; font-size:4rem; }
    .avatar-upload-button { position:absolute; bottom:6px; right:6px; width:42px; height:42px; border-radius:50%; background:#0D6EFD; color:#fff; border:2px solid #fff; display:flex; align-items:center; justify-content:center; cursor:pointer; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('user.dashboard') }}">CafeFinder</a>
  </div>
</nav>

<div class="container my-5">
  <div class="text-center mb-4">
    <h1 class="fw-bold">จัดการโปรไฟล์</h1>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="profile-card p-4 p-md-5">
    <form id="profileForm" method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="row g-5">
        <!-- รูป -->
        <div class="col-md-4 text-center">
          <div class="profile-avatar">
            @if($user->profile_image)
              <img id="imagePreview" src="{{ asset('storage/'.$user->profile_image) }}" alt="รูปโปรไฟล์">
            @else
              <div id="imagePreviewPlaceholder" class="avatar-placeholder">
                <i class="bi bi-person-fill"></i>
              </div>
              <img id="imagePreview" class="d-none" alt="รูปโปรไฟล์">
            @endif
            <label for="profile_image" class="avatar-upload-button" title="อัปโหลดรูป">
              <i class="bi bi-camera-fill"></i>
            </label>
            <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
          </div>
          <div id="imgHint" class="small text-danger d-none mt-2"></div>
        </div>

        <!-- ชื่อ -->
        <div class="col-md-8">
          <div class="mb-3">
            <label for="name" class="form-label">ชื่อผู้ใช้</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                   class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
              <i class="bi bi-save-fill me-2"></i>บันทึกการเปลี่ยนแปลง
            </button>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/**
 * บีบอัดรูปฝั่ง client ด้วย <canvas> ให้ไม่เกิน MAX_BYTES
 * - ลดด้านยาวสุดไม่เกิน MAX_DIM
 * - ลดคุณภาพแบบไล่ระดับจนกว่าจะ < MAX_BYTES (อย่างน้อย 0.5)
 */
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('profile_image');
  const preview = document.getElementById('imagePreview');
  const placeholder = document.getElementById('imagePreviewPlaceholder');
  const form = document.getElementById('profileForm');
  const hint = document.getElementById('imgHint');

  const MAX_BYTES = 3 * 1024 * 1024;   // 3MB ให้ตรงกับ Laravel validation
  const MAX_DIM = 1600;                // ด้านยาวสุดไม่เกิน 1600px
  const MIN_QUALITY = 0.5;

  async function compressImage(file) {
    // ถ้าไฟล์เล็กอยู่แล้วก็คืนไฟล์เดิม
    if (file.size <= MAX_BYTES) return file;

    const img = new Image();
    const dataURL = await fileToDataURL(file);
    img.src = dataURL;
    await img.decode();

    // คำนวณขนาดใหม่รักษาอัตราส่วน
    let { width, height } = img;
    const scale = Math.min(1, MAX_DIM / Math.max(width, height));
    width = Math.round(width * scale);
    height = Math.round(height * scale);

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0, width, height);

    // พยายามบีบอัดเป็น JPEG ไล่คุณภาพลงทีละนิด
    let quality = 0.85;
    let blob = await canvasToBlob(canvas, 'image/jpeg', quality);

    while (blob.size > MAX_BYTES && quality > MIN_QUALITY) {
      quality = Math.max(MIN_QUALITY, quality - 0.1);
      blob = await canvasToBlob(canvas, 'image/jpeg', quality);
    }

    // สร้างไฟล์ใหม่ชื่อ friendly
    const name = (file.name || 'avatar').replace(/\.[^.]+$/, '') + '.jpg';
    return new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() });
  }

  function fileToDataURL(file) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = e => resolve(e.target.result);
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
  }

  function canvasToBlob(canvas, type, quality) {
    return new Promise(resolve => canvas.toBlob(b => resolve(b), type, quality));
  }

  function replaceInputFile(inputEl, newFile) {
    const dt = new DataTransfer();
    dt.items.add(newFile);
    inputEl.files = dt.files;
  }

  input.addEventListener('change', async (e) => {
    hint.classList.add('d-none');
    const file = e.target.files?.[0];
    if (!file) return;

    // บีบอัดถ้าจำเป็น
    const finalFile = await compressImage(file);
    if (finalFile !== file) {
      replaceInputFile(input, finalFile);
      hint.textContent = `บีบอัดรูปจาก ${(file.size/1024/1024).toFixed(2)}MB → ${(finalFile.size/1024/1024).toFixed(2)}MB`;
      hint.classList.remove('d-none');
    } else if (file.size > MAX_BYTES) {
      hint.textContent = 'ไฟล์ใหญ่เกินไป โปรดลองเลือกรูปที่เล็กลง';
      hint.classList.remove('d-none');
    }

    // พรีวิว
    const reader = new FileReader();
    reader.onload = (ev) => {
      if (placeholder) placeholder.classList.add('d-none');
      preview.src = ev.target.result;
      preview.classList.remove('d-none');
    };
    reader.readAsDataURL(finalFile);
  });

  // กันกรณีผู้ใช้ไม่ได้เปลี่ยนไฟล์ แต่ไฟล์เดิมยังใหญ่เกิน
  form.addEventListener('submit', async (e) => {
    const f = input.files?.[0];
    if (f && f.size > MAX_BYTES) {
      e.preventDefault();
      const smaller = await compressImage(f);
      replaceInputFile(input, smaller);
      hint.textContent = `บีบอัดเพิ่มเติมเป็น ${(smaller.size/1024/1024).toFixed(2)}MB แล้ว กดบันทึกอีกครั้ง`;
      hint.classList.remove('d-none');
      // ส่งใหม่อัตโนมัติถ้าหลังบีบอัดแล้วไม่เกิน
      if (smaller.size <= MAX_BYTES) form.submit();
    }
  });
});
</script>
</body>
</html>
