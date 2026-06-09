<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | CityTech Installment System</title>
    
    <!-- Modern Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Khmer OS Siemreap (Khmer + English) -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    
    <style>
        :root {
            --citytech-blue: #0f172a;
            --accent-cyan: #06b6d4;
            --primary-blue: #2563eb;
            --bg-soft: #f8fafc; 
        }

        body, html { 
            margin: 0; padding: 0; min-height: 100%; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
            overflow-x: hidden;
            overflow-y: auto;
        }

        .wrapper { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            width: 100%; 
            padding: 40px 20px;
            box-sizing: border-box;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 36px 36px 44px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.05), 0 0 0 1px rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        /* Language switcher — top right of card, doesn't affect logo centering */
        .lang-switcher {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .lang-switcher-pills {
            display: inline-flex;
            background: #f1f5f9;
            border-radius: 9999px;
            padding: 3px;
            gap: 2px;
        }
        .lang-switcher-pills a {
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 9999px;
            color: #64748b;
        }
        .lang-switcher-pills a.active {
            background: #2563eb;
            color: #fff;
        }

        /* Common Form Elements */
        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            outline: none;
            transition: all 0.2s ease;
            background: #f8fafc;
            box-sizing: border-box;
            color: #1e293b;
        }

        .form-input:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.1), 0 2px 4px -1px rgba(15, 23, 42, 0.06);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #0f172a, #020617);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.1), 0 4px 6px -2px rgba(15, 23, 42, 0.05);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .auth-logo-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .auth-logo-icon {
            width: 40px;
            height: 40px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
            color: white;
            font-size: 18px;
        }

        .auth-logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .auth-logo-text {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        {{ $slot }}
    </div>
</body>
</html>