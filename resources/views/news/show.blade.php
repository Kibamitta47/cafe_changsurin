<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $newsItem->title }} - น้องช้างสะเร็น</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8fafc; /* bg-slate-50 */
        }
        .prose {
            color: #334155; /* text-slate-700 */
        }
        .prose h1, .prose h2, .prose h3 {
            color: #1e293b; /* text-slate-800 */
        }
    </style>
</head>
<body class="bg-slate-50">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest

    @auth
        @include('components.2navbar')
    @endauth

    {{-- Main Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Layout 2 คอลัมน์ --}}
        <div class="flex flex-col lg:flex-row lg:gap-8">

            <!-- ========= คอลัมน์ซ้าย: เนื้อหาข่าวหลัก ========= -->
            <main class="w-full lg:w-2/3 bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200">
                
                {{-- หัวข้อ --}}
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                    {{ $newsItem->title }}
                </h1>
                
                {{-- ข้อมูลเสริม (วันที่เผยแพร่) --}}
                <div class="text-sm text-slate-500 mb-6">
                    <span class="inline-flex items-center">
                        <i class="far fa-calendar-alt mr-1.5"></i>
                        เผยแพร่: {{ optional($newsItem->start_datetime)->format('d F Y, H:i') ?? 'N/A' }}
                    </span>
                </div>

                {{-- รูปภาพหลัก (แสดงแค่รูปแรก) --}}
                @if(!empty($newsItem->images))
                    <div class="mb-6 rounded-xl overflow-hidden">
                        <img
                            src="{{ asset('storage/' . $newsItem->images[0]) }}"
                            alt="{{ $newsItem->title }}"
                            class="w-full h-auto object-cover"
                            loading="lazy">
                    </div>
                @endif
                
                <hr class="my-6 border-slate-200">

                {{-- เนื้อหา --}}
                <div class="prose max-w-none text-lg leading-relaxed whitespace-pre-wrap">
                    {{ $newsItem->content }}
                </div>

                {{-- แกลเลอรีรูปภาพ (รูปที่เหลือ) --}}
                @if(isset($newsItem->images) && count($newsItem->images) > 1)
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold text-slate-800 mb-4">แกลเลอรี</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            {{-- วนลูปเฉพาะรูปที่ 2 เป็นต้นไป --}}
                            @foreach(array_slice($newsItem->images, 1) as $imageFile)
                                <div class="aspect-square overflow-hidden rounded-lg border">
                                    <img
                                        src="{{ asset('storage/' . $imageFile) }}"
                                        alt="{{ $newsItem->title }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ลิงก์เพิ่มเติม --}}
                @if($newsItem->link_url)
                <div class="mt-8 pt-6 border-t border-slate-200">
                    <a
                        href="{{ $newsItem->link_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center text-cyan-600 hover:text-cyan-800 hover:underline transition-colors duration-200 font-semibold">
                        <i class="fas fa-link mr-2"></i>อ่านข้อมูลเพิ่มเติมที่ลิงก์ต้นฉบับ
                    </a>
                </div>
                @endif
            </main>

            <!-- ========= คอลัมน์ขวา: ข่าวสารแนะนำ ========= -->
            <aside class="w-full lg:w-1/3 mt-8 lg:mt-0">
                <div class="sticky top-24 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">ข่าวสารแนะนำ</h2>
                    
                    @if($recommendedNews->isNotEmpty())
                        <div class="space-y-4">
                            {{-- วนลูปแสดงข่าวแนะนำ --}}
                            @foreach($recommendedNews as $recItem)
                                <a
                                    href="{{ route('news.show', $recItem) }}"
                                    class="flex items-start gap-4 group hover:bg-slate-50 p-2 rounded-lg transition-colors">
                                    {{-- รูปของข่าวแนะนำ --}}
                                    <div class="w-24 h-20 shrink-0 overflow-hidden rounded-md">
                                        @php
                                            $recImages = is_array($recItem->images) ? $recItem->images : [];
                                            $recImageUrl = !empty($recImages) ? asset('storage/' . $recImages[0]) : asset('images/no-image.png');
                                        @endphp
                                        <img
                                            src="{{ $recImageUrl }}"
                                            alt="{{ $recItem->title }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy">
                                    </div>
                                    {{-- เนื้อหาของข่าวแนะนำ --}}
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-slate-800 group-hover:text-cyan-600 line-clamp-2 leading-tight">
                                            {{ $recItem->title }}
                                        </h3>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ optional($recItem->start_datetime)->format('d M Y') }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500">ไม่มีข่าวสารแนะนำในขณะนี้</p>
                    @endif
                </div>
            </aside>

        </div>
    </div>

</body>
</html>
