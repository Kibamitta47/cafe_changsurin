<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>จัดการข่าวสาร - รูปแบบผสม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- การออกแบบและ Palette สีใหม่ --- */
        :root {
            --bs-primary-rgb: 71, 129, 230; /* Deep Sky Blue */
            --bs-secondary-rgb: 134, 142, 150; /* Slate Gray */
            --background-color: #f0f2f5; /* Light Gray background */
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
            max-width: 1400px;
        }

        /* --- การ์ดและ Accordion --- */
        .card {
            border: 1px solid var(--card-border-color);
            box-shadow: var(--card-shadow);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .card-header {
            background-color: rgba(var(--bs-primary-rgb), 0.9);
            color: white;
            font-weight: 600;
            border-bottom: none;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        
        .accordion-item {
            border: 1px solid var(--card-border-color);
            border-radius: 0.75rem !important;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
        .accordion-button {
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--bs-dark);
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            color: rgb(var(--bs-primary-rgb));
            box-shadow: inset 0 -1px 0 var(--card-border-color);
        }
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
        }

        /* --- ส่วนอัปโหลดไฟล์ที่ปรับปรุงใหม่ --- */
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

        /* --- การแสดงตัวอย่างรูปภาพที่อัปโหลด --- */
        #image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1.5rem;
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
            background-color: #dc3545; /* Bootstrap Danger color */
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

        /* --- สไตล์สำหรับตารางรูปแบบเดิม --- */
        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .news-image { /* คืนค่าคลาสเดิม */
            max-height: 70px;
            width: auto;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid var(--card-border-color);
        }
        .no-news {
            text-align: center;
            padding: 4rem;
            color: #6c757d;
        }
        .form-check-input:checked {
             background-color: rgb(var(--bs-primary-rgb));
             border-color: rgb(var(--bs-primary-rgb));
        }
    </style>
</head>
<body>
    @include('components.adminmenu')
    <div class="container py-5">
        
        <!-- ส่วนเพิ่ม/แก้ไขข่าวสาร (รูปแบบ Accordion ใหม่) -->
        <section class="mb-5">
            <div class="accordion" id="newsFormAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="fas fa-plus-circle me-2"></i> เพิ่มข่าวสารใหม่
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#newsFormAccordion">
                        <div class="accordion-body p-4">
                            <!-- Form content is the new decorated version -->
                             <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="title" class="form-label fw-bold">หัวข้อ</label>
                                        <input type="text" id="title" name="title" class="form-control" placeholder="กรอกหัวข้อข่าวสาร..." required value="{{ old('title') }}">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="content" class="form-label fw-bold">รายละเอียด</label>
                                        <textarea id="content" name="content" rows="4" class="form-control" placeholder="กรอกรายละเอียดข่าวสาร..." required>{{ old('content') }}</textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="link_url" class="form-label fw-bold">ลิงก์เพิ่มเติม (ถ้ามี)</label>
                                        <input type="url" id="link_url" name="link_url" class="form-control" placeholder="https://example.com/promotion" value="{{ old('link_url') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="start_datetime" class="form-label fw-bold">เริ่มแสดงผล</label>
                                        <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control" required value="{{ old('start_datetime') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="end_datetime" class="form-label fw-bold">สิ้นสุดการแสดงผล</label>
                                        <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" required value="{{ old('end_datetime') }}">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">แนบรูปภาพ</label>
                                        <div class="file-upload-area" id="file-drop-area">
                                            <input type="file" id="images" name="images[]" multiple accept="image/*">
                                            <div id="file-drag-text">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                <p class="mb-1 fw-semibold">ลากและวางไฟล์ที่นี่ หรือคลิกเพื่อเลือก</p>
                                                <p class="mb-0 text-muted small">รองรับหลายไฟล์ (JPG, PNG, WebP)</p>
                                            </div>
                                        </div>
                                        <div id="image-preview-container"></div>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <a href="{{ route('home.admin') }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i> ยกเลิก</a>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> บันทึกข้อมูล</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ส่วนแสดงรายการข่าวสาร (กลับไปใช้รูปแบบเดิม) -->
        <section id="newsSection">
            <h2 class="h4 mb-3 fw-bold"><i class="fas fa-list-alt me-2"></i> รายการข่าวสารที่เพิ่มแล้ว</h2>
            <div class="card">
                <div class="card-header"><i class="fas fa-table me-2"></i> รายการข่าวสาร</div>
                <div class="card-body p-0">
                    @if(isset($news) && $news->isEmpty())
                        <div class="no-news">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <h5>ยังไม่มีข่าวสารในระบบ</h5>
                            <p>เริ่มเพิ่มข่าวสารใหม่ได้จากฟอร์มด้านบน</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">หัวข้อ</th>
                                        <th style="width: 25%;">รายละเอียด</th>
                                        <th class="text-center" style="width: 10%;">รูปภาพ</th>
                                        <th class="text-center" style="width: 12%;">เริ่มแสดง</th>
                                        <th class="text-center" style="width: 12%;">สิ้นสุด</th>
                                        <th class="text-center" style="width: 10%;">วันที่เพิ่ม</th>
                                        <th class="text-center" style="width: 5%;">ลิงก์</th>
                                        <th class="text-center" style="width: 5%;">แสดง</th>
                                        <th class="text-center" style="width: 11%;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($news as $item)
                                        <tr>
                                            <td class="fw-semibold">{{ $item->title }}</td>
                                            <td>{{ Str::limit($item->content, 80) }}</td>
                                            <td class="text-center">
                                                @if($item->images && count($item->images) > 0)
                                                    <img src="{{ asset('storage/' . $item->images[0]) }}" alt="รูปข่าว" class="news-image">
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center small">{{ optional($item->start_datetime)->format('d/m/y H:i') }}</td>
                                            <td class="text-center small">{{ optional($item->end_datetime)->format('d/m/y H:i') }}</td>
                                            <td class="text-center small">{{ optional($item->created_at)->format('d/m/y H:i') }}</td>
                                            <td class="text-center">
                                                @if($item->link_url)
                                                    <a href="{{ $item->link_url }}" target="_blank" class="btn btn-outline-primary btn-sm" title="เปิดลิงก์"><i class="fas fa-external-link-alt"></i></a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.news.toggle', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                      <input type="checkbox" role="switch" name="is_visible" onchange="this.form.submit()" class="form-check-input" {{ $item->is_visible ? 'checked' : '' }}>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-warning btn-sm me-1" title="แก้ไข"><i class="fas fa-edit"></i></a>
                                                <button type="button" class="btn btn-danger btn-sm delete-news-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-news-id="{{ $item->id }}" title="ลบ"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <!-- Modal ยืนยันการลบ -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> ยืนยันการลบ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    คุณแน่ใจหรือไม่ว่าต้องการลบข่าวสารนี้? การกระทำนี้ไม่สามารถย้อนกลับได้
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <form id="deleteNewsForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// โค้ด JavaScript ทั้งหมดยังคงเป็นเวอร์ชันล่าสุดที่จัดการไฟล์อัปโหลดและ Modal
document.addEventListener('DOMContentLoaded', function () {
    // --- Delete Modal Logic ---
    const deleteModal = document.getElementById('deleteConfirmationModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const newsId = button.getAttribute('data-news-id');
            const deleteForm = document.getElementById('deleteNewsForm');
            const actionUrl = `{{ url('admin/news') }}/${newsId}`;
            deleteForm.setAttribute('action', actionUrl);
        });
    }

    // --- Enhanced File Upload Logic ---
    const dropArea = document.getElementById('file-drop-area');
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview-container');
    const dragText = document.getElementById('file-drag-text');
    
    let fileStore = new DataTransfer();

    if (dropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('drag-over'), false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('drag-over'), false);
        });
        dropArea.addEventListener('drop', handleDrop, false);
        imageInput.addEventListener('change', handleFileSelect);
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
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
                // ป้องกันการเพิ่มไฟล์ซ้ำ
                let isDuplicate = Array.from(fileStore.files).some(f => f.name === file.name && f.size === file.size);
                if (!isDuplicate) {
                    fileStore.items.add(file);
                    createPreview(file);
                }
            }
        });
        imageInput.files = fileStore.files;
        updateDropAreaUI();
    }

    function createPreview(file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function() {
            const wrapper = document.createElement('div');
            wrapper.classList.add('image-preview-wrapper');
            const fileId = `file-${file.name}-${file.lastModified}`;
            wrapper.id = fileId;

            wrapper.innerHTML = `
                <img src="${reader.result}" alt="${file.name}">
                <div class="image-info" title="${file.name}">${file.name}</div>
                <div class="btn-remove-img" title="ลบรูปนี้">×</div>
            `;
            previewContainer.appendChild(wrapper);

            wrapper.querySelector('.btn-remove-img').addEventListener('click', () => {
                removeFile(file, fileId);
            });
        }
    }

    function removeFile(fileToRemove, wrapperId) {
        const newFileStore = new DataTransfer();
        Array.from(fileStore.files).forEach(file => {
            if (file !== fileToRemove) {
                newFileStore.items.add(file);
            }
        });
        
        fileStore = newFileStore;
        imageInput.files = fileStore.files;
        
        document.getElementById(wrapperId).remove();
        updateDropAreaUI();
    }
    
    function updateDropAreaUI() {
        if (previewContainer.children.length > 0) {
            dragText.style.display = 'none';
        } else {
            dragText.style.display = 'block';
        }
    }
});
</script>
</body>
</html>