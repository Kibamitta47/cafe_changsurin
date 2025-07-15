<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>จัดการข่าวสาร</title>
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

        /* Table Styling */
        .table {
            margin-bottom: 0;
            background-color: var(--card-light-bg); /* White background for table */
            border-radius: 12px; /* Apply border-radius to table container if needed */
            overflow: hidden; /* For rounded corners */
        }
        .table thead th {
            background-color: #e9ecef; /* Light grey header background */
            border-bottom: 1px solid var(--border-light-color); /* Lighter border */
            font-weight: 600;
            color: var(--text-dark);
            padding: 1rem 1.5rem;
            vertical-align: middle;
        }
        .table tbody td {
            vertical-align: middle;
            padding: 1rem 1.5rem;
            color: var(--text-dark);
            border-color: var(--border-light-color); /* Lighter border */
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa; /* Very light hover background */
        }
        .news-image {
            max-height: 70px; /* Slightly larger image preview */
            width: auto;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid var(--border-light-color);
        }
        .no-news {
            text-align: center;
            padding: 4rem; /* More padding */
            color: var(--text-secondary-dark);
            background-color: var(--card-light-bg);
            border-radius: 12px;
        }
        .no-news .fas {
            color: var(--border-light-color); /* Light grey icon */
            margin-bottom: 1.5rem;
        }
        .no-news h5 {
            color: var(--text-dark);
            font-weight: 600;
        }
        .no-news p {
            color: var(--text-secondary-dark);
        }
        .form-check-input {
            width: 1.2em; /* Make checkbox slightly larger */
            height: 1.2em;
            margin-top: 0.25em;
            border: 1px solid var(--border-light-color);
            border-radius: 0.25em;
            transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-check-input:checked {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }
        .form-check-input:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
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
    </style>
</head>
<body>
    @include('components.adminmenu')
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="container">
                <div class="text-start mb-4">
                    <button id="btnNews" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>ดูข่าวสารที่เพิ่มแล้ว
                    </button>
                </div>
            </div>

            <section class="mb-5">
                <h1 class="section-title text-center">
                    <i class="fas fa-bullhorn"></i>จัดการข่าวสาร
                </h1>

                <div class="card">
                    <div class="card-body">
                        {{-- Custom Alert Message Area --}}
                        <div id="formAlert" class="custom-alert custom-alert-danger" role="alert"></div>

                        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading"></i>หัวข้อ
                                </label>
                                <input type="text" id="title" name="title" class="form-control" placeholder="กรอกหัวข้อข่าวสาร..." required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    <i class="fas fa-align-left"></i>รายละเอียด
                                </label>
                                <textarea id="content" name="content" rows="4" class="form-control" placeholder="กรอกรายละเอียดข่าวสารหรือโปรโมชัน..." required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_datetime" class="form-label">
                                        <i class="fas fa-calendar-alt"></i>เริ่มแสดงผล
                                    </label>
                                    <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_datetime" class="form-label">
                                        <i class="fas fa-calendar-times"></i>สิ้นสุดการแสดงผล
                                    </label>
                                    <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-images"></i>แนบรูปภาพ
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
                                <a href="/home-admin" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-2"></i>ยกเลิก
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>บันทึก
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <hr class="my-5" style="border-top: 1px dashed var(--border-light-color);"> 
            <section id="newsSection">
                <h2 class="section-title text-center">
                    <i class="fas fa-list-alt"></i>รายการข่าวสารที่เพิ่มแล้ว
                </h2>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-table fa-icon"></i> รายการข่าวสาร
                    </div>
                    <div class="card-body p-0">
                        @if(isset($news) && $news->isEmpty())
                            <div class="no-news">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <h5>ยังไม่มีข่าวสารในระบบ</h5>
                                <p>เริ่มเพิ่มข่าวสารใหม่ได้จากฟอร์มด้านบน</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%;">หัวข้อ</th>
                                            <th style="width: 25%;">รายละเอียด</th>
                                            <th style="width: 15%; text-align: center;">รูปภาพ</th>
                                            <th style="width: 12%; text-align: center;">เริ่มแสดง</th>
                                            <th style="width: 12%; text-align: center;">สิ้นสุด</th>
                                            <th style="width: 10%; text-align: center;">วันที่เพิ่ม</th>
                                            <th style="width: 8%; text-align: center;">แสดงหน้าแรก</th>
                                            <th style="width: 11%; text-align: center;">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($news as $item)
                                            <tr>
                                                <td class="fw-semibold">{{ $item->title }}</td>
                                                <td>{{ Str::limit($item->content, 100) }}</td>
                                                <td class="text-center">
                                                    @if($item->images && count($item->images) > 0)
                                                        <img src="{{ asset('storage/' . $item->images[0]) }}" alt="รูปข่าว" class="news-image">
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center small">
                                                    {{ optional($item->start_datetime)->format('d/m/Y H:i') ?? '-' }}
                                                </td>
                                                <td class="text-center small">
                                                    {{ optional($item->end_datetime)->format('d/m/Y H:i') ?? '-' }}
                                                </td>
                                                <td class="text-center small">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('admin.news.toggle', $item->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="checkbox" name="is_visible" onchange="this.form.submit()" class="form-check-input" {{ $item->is_visible ? 'checked' : '' }}>
                                                    </form>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-warning btn-sm me-1" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm delete-news-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-news-id="{{ $item->id }}" title="ลบ">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
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
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    คุณยืนยันที่จะลบข่าวสารนี้หรือไม่? การดำเนินการนี้ไม่สามารถย้อนกลับได้
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <form id="deleteNewsForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">ลบ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('btnNews').addEventListener('click', function() {
        document.getElementById('newsSection').scrollIntoView({ behavior: 'smooth' });
    });

    const formAlert = document.getElementById('formAlert');

    function showFormAlert(message) {
        formAlert.textContent = message;
        formAlert.classList.add('show');
        setTimeout(() => {
            formAlert.classList.remove('show');
        }, 5000); // Hide after 5 seconds
    }

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

    // Handle delete button click to populate modal form action
    const deleteNewsModal = document.getElementById('deleteConfirmationModal');
    deleteNewsModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const newsId = button.getAttribute('data-news-id'); // Extract info from data-* attributes
        const deleteForm = document.getElementById('deleteNewsForm');
        
        // Set the action URL for the delete form
        // Ensure the route is correct: admin.news.delete
        deleteForm.setAttribute('action', `{{ route('admin.news.delete', '') }}/${newsId}`);
    });
</script>
</body>
</html>
