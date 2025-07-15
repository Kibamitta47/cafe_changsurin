@extends('layouts.app') {{-- สมมติว่ามี layout ชื่อ app.blade.php ที่มี @yield('content') --}}

@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">โปรไฟล์ของฉัน</h1>
        <div class="bg-white shadow-md rounded-lg p-6">
            <p class="text-gray-700">ชื่อผู้ใช้: {{ Auth::user()->name ?? 'N/A' }}</p>
            <p class="text-gray-700">อีเมล: {{ Auth::user()->email ?? 'N/A' }}</p>
            {{-- เพิ่มข้อมูลโปรไฟล์อื่นๆ ที่นี่ --}}
            <a href="{{ route('profile.edit') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">แก้ไขโปรไฟล์</a>
        </div>
    </div>
@endsection