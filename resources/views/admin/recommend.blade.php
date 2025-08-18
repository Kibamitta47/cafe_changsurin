<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกคาเฟ่แนะนำ - Admin Panel</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #F8F9FA; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen">

    @include('components.adminmenu')
    <main class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800">เลือกคาเฟ่แนะนำ</h1>
            <a href="{{ route('cafes.recommend') }}" target="_blank" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                ดูหน้าเว็บจริง
            </a>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Top 10 Rated Cafes -->
        @if($topRatedCafes->isNotEmpty())
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">⭐ 10 อันดับคาเฟ่ยอดนิยม</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($topRatedCafes as $cafe)
                    <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 border-4 {{ $cafe->is_recommended ? 'border-green-400' : 'border-transparent' }}">
                        <img src="{{ !empty($cafe->images) ? asset('storage/' . (is_array($cafe->images) ? $cafe->images[0] : json_decode($cafe->images)[0])) : 'https://placehold.co/400x250' }}" class="h-48 w-full object-cover rounded-t-xl">
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-800 truncate">{{ $cafe->cafe_name }}</h3>
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-sm font-semibold {{ $cafe->is_recommended ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $cafe->is_recommended ? 'แนะนำ' : 'ไม่แนะนำ' }}
                                </span>
                                <form action="{{ route('admin.cafes.toggle_recommend', $cafe) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-full {{ $cafe->is_recommended ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                        {{ $cafe->is_recommended ? 'ยกเลิก' : 'ตั้งเป็นแนะนำ' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- New Cafes -->
        @if($newCafes->isNotEmpty())
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">✨ คาเฟ่เปิดใหม่</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($newCafes as $cafe)
                    <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 border-4 {{ $cafe->is_recommended ? 'border-green-400' : 'border-transparent' }}">
                        <img src="{{ !empty($cafe->images) ? asset('storage/' . (is_array($cafe->images) ? $cafe->images[0] : json_decode($cafe->images)[0])) : 'https://placehold.co/400x250' }}" class="h-48 w-full object-cover rounded-t-xl">
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-800 truncate">{{ $cafe->cafe_name }}</h3>
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-sm font-semibold {{ $cafe->is_recommended ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $cafe->is_recommended ? 'แนะนำ' : 'ไม่แนะนำ' }}
                                </span>
                                <form action="{{ route('admin.cafes.toggle_recommend', $cafe) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-full {{ $cafe->is_recommended ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                        {{ $cafe->is_recommended ? 'ยกเลิก' : 'ตั้งเป็นแนะนำ' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Cafes by Style -->
        @foreach($cafesByStyle as $style => $cafes)
            @if($cafes->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">🎨 สไตล์: {{ $style }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($cafes as $cafe)
                        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 border-4 {{ $cafe->is_recommended ? 'border-green-400' : 'border-transparent' }}">
                            <img src="{{ !empty($cafe->images) ? asset('storage/' . (is_array($cafe->images) ? $cafe->images[0] : json_decode($cafe->images)[0])) : 'https://placehold.co/400x250' }}" class="h-48 w-full object-cover rounded-t-xl">
                            <div class="p-4">
                                <h3 class="font-bold text-lg text-gray-800 truncate">{{ $cafe->cafe_name }}</h3>
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-sm font-semibold {{ $cafe->is_recommended ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $cafe->is_recommended ? 'แนะนำ' : 'ไม่แนะนำ' }}
                                    </span>
                                    <form action="{{ route('admin.cafes.toggle_recommend', $cafe) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-full {{ $cafe->is_recommended ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                            {{ $cafe->is_recommended ? 'ยกเลิก' : 'ตั้งเป็นแนะนำ' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif
        @endforeach

    </main>
</body>
</html>