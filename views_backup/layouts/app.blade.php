<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Khaleesitas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Hanken Grotesk', sans-serif; background: #121414; color: #e2e2e2; }
        [x-cloak] { display: none; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="app()">
    
    <x-sidebar />
    
    <div class="pl-[260px] min-h-screen">
        <main id="main-content">
            <div x-show="!content" x-cloak>
                {{ $slot }}
            </div>
            
            <div x-html="content"></div>
        </main>
    </div>

    <script>
        function app() {
            return {
                content: '',
                init() {
                    // Interceptar clics globales
                    window.addEventListener('click', e => {
                        const link = e.target.closest('a');
                        if (link && link.href && link.href.startsWith(window.location.origin) && !link.hasAttribute('download')) {
                            e.preventDefault();
                            this.load(link.href);
                        }
                    });
                },
                load(url) {
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        this.content = html; // Reemplaza contenido
                        history.pushState(null, '', url); // Actualiza URL
                    });
                }
            }
        }
    </script>
</body>
</html>