@include('components.adminmenu')
<div style="padding: 50px;">

<style>

    .form-label {
        font-weight: bold;
        font-size: 0.9rem; /* ลดขนาดตัวอักษร */
        color: #333;
    }

    .form-control {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background-color: #fff;
        font-size: 0.9rem; /* ลดขนาดตัวอักษร */
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .mb-3 {
        margin-bottom: 1.5rem;
    }

    .mt-3 {
        margin-top: 1.5rem;
    }

    .btn {
        padding: 10px 15px;
        font-size: 1rem; /* ลดขนาดตัวอักษรในปุ่ม */
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        font-weight: bold;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        color: #fff;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
        color: #fff;
        transition: background-color 0.3s;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .text-muted {
        color: #6c757d;
    }

    .d-block {
        display: block;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    input[type="file"] {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background-color: #fff;
    }

    .profile-image {
        border-radius: 50%;
        object-fit: cover;
    }
</style>

<form action="{{ route('update.profile') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="form-label">ชื่อผู้ใช้</label>
        <input type="text" name="name" value="{{ Auth::guard('admin')->user()->UserName }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">อีเมล</label>
        <input type="email" name="email" value="{{ Auth::guard('admin')->user()->Email }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">รูปโปรไฟล์</label><br>
        @php
            $profileImage = Auth::guard('admin')->user()->profile_image;
        @endphp

        @if($profileImage && file_exists(public_path('storage/profile_images/' . $profileImage)))
            <img src="{{ asset('storage/profile_images/' . $profileImage) }}" width="100" class="mb-2 d-block profile-image">
        @else
            <p class="text-muted">ยังไม่มีรูปโปรไฟล์</p>
        @endif

        <input type="file" name="profile_image" class="form-control">
    </div>

    <div class="mt-3">
        <a href="{{ url('home-admin') }}" class="btn btn-secondary me-2">
            <i class="fas fa-times me-1"></i> ยกเลิก
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> บันทึก
        </button>
    </div>
</form>

</div>
