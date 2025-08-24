<div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 group flex flex-col">
    <div class="relative h-56 overflow-hidden rounded-t-2xl">
       <a href="{{ route('cafes.show', $cafe) }}">
            @php
                // ไม่จำเป็นต้อง json_decode อีกต่อไป ถ้าคุณใช้ Casting ใน Model แล้ว
                $images = is_array($cafe->images) ? $cafe->images : []; 
                $imageUrl = !empty($images) ? asset('storage/' . $images[0]) : 'https://placehold.co/400x300/E2E8F0/64748B?text=No+Image';
            @endphp
            <img src="{{ $imageUrl }}" alt="รูปคาเฟ่ {{ $cafe->cafe_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
        </a>
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        @if($cafe->is_new_opening)
            <div class="absolute top-3 left-3 bg-fuchsia-500 text-white px-3 py-1 rounded-full text-xs font-semibold tracking-wide shadow-md">✨ เปิดใหม่</div>
        @endif
        <div class="absolute bottom-4 left-4 right-4 text-white">
            
            {{-- ========================================== --}}
            {{-- ✅✅✅ จุดที่แก้ไข 2 อยู่ตรงนี้ ✅✅✅ --}}
            {{-- ========================================== --}}
            <h3 class="font-bold text-xl leading-tight truncate">
                <a href="{{ route('cafes.show', $cafe) }}" class="text-white">{{ $cafe->cafe_name }}</a>
            </h3>
            <p class="text-white/80 text-sm truncate">{{ $cafe->address }}</p>
        </div>
    </div>