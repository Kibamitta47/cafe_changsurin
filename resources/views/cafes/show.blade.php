<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title> {{ $cafe->cafe_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- เพิ่ม Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- เพิ่ม Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: url('https://images.unsplash.com/photo-1511920183303-52c142c6772c?auto=format&fit=crop&w=1350&q=80') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(20, 20, 20, 0.5);
            backdrop-filter: blur(8px);
            z-index: -1;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col text-slate-800 bg-slate-50">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest
    @auth
        @include('components.2navbar')
    @endauth

    <main class="flex-grow p-4 md:p-8" x-data="{ lightboxOpen: false, lightboxSrc: '' }">

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- คอลัมน์ซ้าย: ข้อมูลหลักและรูปภาพ --}}
            <div class="lg:col-span-2 space-y-8">
                <section class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-6 md:p-8">
                    {{-- ส่วนหัว --}}
                    <div class="border-b border-slate-200 pb-5 mb-6">
                        <h1 class="text-4xl md:text-5xl font-bold text-slate-900">{{ $cafe->cafe_name }}</h1>
                        <p class="text-slate-500 mt-2 text-lg">{{ $cafe->place_name }}</p>
                    </div>

                    {{-- รายละเอียดพร้อมไอคอน --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-slate-700">
                        <div class="flex items-start"><i class="fa-solid fa-location-dot text-cyan-500 w-5 mt-1 mr-3 shrink-0"></i><span><strong>ที่อยู่:</strong> {{ $cafe->address }}</span></div>
                        <div class="flex items-center"><i class="fa-solid fa-tags text-cyan-500 w-5 mr-3 shrink-0"></i><span><strong>ช่วงราคา:</strong> {{ $cafe->price_range ?? '-' }}</span></div>
                        
                        {{-- ========= START: CORRECTED PHONE NUMBER FIELD ========= --}}
                        <div class="flex items-center"><i class="fa-solid fa-phone text-cyan-500 w-5 mr-3 shrink-0"></i><span><strong>เบอร์โทรศัพท์:</strong> {{ $cafe->phone ?? '-' }}</span></div>
                        {{-- ========= END: CORRECTED PHONE NUMBER FIELD ========= --}}

                        <div class="flex items-start"><i class="fa-solid fa-clock text-cyan-500 w-5 mt-1 mr-3 shrink-0"></i>
                            <span><strong>เวลาทำการ:</strong>
                                @if($cafe->open_day && $cafe->open_time)
                                    {{ $cafe->open_day }} - {{ $cafe->close_day }}, {{ \Carbon\Carbon::parse($cafe->open_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($cafe->close_time)->format('H:i') }}
                                @else
                                    {{ $cafe->opening_hours ?? '-' }}
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center"><i class="fa-solid fa-globe text-cyan-500 w-5 mr-3 shrink-0"></i>
                            <span>
                                <strong>เว็บไซต์:</strong>
                                @if($cafe->website)
                                    <a href="{{ $cafe->website }}" target="_blank" class="text-cyan-600 hover:underline break-all">{{ $cafe->website }}</a>
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- รายละเอียดเพิ่มเติม --}}
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-lg font-semibold mb-2 flex items-center"><i class="fa-solid fa-circle-info mr-2 text-cyan-500"></i>รายละเอียดเพิ่มเติม</h3>
                        <p class="whitespace-pre-line text-slate-600 leading-relaxed">{{ $cafe->description ?? 'ไม่มีรายละเอียด' }}</p>
                    </div>

                    @php
                        $getDataAsArray = function($data) {
                            if (is_null($data)) return [];
                            $decoded = is_string($data) ? json_decode($data, true) : (is_array($data) ? $data : null);
                            if (is_array($decoded)) return array_filter($decoded);
                            if (is_string($data) && !empty($data)) return array_filter(array_map('trim', explode(',', $data)));
                            return [];
                        };
                        $amenities = $getDataAsArray($cafe->amenities);
                        $cafeStyles = $getDataAsArray($cafe->cafe_styles);
                        $paymentMethods = $getDataAsArray($cafe->payment_methods);
                        $otherServices = $getDataAsArray($cafe->other_services);
                    @endphp

                    @if(!empty($amenities))
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-lg font-semibold mb-3 flex items-center"><i class="fa-solid fa-wifi mr-2 text-cyan-500"></i>สิ่งอำนวยความสะดวก</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($amenities as $item) <span class="bg-cyan-100 text-cyan-800 text-sm font-medium px-3 py-1 rounded-full">{{ $item }}</span> @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($cafeStyles))
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-lg font-semibold mb-3 flex items-center"><i class="fa-solid fa-palette mr-2 text-purple-500"></i>สไตล์คาเฟ่</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($cafeStyles as $item) <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full">{{ $item }}</span> @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($paymentMethods))
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-lg font-semibold mb-3 flex items-center"><i class="fa-regular fa-credit-card mr-2 text-green-500"></i>ช่องทางชำระเงิน</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($paymentMethods as $item) <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">{{ $item }}</span> @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($otherServices))
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-lg font-semibold mb-3 flex items-center"><i class="fa-solid fa-bell-concierge mr-2 text-indigo-500"></i>บริการเพิ่มเติม</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($otherServices as $item) <span class="bg-indigo-100 text-indigo-800 text-sm font-medium px-3 py-1 rounded-full">{{ $item }}</span> @endforeach
                        </div>
                    </div>
                    @endif
                </section>

                {{-- แกลเลอรีรูปภาพ --}}
                @php
                    $images = is_string($cafe->images) ? json_decode($cafe->images, true) : (is_array($cafe->images) ? $cafe->images : []);
                @endphp
                @if (!empty($images))
                    <section class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-6 md:p-8">
                        <h2 class="text-2xl font-bold mb-5 flex items-center"><i class="fa-solid fa-images mr-3 text-amber-500"></i>แกลเลอรี</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach ($images as $img)
                                <div class="aspect-square overflow-hidden rounded-lg shadow-md group">
                                    <img
                                        src="{{ asset('storage/' . $img) }}"
                                        alt="{{ $cafe->cafe_name }}"
                                        class="w-full h-full object-cover cursor-pointer group-hover:scale-110 transition-transform duration-300 ease-in-out"
                                        @click="lightboxSrc='{{ asset('storage/' . $img) }}'; lightboxOpen=true"
                                    >
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- คอลัมน์ขวา: แผนที่และรีวิว --}}
            <div class="lg:col-span-1 space-y-8">
                {{-- แผนที่ --}}
                <section class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-6 text-center">
                    <i class="fa-solid fa-map-location-dot text-4xl text-amber-500 mb-4"></i>
                     <a href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}" target="_blank"
                       class="inline-block w-full px-6 py-3 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 transition-all shadow-lg hover:shadow-amber-500/30 transform hover:-translate-y-0.5">
                        ดูแผนที่ใน Google Maps
                    </a>
                </section>

                {{-- รีวิว --}}
                <section class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-6 md:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold flex items-center"><i class="fa-solid fa-star mr-2 text-amber-500"></i>รีวิวจากผู้ใช้</h2>
                        @auth
                            <a href="{{ route('user.reviews.create', ['cafe_id' => $cafe->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all shadow-md hover:shadow-green-600/30 transform hover:-translate-y-0.5 text-sm">
                                <i class="fa-solid fa-pen-to-square mr-2"></i> เขียนรีวิว
                            </a>
                        @endauth
                    </div>

                    @if($reviews->isEmpty())
    <div class="text-center py-8 bg-slate-50/70 rounded-lg">
        <i class="fa-solid fa-comment-slash text-4xl text-slate-400 mb-3"></i>
        <p class="text-slate-500">ยังไม่มีรีวิวสำหรับคาเฟ่นี้</p>
    </div>
@else
    <div class="space-y-6">
        @foreach ($reviews as $review)
            <div class="border-t border-slate-200 pt-6 first:border-t-0 first:pt-0">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-800">{{ $review->user_name ?? 'ผู้ใช้ไม่ระบุชื่อ' }}</p>
                    <p class="text-sm text-slate-500">{{ $review->created_at->format('d/m/Y') }}</p>
                </div>
                <p class="mt-1 font-bold">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star {{ $i <= ($review->rating ?? 0) ? 'text-amber-500' : 'text-slate-300' }}"></i>
                    @endfor
                    <span class="text-sm ml-1 text-slate-600">({{ $review->rating ?? '-' }}/5)</span>
                </p>
                {{-- === เพิ่มการแสดง title ของรีวิว === --}}
                <h4 class="font-semibold text-lg mt-3 text-slate-800">{{ $review->title }}</h4>
                <p class="mt-1 text-slate-700 whitespace-pre-line leading-relaxed">{{ $review->content }}</p>

                {{-- === จุดที่แก้ไขเพื่อแสดงรูปภาพจาก Relationship === --}}
               @php
    $images = is_string($review->images) ? json_decode($review->images, true) : (is_array($review->images) ? $review->images : []);
@endphp

@if(!empty($images))
    <div class="mt-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-3 lg:grid-cols-4 gap-2">
        @foreach($images as $image)
            <div class="aspect-square overflow-hidden rounded-md shadow-sm group">
                <img
                    src="{{ asset('storage/' . $image) }}"
                    alt="รูปรีวิว"
                    class="w-full h-full object-cover cursor-pointer group-hover:scale-110 transition-transform duration-300"
                    @click="lightboxSrc='{{ asset('storage/' . $image) }}'; lightboxOpen=true"
                >
            </div>
        @endforeach
    </div>
@endif
                {{-- === สิ้นสุดจุดแก้ไข === --}}

            </div>
        @endforeach
    </div>
@endif

        {{-- Lightbox --}}
        <div
            x-show="lightboxOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4"
            @click.self="lightboxOpen = false"
        >
            <button
                class="absolute top-4 right-4 text-white/70 hover:text-white transition w-12 h-12 bg-black/20 rounded-full flex items-center justify-center"
                @click="lightboxOpen = false"
                aria-label="ปิดรูป"
            >
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
            <img
                :src="lightboxSrc"
                alt="รูปขยายใหญ่"
                class="max-w-full max-h-full rounded-lg shadow-2xl"
                @click.stop
            >
        </div>

    </main>

    <footer class="bg-white/80 backdrop-blur-sm shadow-inner py-4 text-center text-slate-600 text-sm mt-8">
        © {{ date('Y') }} ระบบคาเฟ่
    </footer>
</body>
</html>