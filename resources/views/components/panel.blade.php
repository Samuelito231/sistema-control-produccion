<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Khaleesitas - Sistema de Control</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0,1" />
    
    <style>
        body {
            background: radial-gradient(circle at 10% 20%, #1a1c1c, #0f1111);
            font-family: 'Inter', sans-serif;
        }
        
        /* Scrollbar personalizada */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        ::-webkit-scrollbar-thumb { background: #e7c095; border-radius: 20px; }
    </style>
</head>
<body class="text-[#e2e2e2] antialiased">
    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 bg-black/10 backdrop-blur-sm p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</body>
</html>