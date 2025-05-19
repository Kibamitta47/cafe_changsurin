<h2>เพิ่มสมาชิก Admin</h2>
<form action="{{ url('/register-admin') }}" method="POST">
    @csrf
    <input type="text" name="UserName" placeholder="Username" required><br>
    <input type="email" name="Email" placeholder="Email" required><br>
    <input type="password" name="Password" placeholder="Password" required><br>
    <button type="submit">สมัคร</button>
</form>
<a href="{{ url('/login-admin') }}">เข้าสู่ระบบ</a>
