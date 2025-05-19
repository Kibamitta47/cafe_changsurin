@extends('layouts.app') {{-- หรือเปลี่ยนตาม layout ที่คุณใช้ --}}
@section('content')
<div class="container mt-4">
    <h3 class="mb-4">แก้ไขข่าวสารหรือโปรโมชัน</h3>

    <form action="{{ route('news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">หัวข้อ</label>
            <input type="text" name="title" class="form-control" value="{{ $news->title }}" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">รายละเอียด</label>
            <textarea name="content" class="form-control" rows="5" required>{{ $news->content }}</textarea>
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">แนบรูปภาพใหม่ (ไม่บังคับ)</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">อัปเดตข่าว</button>
    </form>
</div>
@endsection
