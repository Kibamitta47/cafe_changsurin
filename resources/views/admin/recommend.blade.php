<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกคาเฟ่แนะนำ - Admin Panel</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #F8F9FA; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #E5E7EB; padding: 12px; text-align: left; }
        th { background-color: #F3F4F6; font-weight: 600; }
        tr:nth-child(even) { background-color: #F9FAFB; }

        .shortcut-bar {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 50;
            padding: 12px 0;
            border-bottom: 1px solid #E5E7EB;
        }
    </style>
</head>
<body class="min-h-screen">

    @include('components.adminmenu')

    <main class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl font-bold text-gray-800">เลือกคาเฟ่แนะนำ</h1>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- ปุ่มลัด -->
        <div class="shortcut-bar flex flex-wrap gap-3 mb-8">
            <a href="#top-rated" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">⭐ ยอดนิยม</a>
            <a href="#new-cafes" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">✨ เปิดใหม่</a>
            
            @foreach($cafesByStyle as $style => $cafes)
                <a href="#style-{{ Str::slug($style) }}" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600">🎨 {{ $style }}</a>
            @endforeach

            @foreach($cafesByPrice as $price => $cafes)
                <a href="#price-{{ Str::slug($price) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">💰 {{ $price }}</a>
            @endforeach

            @foreach($cafesByFacility as $facility => $cafes)
                <a href="#facility-{{ Str::slug($facility) }}" class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600">🛠 {{ $facility }}</a>
            @endforeach
        </div>

        <!-- Top Rated -->
        @if($topRatedCafes->isNotEmpty())
        <section id="top-rated" class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">⭐ 10 อันดับคาเฟ่ยอดนิยม</h2>
            <table class="rounded-xl shadow-md bg-white w-full">
                <thead><tr><th>ชื่อคาเฟ่</th></tr></thead>
                <tbody>
                    @foreach($topRatedCafes as $cafe)
                        <tr><td>{{ $cafe->cafe_name }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endif

        <!-- New Cafes -->
        @if($newCafes->isNotEmpty())
        <section id="new-cafes" class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">✨ คาเฟ่เปิดใหม่</h2>
            <table class="rounded-xl shadow-md bg-white w-full">
                <thead><tr><th>ชื่อคาเฟ่</th></tr></thead>
                <tbody>
                    @foreach($newCafes as $cafe)
                        <tr><td>{{ $cafe->cafe_name }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endif

        <!-- Cafes by Style -->
        @foreach($cafesByStyle as $style => $cafes)
            @if($cafes->isNotEmpty())
            <section id="style-{{ Str::slug($style) }}" class="mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">🎨 สไตล์: {{ $style }}</h2>
                <table class="rounded-xl shadow-md bg-white w-full">
                    <thead><tr><th>ชื่อคาเฟ่</th></tr></thead>
                    <tbody>
                        @foreach($cafes as $cafe)
                            <tr><td>{{ $cafe->cafe_name }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif
        @endforeach

        <!-- Cafes by Price -->
        @foreach($cafesByPrice as $price => $cafes)
            @if($cafes->isNotEmpty())
            <section id="price-{{ Str::slug($price) }}" class="mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">💰 ช่วงราคา: {{ $price }}</h2>
                <table class="rounded-xl shadow-md bg-white w-full">
                    <thead><tr><th>ชื่อคาเฟ่</th></tr></thead>
                    <tbody>
                        @foreach($cafes as $cafe)
                            <tr><td>{{ $cafe->cafe_name }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif
        @endforeach

        <!-- Cafes by Facility -->
        @foreach($cafesByFacility as $facility => $cafes)
            @if($cafes->isNotEmpty())
            <section id="facility-{{ Str::slug($facility) }}" class="mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">🛠 สิ่งอำนวยความสะดวก: {{ $facility }}</h2>
                <table class="rounded-xl shadow-md bg-white w-full">
                    <thead><tr><th>ชื่อคาเฟ่</th></tr></thead>
                    <tbody>
                        @foreach($cafes as $item)
                            <tr><td>{{ $item['cafe']->cafe_name }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif
        @endforeach

    </main>
</body>
</html>
