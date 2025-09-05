<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>จัดการโปรไฟล์ - CafeFinder</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body { font-family: 'Kanit', sans-serif; padding-top: 90px; background:#f8f9fa; }
        .profile-card { background:#fff; border-radius: 0.75rem; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
        .profile-avatar { position: relative; width: 150px; height: 150px; margin: 0 auto 1rem; }
        .profile-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; border:4px solid #fff; }
        .avatar-placeholder { width:100%; height:100%; border-radius:50%; background:#e9ecef; display:flex; align-items:center; justify-content:center; font-size:3rem; color:#adb5bd; }
        .avatar-upload-button { position:absolute; bottom:5px; right:5px; width:40px; height:40px; border-radius:50%; background:#0d6efd; color:#fff; border:2px solid #fff; display:flex; align-items:center; justify-content:center; cursor:pointer; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">จัดการโปรไฟล์</h1>
        <p class="text-secondary">อัปเดตชื่อและรูปโปรไฟล์ของคุณ</p>
    </div>

    <div class="profile-card p-4 p-md-5">
        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-5">
                <!-- Avatar -->
                <div class="col-md-4 text-center">
                    <div class="profile-avatar">
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" id="imagePreview" alt="รูปโปรไฟล์">
                        @else
                            <div class="avatar-placeholder" id="imagePreviewPlaceholder"><i class="bi bi-person-fill"></i></div>
                            <img src="" id="imagePreview" class="d-none" alt="รูปโปรไฟล์">
                        @endif
                        <label for="profile_image" class="avatar-upload-button" title="เปลี่ยนรูปโปรไฟล์">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                        <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                    </div>
                </div>

                <!-- Name -->
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name', Auth::user()->name) }}" required>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill">
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
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('profile_image');
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('imagePreviewPlaceholder');

    input.addEventListener('change', e => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                if (placeholder) placeholder.classList.add('d-none');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>

</body>
</html>
