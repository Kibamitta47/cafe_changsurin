<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title> {{ $cafe->cafe_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1350&q=80') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(30, 30, 30, 0.45);
            backdrop-filter: blur(8px);
            z-index: -1;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-gray-900 font-sans">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest
    @auth
        @include('components.2navbar')
    @endauth

    <main class="flex-grow" x-data="{ lightboxOpen: false, lightboxSrc: '' }">

        <section class="bg-white bg-opacity-90 shadow-lg p-6 mb-8 rounded-md mx-[100px] mt-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $cafe->cafe_name }}</h1>

            <div class="text-gray-700 text-sm space-y-1 max-w-5xl mx-auto">
                <p><strong>สถานที่:</strong> {{ $cafe->place_name }}</p>
                <p><strong>ที่อยู่:</strong> {{ $cafe->address }}</p>
                <p><strong>ช่วงราคา:</strong> {{ $cafe->price_range ?? '-' }}</p>
                <p><strong>เบอร์โทรศัพท์:</strong> {{ $cafe->phone_number ?? '-' }}</p>
                <p>
                    <strong>เว็บไซต์:</strong>
                    @if($cafe->website)
                        <a href="{{ $cafe->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $cafe->website }}</a>
                    @else
                        -
                    @endif
                </p>
                <p><strong>เวลาทำการ:</strong> {{ $cafe->opening_hours ?? '-' }}</p>
                <p class="pt-2"><strong>รายละเอียด:</strong></p>
                <p class="whitespace-pre-line">{{ $cafe->description ?? 'ไม่มีรายละเอียด' }}</p>

                <p class="pt-2"><strong>สิ่งอำนวยความสะดวก:</strong></p>
                @if($cafe->amenities)
                    @php
                        $amenities = is_string($cafe->amenities) ? json_decode($cafe->amenities, true) : (is_array($cafe->amenities) ? $cafe->amenities : explode(',', $cafe->amenities));
                        $amenities = array_filter($amenities);
                    @endphp
                    @if(!empty($amenities))
                        <ul class="list-disc list-inside ml-4">
                            @foreach($amenities as $amenity)
                                <li>{{ trim($amenity) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>-</p>
                    @endif
                @else
                    <p>-</p>
                @endif
            </div>

            @php
                $images = is_string($cafe->images) ? json_decode($cafe->images, true) : (is_array($cafe->images) ? $cafe->images : []);
            @endphp
            @if (!empty($images))
                <div class="mt-6 grid grid-cols-6 gap-2 max-w-5xl mx-auto">
                    @foreach ($images as $img)
                        <img
                            src="{{ asset('storage/' . $img) }}"
                            alt="{{ $cafe->cafe_name }}"
                            class="rounded shadow object-cover aspect-square max-w-[140px] max-h-[140px] cursor-pointer hover:scale-110 transition-transform duration-300"
                            @click="lightboxSrc='{{ asset('storage/' . $img) }}'; lightboxOpen=true"
                        >
                    @endforeach
                </div>
            @endif

            <div class="mt-6 max-w-5xl mx-auto">
                <a href="https://www.google.com/maps/search/?api=1&query={{ $cafe->lat }},{{ $cafe->lng }}" target="_blank"
                   class="inline-block px-6 py-3 bg-amber-600 text-white rounded hover:bg-amber-700 transition">
                    ดูแผนที่ใน Google Maps
                </a>
            </div>
        </section>

        <section class="bg-white bg-opacity-90 rounded-lg shadow-md p-6 max-w-4xl mx-auto mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-900">รีวิวจากผู้ใช้</h2>

            {{-- ปุ่มสำหรับเขียนรีวิว (แสดงเมื่อล็อกอินแล้วเท่านั้น) --}}
            @auth
            <div class="mb-6 text-right">
                {{-- แก้ไขตรงนี้: เปลี่ยนชื่อ Route เป็น 'user.reviews.create' --}}
                <a href="{{ route('user.reviews.create', ['cafe_id' => $cafe->id]) }}" class="inline-block px-5 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    เขียนรีวิว
                </a>
            </div>
            @endauth

            @if($reviews->isEmpty())
                <p class="text-gray-700 text-sm">ยังไม่มีรีวิวสำหรับคาเฟ่นี้</p>
            @else
                <div class="space-y-4">
                    @foreach ($reviews as $review)
                        @php
                            $reviewImages = is_string($review->images) ? json_decode($review->images, true) : (is_array($review->images) ? $review->images : []);
                        @endphp
                        <div class="border rounded p-3 bg-gray-50 bg-opacity-80">
                            <p class="text-sm font-semibold text-gray-900">{{ $review->user_name ?? 'ผู้ใช้ไม่ระบุชื่อ' }}
                                <span class="text-xs text-gray-500">({{ $review->created_at->format('d/m/Y') }})</span>
                            </p>
                            <p class="text-yellow-500 mt-1">⭐️ {{ $review->rating ?? '-' }}/5</p>
                            <p class="mt-2 text-gray-900 text-sm whitespace-pre-line">{{ $review->content }}</p>

                            @if(!empty($reviewImages))
                                <div class="mt-3 grid grid-cols-6 gap-1">
                                    @foreach($reviewImages as $rimg)
                                        <img
                                            src="{{ asset('storage/' . $rimg) }}"
                                            alt="รูปรีวิว"
                                            class="rounded object-cover w-full aspect-square max-w-[80px] max-h-[80px] cursor-pointer hover:scale-110 transition-transform duration-300"
                                            @click="lightboxSrc='{{ asset('storage/' . $rimg) }}'; lightboxOpen=true"
                                        >
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <div
            x-show="lightboxOpen"
            x-transition.opacity
            style="display: none;"
            class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50"
            @click.self="lightboxOpen = false"
        >
            <button
                class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300"
                @click="lightboxOpen = false"
                aria-label="ปิดรูป"
            >×</button>
            <img
                :src="lightboxSrc"
                alt="รูปขยายใหญ่"
                class="max-w-[90vw] max-h-[90vh] rounded shadow-lg"
                @click.stop
            >
        </div>

    </main>

    <footer class="bg-white bg-opacity-80 shadow-inner py-3 text-center text-gray-700 text-xs">
        © {{ date('Y') }} ระบบคาเฟ่
    </footer>
</body>
</html>