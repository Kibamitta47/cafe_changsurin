<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    <style>
        /* โทนสีฟ้าน้ำตาล */
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
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #3a5a99;
            font-weight: 700;
        }
        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .alert-success {
            background-color: #d6eadf;
            color: #3a5a99;
            border: 1px solid #a1c9b8;
        }
        .alert-danger {
            background-color: #fbeaea;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 6px;
            font-weight: 600;
            color: #5a4431;
        }
        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1.8px solid #7b5e57;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
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
        p {
            margin-top: 15px;
            text-align: center;
            font-size: 0.9rem;
        }
        p a {
            color: #7b5e57;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        p a:hover {
            color: #3a5a99;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.admin.post') }}">
            @csrf

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">เข้าสู่ระบบ</button>
        </form>

        <p><a href="{{ route('register.admin') }}">เพิ่มบัญชี Admin</a></p>
    </div>
</body>
</html>
