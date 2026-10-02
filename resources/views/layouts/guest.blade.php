<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SiswaMart') }} - Login</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-body: #FFF8E8;
            --bg-surface: #FFFCF5;
            --text-main: #241A14;
            --text-muted: #64748B;
            --border-color: #241A14;
            --primary-color: #FF7A00;
            --primary-hover: #E66A00;
            --focus-ring: rgba(255, 122, 0, 0.3);
            --bg-input: #FFF8E8;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .auth-card {
            background-color: var(--bg-surface);
            border: 2px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 4px 4px 0px 0px var(--border-color);
            transition: all 0.3s ease;
        }

        .form-input {
            width: 100%;
            background-color: var(--bg-input);
            border: 2px solid var(--border-color);
            color: var(--text-main);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            box-shadow: 2px 2px 0px 0px var(--border-color);
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            background-color: var(--bg-surface);
            box-shadow: 4px 4px 0px 0px var(--border-color);
            transform: translate(-2px, -2px);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: var(--border-color);
            font-weight: 800;
            font-size: 0.875rem;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            transition: all 0.2s ease;
            border: 2px solid var(--border-color);
            box-shadow: 4px 4px 0px 0px var(--border-color);
            cursor: pointer;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px var(--border-color);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col justify-center items-center p-4">

    <!-- Removed Theme Toggle -->

    <div class="w-full max-w-[420px]">
        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <a href="/" class="flex items-center justify-center text-4xl md:text-5xl font-black tracking-tight drop-shadow-sm transition-transform hover:scale-105">
                <span class="text-[var(--text-main)]">Siswa</span><span class="text-[#ea580c]">Mart</span>
            </a>
        </div>

        <!-- Card Container -->
        <div class="auth-card p-8 sm:p-10">
            {{ $slot }}
        </div>
        
        <!-- Footer info -->
        <p class="text-center text-xs text-[var(--text-muted)] mt-8">
            &copy; {{ date('Y') }} SiswaMart. Khusus untuk media promosi internal sekolah.
        </p>
    </div>

    <!-- Removed Dark Mode Script -->
</body>
</html>
