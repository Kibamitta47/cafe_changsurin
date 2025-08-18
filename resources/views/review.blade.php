<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>เขียนรีวิว: {{ $cafe->cafe_name }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <style>
    body {
      font-family: 'Kanit', sans-serif;
      background-image: url('https://images.unsplash.com/photo-1511920183303-52c142c6772c?auto=format&fit=crop&w=1470&q=80');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      display: flex;
      flex-direction: column; /* <<< แก้ไขตรงนี้: เปลี่ยน body เป็นคอลัมน์ */
      min-height: 100vh;
      overflow-y: auto; /* เผื่อกรณีเนื้อหายาว */
    }
    body::before {
        content: ''; position: fixed; inset: 0;
        background: rgba(0,0,0,0.35); z-index: -1;
    }
    [x-cloak] { display: none !important; }

    /* Form Container ไม่ต้องเปลี่ยน */
    .form-container {
      max-width: 750px;
      width: 95%;
      background: rgba(255 255 255 / 0.98);
      backdrop-filter: blur(12px);
      padding: 1.75rem 2rem;
      border-radius: 1rem;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* สไตล์อื่นๆ ไม่ต้องเปลี่ยน */
    .form-label { font-weight: 600; color: #4b5563; font-size: 0.875rem; margin-bottom: 0.375rem; }
    .form-control { background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; transition: all 0.2s ease-in-out; }
    .form-control:focus { outline: none; border-color: #c49a6c; box-shadow: 0 0 0 3px rgba(196, 154, 108, 0.2); background-color: white; }
    .rating { direction: rtl; display: flex; justify-content: center; gap: 0.25rem; }
    .rating input[type="radio"] { display: none; }
    .rating label { font-size: 2rem; color: #e5e7eb; cursor: pointer; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); transition: all 0.2s ease-in-out; }
    .rating label::before { content: "★"; }
    .rating label:hover, .rating label:hover ~ label, .rating input:checked ~ label { color: #f59e0b; transform: scale(1.1); }
    .btn { font-size: 0.875rem; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600; transition: all 0.2s ease-in-out; border: none; }
    .btn-submit { background-color: #c49a6c; color: white; box-shadow: 0 4px 10px -5px rgba(196, 154, 108, 0.6); }
    .btn-submit:hover { background-color: #b58859; transform: translateY(-2px); box-shadow: 0 6px 15px -5px rgba(196, 154, 108, 0.5); }
    .btn-cancel { background-color: transparent; color: #6b7280; }
    .btn-cancel:hover { background-color: #f3f4f6; }
  </style>
</head>
<body>
  
  {{-- Navbar จะแสดงผลที่ด้านบนสุดของหน้าอย่างถูกต้อง --}}
  @include('components.2navbar')

  {{-- <<< แก้ไขตรงนี้: main จะขยายเต็มพื้นที่และจัดฟอร์มให้อยู่ตรงกลาง --}}
  <main class="flex-grow flex items-center justify-center p-4">
    <div class="form-container">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-800">เขียนรีวิวสำหรับ: {{ $cafe->cafe_name }}</h2>
        </div>

        <form action="{{ route('user.reviews.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="cafe_id" value="{{ $cafe->id }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-x-8">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div>
                        <label for="title" class="form-label">หัวข้อรีวิว</label>
                        <input type="text" class="form-control w-full" id="title" name="title" placeholder="เช่น บรรยากาศดี, กาแฟอร่อย" required>
                    </div>
                    <div>
                        <label for="content" class="form-label">รายละเอียด</label>
                        <textarea class="form-control w-full" id="content" name="content" rows="9" placeholder="เล่าประสบการณ์ของคุณที่นี่..." required></textarea>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-4 mt-4 md:mt-0">
                    <div class="text-center bg-slate-50 p-4 rounded-lg">
                        <label class="form-label">ให้คะแนนความประทับใจ</label>
                        <div class="rating mt-1">
                            <input type="radio" name="rating" value="5" id="star5" required /><label for="star5" title="5 ดาว"></label>
                            <input type="radio" name="rating" value="4" id="star4" required /><label for="star4" title="4 ดาว"></label>
                            <input type="radio" name="rating" value="3" id="star3" required /><label for="star3" title="3 ดาว"></label>
                            <input type="radio" name="rating" value="2" id="star2" required /><label for="star2" title="2 ดาว"></label>
                            <input type="radio" name="rating" value="1" id="star1" required /><label for="star1" title="1 ดาว"></label>
                        </div>
                    </div>
                    <div x-data="fileUploader()">
                        <label class="form-label">แนบรูปภาพ</label>
                        <div 
                            class="mt-1 flex justify-center rounded-md border border-dashed border-slate-900/25 px-4 py-6 transition-colors"
                            :class="{ 'bg-amber-50 border-amber-400': isDragging }"
                            @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)">
                            <div class="text-center">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400"></i>
                                <div class="mt-2 flex text-xs leading-5 text-slate-600">
                                    <label for="images" class="relative cursor-pointer rounded-md font-semibold text-amber-600 hover:text-amber-500">
                                        <span>อัปโหลด</span>
                                        <input @change="handleSelect($event)" id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*" x-ref="fileInput">
                                    </label>
                                    <p class="pl-1">หรือลากมาวาง</p>
                                </div>
                            </div>
                        </div>
                        <template x-if="files.length > 0">
                            <div class="mt-2 max-h-20 overflow-y-auto space-y-1 pr-2">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center justify-between text-xs bg-slate-50 p-1.5 rounded">
                                        <span class="truncate w-4/5" x-text="file.name"></span>
                                        <button @click.prevent="removeFile(index)" type="button" class="ml-2 text-red-500 hover:text-red-700">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-3 border-t border-slate-200 pt-5">
                <a href="{{ route('cafes.show', $cafe->id) }}" class="btn btn-cancel">ยกเลิก</a>
                <button type="submit" class="btn btn-submit">ส่งรีวิว</button>
            </div>
        </form>
    </div>
  </main>

  <script>
    function fileUploader(){return{isDragging:!1,files:[],handleSelect(e){this.addFiles(e.target.files)},handleDrop(e){this.isDragging=!1,this.addFiles(e.dataTransfer.files)},addFiles(e){const t=Array.from(e);this.files.push(...t),this.updateFileInput()},removeFile(e){this.files.splice(e,1),this.updateFileInput()},updateFileInput(){const e=new DataTransfer;this.files.forEach(t=>e.items.add(t)),this.$refs.fileInput.files=e.files}}}
  </script>
</body>
</html>