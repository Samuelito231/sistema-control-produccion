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
            background: radial-gradient(circle at 50% 0%, #171919 0%, #090a0a 100%);
            font-family: 'Inter', sans-serif;
        }
        
        /* Barra de desplazamiento orgánica invisible */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(231, 192, 149, 0.12); border-radius: 20px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(231, 192, 149, 0.35); }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        @keyframes fluidFlyIn {
            from {
                opacity: 0;
                transform: translateY(6px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        #panelNotificaciones {
            animation: fluidFlyIn 0.24s cubic-bezier(0.16, 1, 0.3, 1);
            transform-origin: top right;
        }
        
        #listaNotificacionesPanel::-webkit-scrollbar { width: 4px; }
        #listaNotificacionesPanel::-webkit-scrollbar-track { background: transparent; }
        #listaNotificacionesPanel::-webkit-scrollbar-thumb { background: rgba(231, 192, 149, 0.15); border-radius: 10px; }
        #listaNotificacionesPanel::-webkit-scrollbar-thumb:hover { background: rgba(231, 192, 149, 0.4); }
    </style>
</head>
<body class="text-[#e2e2e2] antialiased selection:bg-[#e7c095]/30">
    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 bg-black/[0.02] backdrop-blur-sm p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <!-- MÓDULO INTERACTIVO DE NOTIFICACIONES -->
    <div class="fixed top-5 right-5 z-50 select-none">
        <div class="relative flex flex-col items-end">
            
            <!-- Disparador: Cápsula de Cristal Líquido -->
            <button id="btnNotificacionesPanel" class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-[#111212]/90 backdrop-blur-xl border border-white/10 hover:border-[#e7c095]/30 text-gray-400 hover:text-[#e7c095] shadow-[0_4px_24px_rgba(0,0,0,0.6),inset_0_1px_1px_rgba(255,255,255,0.06)] transition-all duration-300 group focus:outline-none">
                <span class="material-symbols-outlined text-[22px] transition-transform duration-300 group-hover:scale-105">notifications</span>
                
                @php
                    $noLeidas = \App\Models\Alerta::where('usuario_id', auth()->id())->where('leida', false)->count();
                @endphp
                @if($noLeidas > 0)
                    <!-- Indicador de Gas Neon Físico -->
                    <span id="contadorNotificaciones" class="absolute -top-1 -right-1 flex h-4.5 min-w-4.5 px-1 items-center justify-center rounded-full bg-gradient-to-b from-rose-500 to-red-600 text-[9px] font-bold text-white border border-[#090a0a] shadow-[0_0_14px_rgba(225,29,72,0.6)]">
                        {{ $noLeidas > 9 ? '9+' : $noLeidas }}
                    </span>
                @endif
            </button>
            
            <!-- Centro Flotante de Alertas (Monolito Aeroespacial) -->
            <div id="panelNotificaciones" class="hidden absolute right-0 top-14 w-[380px] bg-[#090a0a]/95 backdrop-blur-2xl border border-white/[0.08] rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.9),inset_0_1px_0px_rgba(255,255,255,0.05)] z-50 overflow-hidden">
                
                <!-- Encabezado con Calibración Tipográfica -->
                <div class="px-4 py-3.5 bg-white/[0.01] border-b border-white/[0.05] flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#e7c095] shadow-[0_0_8px_#e7c095]"></span>
                        <h3 class="text-[11px] font-bold tracking-[0.14em] text-white uppercase opacity-90">Centro de Alertas</h3>
                        <span id="contadorPanel" class="text-[10px] bg-white/[0.04] border border-white/[0.06] text-gray-300 px-2 py-0.5 rounded-md font-medium">
                            {{ $noLeidas }} activas
                        </span>
                    </div>
                    <button onclick="marcarTodasNotificaciones()" class="text-[11px] font-semibold text-[#e7c095]/80 hover:text-[#e7c095] transition-all duration-200 flex items-center gap-1 hover:underline">
                        <span class="material-symbols-outlined text-xs">done_all</span>
                        Limpiar panel
                    </button>
                </div>
                
                <!-- Feed de Tarjetas Orgánicas (Sin bordes duros) -->
                <div id="listaNotificacionesPanel" class="max-h-[380px] overflow-y-auto p-2 space-y-1.5 bg-gradient-to-b from-transparent to-white/[0.01]">
                    @php
                        $alertas = \App\Models\Alerta::where('usuario_id', auth()->id())
                                    ->where('leida', false)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(20)
                                    ->get();
                    @endphp
                    @forelse($alertas as $alerta)
                    <div class="p-3 rounded-xl bg-gradient-to-br from-white/[0.03] to-white/[0.01] border border-white/[0.06] shadow-[0_4px_12px_rgba(0,0,0,0.2)] hover:border-white/[0.12] transition-all duration-200 cursor-pointer relative overflow-hidden group/item" 
                         data-notificacion-id="{{ $alerta->id }}" 
                         onclick="marcarNotificacion({{ $alerta->id }})">
                        
                        <!-- Filete de Severidad Asimétrico -->
                        <div class="absolute left-0 top-0 bottom-0 w-[3px] rounded-r-sm
                            {{ $alerta->nivel == 'danger' ? 'bg-gradient-to-b from-red-500 to-rose-600' : ($alerta->nivel == 'warning' ? 'bg-gradient-to-b from-amber-400 to-orange-500' : 'bg-gradient-to-b from-blue-500 to-indigo-600') }}">
                        </div>

                        <div class="flex items-start gap-3 pl-1">
                            <!-- Contenedor Icono Micro-UI -->
                            <div class="p-1.5 rounded-lg shrink-0 mt-0.5
                                {{ $alerta->nivel == 'danger' ? 'bg-red-500/10 text-red-400' : ($alerta->nivel == 'warning' ? 'bg-amber-400/10 text-amber-400' : 'bg-blue-500/10 text-blue-400') }}">
                                <span class="material-symbols-outlined text-sm block font-light">
                                    {{ $alerta->nivel == 'danger' ? 'error' : ($alerta->nivel == 'warning' ? 'warning' : 'info') }}
                                </span>
                            </div>

                            <!-- Contenido Dinámico -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-2">
                                    <p class="text-xs font-bold text-gray-100 truncate tracking-tight transition-colors group-hover/item:text-[#e7c095]">
                                        {{ $alerta->titulo }}
                                    </p>
                                    <span class="text-[9px] text-gray-500 font-medium whitespace-nowrap tracking-tighter">
                                        {{ $alerta->created_at->diffForHumans(null, true) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-relaxed mt-1 break-words line-clamp-2">
                                    {{ $alerta->mensaje }}
                                </p>
                                
                                <div class="mt-1.5 h-3 overflow-hidden">
                                    <span class="text-[9px] font-semibold text-[#e7c095] opacity-0 translate-y-2 group-hover/item:opacity-100 group-hover/item:translate-y-0 transition-all duration-200 inline-flex items-center gap-0.5">
                                        Archivar registro <span class="material-symbols-outlined text-[10px]">arrow_right_alt</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <!-- Estado Vacío Desautomatizado -->
                    <div id="mensajeVacioPanel" class="py-14 text-center text-gray-500 flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-full bg-white/[0.02] flex items-center justify-center border border-white/5 shadow-inner mb-3">
                            <span class="material-symbols-outlined text-xl text-gray-600">notifications_off</span>
                        </div>
                        <p class="text-xs font-bold tracking-wide text-gray-400">Canal libre de alertas</p>
                        <p class="text-[10px] text-gray-600 mt-1 px-8 leading-normal">Los eventos críticos y operacionales del sistema se desplegarán en esta zona.</p>
                    </div>
                    @endforelse
                </div>
                
                <!-- Pie de Módulo Metálico -->
                <div class="p-2.5 bg-white/[0.01] border-t border-white/[0.05] text-center">
                    <a href="{{ route('notificaciones.index') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-gray-400 hover:text-[#e7c095] py-0.5 transition-all group/link">
                        Ver logs de auditoría por completo
                        <span class="material-symbols-outlined text-[11px] transition-transform group-hover/link:translate-x-0.5">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTROLADOR DE EVENTOS INTERACTIVOS (BLINDADO) -->
    <script>
        const btnNotificaciones = document.getElementById('btnNotificacionesPanel');
        const panelNotificaciones = document.getElementById('panelNotificaciones');
        const listaNotificaciones = document.getElementById('listaNotificacionesPanel');
        const contadorPanel = document.getElementById('contadorPanel');
        const contadorNotificaciones = document.getElementById('contadorNotificaciones');
        let closeTimeout;
        
        if (btnNotificaciones) {
            btnNotificaciones.addEventListener('click', function(e) {
                e.stopPropagation();
                panelNotificaciones?.classList.toggle('hidden');
                btnNotificaciones.classList.toggle('border-[#e7c095]/30');
                if (closeTimeout) clearTimeout(closeTimeout);
            });
        }
        
        if (panelNotificaciones) {
            panelNotificaciones.addEventListener('mouseenter', () => {
                if (closeTimeout) clearTimeout(closeTimeout);
            });
            
            panelNotificaciones.addEventListener('mouseleave', () => {
                closeTimeout = setTimeout(() => {
                    panelNotificaciones.classList.add('hidden');
                    btnNotificaciones?.classList.remove('border-[#e7c095]/30');
                }, 400); // Retraso ergonómico para evitar cierres accidentales
            });
        }
        
        document.addEventListener('click', function(event) {
            if (panelNotificaciones && !panelNotificaciones.classList.contains('hidden') && 
                !panelNotificaciones.contains(event.target) && !btnNotificaciones?.contains(event.target)) {
                panelNotificaciones.classList.add('hidden');
                btnNotificaciones?.classList.remove('border-[#e7c095]/30');
            }
        });
        
        // MARCAR UNA NOTIFICACIÓN (Persistencia Blindada)
        function marcarNotificacion(id) {
            const notificacionDiv = document.querySelector(`[data-notificacion-id="${id}"]`);
            
            fetch(`/notificaciones/marcar/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Error de sincronización con la BD.');
                return response.json();
            })
            .then(data => {
                // SÓLO si el servidor guardó el cambio con éxito se altera la UI
                if (notificacionDiv) {
                    notificacionDiv.style.opacity = '0';
                    notificacionDiv.style.transform = 'translateX(12px)';
                    setTimeout(() => {
                        notificacionDiv.remove();
                        actualizarContadores(data.count !== undefined ? data.count : null);
                        verificarEstadoVacio();
                    }, 220);
                }
            })
            .catch(err => console.error('Fallo crítico en guardado:', err));
        }
        
        // MARCAR TODAS (Persistencia Blindada)
        function marcarTodasNotificaciones() {
            fetch(`/notificaciones/marcar-todas`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Error de sincronización masiva.');
                return response.json();
            })
            .then(() => {
                if (listaNotificaciones) {
                    listaNotificaciones.innerHTML = `
                        <div class="py-14 text-center text-gray-500 flex flex-col items-center justify-center animate-[fluidFlyIn_0.2s_ease]">
                            <div class="w-12 h-12 rounded-full bg-white/[0.02] flex items-center justify-center border border-white/5 shadow-inner mb-3">
                                <span class="material-symbols-outlined text-xl text-gray-600">notifications_off</span>
                            </div>
                            <p class="text-xs font-bold tracking-wide text-gray-400">Canal libre de alertas</p>
                            <p class="text-[10px] text-gray-600 mt-1 px-8 leading-normal">Todas las alertas han sido purgadas correctamente de la base de datos.</p>
                        </div>
                    `;
                }
                actualizarContadores(0);
            })
            .catch(err => console.error('Fallo crítico en purga masiva:', err));
        }
        
        function verificarEstadoVacio() {
            if (listaNotificaciones && listaNotificaciones.querySelectorAll('[data-notificacion-id]').length === 0) {
                marcarTodasNotificaciones();
            }
        }
        
        function actualizarContadores(nuevoValor = null) {
            if (nuevoValor !== null) {
                if (contadorPanel) contadorPanel.textContent = `${nuevoValor} activas`;
                if (contadorNotificaciones) {
                    if (nuevoValor > 0) {
                        contadorNotificaciones.textContent = nuevoValor > 9 ? '9+' : nuevoValor;
                        contadorNotificaciones.style.display = 'flex';
                    } else {
                        contadorNotificaciones.style.display = 'none';
                    }
                }
            } else {
                fetch('/notificaciones/count', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                  .then(data => {
                      actualizarContadores(data.count);
                  });
            }
        }
    </script>
</body>
</html>