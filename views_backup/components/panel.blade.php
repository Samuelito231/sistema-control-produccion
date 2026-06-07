<!DOCTYPE html>
<html lang="es" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Khaleesitas | Sistema de Control</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-gold: #e7c095;
            --bg-dark: #0a0c0c;
        }

        body {
            background-color: var(--bg-dark);
            background-image: radial-gradient(circle at 20% 30%, #1a1d1d, var(--bg-dark));
            color: #d1d1d1;
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        ::-webkit-scrollbar-thumb { 
            background: var(--primary-gold); 
            border-radius: 20px;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="h-full font-sans selection:bg-amber-500/30">
    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 overflow-y-auto p-6">
            <div class="fade-in max-w-7xl mx-auto">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </main>
    </div>
</body>
</html>