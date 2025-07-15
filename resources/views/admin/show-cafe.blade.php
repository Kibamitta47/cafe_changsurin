{{-- resources/views/admin/all-cafes.blade.php --}}

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคาเฟ่ทั้งหมด - น้องช้างสะเร็น</title>
    <script src="[https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)"></script>
    <script src="[https://unpkg.com/alpinejs](https://unpkg.com/alpinejs)" defer></script>
    <link rel="preconnect" href="[https://fonts.googleapis.com](https://fonts.googleapis.com)">
    <link rel="preconnect" href="[https://fonts.gstatic.com](https://fonts.gstatic.com)" crossorigin>
    <link href="[https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap](https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap)" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-gray-100">

    {{-- Navbar component --}}
    @include('components.1navbar')

    <div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow-lg mt-8 mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center">รายการคาเฟ่ทั้งหมด</h1>

        @if($cafes->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500 text-2xl mb-4">😞 ยังไม่มีข้อมูลคาเฟ่ในระบบ</p>
                <p class="text-gray-400">คุณสามารถเพิ่มคาเฟ่ใหม่ได้จากส่วนจัดการ</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($cafes as $cafe)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-200 flex flex-col">
                        @php
                            // เนื่องจากกำหนด $casts = ['images' => 'array'] ใน Cafe Model แล้ว
                            // $cafe->images จะเป็น array โดยตรง ไม่ต้อง json_decode() ซ้ำ
                            $images = $cafe->images;
                            $firstImage = !empty($images) && isset($images[0]) ? $images[0] : '[https://placehold.co/400x300/E2E8F0/64748B?text=No+Image](https://placehold.co/400x300/E2E8F0/64748B?text=No+Image)';
                        @endphp
                        <div class="relative h-48 overflow-hidden rounded-t-lg">
                            <img src="{{ asset('storage/' . $firstImage) }}" alt="ภาพคาเฟ่ {{ $cafe->cafe_name }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                            <div class="absolute top-3 right-3">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    {{ $cafe->status_text }}
                                </span>
                            </div>
                            @if($cafe->is_new_opening)
                                <div class="absolute bottom-3 left-3 bg-fuchsia-500 text-white px-3 py-1 rounded-full text-xs font-semibold tracking-wide">✨ เปิดใหม่</div>
                            @endif
                        </div>
                        <div class="p-4 flex flex-col flex-grow">
                            <h2 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">
                                <a href="{{ route('admin.cafes.show', $cafe->id) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $cafe->cafe_name }}
                                </a>
                            </h2>
                            <p class="text-gray-600 text-sm mb-1"><strong>ที่ตั้ง:</strong> {{ $cafe->address }}</p>
                            <p class="text-gray-600 text-sm mb-1"><strong>สถานที่:</strong> {{ $cafe->place_name }}</p>
                            <p class="text-gray-600 text-sm mb-3"><strong>ช่วงราคา:</strong> {{ $cafe->price_range ?? '-' }}</p>

                            {{-- เพิ่มข้อมูลอื่นๆ --}}
                            <div class="text-sm text-gray-700 space-y-1 mb-3">
                                @if($cafe->phone)
                                    <p><strong>โทรศัพท์:</strong> {{ $cafe->phone }}</p>
                                @endif
                                @if($cafe->email)
                                    <p><strong>อีเมล:</strong> {{ $cafe->email }}</p>
                                @endif
                                @if($cafe->website)
                                    <p><strong>เว็บไซต์:</strong> <a href="{{ $cafe->website }}" target="_blank" class="text-blue-500 hover:underline line-clamp-1">{{ $cafe->website }}</a></p>
                                @endif
                                @if($cafe->facebook_page)
                                    <p><strong>Facebook:</strong> <a href="{{ $cafe->facebook_page }}" target="_blank" class="text-blue-500 hover:underline line-clamp-1">{{ $cafe->facebook_page }}</a></p>
                                @endif
                                @if($cafe->instagram_page)
                                    <p><strong>Instagram:</strong> <a href="{{ $cafe->instagram_page }}" target="_blank" class="text-blue-500 hover:underline line-clamp-1">{{ $cafe->instagram_page }}</a></p>
                                @endif
                                @if($cafe->line_id)
                                    <p><strong>Line ID:</strong> {{ $cafe->line_id }}</p>
                                @endif
                                @if($cafe->open_day && $cafe->close_day && $cafe->open_time && $cafe->close_time)
                                    <p><strong>เวลาทำการ:</strong> {{ $cafe->open_day }} - {{ $cafe->close_day }}, {{ $cafe->open_time->format('H:i') }} - {{ $cafe->close_time->format('H:i') }}</p>
                                @endif
                            </div>

                            {{-- สไตล์คาเฟ่ --}}
                            @if(!empty($cafe->cafe_styles))
                                <div class="mb-2">
                                    <strong class="text-gray-800 text-sm">สไตล์:</strong>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($cafe->cafe_styles as $style)
                                            <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-xs">{{ $style }}</span>
                                        @endforeach
                                        @if($cafe->other_style)
                                            <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-xs">อื่นๆ: {{ $cafe->other_style }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- สิ่งอำนวยความสะดวก --}}
                            @if(!empty($cafe->facilities))
                                <div class="mb-2">
                                    <strong class="text-gray-800 text-sm">สิ่งอำนวยความสะดวก:</strong>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($cafe->facilities as $facility)
                                            <span class="bg-cyan-100 text-cyan-800 px-2 py-0.5 rounded-full text-xs">{{ $facility }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- ช่องทางชำระเงิน --}}
                            @if(!empty($cafe->payment_methods))
                                <div class="mb-2">
                                    <strong class="text-gray-800 text-sm">ช่องทางชำระเงิน:</strong>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($cafe->payment_methods as $method)
                                            <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full text-xs">{{ $method }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- บริการเพิ่มเติม --}}
                            @if(!empty($cafe->other_services))
                                <div class="mb-2">
                                    <strong class="text-gray-800 text-sm">บริการเพิ่มเติม:</strong>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($cafe->other_services as $service)
                                            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs">{{ $service }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-auto pt-3 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <span>
                                        @if($cafe->reviews->count() > 0)
                                            {{ number_format($cafe->reviews->avg('rating'), 1) }}/5 ({{ $cafe->reviews->count() }} รีวิว)
                                        @else
                                            ยังไม่มีรีวิว
                                        @endif
                                    </span>
                                </div>
                                <a href="{{ route('admin.cafes.show', $cafe->id) }}" class="inline-flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                                    ดูรายละเอียด
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>