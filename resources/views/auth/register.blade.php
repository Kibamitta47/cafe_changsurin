<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>เข้าสู่ระบบด้วย LINE</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- ฟอนต์ Sarabun -->
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Sarabun', sans-serif;
    }
  </style>
</head>
<body class="bg-green-50 flex items-center justify-center min-h-screen px-4">

  <div class="w-full max-w-sm bg-white rounded-2xl shadow-md p-8 space-y-6">
    
    <!-- โลโก้ LINE -->
    <div class="text-center">
      <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/LINE_logo.svg" alt="LINE Logo" class="w-24 mx-auto mb-2">
      <h2 class="text-2xl font-semibold text-gray-800">เข้าสู่ระบบด้วย LINE</h2>
      <p class="text-sm text-gray-500 mt-1">ใช้บัญชี LINE เพื่อเริ่มต้นใช้งาน</p>
    </div>

    <!-- แสดงข้อความสถานะ -->
    @if (session('status'))
      <div class="text-sm text-green-700 bg-green-100 p-3 rounded text-center">
        {{ session('status') }}
      </div>
    @endif

    <!-- แสดงข้อความผิดพลาด -->
    @if (session('error'))
      <div class="text-sm text-red-700 bg-red-100 p-3 rounded text-center">
        {{ session('error') }}
      </div>
    @endif

    <!-- ปุ่มเข้าสู่ระบบด้วย LINE -->
    <div class="text-center">
      <a href="{{ route('line.login') }}"
         class="inline-flex items-center justify-center w-full px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow transition duration-200">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12c0 5.52 4.48 10 10 10 1.99 0 3.84-.68 5.29-1.82l4.23 1.11-1.11-4.23A9.947 9.947 0 0 0 22 12c0-5.52-4.48-10-10-10zM8 14h-2v-4h2v4zm6 0h-2v-4h2v4z"/>
        </svg>
        เข้าสู่ระบบด้วย LINE
      </a>
    </div>

    <!-- ลิงก์อื่น ๆ -->
    <div class="text-center text-sm text-gray-600">
      ยังไม่มีบัญชี? 
      <a href="{{ route('register') }}" class="text-blue-600 hover:underline">ลงทะเบียน</a>
      <span class="mx-1">|</span>
      <a href="{{ route('login') }}" class="text-blue-600 hover:underline">เข้าสู่ระบบ</a>
    </div>
  </div>

</body>
</html>
