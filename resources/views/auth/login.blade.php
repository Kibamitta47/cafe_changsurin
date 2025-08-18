<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>เข้าสู่ระบบ - น้องช้างสะเร็น</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f4fdf4;
      font-family: 'Sarabun', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-box {
      background: #ffffff;
      padding: 2.5rem;
      border-radius: 1rem;
      box-shadow: 0 0 25px rgba(0,0,0,0.07);
      text-align: center;
      width: 100%;
      max-width: 400px;
    }

    .login-box img {
      width: 100px;
      margin-bottom: 1rem;
    }

    .login-box h1 {
      font-size: 1.8rem;
      margin-bottom: 1.2rem;
      font-weight: 700;
      color: #3c3c3c;
    }

    .btn-line {
      background-color: #06C755;
      color: white;
      font-weight: bold;
      border-radius: 0.5rem;
      padding: 0.75rem 1.25rem;
      font-size: 1.1rem;
      transition: 0.3s;
    }

    .btn-line:hover {
      background-color: #04a344;
    }

    .footer-link {
      margin-top: 1.5rem;
      font-size: 0.95rem;
    }

    .footer-link a {
      color: #0d6efd;
      text-decoration: none;
    }

    .footer-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/LINE_logo.svg/512px-LINE_logo.svg.png" alt="LINE Logo">
    <h1>เข้าสู่ระบบด้วย LINE</h1>

    @if (session('error'))
      <div class="alert alert-danger py-2">
        {{ session('error') }}
      </div>
    @endif

    <a href="{{ route('line.login') }}" class="btn btn-line w-100">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-chat-left-dots me-2" viewBox="0 0 16 16">
        <path d="M14 1a1 1 0 0 1 1 1v11.586l-2.707-2.707A1 1 0 0 0 11 11H3a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h11zm-4 6.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
      </svg>
      เข้าสู่ระบบผ่าน LINE
    </a>

    <div class="footer-link mt-3">
      ยังไม่มีบัญชี? <a href="{{ route('register') }}">ลงทะเบียน</a>
    </div>
  </div>

</body>
</html>
