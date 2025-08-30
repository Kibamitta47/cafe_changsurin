<!-- Header Menu -->
<header class="header-menu" x-data="{ open:false }">
    <nav class="container mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
        <!-- Brand Logo -->
        <a href="{{ route('user.dashboard') }}" class="text-xl sm:text-2xl font-bold gradient-text">
            น้องช้างสะเร็น
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-2">
            <a href="{{ route('welcome') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">หน้าแรก</a>
            
            <a href="{{ route('user.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('user.dashboard') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">แดชบอร์ด</a>
            
            <a href="{{ route('user.cafes.my') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('user.cafes.my') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">คาเฟ่ของฉัน</a>
            
            <a href="{{ route('user.cafes.myLiked') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('user.cafes.myLiked') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">คาเฟ่ที่ถูกใจ</a>

            <a href="{{ route('user.reviews.my') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('user.reviews.my*') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">รีวิวของฉัน</a>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-3">
            <a href="{{ route('user.cafes.create') }}" class="hidden sm:block btn-gradient text-white font-bold py-2 px-4 rounded-full shadow-lg">
                <i class="fas fa-plus mr-1"></i> เพิ่มคาเฟ่
            </a>

            <!-- User Dropdown -->
            <div x-data="{ openProfile:false }" class="relative">
                <button @click="openProfile=!openProfile" class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-full text-blue-600 focus:outline-none">
                    <i class="fas fa-user"></i>
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

            <!-- Hamburger -->
            <button @click="open=true" class="md:hidden p-2 rounded-md hover:bg-gray-100 focus:outline-none">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Drawer -->
    <div x-show="open" class="md:hidden fixed inset-0 z-50">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
        <!-- Panel -->
        <div class="absolute top-0 left-0 w-72 max-w-[80%] h-full bg-white shadow-lg p-4 overflow-y-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            <div class="flex justify-between items-center mb-4">
                <span class="font-bold text-lg">เมนู</span>
                <button @click="open=false"><i class="fas fa-times text-lg"></i></button>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('welcome') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100">หน้าแรก</a>
                <a href="{{ route('user.dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100">หน้าบ้านของฉัน</a>
                <a href="{{ route('user.cafes.my') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100">คาเฟ่ของฉัน</a>
                <a href="{{ route('user.cafes.myLiked') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100">คาเฟ่ที่ถูกใจ</a>
                <a href="{{ route('user.reviews.my') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100">รีวิวของฉัน</a>
                <a href="{{ route('user.cafes.create') }}" class="block px-3 py-2 rounded-md bg-blue-500 text-white font-semibold">
                    <i class="fas fa-plus mr-1"></i> เพิ่มคาเฟ่
                </a>
            </nav>
        </div>
    </div>
</header>
