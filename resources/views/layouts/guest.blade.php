<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | CityTech Installment System</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Brand Tokens -->
    @include('partials.brand')
    
    <style>
        body, html { 
            margin: 0; padding: 0; min-height: 100%; 
            background: var(--surface-muted);
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
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 40px 40px 48px;
            width: 100%;
            max-width: 460px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            box-sizing: border-box;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg), 0 20px 40px -15px rgba(15, 23, 42, 0.12);
        }

        /* Language switcher — top right of card */
        .lang-switcher {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }
        .lang-switcher-pills {
            display: inline-flex;
            background: var(--border);
            border-radius: var(--radius-pill);
            padding: 3px;
            gap: 2px;
        }
        .lang-switcher-pills a {
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            color: var(--text-muted);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .lang-switcher-pills a:hover:not(.active) {
            color: var(--text);
            background: rgba(255, 255, 255, 0.4);
        }
        .lang-switcher-pills a.active {
            background: var(--brand);
            color: var(--on-brand);
            box-shadow: var(--shadow-sm);
        }

        /* Common Form Elements */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: var(--text);
            margin-bottom: 8px;
            transition: color 0.2s ease;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-subtle);
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 15px;
            outline: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--surface-muted);
            box-sizing: border-box;
            color: var(--text);
        }

        .form-input:hover {
            border-color: var(--border-strong);
        }

        .form-input:focus {
            border-color: var(--brand);
            background: var(--surface);
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.15);
        }

        .form-input-wrapper:focus-within .form-input-icon {
            color: var(--brand);
            transform: translateY(-50%) scale(1.05);
        }

        .btn-submit {
            width: 100%;
            background: var(--brand);
            color: var(--on-brand);
            padding: 16px;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: var(--shadow-brand);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--brand-hover);
            transform: translateY(-1.5px);
            box-shadow: 0 10px 20px -8px rgba(30, 58, 95, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
            background: var(--brand-active);
        }

        .auth-logo-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .auth-logo-icon {
            width: 42px;
            height: 42px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }
        
        .auth-logo-header:hover .auth-logo-icon {
            transform: rotate(-6deg) scale(1.05);
            border-color: var(--border-strong);
            box-shadow: var(--shadow);
        }

        .auth-logo-icon img {
            width: 26px;
            height: 26px;
            object-fit: contain;
            display: block;
        }

        .auth-logo-text {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        {{ $slot }}
    </div>
</body>
</html>