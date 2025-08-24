<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>แก้ไขข่าวสาร - รูปแบบใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary-rgb: 71, 129, 230;
            --bs-secondary-rgb: 134, 142, 150;
            --background-color: #f0f2f5;
            --card-bg: #ffffff;
            --card-border-color: #dee2e6;
            --card-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            --font-family-sans-serif: 'Sarabun', sans-serif;
        }

        body {
            font-family: var(--font-family-sans-serif);
            background-color: var(--background-color);
            color: var(--bs-dark);
        }

        .container {
            max-width: 960px; /* ปรับขนาดให้เหมาะสมกับหน้าแก้ไข */
        }

        .card {
            border: 1px solid var(--card-border-color);
            box-shadow: var(--card-shadow);
            border-radius: 0.75rem;
        }

        .section-title {
            color: var(--bs-dark);
            font-weight: 700;
        }

        .file-upload-area {
            border: 2px dashed var(--card-border-color);
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            background-color: var(--bs-light);
            transition: all 0.3s ease;
            position: relative;
        }
        .file-upload-area.drag-over {
            border-color: rgb(var(--bs-primary-rgb));
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }
        .file-upload-area input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        #image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1.5rem;
            padding: 1rem;
            border: 1px solid var(--card-border-color);
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            min-height: 150px; /* กำหนดความสูงขั้นต่ำ */
        }
        #image-preview-container:empty::before {
             content: "ไม่มีรูปภาพ... กรุณาเพิ่มรูปภาพใหม่ด้านล่าง";
             color: #6c757d;
             font-size: 0.9rem;
             width: 100%;
             text-align: center;
             align-self: center;
        }
        
        .image-preview-wrapper {
            position: relative;
            width: 150px;
            border: 1px solid var(--card-border-color);
            border-radius: 0.5rem;
            padding: 0.5rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.07);
        }
        .image-preview-wrapper img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 0.25rem;
        }
        .image-info {
            font-size: 0.75rem;
            text-align: center;
            margin-top: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .btn-remove-img {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 24px;
            height: 24px;
            background-color: #dc3545;
            color: white;
            border: 2px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
        }
        .btn-remove-img:hover {
            transform: scale(1.1);
        }
        
    </style>
</head>
<body>
    @include('components.adminmenu')
    <div class="container py-5">
        <section>
            <h1 class="section-title text-center mb-4">
                <i class="fas fa-edit"></i> แก้ไขข่าวสาร
            </h1>
            <div class="card">
                <div class="card-body p-4 p-md-5">
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                     <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data" id="editNewsForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden input for images to be deleted -->
                        <div id="deleted-images-container"></div>

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">หัวข้อ</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="กรอกหัวข้อข่าวสาร..." value="{{ old('title', $news->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label fw-bold">รายละเอียด</label>
                            <textarea id="content" name="content" rows="4" class="form-control" placeholder="กรอกรายละเอียดข่าวสาร..." required>{{ old('content', $news->content) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_datetime" class="form-label fw-bold">เริ่มแสดงผล</label>
                                <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control" value="{{ old('start_datetime', \Carbon\Carbon::parse($news->start_datetime)->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_datetime" class="form-label fw-bold">สิ้นสุดการแสดงผล</label>
                                <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" value="{{ old('end_datetime', \Carbon\Carbon::parse($news->end_datetime)->format('Y-m-d\TH:i')) }}" required>
                            </div>
                        </div>

                        <!-- Unified Image Management Area -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">จัดการรูปภาพ</label>
                            <div id="image-preview-container">
                                <!-- Existing images will be loaded here by JavaScript -->
                            </div>
                        </div>
                        
                        <!-- File Upload Area -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">เพิ่มรูปภาพใหม่</label>
                            <div class="file-upload-area" id="file-drop-area">
                                <input type="file" id="new_images" name="images[]" multiple accept="image/*">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-1 fw-semibold">ลากและวางไฟล์ที่นี่ หรือคลิกเพื่อเลือก</p>
                                <p class="mb-0 text-muted small">รูปที่เลือกจะแสดงในกล่อง "จัดการรูปภาพ" ด้านบน</p>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('admin.news.add') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i>กลับไปหน้ารายการ
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Configuration ---
    const existingImages = @json($news->images ?? []);
    const storagePath = "{{ asset('storage/') }}";
    
    // --- DOM Elements ---
    const dropArea = document.getElementById('file-drop-area');
    const newImageInput = document.getElementById('new_images');
    const previewContainer = document.getElementById('image-preview-container');
    const deletedContainer = document.getElementById('deleted-images-container');
    
    // --- File Storage ---
    let newFileStore = new DataTransfer();

    // --- Initializer ---
    loadExistingImages();

    // --- Event Listeners ---
    if (dropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('drag-over'), false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('drag-over'), false);
        });
        dropArea.addEventListener('drop', handleDrop, false);
        newImageInput.addEventListener('change', handleFileSelect);
    }

    // --- Functions ---
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function loadExistingImages() {
        existingImages.forEach(imagePath => {
            const isExisting = true;
            createPreview(imagePath, isExisting);
        });
    }

    function handleDrop(e) {
        handleFiles(e.dataTransfer.files);
    }
    
    function handleFileSelect(e) {
        handleFiles(e.target.files);
    }

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                newFileStore.items.add(file);
                createPreview(file, false); // false = not existing
            }
        });
        newImageInput.files = newFileStore.files; // Update the actual input
    }
    
    function createPreview(fileOrPath, isExisting) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('image-preview-wrapper');

        const img = document.createElement('img');
        const info = document.createElement('div');
        info.classList.add('image-info');
        
        let fileIdentifier;

        if (isExisting) {
            fileIdentifier = fileOrPath;
            img.src = `${storagePath}/${fileOrPath}`;
            img.alt = fileOrPath;
            info.textContent = fileOrPath.split('/').pop();
            info.title = fileOrPath.split('/').pop();
            wrapper.dataset.imagePath = fileOrPath; // Mark as existing
        } else { // It's a new File object
            fileIdentifier = fileOrPath;
            img.src = URL.createObjectURL(fileOrPath);
            img.alt = fileOrPath.name;
            info.textContent = fileOrPath.name;
            info.title = fileOrPath.name;
        }

        wrapper.appendChild(img);
        wrapper.appendChild(info);
        
        const removeBtn = document.createElement('div');
        removeBtn.classList.add('btn-remove-img');
        removeBtn.innerHTML = '×';
        removeBtn.title = "ลบรูปนี้";
        
        removeBtn.addEventListener('click', () => {
            removeFile(fileIdentifier, wrapper, isExisting);
        });
        
        wrapper.appendChild(removeBtn);
        previewContainer.appendChild(wrapper);
    }

    function removeFile(fileOrPath, wrapperElement, isExisting) {
        if (isExisting) {
            // Add to a hidden input to mark for deletion on the server
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'deleted_images[]';
            hiddenInput.value = fileOrPath;
            deletedContainer.appendChild(hiddenInput);
        } else { // It's a new file
            // Rebuild the DataTransfer object without the removed file
            const newStore = new DataTransfer();
            Array.from(newFileStore.files).forEach(file => {
                if (file !== fileOrPath) {
                    newStore.items.add(file);
                }
            });
            newFileStore = newStore;
            newImageInput.files = newFileStore.files;
        }
        
        // Remove the preview from the DOM
        wrapperElement.remove();
    }
});
</script>
</body>
</html>