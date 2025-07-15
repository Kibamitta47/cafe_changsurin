<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>น้องช้างสะเร็น</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100">

    {{-- เลือก navbar ตามสถานะ --}}
    @guest
        @include('components.1navbar')
    @endguest

    @auth
        @include('components.2navbar')
    @endauth

    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">คาเฟ่ในระบบ</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($cafes as $cafe)
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold">
                        <a href="{{ route('cafes.show', $cafe->id) }}" class="text-blue-600 hover:underline">
                            {{ $cafe->cafe_name }}
                        </a>
                    </h2>
                    <p class="text-gray-600">{{ $cafe->place_name }}</p>
                    <p class="text-sm text-gray-500 mb-2">{{ $cafe->address }}</p>

                    @php $images = json_decode($cafe->images); @endphp

                    @if (!empty($images) && is_array($images))
                        <div x-data="{ imgIndex: 0, images: {{ json_encode($images) }}, baseUrl: '{{ asset('storage') }}/' }" class="relative w-full h-64 mb-2">
                            <template x-for="(img, index) in images" :key="index">
                                <img
                                    x-show="imgIndex === index"
                                    :src="baseUrl + img"
                                    class="w-full h-64 object-cover rounded transition-all duration-300"
                                >
                            </template>

                            <button @click="imgIndex = (imgIndex === 0 ? images.length - 1 : imgIndex - 1)"
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-white px-2 py-1 rounded-r shadow">
                                ◀
                            </button>

                            <button @click="imgIndex = (imgIndex === images.length - 1 ? 0 : imgIndex + 1)"
                                class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-white px-2 py-1 rounded-l shadow">
                                ▶
                            </button>
                        </div>
                    @endif

                    <a href="https://www.google.com/maps?q={{ $cafe->lat }},{{ $cafe->lng }}" target="_blank"
                       class="text-blue-600 text-sm hover:underline block mb-2">
                        ดูแผนที่
                    </a>

                    <div class="flex justify-between mt-2">
                        <a href="{{ url('/cafes/' . $cafe->id . '/review') }}"
                            class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 text-sm">รีวิว</a>
                        <button class="bg-pink-500 text-white px-3 py-1 rounded hover:bg-pink-600 text-sm">❤️ บันทึก</button>
                        <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">🔗 แชร์</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>
