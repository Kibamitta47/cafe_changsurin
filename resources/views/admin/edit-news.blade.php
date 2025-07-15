<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>แก้ไขข่าวสาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles & Variables (Light Tone for consistency) */
        :root {
            --primary-light: #4a90e2; /* A nice light blue */
            --secondary-light: #6dd5ed; /* Lighter blue/cyan */
            --accent-light: #8e44ad; /* A subtle purple for accents */
            --background-light: #f4f7f6; /* Very light grey/off-white */
            --card-light-bg: #ffffff; /* Pure white cards */
            --text-dark: #333333; /* Dark text for contrast */
            --text-secondary-dark: #666666; /* Slightly lighter dark text */
            --border-light-color: #e0e0e0; /* Light grey border */
            --shadow-light-mild: 0 4px 15px rgba(0, 0, 0, 0.08); /* Soft shadow */
            --transition-smooth: all 0.3s ease;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--background-light); /* Light background */
            padding: 30px 0; /* Slightly more padding */
            color: var(--text-dark); /* Dark text for readability */
        }
        .container { max-width: 1200px; }
        
        /* Card Styling */
        .card {
            border: none; /* Remove default Bootstrap border */
            border-radius: 12px; /* More rounded corners */
            box-shadow: var(--shadow-light-mild); /* Soft shadow for depth */
            background-color: var(--card-light-bg); /* White card background */
            transition: var(--transition-smooth);
        }
        .card-header {
            background-color: var(--primary-light); /* Primary blue header for cards */
            color: #ffffff; /* White text for header */
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: none;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-header .fa-icon {
            font-size: 1.25rem;
        }
        .card-body {
            padding: 2rem; /* More internal padding */
        }

        /* Section Titles */
        .section-title {
            color: var(--primary-light); /* Primary blue for section titles */
            margin-bottom: 2.5rem; /* More spacing below title */
            font-weight: 700;
            font-size: 2.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .section-title .fas {
            color: var(--text-dark); /* Darker icon color for contrast */
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            padding: 0.75rem 1.5rem; /* Larger buttons */
            border-radius: 8px; /* Slightly rounded */
            font-weight: 500;
            transition: var(--transition-smooth);
            display: inline-flex; /* For icon alignment */
            align-items: center;
            gap: 8px;
        }
        .btn-primary:hover {
            background-color: #3a7bd5; /* Slightly darker blue on hover */
            border-color: #3a7bd5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: var(--border-light-color); /* Lighter grey for secondary */
            border-color: var(--border-light-color);
            color: var(--text-dark); /* Dark text on secondary button */
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-secondary:hover {
            background-color: #d0d0d0; /* Darker grey on hover */
            border-color: #d0d0d0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-warning {
            background-color: #ffc107; /* Bootstrap yellow */
            border-color: #ffc107;
            color: #333333; /* Dark text for readability */
            border-radius: 5px;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545; /* Bootstrap red */
            border-color: #dc3545;
            border-radius: 5px;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        .btn-outline-primary {
            color: var(--primary-light);
            border-color: var(--primary-light);
            transition: var(--transition-smooth);
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-light);
            color: #ffffff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }


        /* Form Control Styling */
        .form-label {
            font-weight: 600;
            color: var(--text-dark); /* Dark label text */
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid var(--border-light-color);
            padding: 0.75rem 1rem;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05); /* Subtle inner shadow */
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25); /* Primary light blue focus ring */
        }
        textarea.form-control {
            resize: vertical;
        }

        /* File Upload Area */
        .file-upload-area {
            border: 2px dashed var(--border-light-color); /* Lighter dashed border */
            border-radius: 10px; /* Slightly more rounded */
            padding: 2.5rem; /* More padding */
            text-align: center;
            background-color: #fcfcfc; /* Very light background for contrast */
            transition: var(--transition-smooth);
        }
        .file-upload-area:hover {
            border-color: var(--primary-light); /* Primary blue on hover */
            background-color: #eff6fc; /* Very light blue background on hover */
        }
        .file-upload-area .fas {
            color: var(--primary-light); /* Primary blue icon */
            margin-bottom: 1rem;
        }
        .file-upload-area p {
            color: var(--text-secondary-dark); /* Secondary dark text */
            font-weight: 500;
        }
        .file-upload-area input[type="file"] {
            opacity: 0; /* Hide default input */
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
            left: 0;
            top: 0;
        }
        .file-upload-area {
            position: relative; /* Enable absolute positioning for input */
            overflow: hidden;
        }

        /* Existing Images Preview */
        .existing-images-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
            padding: 15px;
            border: 1px solid var(--border-light-color);
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .existing-image-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        .existing-image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .existing-image-item .delete-image-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .existing-image-item .delete-image-btn:hover {
            background-color: #dc3545;
        }


        /* Utility for text */
        .text-muted {
            color: var(--text-secondary-dark) !important;
        }

        /* Custom Alert Message */
        .custom-alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            display: none; /* Hidden by default */
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .custom-alert.show {
            display: block;
            opacity: 1;
        }
        .custom-alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .custom-alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    @include('components.adminmenu')
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="container">
                <section class="mb-5">
                    <h1 class="section-title text-center">
                        <i class="fas fa-edit"></i>แก้ไขข่าวสาร
                    </h1>

                    <div class="card">
                        <div class="card-body">
                            {{-- Custom Alert Message Area --}}
                            <div id="formAlert" class="custom-alert" role="alert"></div>
                            @if (session('success'))
                                <div class="custom-alert custom-alert-success show" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="custom-alert custom-alert-danger show" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT') {{-- ใช้ PUT สำหรับการอัปเดต --}}

                                <div class="mb-3">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading"></i>หัวข้อ
                                    </label>
                                    <input type="text" id="title" name="title" class="form-control" placeholder="กรอกหัวข้อข่าวสาร..." value="{{ old('title', $news->title) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">
                                        <i class="fas fa-align-left"></i>รายละเอียด
                                    </label>
                                    <textarea id="content" name="content" rows="4" class="form-control" placeholder="กรอกรายละเอียดข่าวสารหรือโปรโมชัน..." required>{{ old('content', $news->content) }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_datetime" class="form-label">
                                            <i class="fas fa-calendar-alt"></i>เริ่มแสดงผล
                                        </label>
                                        <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control" value="{{ old('start_datetime', \Carbon\Carbon::parse($news->start_datetime)->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="end_datetime" class="form-label">
                                            <i class="fas fa-calendar-times"></i>สิ้นสุดการแสดงผล
                                        </label>
                                        <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" value="{{ old('end_datetime', \Carbon\Carbon::parse($news->end_datetime)->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-images"></i>รูปภาพปัจจุบัน
                                    </label>
                                    <div class="existing-images-preview" id="existingImagesPreview">
                                        @if($news->images && is_array($news->images) && count($news->images) > 0)
                                            @foreach($news->images as $imagePath)
                                                <div class="existing-image-item" id="image-{{ Str::slug($imagePath) }}">
                                                    <img src="{{ asset('storage/' . $imagePath) }}" alt="รูปภาพข่าวสาร">
                                                    <input type="hidden" name="existing_images[]" value="{{ $imagePath }}">
                                                    <button type="button" class="delete-image-btn" data-image-path="{{ $imagePath }}" data-news-id="{{ $news->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">ไม่มีรูปภาพปัจจุบัน</p>
                                        @endif
                                    </div>
                                    <small class="form-text text-muted">รูปภาพที่แสดงอยู่ด้านบนจะถูกเก็บไว้ หากต้องการลบให้กดปุ่ม X</small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-cloud-upload-alt"></i>เพิ่มรูปภาพใหม่
                                    </label>
                                    <div class="file-upload-area">
                                        <input type="file" id="images" name="images[]" multiple accept="image/*" class="form-control">
                                        <div class="mt-2">
                                            <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i> <p class="mb-1 fw-semibold">ลากและวางรูปภาพที่นี่</p>
                                            <p class="mb-0 text-muted small">หรือคลิกเพื่อเลือกไฟล์ (JPG, PNG, GIF)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <a href="{{ route('admin.news.add') }}" class="btn btn-secondary me-2"> {{-- เปลี่ยนลิงก์กลับไปหน้า list --}}
                                        <i class="fas fa-times me-2"></i>ยกเลิก
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    {{-- Delete Image Confirmation Modal --}}
    <div class="modal fade" id="deleteImageConfirmationModal" tabindex="-1" aria-labelledby="deleteImageConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteImageConfirmationModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบรูปภาพ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    คุณยืนยันที่จะลบรูปภาพนี้หรือไม่? การดำเนินการนี้ไม่สามารถย้อนกลับได้
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteImageBtn">ลบรูปภาพ</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const formAlert = document.getElementById('formAlert');

    function showFormAlert(message, type = 'danger') {
        formAlert.textContent = message;
        formAlert.className = `custom-alert custom-alert-${type} show`; // Reset classes and add new ones
        setTimeout(() => {
            formAlert.classList.remove('show');
        }, 5000); // Hide after 5 seconds
    }

    // Initial check for session messages
    document.addEventListener('DOMContentLoaded', () => {
        const successAlert = document.querySelector('.custom-alert-success');
        const errorAlert = document.querySelector('.custom-alert-danger');
        if (successAlert && successAlert.classList.contains('show')) {
            setTimeout(() => { successAlert.classList.remove('show'); }, 5000);
        }
        if (errorAlert && errorAlert.classList.contains('show')) {
            setTimeout(() => { errorAlert.classList.remove('show'); }, 5000);
        }
    });


    document.querySelector('form').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const content = document.getElementById('content').value.trim();
        const startDatetime = document.getElementById('start_datetime').value;
        const endDatetime = document.getElementById('end_datetime').value;

        if (!title || !content || !startDatetime || !endDatetime) {
            e.preventDefault();
            showFormAlert('กรุณากรอกข้อมูลให้ครบถ้วน');
            return false;
        }

        if (new Date(startDatetime) > new Date(endDatetime)) {
            e.preventDefault();
            showFormAlert('วันที่สิ้นสุดต้องไม่ก่อนวันที่เริ่มแสดง');
            return false;
        }
    });

    // Image Deletion Logic
    const deleteImageModal = document.getElementById('deleteImageConfirmationModal');
    const confirmDeleteImageBtn = document.getElementById('confirmDeleteImageBtn');
    let imageToDeletePath = null;
    let newsIdForImageDeletion = null;

    document.querySelectorAll('.delete-image-btn').forEach(button => {
        button.addEventListener('click', function() {
            imageToDeletePath = this.dataset.imagePath;
            newsIdForImageDeletion = this.dataset.newsId;
            const modal = new bootstrap.Modal(deleteImageModal);
            modal.show();
        });
    });

    confirmDeleteImageBtn.addEventListener('click', function() {
        if (imageToDeletePath && newsIdForImageDeletion) {
            fetch(`{{ url('admin/news/delete-image') }}/${newsIdForImageDeletion}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ image_path: imageToDeletePath })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the image item from the DOM
                    const imageElement = document.getElementById(`image-${imageToDeletePath.replace(/[^a-zA-Z0-9-]/g, '-')}`);
                    if (imageElement) {
                        imageElement.remove();
                    }
                    showFormAlert(data.message, 'success');
                } else {
                    showFormAlert(data.message, 'danger');
                }
                const modal = bootstrap.Modal.getInstance(deleteImageModal);
                modal.hide();
            })
            .catch(error => {
                console.error('Error:', error);
                showFormAlert('เกิดข้อผิดพลาดในการลบรูปภาพ', 'danger');
                const modal = bootstrap.Modal.getInstance(deleteImageModal);
                modal.hide();
            });
        }
    });
</script>
</body>
</html>
