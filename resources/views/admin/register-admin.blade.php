<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>เพิ่มบัญชี Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e9f0f7;
            color: #4a3c31;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            border: 2px solid #7b5e57;
            border-radius: 8px;
            padding: 30px 40px;
            width: 360px;
            box-shadow: 0 4px 12px rgba(123, 94, 87, 0.4);
            text-align: center;
        }
        h2 {
            color: #3a5a99;
            margin-bottom: 25px;
            font-weight: 700;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1.8px solid #7b5e57;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #3a5a99;
            outline: none;
            box-shadow: 0 0 6px #3a5a99;
        }
        button {
            background-color: #3a5a99;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2a4170;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            color: #7b5e57;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #3a5a99;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>เพิ่มบัญชี Admin</h2>
        <form action="{{ url('/register-admin') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">เพิ่มบัญชี</button>
        </form>
        <a href="{{ url('/login-admin') }}">เข้าสู่ระบบ</a>
    </div>
</body>
</html>
