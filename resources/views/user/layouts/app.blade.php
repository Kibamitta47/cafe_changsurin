<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'น้องช้างสะเร็น')</title> {{-- สามารถกำหนด title ได้จากแต่ละหน้า --}}
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Alpine.js CDN (defer เพื่อให้โหลดหลังจาก DOM พร้อม) --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Font Awesome CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    {{-- Google Fonts: Kanit --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- CSRF Token Meta Tag สำหรับ AJAX Requests (สำคัญมากสำหรับฟังก์ชัน Like) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family: 'Kanit', sans-serif; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .highlight { background-color: #a7f3d0; font-weight: 600; padding: 0 2px; border-radius: 3px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- Navbar --}}
    @guest
        @include('components.1navbar')
    @endguest

    @auth
        @include('components.2navbar')
    @endauth

    {{-- Main Content Section --}}
    <main>
        @yield('content')
    </main>

    {{-- Optional: Footer --}}
    {{-- <footer class="bg-gray-800 text-white py-6 text-center mt-auto">
        <div class="max-w-7xl mx-auto px-6">
            &copy; {{ date('Y') }} น้องช้างสะเร็น. All rights reserved.
        </div>
    </footer> --}}

    {{-- Alpine.js Global Store สำหรับ Like/Unlike (คัดลอกมาจาก welcome.blade.php) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('like', {
                toggle(cafeId, buttonElement) {
                    const icon = buttonElement.querySelector('i');
                    let isCurrentlyLiked = icon.classList.contains('fa-solid'); 

                    fetch(`/cafes/${cafeId}/toggle-like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 401) {
                                alert('กรุณาเข้าสู่ระบบเพื่อกดถูกใจคาเฟ่');
                                window.location.href = '/login';
                            }
                            throw new Error('Network response was not ok.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.liked) {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid', 'text-pink-500');
                        } else {
                            icon.classList.remove('fa-solid', 'text-pink-500');
                            icon.classList.add('fa-regular');
                        }

                        // อัปเดตสถานะใน Alpine.js components ที่เกี่ยวข้อง
                        if (Alpine.raw(Alpine.data(document.querySelector('[x-data="pageController()"]'))).allCafes) {
                            const cafeIndex = Alpine.raw(Alpine.data(document.querySelector('[x-data="pageController()"]'))).allCafes.findIndex(c => c.id == cafeId);
                            if (cafeIndex !== -1) {
                                Alpine.raw(Alpine.data(document.querySelector('[x-data="pageController()"]'))).allCafes[cafeIndex].isLiked = data.liked;
                            }
                        }
                        if (Alpine.raw(Alpine.data(document.querySelector('[x-data="searchComponent()"]'))).searchResults) {
                            const searchCafeIndex = Alpine.raw(Alpine.data(document.querySelector('[x-data="searchComponent()"]'))).searchResults.findIndex(item => item.id == cafeId && item.type === 'cafe');
                            if (searchCafeIndex !== -1) {
                                Alpine.raw(Alpine.data(document.querySelector('[x-data="searchComponent()"]'))).searchResults[searchCafeIndex].isLiked = data.liked;
                            }
                        }
                        // สำหรับหน้า liked-cafes.blade.php ที่อาจจะลบการ์ดออกเมื่อ Unlike
                        if (window.location.pathname.includes('/my-liked-cafes') && !data.liked) {
                            const cafeElement = buttonElement.closest('.relative.bg-white.rounded-lg.shadow-md');
                            if (cafeElement) {
                                cafeElement.remove();
                            }
                            // ตรวจสอบว่าเหลือคาเฟ่ที่ถูกใจอยู่หรือไม่
                            if (document.querySelectorAll('.relative.bg-white.rounded-lg.shadow-md').length === 0) {
                                const container = document.querySelector('.grid.grid-cols-1');
                                if (container) {
                                    container.innerHTML = `
                                        <div class="bg-white shadow-md rounded-lg p-8 text-center col-span-full">
                                            <p class="text-gray-600 text-lg">คุณยังไม่มีคาเฟ่ที่ถูกใจเลย</p>
                                            <a href="{{ route('welcome') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                                ค้นหาคาเฟ่
                                            </a>
                                        </div>
                                    `;
                                }
                            }
                        }

                        console.log(data.message);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('เกิดข้อผิดพลาดในการดำเนินการ กรุณาลองใหม่');
                    });
                }
            });
        });
    </script>

</body>
</html>
