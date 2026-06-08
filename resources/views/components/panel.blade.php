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
            background: radial-gradient(circle at 50% 0%, #1e2020 0%, #0c0d0d 100%);
            font-family: 'Inter', sans-serif;
        }
        
        /* Scrollbar sutil de diseño premium */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(231, 192, 149, 0.2); border-radius: 20px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(231, 192, 149, 0.5); }
    </style>
</head>
<body class="text-[#e2e2e2] antialiased selection:bg-[#e7c095]/30">
    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 bg-black/5 backdrop-blur-sm p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <!-- PANEL DE NOTIFICACIONES FLOTANTE (ARQUITECTURA DE CAPAS AVANZADA) -->
    <div class="fixed top-5 right-5 z-50 select-none">
        <div class="relative flex flex-col items-end">
            
            <!-- Botón de Activación: Estilo Cápsula de Cristal -->
            <button id="btnNotificaciones" class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-[#141515]/80 backdrop-blur-xl border border-white/10 hover:border-[#e7c095]/40 text-gray-400 hover:text-[#e7c095] shadow-[0_4px_20px_rgba(0,0,0,0.4)] transition-all duration-300 group focus:outline-none">
                <span class="material-symbols-outlined text-[22px] transition-transform duration-300 group-hover:scale-110">notifications</span>
                
                @php
                    $noLeidas = \App\Models\Alerta::where('usuario_id', auth()->id())->where('leida', false)->count();
                @endphp
                @if($noLeidas > 0)
                    <!-- Indicador Orgánico Pulsante -->
                    <span class="absolute -top-1 -right-1 flex h-4.5 min-w-4.5 px-1 items-center justify-center rounded-full bg-gradient-to-r from-red-500 to-rose-600 text-[9px] text-white font-bold border border-[#0c0d0d] shadow-[0_0_12px_rgba(244,63,94,0.5)] animate-[pulse_2s_infinite]">
                        {{ $noLeidas > 9 ? '9+' : $noLeidas }}
                    </span>
                @endif
            </button>
            
            <!-- Contenedor del Centro de Notificaciones -->
            <div id="panelNotificaciones" class="hidden absolute right-0 top-14 w-[360px] bg-[#0c0d0d]/95 backdrop-blur-2xl border border-white/[0.08] rounded-2xl shadow-[0_24px_60px_rgba(0,0,0,0.8),inset_0_1px_1px_rgba(255,255,255,0.05)] z-50 overflow-hidden transition-all duration-300">
                
                <!-- Encabezado Estilizado -->
                <div class="p-4 bg-white/[0.02] border-b border-white/[0.06] flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#e7c095] shadow-[0_0_8px_#e7c095]"></span>
                        <h3 class="text-xs font-bold tracking-[0.1em] text-white uppercase opacity-90">Módulo de Alertas</h3>
                    </div>
                    <button onclick="marcarTodasNotificaciones()" class="text-[11px] text-[#e7c095]/80 hover:text-[#e7c095] font-medium transition hover:underline">
                        Limpiar panel
                    </button>
                </div>
                
                <!-- Feed de Alertas sin estructura cuadriculada estándar -->
                <div class="max-h-[420px] overflow-y-auto p-3 space-y-2">
                    @php
                        $alertas = \App\Models\Alerta::where('usuario_id', auth()->id())
                                    ->orderBy('created_at', 'desc')
                                    ->limit(15)
                                    ->get();
                    @endphp
                    @forelse($alertas as $alerta)
                    <div class="group/item p-3.5 rounded-xl border transition-all duration-200 relative overflow-hidden
                        {{ !$alerta->leida 
                            ? 'bg-gradient-to-br from-white/[0.04] to-white/[0.01] border-white/[0.08] shadow-[0_4px_12px_rgba(0,0,0,0.2)]' 
                            : 'bg-transparent border-transparent opacity-60 hover:opacity-90' }}">
                        
                        <!-- Barra Lateral Estética según severidad (Reemplaza las líneas aburridas) -->
                        <div class="absolute left-0 top-0 bottom-0 w-[3px] rounded-r-sm
                            {{ $alerta->nivel == 'danger' ? 'bg-gradient-to-b from-red-500 to-rose-600' : ($alerta->nivel == 'warning' ? 'bg-gradient-to-b from-amber-400 to-orange-500' : 'bg-gradient-to-b from-blue-500 to-indigo-600') }}">
                        </div>

                        <div class="flex items-start gap-3">
                            <!-- Icono Integrado Moderno -->
                            <div class="p-1.5 rounded-lg shrink-0 mt-0.5
                                {{ $alerta->nivel == 'danger' ? 'bg-red-500/10 text-red-400' : ($alerta->nivel == 'warning' ? 'bg-amber-400/10 text-amber-400' : 'bg-blue-500/10 text-blue-400') }}">
                                <span class="material-symbols-outlined text-base block font-light">
                                    {{ $alerta->nivel == 'danger' ? 'error' : ($alerta->nivel == 'warning' ? 'warning' : 'info') }}
                                </span>
                            </div>

                            <!-- Cuerpo de la Notificación -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-2">
                                    <h4 class="text-xs font-bold text-gray-100 truncate tracking-tight transition-colors group-hover/item:text-[#e7c095]">
                                        {{ $alerta->titulo }}
                                    </h4>
                                    <span class="text-[9px] text-gray-500 font-medium whitespace-nowrap shrink-0 tracking-tighter">
                                        {{ $alerta->created_at->diffForHumans(null, true) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-normal font-normal mt-1 break-words">
                                    {{ Str::limit($alerta->mensaje, 75) }}
                                </p>
                            </div>
                            
                            <!-- Acción Interactiva Tipo Micro-UI -->
                            @if(!$alerta->leida)
                            <button onclick="marcarNotificacion({{ $alerta->id }})" class="opacity-0 group-hover/item:opacity-100 flex items-center justify-center w-6 h-6 rounded-md bg-[#e7c095]/10 hover:bg-[#e7c095] text-[#e7c095] hover:text-black border border-[#e7c095]/20 hover:border-transparent text-[10px] font-black transition-all duration-200 shadow-sm shrink-0" title="Archivar Alerta">
                                <span class="material-symbols-outlined text-xs">done</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <!-- Estado de Bandeja Vacía Minimalista -->
                    <div class="py-14 text-center text-gray-500 flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-full bg-white/[0.02] flex items-center justify-center border border-white/5 shadow-inner mb-3">
                            <span class="material-symbols-outlined text-xl text-gray-600">notifications_off</span>
                        </div>
                        <p class="text-xs font-semibold tracking-wide text-gray-400">Canal libre de alertas</p>
                        <p class="text-[10px] text-gray-600 mt-0.5 px-6">Todo marcha de acuerdo a los parámetros establecidos.</p>
                    </div>
                    @endforelse
                </div>
                
                <!-- Footer Integrado -->
                <div class="p-2.5 bg-white/[0.01] border-t border-white/[0.06] text-center">
                    <a href="{{ route('notificaciones.index') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-gray-400 hover:text-[#e7c095] py-1 transition-colors group/link">
                        Historial completo
                        <span class="material-symbols-outlined text-[10px] transition-transform group-hover/link:translate-x-0.5">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTROLADORES JAVASCRIPT: Limpieza y Despliegues Fluidos -->
    <script>
        const btnNotificaciones = document.getElementById('btnNotificaciones');
        const panelNotificaciones = document.getElementById('panelNotificaciones');

        // Toggle Dinámico con detención de propagación de eventos
        btnNotificaciones?.addEventListener('click', function(e) {
            e.stopPropagation();
            panelNotificaciones?.classList.toggle('hidden');
            btnNotificaciones.classList.toggle('border-[#e7c095]/40');
        });
        
        // Cerrar al clickear fuera de la zona interactiva
        document.addEventListener('click', function(event) {
            if (panelNotificaciones && !panelNotificaciones.classList.contains('hidden') && 
                !panelNotificaciones.contains(event.target) && !btnNotificaciones?.contains(event.target)) {
                panelNotificaciones.classList.add('hidden');
                btnNotificaciones?.classList.remove('border-[#e7c095]/40');
            }
        });
        
        // Consultas asíncronas optimizadas para el Backend Laravel
        function marcarNotificacion(id) {
            fetch(`/notificaciones/marcar/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => location.reload());
        }
        
        function marcarTodasNotificaciones() {
            fetch(`/notificaciones/marcar-todas`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => location.reload());
        }
    </script>
</body>
</html>