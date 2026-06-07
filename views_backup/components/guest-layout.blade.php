<!DOCTYPE html>
<html lang="es" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Khaleesitas | Sistema de Control</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
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
        }

        .fade-mask {
            mask-image: linear-gradient(to right, black 70%, transparent 95%);
            -webkit-mask-image: linear-gradient(to right, black 70%, transparent 95%);
        }

        .glass-panel {
            background: rgba(15, 17, 17, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(231, 192, 149, 0.2);
        }

        /* Transición de hover mejorada */
        .glass-panel:hover {
            border-color: rgba(231, 192, 149, 0.4);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.6);
        }

        /* Scrollbar con feedback visual */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        ::-webkit-scrollbar-thumb { 
            background: var(--primary-gold); 
            border-radius: 20px;
        }
    </style>
</head>

<body class="h-full font-sans selection:bg-amber-500/30">
    <div class="min-h-screen flex flex-col md:flex-row">
        
        <aside class="hidden md:flex w-1/2 relative overflow-hidden bg-black" aria-hidden="true">
            <img src="{{ asset('images/fondo-login.jpg') }}" 
                 alt="" 
                 loading="eager"
                 class="w-full h-full object-cover fade-mask opacity-80 scale-[1.02] transition-transform duration-[2s]">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
            
            <div class="absolute bottom-16 w-full text-center">
                <div class="animate-in fade-in slide-in-from-bottom-6 duration-1000">
                    <span class="text-[#e7c095] text-xs tracking-[0.4em] uppercase font-semibold opacity-90">
                        CERTIFICADO POR KHALEESI
                    </span>
                    <div class="h-px w-16 bg-[#e7c095]/40 mx-auto mt-4"></div>
                </div>
            </div>
        </aside>

        <main class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-12">
            <section class="w-full max-w-sm glass-panel rounded-3xl p-8 shadow-2xl animate-in zoom-in-95 duration-700">
                {{ $slot }}
            </section>
        </main>
    </div>
</body>
</html>