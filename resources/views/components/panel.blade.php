<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Khaleesitas - Sistema de Control</title>

    {{-- Preconexiones a Google Fonts (reduce tiempo de petición externa) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Vite (assets locales) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fuente Inter (carga no bloqueante) --}}
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" as="style" onload="this.rel='stylesheet'">

    {{-- Fuente Material Symbols (carga no bloqueante) --}}
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0,1" as="style" onload="this.rel='stylesheet'">

    {{-- Fallback para navegadores sin JavaScript --}}
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0,1">
    </noscript>

    <style>
        body {
            background: radial-gradient(circle at 10% 20%, #1a1c1c, #0f1111);
            font-family: 'Inter', sans-serif;
        }

        /* Scrollbar personalizada */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        ::-webkit-scrollbar-thumb { background: #e7c095; border-radius: 20px; }

        /* Ocultar íconos hasta que la fuente cargue (evita el parpadeo de texto raro) */
        .material-symbols-outlined {
            visibility: hidden;
        }
        .fonts-loaded .material-symbols-outlined {
            visibility: visible;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        #panelNotificaciones {
            animation: fadeInScale 0.2s ease-out;
            transform-origin: top right;
        }

        #listaNotificacionesPanel::-webkit-scrollbar {
            width: 4px;
        }

        #listaNotificacionesPanel::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        #listaNotificacionesPanel::-webkit-scrollbar-thumb {
            background: rgba(231, 192, 149, 0.3);
            border-radius: 10px;
        }

        #listaNotificacionesPanel::-webkit-scrollbar-thumb:hover {
            background: rgba(231, 192, 149, 0.6);
        }
    </style>
</head>
<body class="text-[#e2e2e2] antialiased">
    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 bg-black/10 backdrop-blur-sm p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <!-- Panel de Notificaciones Flotante -->
    <div class="fixed top-4 right-4 z-50">
        <div class="relative">
            <button id="btnNotificacionesPanel" class="relative p-2 rounded-xl bg-black/50 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all duration-200 group">
                <span class="material-symbols-outlined text-xl text-gray-400 group-hover:text-[#e7c095] transition-colors">notifications</span>
                @php
                    $noLeidas = \App\Models\Alerta::where('usuario_id', auth()->id())->where('leida', false)->count();
                @endphp
                @if($noLeidas > 0)
                    <span id="contadorNotificaciones" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 rounded-full text-[9px] font-bold text-white flex items-center justify-center px-1 shadow-lg ring-2 ring-black/20">
                        {{ $noLeidas > 9 ? '9+' : $noLeidas }}
                    </span>
                @endif
            </button>
            
            <div id="panelNotificaciones" class="hidden absolute right-0 mt-2 w-96 bg-gray-900/95 backdrop-blur-md border border-white/10 rounded-2xl shadow-2xl z-50 overflow-hidden">
                <!-- Header -->
                <div class="px-4 py-3 border-b border-white/10 bg-gradient-to-r from-gray-900 to-gray-800/50">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#e7c095] text-xl">notifications</span>
                            <h3 class="font-bold text-white">Notificaciones</h3>
                            <span id="contadorPanel" class="text-xs bg-[#e7c095]/20 text-[#e7c095] px-2 py-0.5 rounded-full">
                                {{ $noLeidas }} nuevas
                            </span>
                        </div>
                        <button onclick="marcarTodasNotificaciones()" class="text-xs text-gray-400 hover:text-[#e7c095] transition-all duration-200 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">done_all</span>
                            Marcar todas
                        </button>
                    </div>
                </div>
                
                <!-- Lista de notificaciones - SOLO NO LEÍDAS -->
                <div id="listaNotificacionesPanel" class="max-h-96 overflow-y-auto divide-y divide-white/5">
                    @php
                        $alertas = \App\Models\Alerta::where('usuario_id', auth()->id())
                                    ->where('leida', false)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(20)
                                    ->get();
                    @endphp
                    @forelse($alertas as $alerta)
                    <div class="px-4 py-3 hover:bg-white/5 transition-all duration-200 cursor-pointer group bg-[#e7c095]/5 border-l-4 border-l-[#e7c095]" 
                         data-notificacion-id="{{ $alerta->id }}" 
                         onclick="marcarNotificacion({{ $alerta->id }})">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                    {{ $alerta->nivel == 'danger' ? 'bg-red-500/20' : ($alerta->nivel == 'warning' ? 'bg-yellow-500/20' : 'bg-blue-500/20') }}">
                                    <span class="material-symbols-outlined text-sm
                                        {{ $alerta->nivel == 'danger' ? 'text-red-400' : ($alerta->nivel == 'warning' ? 'text-yellow-400' : 'text-blue-400') }}">
                                        {{ $alerta->nivel == 'danger' ? 'error' : ($alerta->nivel == 'warning' ? 'warning' : 'info') }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-semibold text-white truncate">{{ $alerta->titulo }}</p>
                                    <span class="text-[10px] text-gray-500 flex-shrink-0">{{ $alerta->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $alerta->mensaje }}</p>
                                <div class="mt-2">
                                    <span class="text-[10px] text-[#e7c095] opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        Haz clic para marcar como leída
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div id="mensajeVacioPanel" class="px-4 py-12 text-center">
                        <span class="material-symbols-outlined text-5xl text-gray-600">notifications_none</span>
                        <p class="text-sm text-gray-500 mt-2">No hay notificaciones nuevas</p>
                        <p class="text-xs text-gray-600 mt-1">Las alertas aparecerán aquí</p>
                    </div>
                    @endforelse
                </div>
                
                <!-- Footer -->
                <div class="px-4 py-2 border-t border-white/10 bg-gray-900/50">
                    <a href="{{ route('notificaciones.index') }}" class="flex items-center justify-between text-xs text-gray-400 hover:text-[#e7c095] transition-colors">
                        <span>Ver historial completo</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables
        const btnNotificaciones = document.getElementById('btnNotificacionesPanel');
        const panelNotificaciones = document.getElementById('panelNotificaciones');
        const listaNotificaciones = document.getElementById('listaNotificacionesPanel');
        const contadorPanel = document.getElementById('contadorPanel');
        const contadorNotificaciones = document.getElementById('contadorNotificaciones');
        let closeTimeout;
        
        // Toggle panel
        if (btnNotificaciones) {
            btnNotificaciones.addEventListener('click', function(e) {
                e.stopPropagation();
                panelNotificaciones?.classList.toggle('hidden');
                if (closeTimeout) clearTimeout(closeTimeout);
            });
        }
        
        // Mantener abierto al hacer hover
        if (panelNotificaciones) {
            panelNotificaciones.addEventListener('mouseenter', () => {
                if (closeTimeout) clearTimeout(closeTimeout);
            });
            
            panelNotificaciones.addEventListener('mouseleave', () => {
                closeTimeout = setTimeout(() => {
                    panelNotificaciones.classList.add('hidden');
                }, 300);
            });
        }
        
        // Cerrar al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (panelNotificaciones && !panelNotificaciones.classList.contains('hidden') && 
                !panelNotificaciones.contains(event.target) && 
                !btnNotificaciones?.contains(event.target)) {
                closeTimeout = setTimeout(() => {
                    panelNotificaciones.classList.add('hidden');
                }, 100);
            }
        });
        
        // Marcar una notificación (sin recargar)
        function marcarNotificacion(id) {
            const notificacionDiv = document.querySelector(`[data-notificacion-id="${id}"]`);
            
            fetch(`/notificaciones/marcar/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
              .then(() => {
                  if (notificacionDiv) {
                      notificacionDiv.remove();
                  }
                  actualizarContadores();
                  if (listaNotificaciones && listaNotificaciones.children.length === 0) {
                      listaNotificaciones.innerHTML = `
                          <div class="px-4 py-12 text-center">
                              <span class="material-symbols-outlined text-5xl text-gray-600">notifications_none</span>
                              <p class="text-sm text-gray-500 mt-2">No hay notificaciones nuevas</p>
                              <p class="text-xs text-gray-600 mt-1">Las alertas aparecerán aquí</p>
                          </div>
                      `;
                  }
              });
        }
        
        // Marcar todas (sin recargar)
        function marcarTodasNotificaciones() {
            fetch(`/notificaciones/marcar-todas`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
              .then(() => {
                  if (listaNotificaciones) {
                      listaNotificaciones.innerHTML = `
                          <div class="px-4 py-12 text-center">
                              <span class="material-symbols-outlined text-5xl text-gray-600">notifications_none</span>
                              <p class="text-sm text-gray-500 mt-2">No hay notificaciones nuevas</p>
                              <p class="text-xs text-gray-600 mt-1">Todas las notificaciones han sido leídas</p>
                          </div>
                      `;
                  }
                  actualizarContadores(0);
              });
        }
        
        // Actualizar contadores
        function actualizarContadores(nuevoValor = null) {
            if (nuevoValor !== null) {
                const count = nuevoValor;
                if (contadorPanel) contadorPanel.textContent = `${count} nuevas`;
                if (contadorNotificaciones) {
                    if (count > 0) {
                        contadorNotificaciones.textContent = count > 9 ? '9+' : count;
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
                      const count = data.count;
                      if (contadorPanel) contadorPanel.textContent = `${count} nuevas`;
                      if (contadorNotificaciones) {
                          if (count > 0) {
                              contadorNotificaciones.textContent = count > 9 ? '9+' : count;
                              contadorNotificaciones.style.display = 'flex';
                          } else {
                              contadorNotificaciones.style.display = 'none';
                          }
                      }
                  });
            }
        }
    </script>

    <script>
        // Mostrar íconos solo cuando la fuente haya cargado (elimina el parpadeo)
        document.fonts.ready.then(function() {
            document.body.classList.add('fonts-loaded');
        });
    </script>
</body>
</html>