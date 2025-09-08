<!-- User Dropdown -->
<div x-data="{ openProfile:false }" class="relative">
    <button @click="openProfile=!openProfile"
            class="flex items-center justify-center w-10 h-10 rounded-full focus:outline-none overflow-hidden border border-gray-300">
        @if(Auth::user()->profile_image)
            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}"
                 alt="โปรไฟล์"
                 class="w-10 h-10 object-cover">
        @else
            <!-- ถ้าไม่มีรูป ใช้ ui-avatars สร้างอัตโนมัติ -->
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D6EFD&color=fff"
                 alt="default avatar"
                 class="w-10 h-10 object-cover">
        @endif
    </button>
    <div x-show="openProfile" @click.away="openProfile=false"
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
        <div class="px-4 py-2 text-sm text-gray-700 border-b">
            ลงชื่อเข้าใช้ในชื่อ <br> 
            <strong>{{ Auth::user()->name }}</strong>
        </div>
        <a href="{{ route('user.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            <i class="fas fa-user-edit w-6 mr-1"></i>แก้ไขโปรไฟล์
        </a>
        <form action="{{ route('user.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                <i class="fas fa-sign-out-alt w-6 mr-1"></i>ออกจากระบบ
            </button>
        </form>
    </div>
</div>
