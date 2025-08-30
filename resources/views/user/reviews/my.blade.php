<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รีวิวของฉัน</title>
    <!-- เพิ่ม CSS ของคุณตามต้องการ -->
</head>
<body>
    <h1>รีวิวทั้งหมดของฉัน</h1>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if ($reviews->isEmpty())
        <p>คุณยังไม่มีรีวิว</p>
    @else
        <ul>
            @foreach ($reviews as $review)
                <li>
                    <h3>
                        รีวิวสำหรับร้าน: 
                        <a href="#">{{ $review->cafe->cafe_name ?? 'ไม่มีชื่อร้าน' }}</a>
                    </h3>
                    <h4>{{ $review->title }} (คะแนน: {{ $review->rating }}/5)</h4>
                    <p>{{ $review->content }}</p>
                    <small>เขียนเมื่อ: {{ $review->created_at->format('d/m/Y') }}</small>
                    <br>
                    <a href="{{ route('user.reviews.edit', $review) }}">แก้ไข</a>
                    <form action="{{ route('user.reviews.destroy', $review) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรีวิวนี้?')">ลบ</button>
                    </form>
                </li>
                <hr>
            @endforeach
        </ul>
        
        <!-- แสดง Pagination links -->
        {{ $reviews->links() }}
    @endif
</body>
</html>