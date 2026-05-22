<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>เข้าสู่ระบบ - ระบบขนย้ายมูลไก่</title>

    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #123014 0%, #1e4620 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: #ffffff;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .login-card .logo-container {
            margin: 0 auto 1.5rem;
            max-width: 280px;
        }

        .login-card .logo-container img {
            width: 100%;
            height: auto;
            display: block;
            background-color: rgba(255, 255, 255, 0.96);
            border-radius: 16px;
            padding: 10px 14px;
        }

        .login-card h3 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .login-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            display: block;
            text-align: left;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.25);
            border-color: #81c784;
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(129, 199, 132, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-login {
            background-color: #81c784;
            border: none;
            color: #123014;
            font-weight: 600;
            border-radius: 12px;
            padding: 12px;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #66bb6a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 187, 106, 0.4);
            color: #123014;
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }

        .form-check-input {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-check-input:checked {
            background-color: #81c784;
            border-color: #81c784;
        }

        .invalid-feedback {
            display: block;
            text-align: left;
            color: #ff8a80;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-container">
            <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM Logo">
        </div>
        
        <h3>ระบบจัดการมูลไก่</h3>
        <p>บันทึกการขนย้ายมูลไก่ออกจากฟาร์มและรับเข้ากอง</p>

        {{ $slot }}
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
