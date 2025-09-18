{{-- resources/views/cafes/top10.blade.php --}}
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Top10 คาเฟ่ยอดนิยม</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Kanit', sans-serif; background-color: #F8F9FA; }
    .card { border:1px solid #E5E7EB; border-radius:16px; background:#fff; box-shadow:0 8px 20px rgba(0,0,0,.05); }
  </style>
</head>
<body class="min-h-screen">
  @include('components.2navbar')

  <main class="container mx-auto px-4 py-10">
    <h1 class="text-4xl font-bold text-gray-800 mb-2">⭐ 10 อันดับคาเฟ่ยอดนิยม</h1>
    <p class="text-gray-500 mb-8">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>

    @if($topRatedCafes->isEmpty())
      <div class="card p-6 text-gray-600">ยังไม่มีข้อมูล Top10</div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($topRatedCafes as $idx => $cafe)
          <a href="{{ url('/cafes/'.$cafe->id) }}" class="card overflow-hidden group">
            <div class="relative h-48 w-full overflow-hidden">
              <img
                src="{{ asset($cafe->image_path ? 'images/'.$cafe->image_path : 'images/placeholder-cafe.jpg') }}"
                alt="{{ $cafe->cafe_name }}"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
              <div class="absolute top-3 left-3 bg-black/70 text-white text-sm px-3 py-1 rounded-full">
                #{{ $idx + 1 }}
              </div>
            </div>
            <div class="p-5">
              <h2 class="text-xl font-semibold text-gray-800 mb-1 line-clamp-1">
                {{ $cafe->cafe_name }}
              </h2>

              <div class="flex items-center gap-3 text-sm text-gray-600">
                <span class="inline-flex items-center">
                  ⭐ <span class="ml-1">{{ number_format($cafe->rating_avg ?? 0, 2) }}</span>
                </span>
                <span class="inline-flex items-center">
                  <i class="fa-regular fa-thumbs-up mr-1"></i>{{ $cafe->likes_count ?? 0 }}
                </span>
                <span class="inline-flex items-center">
                  <i class="fa-regular fa-comment-dots mr-1"></i>{{ $cafe->review_count ?? 0 }}
                </span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </main>
</body>
</html>
