<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>เขียนรีวิว: {{ $cafe->cafe_name }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #F9F7F5;
            overflow: hidden;
        }
        .font-serif {
            font-family: 'Playfair Display', serif;
        }
        [x-cloak] { display: none !important; }

        .form-input-elegant {
            background: transparent;
            border: 0;
            border-bottom: 1.5px solid #d1d5db;
            border-radius: 0;
            padding: 0.25rem 0.25rem;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }
        .form-input-elegant:focus {
            outline: none;
            box-shadow: none;
            border-color: #6F4E37;
            --tw-ring-shadow: 0 0 #0000;
        }

        /* Star Rating */
        .star-rating input { display: none; }
        .star-rating label {
            font-size: 1.5rem;
            color: #e0e0e0;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label { color: #f59e0b; }
        .star-rating label:hover { transform: scale(1.05); }
    </style>
</head>
<body class="h-screen flex flex-col">
  
    @include('components.2navbar')

    <main class="flex-grow w-full max-w-4xl mx-auto p-2 flex" x-data="reviewForm()">
        <!-- ✨ MODIFIED: เพิ่ม @submit.prevent เพื่อเรียกฟังก์ชัน validateAndSubmit ก่อนส่งฟอร์ม -->
        <form action="{{ route('user.reviews.store') }}" method="POST" enctype="multipart/form-data"
              @submit.prevent="validateAndSubmit($event)" 
              class="w-full h-full bg-white rounded-xl shadow-lg border border-slate-100 flex overflow-hidden">
            @csrf
            <input type="hidden" name="cafe_id" value="{{ $cafe->id }}">
            
            <!-- Left Column -->
            <div 
                class="w-1/2 p-4 bg-slate-50 border-r border-slate-200 flex flex-col"
                @dragover.prevent="isDragging = true" 
                @dragleave.prevent="isDragging = false" 
                @drop.prevent="handleDrop($event)">
                
                <template x-if="files.length === 0">
                    <div class="flex-grow flex items-center justify-center border-2 border-dashed rounded-lg transition-colors"
                         :class="isDragging ? 'border-cyan-500 bg-cyan-50' : 'border-slate-300'">
                        <div class="text-center text-slate-500 text-sm">
                            <i class="fa-solid fa-images text-3xl"></i>
                            <p class="mt-2 font-semibold">ลากรูปภาพของคุณมาวางที่นี่</p>
                            <p class="text-xs mt-1">หรือ</p>
                            <label for="images" class="mt-2 inline-block px-3 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-semibold cursor-pointer hover:bg-slate-100">
                                เลือกไฟล์
                            </label>
                            <input @change="handleSelect($event)" id="images" name="images[]" type="file" class="hidden" multiple accept="image/*" x-ref="fileInput">
                        </div>
                    </div>
                </template>

                <template x-if="files.length > 0">
                    <div class="flex-grow relative">
                         <div class="absolute inset-0 overflow-y-auto pr-2">
                             <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="relative aspect-square">
                                        <img :src="file.preview" class="w-full h-full object-cover rounded-md shadow">
                                        <button @click.prevent="removeFile(index)" type="button" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-bold shadow">&times;</button>
                                    </div>
                                </template>
                            </div>
                         </div>
                    </div>
                </template>
            </div>

            <!-- Right Column -->
            <div class="w-1/2 p-4 flex flex-col">
                <header class="mb-4">
                    <p class="text-slate-500 text-sm">เขียนรีวิวสำหรับ</p>
                    <h1 class="font-serif text-2xl font-bold text-[#6F4E37]">{{ $cafe->cafe_name }}</h1>
                </header>

                <div class="flex-grow flex flex-col space-y-4 overflow-y-auto pr-2 text-sm">
                    <!-- Rating -->
                    <div class="text-center">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ให้คะแนน</label>
                        <div class="star-rating inline-flex flex-row-reverse justify-center">
                            <input type="radio" id="star5" name="rating" value="5" required /><label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4" /><label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3" /><label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2" /><label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1" /><label for="star1">★</label>
                        </div>
                    </div>
                    
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">หัวข้อรีวิว</label>
                        <!-- ✨ MODIFIED: เพิ่ม x-model="title" -->
                        <input x-model="title" type="text" class="form-input-elegant w-full" id="title" name="title" placeholder="สรุปสั้นๆ..." required>
                    </div>

                    <!-- Content -->
                    <div class="flex flex-col flex-grow">
                        <label for="content" class="block text-sm font-semibold text-slate-700 mb-1">รายละเอียด</label>
                        <!-- ✨ MODIFIED: เพิ่ม x-model="content" -->
                        <textarea x-model="content" class="form-input-elegant w-full flex-grow text-sm" id="content" name="content" placeholder="เล่าประสบการณ์ของคุณ..." required></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-auto pt-4 flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 text-sm font-semibold bg-[#6F4E37] text-white rounded-lg hover:bg-[#5a3e2b] transition-all shadow-md">
                        ส่งรีวิว
                    </button>
                    <a href="{{ route('cafes.show', $cafe->id) }}" class="w-full text-center px-4 py-2 text-sm font-semibold bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200">
                        ยกเลิก
                    </a>
                </div>
            </div>
        </form>
    </main>

  <script>
    function reviewForm() {
      // ✨ NEW: กำหนดรายการคำหยาบ (สามารถเพิ่มคำอื่นๆ ได้ตามต้องการ)
      const badWords = ['เหี้ย', 'สัส', 'ควย', 'ไอ้', 'อี', 'ชิบหาย'];

      return {
        isDragging: false,
        files: [],
        // ✨ NEW: สร้างตัวแปรสำหรับเก็บค่าจากฟอร์ม
        title: '',
        content: '',

        // ✨ NEW: ฟังก์ชันสำหรับตรวจสอบคำหยาบ
        containsBadWords(text) {
          if (!text) return false;
          // ตรวจสอบว่ามีคำใดใน badWords อยู่ใน text หรือไม่
          return badWords.some(word => text.toLowerCase().includes(word));
        },

        // ✨ NEW: ฟังก์ชันที่จะทำงานเมื่อกดปุ่ม Submit
        validateAndSubmit(event) {
          // ตรวจสอบหัวข้อและเนื้อหา
          if (this.containsBadWords(this.title) || this.containsBadWords(this.content)) {
            // ถ้าเจอคำหยาบ ให้แจ้งเตือนและหยุดการทำงาน
            alert('กรุณาอย่าใช้คำไม่สุภาพในการรีวิว');
          } else {
            // ถ้าไม่เจอคำหยาบ ให้ส่งฟอร์มตามปกติ
            event.target.submit();
          }
        },

        handleSelect(event) {
          this.addFiles(event.target.files);
        },
        handleDrop(event) {
          this.isDragging = false;
          this.addFiles(event.dataTransfer.files);
        },
        addFiles(fileList) {
          const newFiles = Array.from(fileList);
          newFiles.forEach(file => {
            if (file.type.startsWith('image/')) {
              const reader = new FileReader();
              reader.onload = (e) => {
                this.files.push({ file: file, preview: e.target.result });
                this.updateFileInput();
              };
              reader.readAsDataURL(file);
            }
          });
        },
        removeFile(index) {
          this.files.splice(index, 1);
          this.updateFileInput();
        },
        updateFileInput() {
          const dataTransfer = new DataTransfer();
          this.files.forEach(item => dataTransfer.items.add(item.file));
          this.$refs.fileInput.files = dataTransfer.files;
        }
      }
    }
  </script>
</body>
</html>