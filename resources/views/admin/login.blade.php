<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

    body {
        background-color: #f4f6f8;
        font-family: 'Inter', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }

    .login-wrapper {
        background-color: #ffffff;
        padding: 40px 32px;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 400px;
    }

    h2 {
        font-size: 24px;
        font-weight: 600;
        color: #1a1a1a;
        text-align: center;
        margin-bottom: 24px;
    }

    label {
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        font-size: 14px;
        transition: border 0.2s;
    }

    .form-control:focus {
        border-color: #3b82f6;
        outline: none;
        background-color: #fff;
    }

    .btn-login {
        background-color: #3b82f6;
        color: #fff;
        border: none;
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 15px;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-top: 16px; /* ✅ เพิ่มช่องว่างด้านบน */
    }

    .btn-login:hover {
        background-color: #2563eb;
    }

    .error-message {
        color: #e11d48;
        font-size: 14px;
        text-align: center;
        margin-bottom: 15px;
    }

    .register-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #3b82f6;
        text-decoration: none;
    }

    .register-link:hover {
        text-decoration: underline;
    }
</style>

<div class="login-wrapper">
    <h2>เข้าสู่ระบบ Admin</h2>

    @if($errors->has('login_error'))
        <div class="error-message">{{ $errors->first('login_error') }}</div>
    @endif

    <form action="{{ url('/login-admin') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="UserName">ชื่อผู้ใช้</label>
            <input type="text" name="UserName" id="UserName" class="form-control" placeholder="Username" required>
        </div>

        <div class="mb-3">
            <label for="Password">รหัสผ่าน</label>
            <input type="password" name="Password" id="Password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" class="btn-login">เข้าสู่ระบบ</button>
    </form>

    <a href="{{ url('/register-admin') }}" class="register-link">ยังไม่มีบัญชี? สมัครสมาชิก</a>
</div>
