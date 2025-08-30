<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>น้องช้างสะเร็น - ระบบคาเฟ่และข่าวสาร</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    body { font-family: 'Kanit', sans-serif; }
    .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .line-clamp-3 { display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
    [x-cloak] { display:none !important; }
    .slider { position:relative; width:100%; margin:auto; overflow:hidden; border-radius:1rem; box-shadow:0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
    .slides { display:flex; transition:transform .5s ease-in-out; }
    .slides a { min-width:100%; box-sizing:border-box; }
    .slides img { width:100%; display:block; aspect-ratio: 16 / 9; object-fit:cover; }
    .nav-btn { position:absolute; top:50%; transform:translateY(-50%); background-color:rgba(0,0,0,.4); color:#fff; border:none; padding:10px; cursor:pointer; border-radius:9999px; width:40px; height:40px; font-size:18px; display:flex; align-items:center; justify-content:center; transition:background-color .3s; z-index:10; }
    .nav-btn:hover { background-color:rgba(0,0,0,.6); }
    .prev { left:10px; } .next { right:10px; }
    .dots { position:absolute; bottom:10px; left:50%; transform:translateX(-50%); display:flex; z-index:10; }
    .dot { height:10px; width:10px; margin:0 4px; background-color:rgba(255,255,255,.5); border-radius:9999px; display:inline-block; cursor:pointer; transition:background-color .3s; }
    .dot.active, .dot:hover { background-color:#fff; }
    .fa-heart { transition:all .25s ease; }
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

  {{-- Page --}}
  <div
    class="w-full"
    x-data="pageController({ initialLikedIds: {{ json_encode($likedCafeIds ?? []) }} })"
    x-init="initializeAllData()"
    x-cloak
  >
    <!-- Mobile top bar -->
    <div class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200 px-3 py-2 lg:hidden">
      <div class="max-w-screen-2xl mx-auto flex items-center gap-2">
        <button
          @click="mobileFilterOpen = true"
          class="shrink-0 flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm"
        >
          <i class="fa-solid fa-sliders"></i><span>ตัวกรอง</span>
        </button>
        <div class="relative flex-1">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input
            type="search"
            x-model.debounce.300ms="searchTerm"
            placeholder="ค้นหาคาเฟ่หรือข่าว..."
            class="w-full pl-9 pr-3 py-2 bg-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"
          />
        </div>
        <a href="#news" class="shrink-0 px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm">ข่าว</a>
      </div>
    </div>

    <!-- Mobile filter drawer (ใช้ state จาก root) -->
    <template x-if="mobileFilterOpen">
      <div class="fixed inset-0 z-50 lg:hidden" @keydown.escape.window="mobileFilterOpen=false" x-init="$watch('mobileFilterOpen', v => document.documentElement.classList.toggle('overflow-hidden', v))">
        <div class="absolute inset-0 bg-black/40" @click="mobileFilterOpen = false"></div>
        <div class="absolute right-0 top-0 h-full w-[85%] max-w-sm bg-white shadow-xl p-4 overflow-y-auto">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-slate-800">ค้นหา & ตัวกรอง</h3>
            <button class="p-2 rounded-lg hover:bg-slate-100" @click="mobileFilterOpen = false">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <!-- FILTER CONTENT (mobile copy) -->
          <div class="space-y-4">
            <div class="relative">
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
              <input type="text" x-model.debounce.300ms="searchTerm" placeholder="ชื่อคาเฟ่, สไตล์, ข่าว..."
                     class="w-full py-2 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-cyan-500">
            </div>

            <div class="flex items-center justify-between">
              <h4 class="font-semibold text-slate-700 text-sm">เรตติ้ง</h4>
              <button @click="clearFilters()" class="text-xs text-cyan-600 hover:underline">ล้างทั้งหมด</button>
            </div>
            <div class="flex items-center gap-1">
              <template x-for="star in 5">
                <button @click="setRatingFilter(star)"
                        :class="{'text-amber-400': filters.rating >= star, 'text-slate-300': filters.rating < star}">
                  <i class="fa-solid fa-star text-lg"></i>
                </button>
              </template>
            </div>

            <div>
              <h4 class="font-semibold text-slate-700 text-sm mb-2">วันเปิดทำการ</h4>
              <div class="grid grid-cols-2 gap-2">
                <template x-for="day in availableFilters.days" :key="day">
                  <label class="flex items-center text-sm text-slate-600">
                    <input type="checkbox" x-model="filters.days" :value="day"
                           class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                    <span class="ml-2" x-text="day"></span>
                  </label>
                </template>
              </div>
            </div>

            <div>
              <h4 class="font-semibold text-slate-700 text-sm mb-2">เวลาเปิดทำการ</h4>
              <select x-model="selectedHour"
                      class="w-full bg-slate-50 border border-slate-200 text-sm rounded-lg p-2 focus:ring-cyan-500">
                <option value="">-- เลือกเวลา --</option>
                <template x-for="hour in Array.from({ length: 18 }, (_, i) => i + 6)">
                  <option :value="String(hour).padStart(2, '0')"
                          x-text="`${String(hour).padStart(2,'0')}:00 น.`"></option>
                </template>
              </select>
            </div>

            <div>
              <h4 class="font-semibold text-slate-700 text-sm mb-2">ช่วงราคา</h4>
              <div class="flex flex-wrap gap-2">
                <template x-for="p in availableFilters.priceRanges" :key="p">
                  <label class="inline-flex items-center text-sm text-slate-600">
                    <input type="checkbox" x-model="filters.priceRanges" :value="p"
                           class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                    <span class="ml-2" x-text="p"></span>
                  </label>
                </template>
              </div>
            </div>

            <div>
              <h4 class="font-semibold text-slate-700 text-sm mb-2">สไตล์คาเฟ่</h4>
              <div class="max-h-40 overflow-y-auto pr-1 space-y-1">
                <template x-for="style in availableFilters.styles" :key="style">
                  <label class="flex items-center text-sm text-slate-600">
                    <input type="checkbox" x-model="filters.styles" :value="style"
                           class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                    <span class="ml-2" x-text="style"></span>
                  </label>
                </template>
              </div>
            </div>

            <div>
              <h4 class="font-semibold text-slate-700 text-sm mb-2">ช่องทางชำระเงิน</h4>
              <div class="space-y-1">
                <template x-for="m in availableFilters.paymentMethods" :key="m">
                  <label class="flex items-center text-sm text-slate-600">
                    <input type="checkbox" x-model="filters.paymentMethods" :value="m"
                           class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                    <span class="ml-2" x-text="m"></span>
                  </label>
                </template>
              </div>
            </div>

            <div>
              <h4 class="font-semibold text-slate-700 text-sm mb-2">สิ่งอำนวยความสะดวก</h4>
              <div class="max-h-40 overflow-y-auto pr-1 space-y-1">
                <template x-for="f in availableFilters.facilities" :key="f">
                  <label class="flex items-center text-sm text-slate-600">
                    <input type="checkbox" x-model="filters.facilities" :value="f"
                           class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                    <span class="ml-2" x-text="f"></span>
                  </label>
                </template>
              </div>
            </div>

            <div>
              <h4 class="font-semibold text-slate-700 text-sm mb-2">บริการเพิ่มเติม</h4>
              <div class="space-y-1">
                <template x-for="s in availableFilters.otherServices" :key="s">
                  <label class="flex items-center text-sm text-slate-600">
                    <input type="checkbox" x-model="filters.otherServices" :value="s"
                           class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                    <span class="ml-2" x-text="s"></span>
                  </label>
                </template>
              </div>
            </div>

            <label class="flex items-center text-sm text-slate-600">
              <input type="checkbox" x-model="filters.isNewOpening"
                     class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
              <span class="ml-2">✨ คาเฟ่เปิดใหม่</span>
            </label>

            <button class="w-full bg-cyan-600 text-white font-semibold py-2 rounded-lg"
                    @click="mobileFilterOpen = false">ใช้ตัวกรอง</button>
          </div>
        </div>
      </div>
    </template>

    <!-- Desktop grid -->
    <div class="max-w-screen-2xl mx-auto px-3 sm:px-4">
      <div class="grid grid-cols-12 gap-4 sm:gap-6 lg:gap-8">
        <!-- Left: Filters (Desktop) -->
        <aside class="hidden lg:block lg:col-span-3 py-6">
          <div class="sticky top-24">
            <div class="bg-white/80 backdrop-blur rounded-2xl shadow-sm p-4 space-y-4 border border-slate-200">
              <div class="pb-3 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 mb-3">ค้นหา & ตัวกรอง</h3>
                <div class="relative">
                  <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                  <input type="text" x-model.debounce.300ms="searchTerm" placeholder="ชื่อคาเฟ่, สไตล์, ข่าว..."
                         class="w-full py-2 pl-9 pr-3 text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-cyan-500">
                </div>
              </div>

              <div class="flex justify-between items-center">
                <h3 class="text-md font-bold text-slate-700">ตัวกรองละเอียด</h3>
                <button @click="clearFilters()" class="text-sm text-cyan-600 hover:underline">ล้างทั้งหมด</button>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <h4 class="font-semibold text-slate-700 mb-2 text-sm">เรตติ้ง</h4>
                <div class="flex items-center gap-1">
                  <template x-for="star in 5">
                    <button @click="setRatingFilter(star)"
                            :class="{'text-amber-400': filters.rating >= star, 'text-slate-300': filters.rating < star}">
                      <i class="fa-solid fa-star"></i>
                    </button>
                  </template>
                </div>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <h4 class="font-semibold text-slate-700 mb-2 text-sm">วันเปิดทำการ</h4>
                <div class="grid grid-cols-2 gap-2">
                  <template x-for="day in availableFilters.days" :key="day">
                    <label class="flex items-center text-slate-600 text-sm">
                      <input type="checkbox" x-model="filters.days" :value="day"
                             class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                      <span class="ml-2" x-text="day"></span>
                    </label>
                  </template>
                </div>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <h4 class="font-semibold text-slate-700 mb-2 text-sm">เวลาเปิดทำการ</h4>
                <select x-model="selectedHour"
                        class="bg-slate-50 border border-slate-200 text-sm rounded-lg w-full p-2 focus:ring-cyan-500">
                  <option value="">-- เลือกเวลา --</option>
                  <template x-for="hour in Array.from({ length: 18 }, (_, i) => i + 6)">
                    <option :value="String(hour).padStart(2, '0')"
                            x-text="`${String(hour).padStart(2,'0')}:00 น.`"></option>
                  </template>
                </select>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <h4 class="font-semibold text-slate-700 mb-2 text-sm">ช่วงราคา</h4>
                <div class="flex flex-wrap gap-2">
                  <template x-for="p in availableFilters.priceRanges" :key="p">
                    <label class="inline-flex items-center text-sm text-slate-600">
                      <input type="checkbox" x-model="filters.priceRanges" :value="p"
                             class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                      <span class="ml-2" x-text="p"></span>
                    </label>
                  </template>
                </div>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <h4 class="font-semibold text-slate-700 mb-2 text-sm">สไตล์คาเฟ่</h4>
                <div class="space-y-1 max-h-40 overflow-y-auto pr-2">
                  <template x-for="style in availableFilters.styles" :key="style">
                    <label class="flex items-center text-slate-600 text-sm">
                      <input type="checkbox" x-model="filters.styles" :value="style"
                             class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                      <span class="ml-2" x-text="style"></span>
                    </label>
                  </template>
                </div>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <h4 class="font-semibold text-slate-700 mb-2 text-sm">ช่องทางชำระเงิน</h4>
                <div class="space-y-1">
                  <template x-for="m in availableFilters.paymentMethods" :key="m">
                    <label class="flex items-center text-slate-600 text-sm">
                      <input type="checkbox" x-model="filters.paymentMethods" :value="m"
                             class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                      <span class="ml-2" x-text="m"></span>
                    </label>
                  </template>
                </div>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <h4 class="font-semibold text-slate-700 mb-2 text-sm">สิ่งอำนวยความสะดวก</h4>
                <div class="space-y-1 max-h-40 overflow-y-auto pr-2">
                  <template x-for="f in availableFilters.facilities" :key="f">
                    <label class="flex items-center text-slate-600 text-sm">
                      <input type="checkbox" x-model="filters.facilities" :value="f"
                             class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                      <span class="ml-2" x-text="f"></span>
                    </label>
                  </template>
                </div>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <h4 class="font-semibold text-slate-700 mb-2 text-sm">บริการเพิ่มเติม</h4>
                <div class="space-y-1">
                  <template x-for="s in availableFilters.otherServices" :key="s">
                    <label class="flex items-center text-slate-600 text-sm">
                      <input type="checkbox" x-model="filters.otherServices" :value="s"
                             class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                      <span class="ml-2" x-text="s"></span>
                    </label>
                  </template>
                </div>
              </div>

              <div class="border-t border-slate-200 pt-3">
                <label class="flex items-center text-slate-600 text-sm">
                  <input type="checkbox" x-model="filters.isNewOpening"
                         class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                  <span class="ml-2">✨ คาเฟ่เปิดใหม่</span>
                </label>
              </div>
            </div>
          </div>
        </aside>

        <!-- Middle: Main -->
        <main class="col-span-12 lg:col-span-6 py-4 sm:py-6 space-y-6">
          <!-- Slider -->
          <div class="slider">
            <div class="slides">
              <a href="{{ route('cafes.top10') }}">
                <img loading="lazy" src="{{ asset('/images/TOP-10.png') }}" alt="10 อันดับคาเฟ่แนะนำ">
              </a>
              <a href="{{ route('cafes.newly') }}">
                <img loading="lazy" src="{{ asset('/images/คาเฟ่เปิดใหม่.png') }}" alt="พิกัดคาเฟ่เปิดใหม่">
              </a>
            </div>
            <button class="nav-btn prev">&#10094;</button>
            <button class="nav-btn next">&#10095;</button>
            <div class="dots"></div>
          </div>

          <!-- Cafes -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6" id="cafesSection">
            <template x-for="cafe in filteredCafes.slice(0, displayedCafeCount)" :key="cafe.id">
              <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="bg-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-cyan-200 group flex flex-col"
              >
                <div x-data="{ activeImageIndex: 0 }" class="relative h-40 sm:h-48 overflow-hidden rounded-t-2xl">
                  <a :href="cafe.link">
                    <img loading="lazy" :src="cafe.imageUrls[activeImageIndex]" :alt="'รูปคาเฟ่ ' + cafe.title"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
                  </a>
                  <template x-if="cafe.imageUrls.length > 1">
                    <div class="absolute inset-0 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                      <button @click.prevent="activeImageIndex = (activeImageIndex - 1 + cafe.imageUrls.length) % cafe.imageUrls.length"
                              class="w-8 h-8 flex items-center justify-center bg-black/40 text-white rounded-full hover:bg-black/60 ml-2">
                        <i class="fa-solid fa-chevron-left"></i>
                      </button>
                      <button @click.prevent="activeImageIndex = (activeImageIndex + 1) % cafe.imageUrls.length"
                              class="w-8 h-8 flex items-center justify-center bg-black/40 text-white rounded-full hover:bg-black/60 mr-2">
                        <i class="fa-solid fa-chevron-right"></i>
                      </button>
                    </div>
                  </template>
                  <template x-if="cafe.imageUrls.length > 1">
                    <div x-text="`${activeImageIndex + 1} / ${cafe.imageUrls.length}`"
                         class="absolute bottom-2 right-2 bg-black/50 text-white text-xs font-bold px-2 py-1 rounded-md"></div>
                  </template>

                  @auth
                  <button @click.prevent="toggleLike(cafe.id, $event)"
                          class="absolute top-2 right-2 w-9 h-9 bg-black/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/60 transition-colors duration-300 z-10">
                    <i class="fa-heart text-lg" :class="isLiked(cafe.id) ? 'fa-solid text-pink-500' : 'fa-regular'"></i>
                  </button>
                  @endauth

                  <div x-show="cafe.isNewOpening"
                       class="absolute bottom-2 left-2 bg-fuchsia-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-md">
                    ✨ เปิดใหม่
                  </div>
                </div>

                <div class="p-3 sm:p-4 flex flex-col flex-grow">
                  <h3 class="text-base sm:text-lg font-bold mb-1 text-slate-900 group-hover:text-cyan-600 transition-colors line-clamp-2">
                    <a :href="cafe.link" x-text="cafe.title"></a>
                  </h3>
                  <p class="text-slate-500 text-xs sm:text-sm mb-2 line-clamp-2" x-text="cafe.address"></p>

                  <div class="flex-grow space-y-2">
                    <div class="flex items-center">
                      <template x-for="i in 5">
                        <i class="fa-solid fa-star text-xs" :class="i <= Math.round(cafe.rating) ? 'text-amber-400' : 'text-slate-300'"></i>
                      </template>
                      <span x-show="cafe.rating > 0" class="ml-2 text-xs text-slate-500" x-text="`(${parseFloat(cafe.rating).toFixed(1)})`"></span>
                      <span x-show="cafe.rating == 0" class="ml-2 text-xs text-slate-400">ยังไม่มีรีวิว</span>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                      <template x-for="style in cafe.cafeStyles.slice(0,2)" :key="style">
                        <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-[11px] font-medium" x-text="style"></span>
                      </template>
                    </div>

                    <div class="text-xs space-y-1.5 text-slate-600">
                      <div class="flex items-center">
                        <i class="fa-regular fa-clock w-4 h-4 mr-1.5 text-indigo-500 shrink-0"></i>
                        <span x-text="`${cafe.openDay}${cafe.closeDay ? ' - ' + cafe.closeDay : ''}${cafe.openTime ? `, ${cafe.openTime} น. - ${cafe.closeTime} น.` : ''}`"></span>
                      </div>
                      <div x-show="cafe.originalPriceRange" class="flex items-center">
                        <i class="fa-solid fa-tags w-4 h-4 mr-1.5 text-green-500 shrink-0"></i>
                        <span>ราคา: </span><span class="ml-1" x-text="cafe.originalPriceRange"></span>
                      </div>
                    </div>
                  </div>

                  <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between">
                    <a :href="cafe.link" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 font-semibold text-sm">
                      ดูรายละเอียด <i class="fa-solid fa-arrow-right w-3 h-3 ml-1.5"></i>
                    </a>
                    <div x-data="{ shareText: 'แชร์' }" class="flex items-center space-x-2">
                      <button @click.prevent="share(cafe.title, cafe.link, $el.querySelector('span'))"
                              class="flex items-center space-x-1.5 text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded-lg text-xs">
                        <i class="fa-solid fa-share-nodes"></i><span x-text="shareText"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <div x-show="allCafes.length > 0 && filteredCafes.length > 0 && displayedCafeCount < filteredCafes.length" class="mt-4 sm:mt-6 text-center">
            <p class="text-slate-500 mb-3 text-sm"
               x-text="`กำลังแสดง ${Math.min(displayedCafeCount, filteredCafes.length)} จาก ${filteredCafes.length} คาเฟ่`"></p>
            <button @click="loadMoreCafes()"
                    class="bg-cyan-600 text-white font-bold py-2.5 px-6 rounded-lg hover:bg-cyan-700 transition shadow-lg">
              โหลดเพิ่มเติม
            </button>
          </div>

          <div x-show="allCafes.length > 0 && filteredCafes.length === 0"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0"
               x-transition:enter-end="opacity-100"
               class="text-center py-10 px-6 bg-white rounded-2xl shadow mt-6">
            <div class="text-cyan-500 text-5xl mb-3"><i class="fa-solid fa-mug-saucer"></i></div>
            <h3 class="text-xl font-bold text-slate-800 mb-1">ไม่พบคาเฟ่ที่ตรงกับตัวกรอง</h3>
            <p class="text-slate-500 text-sm">ลองปรับคำค้นหา หรือ
              <button @click="clearFilters()" class="text-cyan-600 hover:underline font-medium">ล้างตัวกรองทั้งหมด</button>
            </p>
          </div>

          <div x-show="allCafes.length === 0" class="text-center py-12 bg-white rounded-2xl mt-6">
            <div class="text-gray-400 text-7xl mb-4">☕</div>
            <h3 class="text-xl font-bold text-gray-600 mb-1">ยังไม่มีคาเฟ่ในระบบ</h3>
            <p class="text-gray-400 text-sm">กรุณาติดตามคาเฟ่ใหม่ๆ ในภายหลัง</p>
          </div>
        </main>

        <!-- Right: News -->
        <aside id="news" class="col-span-12 lg:col-span-3 py-4 sm:py-6">
          <div class="lg:sticky lg:top-24">
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm space-y-4">
              <div class="flex items-center justify-between">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">ข่าวทั้งหมด</h2>
                <button class="lg:hidden text-sm text-cyan-600" @click="$refs.newsList.classList.toggle('hidden')">ซ่อน/แสดง</button>
              </div>
              <div x-ref="newsList" class="space-y-3">
                <template x-for="newsItem in filteredNews" :key="newsItem.id">
                  <div class="flex items-start gap-x-3 pt-3 border-t border-slate-200/80">
                    <a :href="newsItem.link" class="flex-shrink-0">
                      <img loading="lazy" :src="newsItem.image" :alt="'รูปภาพข่าว ' + newsItem.title"
                           class="w-14 h-14 sm:w-16 sm:h-16 object-cover rounded-lg bg-slate-200">
                    </a>
                    <div class="min-w-0">
                      <a :href="newsItem.link"
                         class="text-slate-700 font-semibold hover:text-cyan-600 hover:underline block leading-tight line-clamp-3"
                         x-text="newsItem.title"></a>
                      <p class="text-xs text-slate-500 mt-1" x-text="newsItem.dateString"></p>
                    </div>
                  </div>
                </template>
                <div x-show="filteredNews.length === 0 && allNews.length > 0" class="pt-3 border-t">
                  <p class="text-sm text-slate-500">ไม่พบข่าวสารที่ตรงกับคำค้นหา</p>
                </div>
                <div x-show="allNews.length === 0" class="pt-3 border-t">
                  <p class="text-sm text-slate-500">ยังไม่มีข่าวสาร</p>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>

  {{-- Data source --}}
  <div id="cafe-data-source" class="hidden">
    @if (isset($cafes) && $cafes->count() > 0)
      @foreach ($cafes as $cafe)
        @php
          $imageUrls = [];
          if (is_array($cafe->images)) {
              foreach ($cafe->images as $image) { $imageUrls[] = asset('storage/' . $image); }
          }
          if (empty($imageUrls)) { $imageUrls[] = asset('images/no-image.png'); }
          $openTime = $cafe->open_time ? \Carbon\Carbon::parse($cafe->open_time)->format('H:i') : '';
          $closeTime = $cafe->close_time ? \Carbon\Carbon::parse($cafe->close_time)->format('H:i') : '';
          $priceSymbol = str_repeat('฿', (int)($cafe->price_range ?? 1));
          $cafeStyles = is_array($cafe->cafe_styles) ? $cafe->cafe_styles : [];
          $facilities = is_array($cafe->facilities) ? $cafe->facilities : [];
          $payment_methods = is_array($cafe->payment_methods) ? $cafe->payment_methods : [];
          $other_services = is_array($cafe->other_services) ? $cafe->other_services : [];
        @endphp
        <div class="cafe-item"
             data-id="{{ $cafe->cafe_id }}"
             data-like-url="{{ route('cafes.toggle-like', ['cafe' => $cafe->cafe_id]) }}"
             data-link="{{ route('cafes.show', ['cafe' => $cafe->cafe_id]) }}"
             data-images='@json($imageUrls)'
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
    @if (isset($news) && $news->count() > 0)
      @foreach ($news as $item)
        @php
          $newsImages = is_array($item->images) ? $item->images : [];
          $imageUrl = !empty($newsImages) ? asset('storage/' . $newsImages[0]) : asset('images/no-image.png');
          $dateString = $item->created_at->translatedFormat('j M Y');
        @endphp
        <div class="news-item"
             data-id="{{ $item->id }}"
             data-title="{{ $item->title }}"
             data-link="{{ route('news.show', $item) }}"
             data-image="{{ $imageUrl }}"
             data-date-string="{{ $dateString }}">
        </div>
      @endforeach
    @endif
  </div>

  {{-- Footer --}}
  @include('components.footer')

  {{-- ================== JS ================== --}}
  <script>
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
        mobileFilterOpen: false, // ✅ ย้ายมาไว้ที่ root เพื่อให้ปุ่มด้านบนสั่งงานได้
        availableFilters: {
          priceRanges: ['฿','฿฿','฿฿฿','฿฿฿฿','฿฿฿฿฿'],
          days: ['จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์'],
          styles: [], facilities: [], paymentMethods: [], otherServices: []
        },
        filters: { rating: 0, time: '', days: [], isNewOpening: false, priceRanges: [], styles: [], facilities: [], paymentMethods: [], otherServices: [] },
        likedCafeIds: config.initialLikedIds || [],

        initializeAllData() {
          this.loadCafeData();
          this.loadNewsData();
          this.extractAvailableFilters();
          this.$watch('searchTerm', () => this.applyFilters());
          this.$watch('filters', () => this.applyFilters(), { deep: true });
          this.$watch('selectedHour', (newHour) => { this.filters.time = newHour ? `${newHour}:00` : ''; this.applyFilters(); });
        },

        loadNewsData() {
          const els = document.querySelectorAll('#news-data-source .news-item');
          this.allNews = Array.from(els).map(el => ({
            id: parseInt(el.dataset.id), title: el.dataset.title, link: el.dataset.link, image: el.dataset.image, dateString: el.dataset.dateString
          }));
          this.filteredNews = this.allNews;
        },

        loadCafeData() {
          const els = document.querySelectorAll('#cafe-data-source .cafe-item');
          this.allCafes = Array.from(els).map(el => ({
            id: parseInt(el.dataset.id),
            likeUrl: el.dataset.likeUrl,
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
            cafeStyles: el.dataset.styles ? el.dataset.styles.split(',').filter(Boolean) : [],
            facilities: el.dataset.facilities ? el.dataset.facilities.split(',').filter(Boolean) : [],
            paymentMethods: el.dataset.paymentMethods ? el.dataset.paymentMethods.split(',').filter(Boolean) : [],
            otherServices: el.dataset.otherServices ? el.dataset.otherServices.split(',').filter(Boolean) : [],
          }));
          this.filteredCafes = this.allCafes;
        },

        extractAvailableFilters() {
          const styles = new Set(), fac = new Set(), pay = new Set(), more = new Set();
          this.allCafes.forEach(c => {
            c.cafeStyles?.forEach(s => styles.add(s));
            c.facilities?.forEach(f => fac.add(f));
            c.paymentMethods?.forEach(p => pay.add(p));
            c.otherServices?.forEach(o => more.add(o));
          });
          this.availableFilters.styles = Array.from(styles).sort();
          this.availableFilters.facilities = Array.from(fac).sort();
          this.availableFilters.paymentMethods = Array.from(pay).sort();
          this.availableFilters.otherServices = Array.from(more).sort();
        },

        setRatingFilter(star) { this.filters.rating = (this.filters.rating === star) ? 0 : star; },

        applyFilters() {
          this.displayedCafeCount = this.cafesPerPage;
          const q = this.searchTerm.toLowerCase().trim();
          this.filteredCafes = this.allCafes.filter(cafe => {
            if (this.filters.rating > 0 && cafe.rating < this.filters.rating) return false;
            if (this.filters.isNewOpening && !cafe.isNewOpening) return false;
            if (this.filters.time) {
              const open = cafe.openTime || '00:00', close = cafe.closeTime || '23:59';
              if (!(open <= this.filters.time && this.filters.time <= close)) return false;
            }
            if (this.filters.days.length) {
              const dayStr = `${cafe.openDay || ''} ${cafe.closeDay || ''}`;
              if (!this.filters.days.some(d => dayStr.includes(d))) return false;
            }
            if (this.filters.priceRanges.length && !this.filters.priceRanges.includes(cafe.priceRange)) return false;
            if (this.filters.styles.length && !this.filters.styles.some(s => cafe.cafeStyles.includes(s))) return false;
            if (this.filters.facilities.length && !this.filters.facilities.some(f => cafe.facilities.includes(f))) return false;
            if (this.filters.paymentMethods.length && !this.filters.paymentMethods.some(p => cafe.paymentMethods.includes(p))) return false;
            if (this.filters.otherServices.length && !this.filters.otherServices.some(o => cafe.otherServices.includes(o))) return false;

            if (q) {
              const hay = `${cafe.title} ${cafe.address} ${cafe.placeName} ${cafe.cafeStyles.join(' ')}`.toLowerCase();
              if (!hay.includes(q)) return false;
            }
            return true;
          });

          this.filteredNews = this.allNews.filter(n => !q || n.title.toLowerCase().includes(q));
        },

        clearFilters() {
          this.filters = { rating:0, time:'', days:[], isNewOpening:false, priceRanges:[], styles:[], facilities:[], paymentMethods:[], otherServices:[] };
          this.selectedHour = '';
          this.searchTerm = '';
        },

        isLiked(id) { return Array.isArray(this.likedCafeIds) && this.likedCafeIds.includes(id); },
        loadMoreCafes() { this.displayedCafeCount += this.cafesPerPage; },

        share(title, url, spanEl) {
          if (navigator.share) { navigator.share({ title, url }).catch(()=>{}); }
          else {
            navigator.clipboard.writeText(url).then(()=>{
              const t = spanEl.innerText; spanEl.innerText = 'คัดลอกแล้ว!'; setTimeout(()=>spanEl.innerText=t, 1800);
            });
          }
        },

        async toggleLike(cafeId, event) {
          if (!cafeId) return;
          const btn = event.currentTarget;
          if (btn.disabled) return;
          btn.disabled = true;

          try {
            const cafe = this.allCafes.find(c => c.id === cafeId);
            if (!cafe || !cafe.likeUrl) throw new Error('ไม่พบ URL สำหรับไลค์');

            const liked = this.isLiked(cafeId);
            this.likedCafeIds = liked ? this.likedCafeIds.filter(id => id !== cafeId) : [...this.likedCafeIds, cafeId];

            const res = await fetch(cafe.likeUrl, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              credentials: 'same-origin'
            });

            if (!res.ok) {
              this.likedCafeIds = liked ? [...this.likedCafeIds, cafeId] : this.likedCafeIds.filter(id => id !== cafeId);
              throw new Error(`Server ${res.status}`);
            }
            const data = await res.json();
            if (data.status !== 'success') {
              this.likedCafeIds = liked ? [...this.likedCafeIds, cafeId] : this.likedCafeIds.filter(id => id !== cafeId);
              throw new Error('API non-success');
            }
          } catch (e) {
            console.error(e);
            alert('เกิดข้อผิดพลาดในการกดไลค์');
          } finally {
            btn.disabled = false;
          }
        }
      };
    }

    // Slider
    document.addEventListener('DOMContentLoaded', function () {
      const slider = document.querySelector('.slider');
      if (!slider) return;
      const slides = slider.querySelector('.slides');
      const items = slider.querySelectorAll('.slides a');
      if (items.length <= 1) return;

      const prev = slider.querySelector('.prev');
      const next = slider.querySelector('.next');
      const dotsWrap = slider.querySelector('.dots');
      let index = 0, itv;

      items.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => { go(i); reset(); });
        dotsWrap.appendChild(dot);
      });

      const dots = dotsWrap.querySelectorAll('.dot');

      function show(i) {
        slides.style.transform = `translateX(-${i * 100}%)`;
        dots.forEach(d => d.classList.remove('active'));
        if (dots[i]) dots[i].classList.add('active');
      }
      function go(i) { index = i; show(index); }
      function nextSlide() { index = (index + 1) % items.length; show(index); }
      function start() { itv = setInterval(nextSlide, 4000); }
      function reset() { clearInterval(itv); start(); }

      next.addEventListener('click', () => { nextSlide(); reset(); });
      prev.addEventListener('click', () => { index = (index - 1 + items.length) % items.length; show(index); reset(); });
      start();
    });
  </script>
</body>
</html>
