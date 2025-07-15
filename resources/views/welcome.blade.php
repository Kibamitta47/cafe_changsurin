<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .highlight { background-color: #a7f3d0; font-weight: 600; padding: 0 2px; border-radius: 3px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- เลือก navbar ตามสถานะ --}}
    @guest
        @include('components.1navbar')
    @endguest

    @auth
        @include('components.2navbar')
    @endauth

    {{-- Hero Section with Search --}}
    <div class="bg-gradient-to-br from-cyan-50 to-blue-200 text-slate-800 py-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('/img/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
        <div class="max-w-7xl mx-auto px-6 text-center relative">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-3 text-slate-900">🐘 น้องช้างสะเร็น</h1>
            <p class="text-lg md:text-xl text-slate-600 mb-8">ค้นหาทุกเรื่องราวและคาเฟ่ดีๆ ในสุรินทร์</p>
            
            <div class="max-w-2xl mx-auto" x-data="searchComponent()" x-init="initializeSearchData()">
                <div class="relative">
                    <div class="flex bg-white/70 backdrop-blur-sm rounded-xl shadow-lg overflow-hidden border border-slate-200">
                        <div class="flex-1 relative">
                            <input 
                                type="text" 
                                x-model="searchQuery"
                                @input.debounce.300ms="performSearch()"
                                placeholder="พิมพ์เพื่อค้นหาข่าวสาร, คาเฟ่, หรือกิจกรรม..."
                                class="w-full pl-5 pr-12 py-3 text-gray-800 text-base focus:outline-none bg-transparent placeholder-gray-500"
                            >
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        <button 
                            @click="performSearch()"
                            class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-6 py-3 font-bold text-base transition-all duration-300 hover:shadow-md"
                        >
                            ค้นหา
                        </button>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center items-center gap-x-2 gap-y-2 mt-4">
                    <span class="text-slate-600 text-xs font-medium">ค้นหาด่วน:</span>
                    <template x-for="tag in quickSearchTags" :key="tag">
                        <button @click="searchQuery = tag; performSearch()" class="bg-white/60 text-slate-700 px-2.5 py-1 rounded-full text-xs hover:bg-white transition-all backdrop-blur-sm border border-slate-200/50 hover:border-slate-300" x-text="tag"></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Results Section --}}
    <div x-show="$store.search.showResults" x-cloak class="max-w-7xl mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">🔍 ผลการค้นหาสำหรับ "<span x-text="$store.search.currentSearchQuery" class="text-cyan-600"></span>"</h3>
            <div id="searchResults" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <template x-for="item in $store.search.searchResults" :key="item.id">
                    <div x-data="{ isLiked: false, highlightText: searchComponent().highlightText }" class="bg-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 group flex flex-col">
                        <div class="relative h-56 overflow-hidden rounded-t-2xl">
                            <img :src="item.imageUrl" alt="รูปภาพ" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-3 left-3 bg-opacity-80 px-3 py-1 rounded-full text-sm font-medium" :class="item.type === 'news' ? 'bg-blue-600 text-white' : 'bg-amber-600 text-white'" x-text="item.type === 'news' ? 'ข่าวสาร' : 'คาเฟ่'"></div>
                            
                            <template x-if="item.type === 'cafe'">
                                <button @click.prevent="isLiked = !isLiked" class="absolute top-3 right-3 w-10 h-10 bg-black/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/60 transition-colors duration-300 z-10">
                                    <i class="fa-heart text-xl transition-all" :class="isLiked ? 'fa-solid text-pink-500' : 'fa-regular'"></i>
                                </button>
                            </template>

                            <template x-if="item.type === 'cafe' && item.isNewOpening"><div class="absolute bottom-3 left-3 bg-fuchsia-500 text-white px-3 py-1 rounded-full text-xs font-semibold tracking-wide">✨ เปิดใหม่</div></template>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold mb-3 text-slate-800 group-hover:text-cyan-600 transition-colors line-clamp-2" x-html="highlightText(item.title, $store.search.currentSearchQuery)"></h3>
                            <template x-if="item.type === 'news'"><div class="text-slate-600 mb-4 text-sm flex-grow"><p class="line-clamp-3 leading-relaxed" x-html="highlightText(item.subtitle, $store.search.currentSearchQuery)"></p><div class="bg-slate-50 rounded-lg p-3 mt-4 text-xs"><p x-show="item.startDate"><strong>เริ่ม:</strong> <span x-text="item.startDate"></span></p><p x-show="item.endDate"><strong>สิ้นสุด:</strong> <span x-text="item.endDate"></span></p></div><p class="text-xs text-slate-400 mt-2">เผยแพร่: <span x-text="item.createdAt"></span></p></div></template>
                            <template x-if="item.type === 'cafe'"><div class="space-y-3 mb-4 text-sm text-slate-700 flex-grow"><p class="flex items-center font-medium"><svg class="w-4 h-4 mr-2 text-cyan-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg><span x-html="highlightText(item.placeName, $store.search.currentSearchQuery)"></span></p><p class="flex items-start"><svg class="w-4 h-4 mr-2 mt-0.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span x-html="highlightText(item.address, $store.search.currentSearchQuery)" class="line-clamp-2"></span></p><div class="pt-2 border-t border-slate-100 space-y-2"><p class="flex items-center"><svg class="w-4 h-4 mr-2 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span x-html="highlightText(`${item.openDay} - ${item.closeDay}, ${item.openTime} - ${item.closeTime}`, $store.search.currentSearchQuery)"></span></p><p x-show="item.phone" class="flex items-center"><svg class="w-4 h-4 mr-2 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.135a11.249 11.249 0 005.422 5.422l1.135-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg><span x-html="highlightText(item.phone, $store.search.currentSearchQuery)"></span></p></div><div x-show="item.facilities && item.facilities.length > 0" class="flex flex-wrap gap-2 pt-2"><template x-for="facility in item.facilities" :key="facility"><span class="bg-cyan-50 text-cyan-700 px-2.5 py-1 rounded-full text-xs font-medium" x-html="highlightText(facility, $store.search.currentSearchQuery)"></span></template></div></div></template>
                            <div class="mt-auto pt-4 border-t border-slate-100">
                                <a :href="item.link" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 font-semibold group-hover:underline">ดูรายละเอียด<svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"></path></svg></a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
             <div id="noResults" class="text-center py-16 text-slate-500" x-show="$store.search.searchResults.length === 0 && $store.search.currentSearchQuery.length > 0">
                <div class="text-6xl mb-4">😞</div><p class="text-2xl font-semibold">ไม่พบผลลัพธ์</p><p class="text-md mt-2">ลองใช้คำค้นหาอื่น หรือตรวจสอบการสะกดคำ</p>
            </div>
        </div>
    </div>

    {{-- Main Content & Filters Section --}}
    <div 
        class="max-w-7xl mx-auto px-6 py-12" 
        x-data="pageController()" 
        x-init="initializeAllData()"
        x-show="!$store.search.showResults" 
        x-cloak>
        <div class="flex flex-col lg:flex-row lg:gap-8">
            
            {{-- Left Column - Filters --}}
            <aside class="w-full lg:w-1/4 mb-8 lg:mb-0">
                <div class="sticky top-24">
                    <div class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-slate-800">ตัวกรองคาเฟ่</h3>
                            <button @click="clearFilters()" class="text-sm font-medium text-cyan-600 hover:text-cyan-800 hover:underline">ล้างทั้งหมด</button>
                        </div>
                        <div class="border-t border-slate-200 pt-4">
                            <h4 class="font-semibold text-slate-700 mb-3">เรตติ้ง</h4>
                            <div class="flex items-center space-x-1 text-slate-300">
                                <template x-for="star in 5">
                                    <button class="focus:outline-none" :class="{ 'text-amber-400': filters.rating >= star }">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    </button>
                                </template>
                            </div>
                             <p class="text-xs text-slate-400 mt-2">หมายเหตุ: ตัวกรองเรตติ้งยังไม่เปิดใช้งาน</p>
                        </div>
                        <div class="border-t border-slate-200 pt-4">
                            <h4 class="font-semibold text-slate-700 mb-3">ช่วงราคา</h4>
                            <div class="space-y-2">
                                <template x-for="price in availableFilters.priceRanges" :key="price">
                                    <label class="flex items-center text-slate-600"><input type="checkbox" x-model="filters.priceRanges" :value="price" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-3" x-text="price"></span></label>
                                </template>
                            </div>
                        </div>
                        <div class="border-t border-slate-200 pt-4">
                            <h4 class="font-semibold text-slate-700 mb-3">สไตล์คาเฟ่</h4>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                <template x-for="style in availableFilters.styles" :key="style">
                                    <label class="flex items-center text-slate-600"><input type="checkbox" x-model="filters.styles" :value="style" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-3" x-text="style"></span></label>
                                </template>
                            </div>
                        </div>
                        <div class="border-t border-slate-200 pt-4">
                            <h4 class="font-semibold text-slate-700 mb-3">ช่องทางชำระเงิน</h4>
                            <div class="space-y-2">
                                <template x-for="method in availableFilters.paymentMethods" :key="method">
                                    <label class="flex items-center text-slate-600"><input type="checkbox" x-model="filters.paymentMethods" :value="method" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-3" x-text="method"></span></label>
                                </template>
                            </div>
                        </div>
                        <div class="border-t border-slate-200 pt-4">
                            <h4 class="font-semibold text-slate-700 mb-3">สิ่งอำนวยความสะดวก</h4>
                             <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                <template x-for="facility in availableFilters.facilities" :key="facility">
                                    <label class="flex items-center text-slate-600"><input type="checkbox" x-model="filters.facilities" :value="facility" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-3" x-text="facility"></span></label>
                                </template>
                            </div>
                        </div>
                        <div class="border-t border-slate-200 pt-4">
                            <h4 class="font-semibold text-slate-700 mb-3">บริการเพิ่มเติม</h4>
                            <div class="space-y-2">
                                <template x-for="service in availableFilters.otherServices" :key="service">
                                    <label class="flex items-center text-slate-600"><input type="checkbox" x-model="filters.otherServices" :value="service" class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"><span class="ml-3" x-text="service"></span></label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            
            {{-- Right Column - Content --}}
            <main class="w-full lg:w-3/4 space-y-16">
                <div id="newsSectionWrapper">
                    @if ($news && $news->count() > 0)
                        <div x-data="newsCarousel({ totalSlides: {{ $news->count() }}, autoplay: true })" x-init="startAutoplay()" class="relative rounded-2xl shadow-lg overflow-hidden bg-slate-200 aspect-[16/9] md:aspect-[21/9]">
                            
                            <div id="news-data-source" class="hidden">
                                @foreach($news as $item)
                                    @php
                                        $newsImages = is_array($item->images) ? $item->images : [];
                                        $startDateTime = $item->start_datetime ? \Carbon\Carbon::parse($item->start_datetime)->format('d/m/Y H:i') : '';
                                        $endDateTime = $item->end_datetime ? \Carbon\Carbon::parse($item->end_datetime)->format('d/m/Y H:i') : '';
                                        $createdAt = $item->created_at ? $item->created_at->format('d/m/Y H:i') : '';
                                        $searchableNewsText = trim(implode(' ', array_filter([ 
                                            (string)($item->title ?? ''), 
                                            strip_tags((string)($item->content ?? '')), 
                                            $startDateTime, $endDateTime, $createdAt, 
                                            'ข่าวสาร', 'กิจกรรม', 'อีเว้นท์'
                                        ])));
                                    @endphp
                                    <div class="news-item" 
                                        data-id="{{ $item->id }}" 
                                        data-type="news" 
                                        data-link="{{ route('news.show', $item->id) }}" 
                                        data-image="{{ !empty($newsImages) ? asset('storage/' . $newsImages[0]) : asset('images/no-image.png') }}" 
                                        data-title="{{ $item->title ?? '' }}" 
                                        data-subtitle="{{ strip_tags($item->content ?? '') }}" 
                                        data-full-content="{{ $searchableNewsText }}" 
                                        data-start-date="{{ $startDateTime }}" 
                                        data-end-date="{{ $endDateTime }}" 
                                        data-created-at="{{ $createdAt }}">
                                    </div>
                                @endforeach
                            </div>

                            <div class="w-full h-full">
                                @foreach ($news as $item)
                                    @php
                                        $newsImages = is_array($item->images) ? $item->images : [];
                                        $imageUrl = !empty($newsImages) ? asset('storage/' . $newsImages[0]) : asset('images/no-image.png');
                                    @endphp
                                    <div x-show="activeSlide === {{ $loop->iteration }}" class="absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out" x-transition:enter="opacity-0" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="opacity-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
                                        <img src="{{ $imageUrl }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 p-6 md:p-10 text-white w-full md:w-3/4 lg:w-2/3">
                                            <span class="inline-block bg-cyan-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-3">ข่าวสารล่าสุด</span>
                                            <h2 class="text-2xl md:text-4xl font-extrabold leading-tight mb-4 line-clamp-2">{{ $item->title }}</h2>
                                            <a href="{{ route('news.show', $item->id) }}" class="inline-flex items-center bg-white text-slate-800 font-bold px-6 py-3 rounded-lg hover:bg-slate-200 transition-colors">อ่านเพิ่มเติม <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button @click="prev()" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-black/40 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-black/60 transition z-10"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                            <button @click="next()" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black/40 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-black/60 transition z-10"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
                                <template x-for="i in totalSlides" :key="i">
                                    <button @click="goToSlide(i)" class="w-2.5 h-2.5 rounded-full transition" :class="{'bg-white': activeSlide === i, 'bg-white/50 hover:bg-white/75': activeSlide !== i}"></button>
                                </template>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16 bg-white rounded-2xl"><div class="text-gray-400 text-8xl mb-6">📰</div><h3 class="text-2xl font-bold text-gray-500 mb-2">ยังไม่มีข่าวสารในขณะนี้</h3><p class="text-gray-400">กรุณาติดตามข่าวสารใหม่ๆ ในภายหลัง</p></div>
                    @endif
                </div>

                {{-- Cafe Section --}}
                <div id="cafesSectionWrapper">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-3">☕ คาเฟ่แนะนำ</h2>
                        <div class="w-24 h-1.5 bg-gradient-to-r from-amber-400 to-orange-500 mx-auto rounded-full"></div>
                        <p class="text-slate-600 mt-4 max-w-2xl mx-auto">ค้นพบคาเฟ่สุดชิค บรรยากาศดี และกาแฟอร่อยในท้องถิ่น</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8" id="cafesSection">
                        <template x-for="cafe in filteredCafes" :key="cafe.id">
                            <div x-data="{ isLiked: false }" class="bg-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 group flex flex-col">
                                <div class="relative h-56 overflow-hidden rounded-t-2xl">
                                    <img :src="cafe.imageUrl" :alt="'รูปคาเฟ่ ' + cafe.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    
                                    <button @click.prevent="isLiked = !isLiked" class="absolute top-3 right-3 w-10 h-10 bg-black/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/60 transition-colors duration-300 z-10">
                                        <i class="fa-heart text-xl transition-all" :class="isLiked ? 'fa-solid text-pink-500' : 'fa-regular'"></i>
                                    </button>

                                    <div x-show="cafe.isNewOpening" class="absolute bottom-3 left-3 bg-fuchsia-500 text-white px-3 py-1 rounded-full text-xs font-semibold tracking-wide">✨ เปิดใหม่</div>
                                </div>
                                <div class="p-5 flex flex-col flex-grow">
                                    <h3 class="text-xl font-bold mb-1 text-slate-800 group-hover:text-cyan-600 transition-colors line-clamp-2"><a :href="cafe.link" x-text="cafe.title"></a></h3>
                                    <p class="text-slate-500 text-sm mb-4 line-clamp-2" x-text="cafe.address"></p>
                                    <div class="flex-grow space-y-4">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="style in cafe.cafeStyles" :key="style"><span class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full text-xs font-medium" x-text="style"></span></template>
                                            <template x-for="facility in cafe.facilities" :key="facility"><span class="bg-cyan-50 text-cyan-700 px-2.5 py-1 rounded-full text-xs font-medium" x-text="facility"></span></template>
                                        </div>
                                        <div class="text-sm space-y-2 text-slate-600">
                                            <div class="flex items-center"><i class="fa-regular fa-clock w-4 h-4 mr-2 text-indigo-500 shrink-0"></i><span x-text="`${cafe.openDay} - ${cafe.closeDay}, ${cafe.openTime} - ${cafe.closeTime}`"></span></div>
                                            <div x-show="cafe.priceRange" class="flex items-center"><i class="fa-solid fa-tags w-4 h-4 mr-2 text-green-500 shrink-0"></i><span>ราคา: </span><span x-text="cafe.priceRange"></span></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                        <a :href="cafe.link" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 font-semibold group-hover:underline">
                                            ดูรายละเอียด
                                            <i class="fa-solid fa-arrow-right w-4 h-4 ml-1.5 transition-transform group-hover:translate-x-1"></i>
                                        </a>
                                        <div x-data="{ shareText: 'แชร์' }" class="flex items-center space-x-2">
                                            <button @click.prevent="share(cafe.title, cafe.link, $el.querySelector('span'))" class="flex items-center space-x-2 text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg text-sm transition">
                                                <i class="fa-solid fa-share-nodes"></i>
                                                <span x-text="shareText"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="allCafes.length > 0 && filteredCafes.length === 0" class="text-center py-16 bg-white rounded-2xl mt-8"><div class="text-gray-400 text-8xl mb-6">🤷‍♀️</div><h3 class="text-2xl font-bold text-gray-500 mb-2">ไม่พบคาเฟ่ที่ตรงกับตัวกรอง</h3><p class="text-gray-400">ลองปรับเปลี่ยนตัวกรอง หรือ <button @click="clearFilters()" class="text-cyan-600 hover:underline font-medium">ล้างตัวกรองทั้งหมด</button></p></div>
                    <div x-show="allCafes.length === 0" class="text-center py-16 bg-white rounded-2xl mt-8"><div class="text-gray-400 text-8xl mb-6">☕</div><h3 class="text-2xl font-bold text-gray-500 mb-2">ยังไม่มีคาเฟ่ในระบบ</h3><p class="text-gray-400">กรุณาติดตามคาเฟ่ใหม่ๆ ในภายหลัง</p></div>
                </div>
            </main>
        </div>
    </div>


    {{-- Hidden data source for cafes --}}
    <div id="cafe-data-source" class="hidden">
        @if ($cafes && $cafes->count() > 0)
            @foreach ($cafes as $cafe)
                @php 
                    $cafeImages = is_array($cafe->images) ? $cafe->images : [];
                    $openTime = $cafe->open_time ? \Carbon\Carbon::parse($cafe->open_time)->format('H:i') : '';
                    $closeTime = $cafe->close_time ? \Carbon\Carbon::parse($cafe->close_time)->format('H:i') : '';
                    $cafeStyles = is_array($cafe->cafe_styles) ? $cafe->cafe_styles : [];
                    $facilities = is_array($cafe->facilities) ? $cafe->facilities : [];
                    $payment_methods = is_array($cafe->payment_methods) ? $cafe->payment_methods : [];
                    $other_services = is_array($cafe->other_services) ? $cafe->other_services : [];
                    
                    // --- CORRECTED: Added openTime and closeTime to the searchable text ---
                    $searchableCafeText = trim(implode(' ', array_filter([ 
                        (string)($cafe->cafe_name ?? ''), (string)($cafe->address ?? ''), (string)($cafe->place_name ?? ''), (string)($cafe->phone ?? ''), 
                        (string)($cafe->open_day ?? ''), (string)($cafe->close_day ?? ''), (string)($openTime ?? ''), (string)($closeTime ?? ''), (string)($cafe->price_range ?? ''), 
                        implode(' ', $cafeStyles), implode(' ', $facilities), implode(' ', $payment_methods), implode(' ', $other_services), 
                        'คาเฟ่', 'ร้านกาแฟ', 'สถานที่'
                    ])));
                @endphp
                <div class="cafe-item" 
                    data-id="{{ $cafe->id }}" 
                    data-type="cafe" 
                    data-link="{{ route('cafes.show', $cafe->id) }}" 
                    data-image="{{ !empty($cafeImages) ? asset('storage/' . $cafeImages[0]) : asset('images/no-image.png') }}"
                    data-title="{{ $cafe->cafe_name ?? '' }}"
                    data-address="{{ $cafe->address }}"
                    data-place-name="{{ $cafe->place_name }}"
                    data-open-day="{{ $cafe->open_day }}"
                    data-close-day="{{ $cafe->close_day }}"
                    data-open-time="{{ $openTime }}"
                    data-close-time="{{ $closeTime }}"
                    data-phone="{{ $cafe->phone ?? '' }}"
                    data-price-range="{{ $cafe->price_range ?? '' }}"
                    data-is-new-opening="{{ $cafe->is_new_opening ? 'true' : 'false' }}"
                    data-styles="{{ implode(',', $cafeStyles) }}"
                    data-facilities="{{ implode(',', $facilities) }}"
                    data-payment-methods="{{ implode(',', $payment_methods) }}"
                    data-other-services="{{ implode(',', $other_services) }}"
                    data-full-content="{{ $searchableCafeText }}"
                ></div>
            @endforeach
        @endif
    </div>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div>
                <h4 class="text-2xl font-bold mb-4 text-cyan-400">น้องช้างสะเร็น</h4>
                <p class="text-slate-400 text-sm">แพลตฟอร์มค้นหาข่าวสารและคาเฟ่ในจังหวัดสุรินทร์</p>
            </div>
            <div>
                <h5 class="text-lg font-semibold mb-4 text-slate-200">หมวดหมู่</h5>
                <ul class="space-y-3 text-slate-400 text-sm">
                    <li><a href="#" class="hover:text-cyan-300 transition-colors">ข่าวสาร</a></li>
                    <li><a href="#" class="hover:text-cyan-300 transition-colors">คาเฟ่</a></li>
                    <li><a href="#" class="hover:text-cyan-300 transition-colors">กิจกรรม</a></li>
                    <li><a href="#" class="hover:text-cyan-300 transition-colors">โปรโมชั่น</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-lg font-semibold mb-4 text-slate-200">ลิงก์ด่วน</h5>
                <ul class="space-y-3 text-slate-400 text-sm">
                    <li><a href="#" class="hover:text-cyan-300 transition-colors">เกี่ยวกับเรา</a></li>
                    <li><a href="#" class="hover:text-cyan-300 transition-colors">ติดต่อ</a></li>
                    <li><a href="#" class="hover:text-cyan-300 transition-colors">เข้าร่วม</a></li>
                    <li><a href="{{ url('/login-admin') }}" class="hover:text-cyan-300 transition-colors">สำหรับ Admin Login</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-lg font-semibold mb-4 text-slate-200">ติดตามเรา</h5>
                <div class="flex space-x-4">
                    <a href="#" class="text-slate-400 hover:text-cyan-300 transition-colors"><i class="fab fa-facebook-f text-2xl"></i></a>
                    <a href="#" class="text-slate-400 hover:text-cyan-300 transition-colors"><i class="fab fa-instagram text-2xl"></i></a>
                    <a href="#" class="text-slate-400 hover:text-cyan-300 transition-colors"><i class="fab fa-twitter text-2xl"></i></a>
                </div>
                <div class="mt-8 text-slate-400 text-sm">
                    <p class="font-semibold">ติดต่อ: info@nongchangsurin.com</p>
                    <p>โทรศัพท์: 08-XXXX-XXXX</p>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-800 mt-12 pt-8 text-center text-slate-500 text-sm">
            © 2024 น้องช้างสะเร็น. สงวนลิขสิทธิ์
        </div>
    </footer>

    <script>
        // Alpine Store for Search Management
        document.addEventListener('alpine:init', () => {
            Alpine.store('search', {
                showResults: false,
                searchResults: [],
                currentSearchQuery: '',
            });
        });

        // Alpine Component for Search Functionality
        function searchComponent() {
            return {
                searchQuery: '',
                allData: [],
                quickSearchTags: ['คาเฟ่เปิดใหม่', 'กาแฟอร่อย', 'ข่าวสารล่าสุด', 'งานอีเว้นท์'],

                initializeSearchData() {
                    this.loadNewsData();
                    this.loadCafeData();
                },

                loadNewsData() {
                    const newsElements = document.querySelectorAll('#news-data-source .news-item');
                    const newsData = Array.from(newsElements).map(el => ({
                        id: el.getAttribute('data-id'), type: el.getAttribute('data-type'), link: el.getAttribute('data-link'),
                        imageUrl: el.getAttribute('data-image'), title: el.getAttribute('data-title'), subtitle: el.getAttribute('data-subtitle'),
                        fullContent: el.getAttribute('data-full-content'), startDate: el.getAttribute('data-start-date'),
                        endDate: el.getAttribute('data-end-date'), createdAt: el.getAttribute('data-created-at'),
                    }));
                    this.allData = this.allData.concat(newsData);
                },

                loadCafeData() {
                    const cafeElements = document.querySelectorAll('#cafe-data-source .cafe-item');
                    const cafeData = Array.from(cafeElements).map(el => ({
                        id: el.getAttribute('data-id'), type: el.getAttribute('data-type'), link: el.getAttribute('data-link'),
                        imageUrl: el.getAttribute('data-image'), title: el.getAttribute('data-title'), address: el.getAttribute('data-address'),
                        placeName: el.getAttribute('data-place-name'), openDay: el.getAttribute('data-open-day'), closeDay: el.getAttribute('data-close-day'),
                        openTime: el.getAttribute('data-open-time'), closeTime: el.getAttribute('data-close-time'), phone: el.getAttribute('data-phone'),
                        priceRange: el.getAttribute('data-price-range'), isNewOpening: el.getAttribute('data-is-new-opening') === 'true',
                        cafeStyles: el.getAttribute('data-styles') ? el.getAttribute('data-styles').split(',').filter(s => s) : [],
                        facilities: el.getAttribute('data-facilities') ? el.getAttribute('data-facilities').split(',').filter(f => f) : [],
                        paymentMethods: el.getAttribute('data-payment-methods') ? el.getAttribute('data-payment-methods').split(',').filter(p => p) : [],
                        otherServices: el.getAttribute('data-other-services') ? el.getAttribute('data-other-services').split(',').filter(o => o) : [],
                        fullContent: el.getAttribute('data-full-content'),
                    }));
                    this.allData = this.allData.concat(cafeData);
                },

                performSearch() {
                    if (this.searchQuery.trim().length === 0) {
                        Alpine.store('search').showResults = false;
                        Alpine.store('search').searchResults = [];
                        Alpine.store('search').currentSearchQuery = '';
                        return;
                    }
                    const query = this.searchQuery.toLowerCase().trim();
                    const results = this.allData.filter(item => {
                        const content = (item.fullContent || '').toLowerCase();
                        return content.includes(query);
                    });
                    Alpine.store('search').searchResults = results;
                    Alpine.store('search').currentSearchQuery = this.searchQuery;
                    Alpine.store('search').showResults = true;
                },
                
                highlightText(text, query) {
                    const originalText = text || '';
                    if (!query || query.trim().length === 0) { return originalText; }
                    try {
                        const escapedQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                        const regex = new RegExp(`(${escapedQuery})`, 'gi');
                        return originalText.replace(regex, '<span class="highlight">$1</span>');
                    } catch (e) {
                        console.error("Error in highlightText:", e);
                        return originalText;
                    }
                },
            };
        }

        // Alpine Component for News Carousel
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

        // Alpine Component for Page and Filter Management
        function pageController() {
            return {
                allCafes: [], filteredCafes: [],
                availableFilters: { priceRanges: ['$', '$$', '$$$', '$$$$'], styles: [], facilities: [], paymentMethods: [], otherServices: [] },
                filters: { rating: 0, priceRanges: [], styles: [], facilities: [], paymentMethods: [], otherServices: [] },
                
                initializeAllData() {
                    this.loadCafeData();
                    this.extractAvailableFilters();
                    this.$watch('filters', () => this.applyFilters(), { deep: true });
                    this.applyFilters();
                },

                loadCafeData() {
                    const cafeElements = document.querySelectorAll('#cafe-data-source .cafe-item');
                    this.allCafes = Array.from(cafeElements).map(el => ({
                        id: el.getAttribute('data-id'), title: el.getAttribute('data-title'), address: el.getAttribute('data-address'),
                        link: el.getAttribute('data-link'), imageUrl: el.getAttribute('data-image'), placeName: el.getAttribute('data-place-name'),
                        openDay: el.getAttribute('data-open-day'), closeDay: el.getAttribute('data-close-day'), openTime: el.getAttribute('data-open-time'),
                        closeTime: el.getAttribute('data-close-time'), phone: el.getAttribute('data-phone'), priceRange: el.getAttribute('data-price-range'),
                        isNewOpening: el.getAttribute('data-is-new-opening') === 'true',
                        cafeStyles: el.getAttribute('data-styles') ? el.getAttribute('data-styles').split(',').filter(s => s) : [],
                        facilities: el.getAttribute('data-facilities') ? el.getAttribute('data-facilities').split(',').filter(f => f) : [],
                        paymentMethods: el.getAttribute('data-payment-methods') ? el.getAttribute('data-payment-methods').split(',').filter(p => p) : [],
                        otherServices: el.getAttribute('data-other-services') ? el.getAttribute('data-other-services').split(',').filter(o => o) : [],
                    }));
                },

                extractAvailableFilters() {
                    const allStyles = new Set(), allFacilities = new Set(), allPaymentMethods = new Set(), allOtherServices = new Set();
                    this.allCafes.forEach(cafe => {
                        if(cafe.cafeStyles) cafe.cafeStyles.forEach(style => allStyles.add(style));
                        if(cafe.facilities) cafe.facilities.forEach(facility => allFacilities.add(facility));
                        if(cafe.paymentMethods) cafe.paymentMethods.forEach(method => allPaymentMethods.add(method));
                        if(cafe.otherServices) cafe.otherServices.forEach(service => allOtherServices.add(service));
                    });
                    this.availableFilters.styles = Array.from(allStyles).sort();
                    this.availableFilters.facilities = Array.from(allFacilities).sort();
                    this.availableFilters.paymentMethods = Array.from(allPaymentMethods).sort();
                    this.availableFilters.otherServices = Array.from(allOtherServices).sort();
                },

                applyFilters() {
                    this.filteredCafes = this.allCafes.filter(cafe => {
                        if (this.filters.priceRanges.length > 0 && !this.filters.priceRanges.includes(cafe.priceRange)) return false;
                        if (this.filters.styles.length > 0 && (!cafe.cafeStyles || !this.filters.styles.some(style => cafe.cafeStyles.includes(style)))) return false;
                        if (this.filters.facilities.length > 0 && (!cafe.facilities || !this.filters.facilities.every(facility => cafe.facilities.includes(facility)))) return false;
                        if (this.filters.paymentMethods.length > 0 && (!cafe.paymentMethods || !this.filters.paymentMethods.some(method => cafe.paymentMethods.includes(method)))) return false;
                        if (this.filters.otherServices.length > 0 && (!cafe.otherServices || !this.filters.otherServices.every(service => cafe.otherServices.includes(service)))) return false;
                        return true;
                    });
                },

                clearFilters() {
                    this.filters.rating = 0; this.filters.priceRanges = []; this.filters.styles = []; this.filters.facilities = []; this.filters.paymentMethods = []; this.filters.otherServices = [];
                },

                share(title, url, buttonElement) {
                    if (navigator.share) {
                        navigator.share({ title: title, url: url }).catch(error => console.error('Error sharing:', error));
                    } else {
                        navigator.clipboard.writeText(url)
                            .then(() => {
                                const originalText = buttonElement.innerText;
                                buttonElement.innerText = 'คัดลอกลิงก์แล้ว!';
                                setTimeout(() => { buttonElement.innerText = originalText; }, 2000);
                            })
                            .catch(err => {
                                console.error('Could not copy text: ', err);
                                alert('ไม่รองรับการแชร์: ไม่สามารถคัดลอกลิงก์ได้');
                            });
                    }
                }
            };
        }
    </script>
</body>
</html>