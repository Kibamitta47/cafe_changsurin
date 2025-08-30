<!-- Header Menu -->
<header class="header-menu">
    <nav class="container mx-auto px-6 py-3 flex justify-between items-center">
        <!-- Brand Logo -->
        <a href="{{ route('user.dashboard') }}" class="text-2xl font-bold gradient-text">
            น้องช้างสะเร็น
        </a>

        <!-- Main Navigation Links -->
        <div class="hidden md:flex items-center space-x-2">
            <a href="{{ route('welcome') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">หน้าแรก</a>
            
            <a href="{{ route('user.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('user.dashboard') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">แดชบอร์ด</a>
            
            <a href="{{ route('user.cafes.my') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('user.cafes.my') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">คาเฟ่ของฉัน</a>
            
            <a href="{{ route('user.cafes.myLiked') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('user.cafes.myLiked') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">คาเฟ่ที่ถูกใจ</a>

            <!-- เมนูที่เพิ่มเข้ามาใหม่ -->
            <a href="{{ route('user.reviews.my') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('user.reviews.my*') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">รีวิวของฉัน</a>
        </div>

        <!-- Profile Dropdown & CTA Button -->
        <div class="flex items-center gap-4">
            <a href="{{ route('user.cafes.create') }}" class="hidden sm:block btn-gradient text-white font-bold py-2 px-4 rounded-full shadow-lg">
                <i class="fas fa-plus mr-2"></i> เพิ่มคาเฟ่
            </a>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-full text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-user"></i>
                </button>

                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    
                    <div class="px-4 py-2 text-sm text-gray-700 border-b">
                        ลงชื่อเข้าใช้ในชื่อ <br> <strong class="font-bold">{{ Auth::user()->name }}</strong>
                    </div>
                    <a href="{{ route('user.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-user-edit w-6 mr-1"></i>แก้ไขโปรไฟล์</a>
                    <form action="{{ route('user.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt w-6 mr-1"></i>ออกจากระบบ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</header>