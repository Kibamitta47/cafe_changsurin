<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>น้องช้างสะเร็น - ระบบคาเฟ่และข่าวสาร</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body { font-family: 'Kanit', sans-serif; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        [x-cloak] { display: none !important; }

        /* ===== [ CSS สำหรับ Slider ใหม่ ] ===== */
        .slider {
            position: relative;
            width: 100%;
            margin: auto;
            overflow: hidden;
            border-radius: 1rem; /* 16px */
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .slides a {
            min-width: 100%;
            box-sizing: border-box;
        }
        .slides img {
            width: 100%;
            display: block;
            aspect-ratio: 2 / 1; /* 16:8 Aspect Ratio */
            object-fit: cover;
        }
        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0,0,0,0.4);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
            z-index: 10;
        }
        .nav-btn:hover {
            background-color: rgba(0,0,0,0.6);
        }
        .prev { left: 10px; }
        .next { right: 10px; }
        .dots {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            z-index: 10;
        }
        .dot {
            height: 12px;
            width: 12px;
            margin: 0 5px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .dot.active, .dot:hover {
            background-color: white;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest

    @auth
        @include('components.2navbar')
    @endauth

    {{-- Main Content --}}
    <div
        class="w-full"
        x-data="pageController({ initialLikedIds: {{ json_encode($likedCafeIds ?? []) }} })"
        x-init="initializeAllData()"
        x-cloak>

        <div class="grid grid-cols-12 gap-8 max-w-screen-2xl mx-auto">

            {{-- Left Column - Filters --}}
            <aside class="col-span-12 lg:col-span-3 py-12 px-4">
                <div class="sticky top-24">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm p-4 space-y-4 border border-slate-200">
                        <div class="pb-4 border-b border-slate-200">
                             <h3 class="text-lg font-bold text-slate-800 mb-3">ค้นหา & ตัวกรอง</h3>
                             <div class="relative">
                                 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                     <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                 </div>
                                 <input
                                     type="text"
                                     x-model.debounce.300ms="searchTerm"
                                     placeholder="ชื่อคาเฟ่, สไตล์, ข่าว..."
                                     class="w-full py-2 pl-9 pr-3 text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-cyan-500 focus:bg-white transition-all"
                                 >
                             </div>
                        </div>

                        <div class="flex justify-between items-center pt-1">
                            <h3 class="text-md font-bold text-slate-700">ตัวกรองละเอียด</h3>
                            <button @click="clearFilters()" class="text-sm font-medium text-cyan-600 hover:text-cyan-800 hover:underline">ล้างทั้งหมด</button>
                        </div>

                         <div class="border-t border-slate-200 pt-3">
                            <h4 class="font-semibold text-slate-700 mb-2 text-sm">เรตติ้ง</h4>
                            <div class="flex items-center space-x-1">
                                <template x-for="star in 5">
                                    <button
                                        @click="setRatingFilter(star)"
                                        class="focus:outline-none transition-colors"
                                        :class="{
                                            'text-amber-400': filters.rating >= star,
                                            'text-slate-300': filters.rating < star
                                        }">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    </button>
                                </template>
                            </div>
                            <p x-show="filters.rating > 0" class="text-xs text-slate-500 mt-2">
                                แสดงคาเฟ่เรตติ้ง <span x-text="filters.rating"></span> ดาวขึ้นไป <button @click="setRatingFilter(filters.rating)" class="text-cyan-600 hover:underline">(ยกเลิก)</button>
                            </p>
                        </div>

                        <div class="border-t border-slate-200 pt-3 space-y-3">
                             <h4 class="font-semibold text-slate-700 mb-2 text-sm">วันเปิดทำการ</h4>
                             <div class="space-y-1">
                                <template x-for="day in availableFilters.days" :key="day">
                                    <label class="flex items-center text-slate-600 text-sm hover:text-cyan-700 cursor-pointer transition-colors">
                                        <input type="checkbox" x-model="filters.days" :value="day" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                        <span class="ml-2" x-text="day"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 pt-3">
                             <h4 class="font-semibold text-slate-700 mb-2 text-sm">สถานะร้าน</h4>
                             <label class="flex items-center text-slate-600 text-sm hover:text-cyan-700 cursor-pointer transition-colors">
                                <input type="checkbox" x-model="filters.isNewOpening" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                <span class="ml-2">✨ คาเฟ่เปิดใหม่</span>
                            </label>
                        </div>

                        <div class="border-t border-slate-200 pt-3">
                            <h4 class="font-semibold text-slate-700 mb-2 text-sm">เวลาเปิดทำการ</h4>
                            <div class="relative">
                                <select x-model="selectedHour" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2">
                                    <option value="">-- เลือกเวลา --</option>
                                    <template x-for="hour in Array.from({ length: 18 }, (_, i) => i + 6)">
                                        <option :value="String(hour).padStart(2, '0')" x-text="`${String(hour).padStart(2, '0')}:00 น.`"></option>
                                    </template>
                                </select>
                            </div>
                             <p x-show="selectedHour" class="text-xs text-slate-500 mt-2">
                                ค้นหาร้านที่เปิด ณ เวลา <span x-text="`${selectedHour}:00 น.`"></span>
                            </p>
                        </div>

                        <div class="border-t border-slate-200 pt-3">
                            <h4 class="font-semibold text-slate-700 mb-2 text-sm">ช่วงราคา</h4>
                            <div class="space-y-1">
                                <template x-for="priceSymbol in availableFilters.priceRanges" :key="priceSymbol">
                                    <label class="flex items-center text-slate-600 text-sm hover:text-cyan-700 cursor-pointer transition-colors">
                                        <input type="checkbox" x-model="filters.priceRanges" :value="priceSymbol" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                        <span class="ml-2" x-text="priceSymbol"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 pt-3"><h4 class="font-semibold text-slate-700 mb-2 text-sm">สไตล์คาเฟ่</h4><div class="space-y-1 max-h-40 overflow-y-auto pr-2"><template x-for="style in availableFilters.styles" :key="style"><label class="flex items-center text-slate-600 text-sm hover:text-cyan-700 cursor-pointer transition-colors"><input type="checkbox" x-model="filters.styles" :value="style" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-2" x-text="style"></span></label></template></div></div>
                        <div class="border-t border-slate-200 pt-3"><h4 class="font-semibold text-slate-700 mb-2 text-sm">ช่องทางชำระเงิน</h4><div class="space-y-1"><template x-for="method in availableFilters.paymentMethods" :key="method"><label class="flex items-center text-slate-600 text-sm hover:text-cyan-700 cursor-pointer transition-colors"><input type="checkbox" x-model="filters.paymentMethods" :value="method" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-2" x-text="method"></span></label></template></div></div>
                        <div class="border-t border-slate-200 pt-3"><h4 class="font-semibold text-slate-700 mb-2 text-sm">สิ่งอำนวยความสะดวก</h4><div class="space-y-1 max-h-40 overflow-y-auto pr-2"><template x-for="facility in availableFilters.facilities" :key="facility"><label class="flex items-center text-slate-600 text-sm hover:text-cyan-700 cursor-pointer transition-colors"><input type="checkbox" x-model="filters.facilities" :value="facility" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-2" x-text="facility"></span></label></template></div></div>
                        <div class="border-t border-slate-200 pt-3"><h4 class="font-semibold text-slate-700 mb-2 text-sm">บริการเพิ่มเติม</h4><div class="space-y-1"><template x-for="service in availableFilters.otherServices" :key="service"><label class="flex items-center text-slate-600 text-sm hover:text-cyan-700 cursor-pointer transition-colors"><input type="checkbox" x-model="filters.otherServices" :value="service" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-2" x-text="service"></span></label></template></div></div>
                    </div>
                </div>
            </aside>

            {{-- Middle Column - Main Content --}}
            <main class="col-span-12 lg:col-span-6 py-12 px-4 space-y-8">

                    <!-- ===== [ Slider ใหม่ ] ===== -->
                    <div class="slider mb-8">
                        <div class="slides">
                          <a href="{{ route('cafes.top10') }}">
                            <img src="{{ asset('/images/TOP-10.png') }}" alt="10 อันดับคาเฟ่แนะนำ">
                          </a>
                          <a href="{{ route('cafes.top10') }}">
                            {{-- คุณสามารถเปลี่ยนรูปที่ 2 ได้ที่นี่ --}}
                            <img src="{{ asset('/images/banner2.jpg') }}" alt="คาเฟ่บรรยากาศดี">
                          </a>
                          <a href="{{ route('cafes.top10') }}">
                             {{-- คุณสามารถเปลี่ยนรูปที่ 3 ได้ที่นี่ --}}
                            <img src="{{ asset('/images/banner3.jpg') }}" alt="กาแฟรสเลิศ">
                          </a>
                        </div>
                        <button class="nav-btn prev">&#10094;</button>
                        <button class="nav-btn next">&#10095;</button>
                        <div class="dots"></div>
                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="cafesSection">
                        <template x-for="cafe in filteredCafes.slice(0, displayedCafeCount)" :key="cafe.id">
                            <div
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="bg-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 border border-transparent hover:border-cyan-200 group flex flex-col">

                                <div x-data="{ activeImageIndex: 0 }" class="relative h-48 overflow-hidden rounded-t-2xl">
                                    <a :href="cafe.link">
                                        <img :src="cafe.imageUrls[activeImageIndex]" :alt="'รูปคาเฟ่ ' + cafe.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
                                    </a>
                                    <template x-if="cafe.imageUrls.length > 1">
                                        <div class="absolute inset-0 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <button @click.prevent="activeImageIndex = (activeImageIndex - 1 + cafe.imageUrls.length) % cafe.imageUrls.length" class="w-8 h-8 flex items-center justify-center bg-black/40 text-white rounded-full hover:bg-black/60 transition ml-2"><i class="fa-solid fa-chevron-left"></i></button>
                                            <button @click.prevent="activeImageIndex = (activeImageIndex + 1) % cafe.imageUrls.length" class="w-8 h-8 flex items-center justify-center bg-black/40 text-white rounded-full hover:bg-black/60 transition mr-2"><i class="fa-solid fa-chevron-right"></i></button>
                                        </div>
                                    </template>
                                    <template x-if="cafe.imageUrls.length > 1">
                                        <div x-text="`${activeImageIndex + 1} / ${cafe.imageUrls.length}`" class="absolute bottom-2 right-2 bg-black/50 text-white text-xs font-bold px-2 py-1 rounded-md"></div>
                                    </template>
                                    @auth
                                     <button @click.prevent="toggleLike(cafe.id)" class="absolute top-2 right-2 w-9 h-9 bg-black/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/60 transition-colors duration-300 z-10">
                                        <i class="fa-heart text-lg transition-all" :class="isLiked(cafe.id) ? 'fa-solid text-pink-500' : 'fa-regular'"></i>
                                    </button>
                                    @endauth
                                    <div x-show="cafe.isNewOpening" class="absolute bottom-2 left-2 bg-fuchsia-500 text-white px-3 py-1 rounded-full text-xs font-semibold tracking-wide shadow-md">✨ เปิดใหม่</div>
                                </div>

                                <div class="p-4 flex flex-col flex-grow">
                                    <h3 class="text-lg font-bold mb-1 text-slate-900 group-hover:text-cyan-600 transition-colors line-clamp-2"><a :href="cafe.link" x-text="cafe.title"></a></h3>
                                    <p class="text-slate-500 text-sm mb-3 line-clamp-2" x-text="cafe.address"></p>
                                    <div class="flex-grow space-y-3">
                                        <div class="flex items-center mb-2">
                                            <template x-for="i in 5">
                                                <i class="fa-solid fa-star text-xs" :class="i <= Math.round(cafe.rating) ? 'text-amber-400' : 'text-slate-300'"></i>
                                            </template>
                                            <span x-show="cafe.rating > 0" class="ml-2 text-xs text-slate-500" x-text="`(${parseFloat(cafe.rating).toFixed(1)})`"></span>
                                            <span x-show="cafe.rating == 0" class="ml-2 text-xs text-slate-400">ยังไม่มีรีวิว</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="style in cafe.cafeStyles.slice(0, 2)" :key="style"><span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-xs font-medium" x-text="style"></span></template>
                                        </div>
                                        <div class="text-xs space-y-1.5 text-slate-600">
                                            <div class="flex items-center">
                                                <i class="fa-regular fa-clock w-4 h-4 mr-1.5 text-indigo-500 shrink-0"></i>
                                                <span x-text="`${cafe.openDay}${cafe.closeDay ? ' - ' + cafe.closeDay : ''}${cafe.openTime ? `, ${cafe.openTime} น. - ${cafe.closeTime} น.` : ''}`"></span>
                                            </div>

                                            <div x-show="cafe.originalPriceRange" class="flex items-center"><i class="fa-solid fa-tags w-4 h-4 mr-1.5 text-green-500 shrink-0"></i><span>ราคา: </span><span x-text="cafe.originalPriceRange"></span></div>

                                        </div>
                                    </div>
                                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                        <a :href="cafe.link" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 font-semibold group-hover:underline text-sm">ดูรายละเอียด<i class="fa-solid fa-arrow-right w-3 h-3 ml-1.5 transition-transform group-hover:translate-x-1"></i></a>
                                        <div x-data="{ shareText: 'แชร์' }" class="flex items-center space-x-2">
                                            <button @click.prevent="share(cafe.title, cafe.link, $el.querySelector('span'))" class="flex items-center space-x-1.5 text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded-lg text-xs transition"><i class="fa-solid fa-share-nodes"></i><span x-text="shareText"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="allCafes.length > 0 && filteredCafes.length > 0 && displayedCafeCount < filteredCafes.length" class="mt-12 text-center">
                        <p class="text-slate-500 mb-4" x-text="`กำลังแสดง ${Math.min(displayedCafeCount, filteredCafes.length)} จาก ${filteredCafes.length} คาเฟ่`"></p>
                        <button @click="loadMoreCafes()"
                                class="bg-cyan-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-cyan-700 transition-all duration-300 shadow-lg hover:shadow-cyan-500/30 transform hover:-translate-y-0.5">
                            โหลดเพิ่มเติม
                        </button>
                    </div>

                    <div x-show="allCafes.length > 0 && filteredCafes.length === 0"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        class="text-center py-16 px-6 bg-white rounded-2xl shadow-lg mt-8 col-span-full">
                        <div class="text-cyan-500 text-6xl mb-4">
                            <i class="fa-solid fa-mug-saucer"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">ไม่พบคาเฟ่ที่ตรงกับตัวกรอง</h3>
                        <p class="text-slate-500">ลองปรับเปลี่ยนคำค้นหา หรือ <button @click="clearFilters()" class="text-cyan-600 hover:underline font-medium">ล้างตัวกรองทั้งหมด</button> เพื่อเริ่มต้นใหม่</p>
                    </div>

                    <div x-show="allCafes.length === 0" class="text-center py-16 bg-white rounded-2xl mt-8 col-span-full"><div class="text-gray-400 text-8xl mb-6">☕</div><h3 class="text-2xl font-bold text-gray-500 mb-2">ยังไม่มีคาเฟ่ในระบบ</h3><p class="text-gray-400">กรุณาติดตามคาเฟ่ใหม่ๆ ในภายหลัง</p></div>
            </main>

            {{-- Right Column - News --}}
            <aside class="col-span-12 lg:col-span-3 py-12 px-4 hidden lg:block">
                 <div class="sticky top-24">
                    <div class="bg-white p-6 rounded-2xl shadow-sm space-y-4">
                        <h2 class="text-xl font-bold text-slate-800">ข่าวทั้งหมด</h2>
                        <template x-for="newsItem in filteredNews" :key="newsItem.id">
                            <div class="flex items-start gap-x-4 pt-4 border-t border-slate-200/80">
                                <a :href="newsItem.link" class="flex-shrink-0">
                                    <img :src="newsItem.image" :alt="'รูปภาพข่าว ' + newsItem.title" class="w-16 h-16 object-cover rounded-lg bg-slate-200">
                                </a>
                                <div>
                                    <a :href="newsItem.link" class="text-slate-700 font-semibold hover:text-cyan-600 hover:underline block leading-tight line-clamp-3" x-text="newsItem.title">
                                    </a>
                                    <p class="text-xs text-slate-500 mt-1" x-text="newsItem.dateString"></p>
                                </div>
                            </div>
                        </template>
                        <div x-show="filteredNews.length === 0 && allNews.length > 0" class="pt-4 border-t">
                            <p class="text-sm text-slate-500">ไม่พบข่าวสารที่ตรงกับคำค้นหา</p>
                        </div>
                        <div x-show="allNews.length === 0" class="pt-4 border-t">
                            <p class="text-sm text-slate-500">ยังไม่มีข่าวสาร</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <div id="cafe-data-source" class="hidden">
            @if ($cafes && $cafes->count() > 0)
                @foreach ($cafes as $cafe)
                @php
                    $cafeImages = is_array($cafe->images) ? $cafe->images : [];
                    $imageUrls = [];
                    foreach ($cafeImages as $img) { $imageUrls[] = asset('storage/' . $img); }
                    if (empty($imageUrls)) { $imageUrls[] = asset('images/no-image.png'); }
                    $openTime = $cafe->open_time ? \Carbon\Carbon::parse($cafe->open_time)->format('H:i') : '';
                    $closeTime = $cafe->close_time ? \Carbon\Carbon::parse($cafe->close_time)->format('H:i') : '';
                    $cafeStyles = is_array($cafe->cafe_styles) ? $cafe->cafe_styles : [];
                    $facilities = is_array($cafe->facilities) ? $cafe->facilities : [];
                    $payment_methods = is_array($cafe->payment_methods) ? $cafe->payment_methods : [];
                    $other_services = is_array($cafe->other_services) ? $cafe->other_services : [];

                    $priceSymbol = '';
                    switch ($cafe->price_range) {
                        case 'ต่ำกว่า 100':   $priceSymbol = '$';     break;
                        case '101 - 250':     $priceSymbol = '$$';    break;
                        case '251 - 500':     $priceSymbol = '$$$';   break;
                        case '501 - 1,000':   $priceSymbol = '$$$$';  break;
                        case 'มากกว่า 1,000': $priceSymbol = '$$$$$'; break;
                    }
                @endphp
                <div class="cafe-item"
                     data-id="{{ $cafe->cafe_id }}"
                     data-link="{{ route('cafes.show', $cafe) }}"
                    data-images="{{ json_encode($imageUrls) }}"
                    data-title="{{ $cafe->cafe_name ?? '' }}"
                    data-address="{{ $cafe->address }}"
                    data-place-name="{{ $cafe->place_name }}"
                    data-rating="{{ $cafe->reviews_avg_rating ?? 0 }}"
                    data-open-day="{{ $cafe->open_day }}"
                    data-close-day="{{ $cafe->close_day ?? '' }}"
                    data-open-time="{{ $openTime }}"
                    data-close-time="{{ $closeTime }}"
                    data-phone="{{ $cafe->phone ?? '' }}"
                    data-price-range="{{ $priceSymbol }}"
                    data-original-price-range="{{ $cafe->price_range ?? '' }}"
                    data-is-new-opening="{{ $cafe->is_new_opening ? 'true' : 'false' }}"
                    data-styles="{{ implode(',', $cafeStyles) }}"
                    data-facilities="{{ implode(',', $facilities) }}"
                    data-payment-methods="{{ implode(',', $payment_methods) }}"
                    data-other-services="{{ implode(',', $other_services) }}">
                </div>
            @endforeach
        @endif
    </div>

    <div id="news-data-source" class="hidden">
        @if ($news && $news->count() > 0)
            @foreach ($news as $item)
                @php
                    $newsImages = is_array($item->images) ? $item->images : [];
                    $imageUrl = !empty($newsImages) ? asset('storage/' . $newsImages[0]) : asset('images/no-image.png');
                    $dateString = $item->created_at->translatedFormat('j M Y');
                @endphp
                <div class="news-item"
                     data-id="{{ $item->addnews_admin_id }}"
                     data-title="{{ $item->title }}"
                     data-link="{{ route('news.show', $item) }}"
                     data-image="{{ $imageUrl }}"
                     data-date-string="{{ $dateString }}">
                </div>
            @endforeach
        @endif
    </div>

    <footer class="bg-slate-900 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div><h4 class="text-2xl font-bold mb-4 text-cyan-400">น้องช้างสะเร็น</h4>
            <p class="text-slate-400 text-sm">แพลตฟอร์มค้นหาข่าวสารและคาเฟ่ในจังหวัดสุรินทร์</p></div>
            <div><h5 class="text-lg font-semibold mb-4 text-slate-200">หมวดหมู่</h5>
            <ul class="space-y-3 text-slate-400 text-sm">
                <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">ข่าวสาร</a></li>
                <li><a  href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">คาเฟ่</a></li>
                <li><a  href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">โปรโมชั่น</a></li>
            </ul></div>
            <div><h5 class="text-lg font-semibold mb-4 text-slate-200">ลิงก์ด่วน</h5>
            <ul class="space-y-3 text-slate-400 text-sm"><li>
            <a href="{{ route('about.us') }}" class="hover:text-cyan-300 transition-colors">เกี่ยวกับเรา</a></li>
            <li><a href="{{ route('problem.info') }}" class="hover:text-cyan-300 transition-colors">แจ้งปัญหา</a>
            </li><li><a href="{{ route('advertising.packages') }}" class="hover:text-cyan-300 transition-colors">ติดต่อโฆษณา</a></li>
            <li><a href="{{ url('/login-admin') }}" class="hover:text-cyan-300 transition-colors">สำหรับ Admin Login</a></li></ul></div>
            <div><h5 class="text-lg font-semibold mb-4 text-slate-200">ติดตามเรา</h5>
           <div class="flex flex-col space-y-2 text-slate-400">
    <div class="flex space-x-4">
                    <a href="#" class="text-black">
                        <i class="fab fa-facebook-f text-2xl" style="color: #1877F2; transition: color 0.3s;"></i>
                    </a>
                    <a href="https://line.me/ti/p/@363tvzhr" class="text-black" target="_blank">
                        <i class="fab fa-line text-2xl" style="color: #00C300; transition: color 0.3s;"></i>
                    </a>
                </div>
    <p class="font-semibold">ติดต่อ: nongchangsaren@gmail.com</p>
    <p>โทรศัพท์: 08-XXXX-XXXX</p>
</div>

        <div class="border-t border-slate-800 mt-12 pt-8 text-center text-slate-500 text-sm">© 2024 น้องช้างสะเร็น. สงวนลิขสิทธิ์</div>
    </footer>

    <script>
    // โค้ด Alpine.js เดิม
    function newsCarousel(config) {
        return {
            activeSlide: 1, totalSlides: config.totalSlides, autoplayInterval: null, autoplay: config.autoplay || false,
            goToSlide(index) { this.activeSlide = index; this.resetAutoplay(); },
            next() { this.activeSlide = this.activeSlide === this.totalSlides ? 1 : this.activeSlide + 1; this.resetAutoplay(); },
            prev() { this.activeSlide = this.activeSlide === 1 ? this.totalSlides : this.activeSlide - 1; this.resetAutoplay(); },
            startAutoplay() { if (this.autoplay) { this.autoplayInterval = setInterval(() => { this.next(); }, 5000); } },
            resetAutoplay() { if (this.autoplayInterval) { clearInterval(this.autoplayInterval); this.startAutoplay(); } },
        };
    }

    function pageController(config) {
        return {
            allCafes: [],
            filteredCafes: [],
            allNews: [],
            filteredNews: [],
            displayedCafeCount: 12,
            cafesPerPage: 12,
            searchTerm: '',
            selectedHour: '',
            availableFilters: {
                priceRanges: ['$', '$$', '$$$', '$$$$', '$$$$$'],
                days: ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'],
                styles: [], facilities: [], paymentMethods: [], otherServices: []
            },
            filters: {
                rating: 0,
                time: '',
                days: [],
                isNewOpening: false,
                priceRanges: [],
                styles: [],
                facilities: [],
                paymentMethods: [],
                otherServices: []
            },
            likedCafeIds: new Set(),

            initializeAllData() {
                this.likedCafeIds = new Set(config.initialLikedIds || []);
                this.loadCafeData();
                this.loadNewsData();
                this.extractAvailableFilters();
                this.$watch('searchTerm', () => this.applyFilters());
                this.$watch('filters', () => this.applyFilters(), { deep: true });
                this.$watch('selectedHour', (newHour) => {
                    this.filters.time = newHour ? `${newHour}:00` : '';
                });
                this.applyFilters();
            },
            loadNewsData() {
                const newsElements = document.querySelectorAll('#news-data-source .news-item');
                this.allNews = Array.from(newsElements).map(el => ({ id: parseInt(el.dataset.id), title: el.dataset.title, link: el.dataset.link, image: el.dataset.image, dateString: el.dataset.dateString, }));
            },
            loadCafeData() {
                const cafeElements = document.querySelectorAll('#cafe-data-source .cafe-item');
                this.allCafes = Array.from(cafeElements).map(el => ({
                    id: parseInt(el.dataset.id),
                    title: el.dataset.title,
                    address: el.dataset.address,
                    link: el.dataset.link,
                    imageUrls: JSON.parse(el.dataset.images || '[]'),
                    placeName: el.dataset.placeName,
                    rating: parseFloat(el.dataset.rating || 0),
                    openDay: el.dataset.openDay,
                    closeDay: el.dataset.closeDay,
                    openTime: el.dataset.openTime,
                    closeTime: el.dataset.closeTime,
                    phone: el.dataset.phone,
                    priceRange: el.dataset.priceRange,
                    originalPriceRange: el.dataset.originalPriceRange,
                    isNewOpening: el.dataset.isNewOpening === 'true',
                    cafeStyles: el.dataset.styles ? el.dataset.styles.split(',').filter(s => s) : [],
                    facilities: el.dataset.facilities ? el.dataset.facilities.split(',').filter(f => f) : [],
                    paymentMethods: el.dataset.paymentMethods ? el.dataset.paymentMethods.split(',').filter(p => p) : [],
                    otherServices: el.dataset.otherServices ? el.dataset.otherServices.split(',').filter(o => o) : [],
                }));
            },
            extractAvailableFilters() {
                const allStyles = new Set(), allFacilities = new Set(), allPaymentMethods = new Set(), allOtherServices = new Set();
                this.allCafes.forEach(cafe => { if(cafe.cafeStyles) cafe.cafeStyles.forEach(style => allStyles.add(style)); if(cafe.facilities) cafe.facilities.forEach(facility => allFacilities.add(facility)); if(cafe.paymentMethods) cafe.paymentMethods.forEach(method => allPaymentMethods.add(method)); if(cafe.otherServices) cafe.otherServices.forEach(service => allOtherServices.add(service)); });
                this.availableFilters.styles = Array.from(allStyles).sort(); this.availableFilters.facilities = Array.from(allFacilities).sort(); this.availableFilters.paymentMethods = Array.from(allPaymentMethods).sort(); this.availableFilters.otherServices = Array.from(allOtherServices).sort();
            },
            setRatingFilter(star) {
                this.filters.rating = (this.filters.rating === star) ? 0 : star;
            },
            applyFilters() {
                this.displayedCafeCount = this.cafesPerPage;
                const lowerCaseSearchTerm = this.searchTerm.toLowerCase().trim();
                const dayMap = { 'จันทร์': 0, 'อังคาร': 1, 'พุธ': 2, 'พฤหัสบดี': 3, 'ศุกร์': 4, 'เสาร์': 5, 'อาทิตย์': 6 };
                this.filteredCafes = this.allCafes.filter(cafe => {
                    if (this.filters.rating > 0 && cafe.rating < this.filters.rating) return false;
                    if (this.filters.isNewOpening && !cafe.isNewOpening) return false;
                    if (this.filters.days.length > 0) {
                        let isCafeOpenOnSelectedDays = false;
                        if (cafe.openDay === 'ทุกวัน') {
                            isCafeOpenOnSelectedDays = true;
                        } else if (cafe.openDay === 'จันทร์-ศุกร์') {
                            const weekdays = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์'];
                            if (this.filters.days.some(day => weekdays.includes(day))) {
                                isCafeOpenOnSelectedDays = true;
                            }
                        } else {
                            const openDayNum = dayMap[cafe.openDay];
                            const closeDayNum = dayMap[cafe.closeDay];
                            if (openDayNum !== undefined && closeDayNum !== undefined) {
                                if (this.filters.days.some(selectedDay => {
                                    const selectedDayNum = dayMap[selectedDay];
                                    if (closeDayNum >= openDayNum) {
                                        return selectedDayNum >= openDayNum && selectedDayNum <= closeDayNum;
                                    } else {
                                        return selectedDayNum >= openDayNum || selectedDayNum <= closeDayNum;
                                    }
                                })) {
                                    isCafeOpenOnSelectedDays = true;
                                }
                            }
                        }
                        if (!isCafeOpenOnSelectedDays) return false;
                    }
                    if (this.filters.time) {
                        if (!cafe.openTime || !cafe.closeTime) return false;
                        const userTime = this.filters.time;
                        if (cafe.closeTime >= cafe.openTime) {
                            if (userTime < cafe.openTime || userTime >= cafe.closeTime) return false;
                        } else {
                            if (userTime < cafe.openTime && userTime >= cafe.closeTime) return false;
                        }
                    }
                    if (this.filters.priceRanges.length > 0 && !this.filters.priceRanges.includes(cafe.priceRange)) return false;
                    if (this.filters.styles.length > 0 && (!cafe.cafeStyles || !this.filters.styles.some(style => cafe.cafeStyles.includes(style)))) return false;
                    if (this.filters.facilities.length > 0 && (!cafe.facilities || !this.filters.facilities.every(facility => cafe.facilities.includes(facility)))) return false;
                    if (this.filters.paymentMethods.length > 0 && (!cafe.paymentMethods || !this.filters.paymentMethods.some(method => cafe.paymentMethods.includes(method)))) return false;
                    if (this.filters.otherServices.length > 0 && (!cafe.otherServices || !this.filters.otherServices.every(service => cafe.otherServices.includes(service)))) return false;
                    if (lowerCaseSearchTerm) {
                        const searchableContent = `${cafe.title} ${cafe.address} ${cafe.placeName} ${cafe.cafeStyles.join(' ')} ${cafe.facilities.join(' ')} ${cafe.otherServices.join(' ')}`.toLowerCase();
                        if (!searchableContent.includes(lowerCaseSearchTerm)) return false;
                    }
                    return true;
                });
                this.filteredNews = this.allNews.filter(newsItem => {
                    if (!lowerCaseSearchTerm) return true;
                    return newsItem.title.toLowerCase().includes(lowerCaseSearchTerm);
                });
            },
            clearFilters() {
                this.filters.rating = 0;
                this.filters.time = '';
                this.selectedHour = '';
                this.filters.days = [];
                this.filters.isNewOpening = false;
                this.filters.priceRanges = [];
                this.filters.styles = [];
                this.filters.facilities = [];
                this.filters.paymentMethods = [];
                this.filters.otherServices = [];
                this.searchTerm = '';
            },
            isLiked(cafeId) {
                return this.likedCafeIds.has(cafeId);
            },
            toggleLike(cafeId) {
                if (!cafeId || isNaN(parseInt(cafeId))) {
                    console.error('Invalid cafeId passed to toggleLike:', cafeId);
                    return;
                }
                fetch(`/cafes/${cafeId}/toggle-like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                       console.error('Server responded with an error.');
                       return Promise.reject('Server error');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        if (data.is_liked) {
                            this.likedCafeIds.add(cafeId);
                        } else {
                            this.likedCafeIds.delete(cafeId);
                        }
                    } else {
                        console.error('Server returned a non-success status.');
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the like operation:', error);
                });
            },
            loadMoreCafes() { this.displayedCafeCount += this.cafesPerPage; },
            share(title, url, buttonElement) {
                if (navigator.share) { navigator.share({ title: title, url: url }).catch(console.error); }
                else { navigator.clipboard.writeText(url).then(() => { const originalText = buttonElement.innerText; buttonElement.innerText = 'คัดลอกแล้ว!'; setTimeout(() => { buttonElement.innerText = originalText; }, 2000); }).catch(err => console.error('Could not copy text: ', err)); }
            }
        };
    }

    // โค้ดสำหรับ Slider ใหม่
    document.addEventListener('DOMContentLoaded', function () {
        const slider = document.querySelector('.slider');
        if (!slider) return; // ป้องกัน Error ถ้าไม่มี Slider ในหน้านั้น

        const slides = slider.querySelector('.slides');
        const slideItems = slider.querySelectorAll('.slides a');
        const prevBtn = slider.querySelector('.prev');
        const nextBtn = slider.querySelector('.next');
        const dotsContainer = slider.querySelector('.dots');
        let index = 0;
        let slideInterval;

        // สร้าง dot
        slideItems.forEach((_, i) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                goToSlide(i);
                resetInterval();
            });
            dotsContainer.appendChild(dot);
        });
        const dots = dotsContainer.querySelectorAll('.dot');

        function showSlide(i) {
            slides.style.transform = `translateX(-${i * 100}%)`;
            dots.forEach(d => d.classList.remove('active'));
            dots[i].classList.add('active');
        }

        function goToSlide(i) {
            index = i;
            showSlide(index);
        }

        function nextSlide() {
            index = (index + 1) % slideItems.length;
            showSlide(index);
        }

        function prevSlide() {
            index = (index - 1 + slideItems.length) % slideItems.length;
            showSlide(index);
        }

        function startInterval() {
            slideInterval = setInterval(nextSlide, 4000);
        }

        function resetInterval() {
            clearInterval(slideInterval);
            startInterval();
        }

        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetInterval();
        });

        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetInterval();
        });
        
        startInterval(); // เริ่มการเลื่อนอัตโนมัติ
    });
    </script>
</body>
</html>