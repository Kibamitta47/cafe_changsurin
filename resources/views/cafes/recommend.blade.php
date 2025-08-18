<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คาเฟ่แนะนำ - น้องช้างสะเร็น</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #F8F9FA; }
        [x-cloak] { display: none !important; }
        .sidebar-link.active {
            background-color: #E0F2FF;
            color: #8B5E3C;
            font-weight: 600;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest
    @auth
        @include('components.2navbar')
    @endauth

    <main class="container mx-auto px-4 py-8 flex-grow">
        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-[#E0F2FF] to-[#B3DFF2] rounded-2xl p-8 md:p-12 text-center mb-12 shadow-lg">
            <h1 class="text-4xl md:text-5xl font-bold text-[#8B5E3C]">คาเฟ่ที่เราแนะนำ</h1>
            <p class="mt-3 text-lg text-slate-600">คัดสรรคาเฟ่คุณภาพ บรรยากาศดี ที่คุณไม่ควรพลาด</p>
        </section>

        <div x-data="{ activeTab: 'topRated' }">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Sidebar Column -->
                <aside class="lg:col-span-1">
                    <div class="sticky top-24 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">หมวดหมู่แนะนำ</h2>
                            <ul class="space-y-2">
                                @if($topRatedCafes->isNotEmpty())
                                <li>
                                    <button @click="activeTab = 'topRated'" 
                                            :class="{ 'sidebar-link active': activeTab === 'topRated' }" 
                                            class="w-full text-left flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                        <span class="w-8 h-8 flex items-center justify-center bg-amber-100 text-amber-500 rounded-lg">⭐</span>
                                        <span>10 อันดับยอดนิยม</span>
                                    </button>
                                </li>
                                @endif

                                @if($newCafes->isNotEmpty())
                                <li>
                                    <button @click="activeTab = 'newCafes'" 
                                            :class="{ 'sidebar-link active': activeTab === 'newCafes' }" 
                                            class="w-full text-left flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                        <span class="w-8 h-8 flex items-center justify-center bg-fuchsia-100 text-fuchsia-500 rounded-lg">✨</span>
                                        <span>คาเฟ่เปิดใหม่</span>
                                    </button>
                                </li>
                                @endif

                                @foreach($cafesByStyle as $style => $cafes)
                                    @if($cafes->isNotEmpty())
                                    <li>
                                        <button @click="activeTab = '{{ Str::slug($style) }}'" 
                                                :class="{ 'sidebar-link active': activeTab === '{{ Str::slug($style) }}' }" 
                                                class="w-full text-left flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                            <span class="w-8 h-8 flex items-center justify-center bg-pink-100 text-pink-500 rounded-lg">🎨</span>
                                            <span>สไตล์: {{ $style }}</span>
                                        </button>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </aside>

                <!-- Main Content Column -->
                <div class="lg:col-span-3">
                    <!-- Section: Top 10 -->
                    <template x-if="activeTab === 'topRated'">
                        <section x-transition x-cloak>
                            <h1 class="text-4xl font-bold text-gray-800 mb-6">⭐ 10 อันดับคาเฟ่ยอดนิยม</h1>
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                                @foreach($topRatedCafes as $cafe)
                                    @include('partials.cafe-card', ['cafe' => $cafe])
                                @endforeach
                            </div>
                        </section>
                    </template>
                    
                    <!-- Section: New Cafes -->
                    <template x-if="activeTab === 'newCafes'">
                        <section x-transition x-cloak>
                            <h1 class="text-4xl font-bold text-gray-800 mb-6">✨ คาเฟ่เปิดใหม่</h1>
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                                @foreach($newCafes as $cafe)
                                    @include('partials.cafe-card', ['cafe' => $cafe])
                                @endforeach
                            </div>
                        </section>
                    </template>

<!-- Section: By Style -->
@foreach($cafesByStyle as $style => $cafes)
    @if($cafes->isNotEmpty())
        <div x-show="activeTab === '{{ Str::slug($style) }}'" x-transition x-cloak>
            <section>
                <h1 class="text-4xl font-bold text-gray-800 mb-6">🎨 สไตล์: {{ $style }}</h1>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($cafes as $cafe)
                        @include('partials.cafe-card', ['cafe' => $cafe])
                    @endforeach
                </div>
            </section>
        </div>
    @endif
@endforeach


    
    @include('components.footer')

</body>
</html>
