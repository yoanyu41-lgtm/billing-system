<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | CityTech Installment System</title>
    
    <!-- Modern Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --citytech-blue: #0f172a;
            --accent-cyan: #06b6d4;
            --primary-blue: #2563eb;
            /* កែពណ៌ Background ឱ្យទៅជា Soft Matte Gray (លែងឆ្លុះភ្នែក) */
            --bg-soft: #f1f5f9; 
        }

        body, html { 
            margin: 0; padding: 0; height: 100%; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-soft); overflow: hidden; 
        }

        .wrapper { display: flex; height: 100vh; width: 100vw; }

        /* Left Side: Tech Visual */
        .side-visual {
            flex: 1; background: var(--citytech-blue);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            position: relative; padding: 60px; color: white;
            /* បន្ថែមពន្លឺឱ្យតិចជាងមុន ដើម្បីកុំឱ្យចាំង */
            background-image: 
                radial-gradient(at 0% 0%, rgba(6, 182, 212, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.1) 0px, transparent 50%);
        }

        /* Modern Rounded Image Card */
        .image-card-wrapper {
            position: relative;
            width: 85%;
            max-width: 400px;
            margin-bottom: 40px;
            z-index: 5;
        }

        .modern-image-card {
            width: 100%;
            height: 460px;
            background: #1e293b;
            border-radius: 50px; 
            overflow: hidden;
            position: relative;
            z-index: 2;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }

        .modern-image-card img {
            width: 100%; height: 100%; object-fit: cover;
            transition: 0.6s ease;
        }

        /* បន្ថែម Overlay លើរូបភាពឱ្យងងឹតបន្តិច លែងឆ្លុះ */
        .modern-image-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 60%, rgba(15, 23, 42, 0.7));
            z-index: 3;
        }

        /* Right Side: Form Area (ប្តូរពណ៌ពី White មកជា Soft Gray) */
        .login-area {
            flex: 1.2; 
            background: var(--bg-soft); 
            display: flex; align-items: center; justify-content: center;
            padding: 60px; position: relative;
        }

        /* បង្កើត Shadow ស្រាលៗជុំវិញ Form ឱ្យឃើញដាច់ពី Background */
        .form-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        @media (max-width: 1024px) {
            .side-visual { display: none; }
            .login-area { flex: 1; padding: 30px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        {{ $slot }}
    </div>
</body>
</html>