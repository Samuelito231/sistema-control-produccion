<style>
    #mainSidebar {
        scrollbar-width: thin;
        scrollbar-color: rgba(231,192,149,0.25) transparent;
    }
    #mainSidebar::-webkit-scrollbar { width: 3px; }
    #mainSidebar::-webkit-scrollbar-track { background: transparent; }
    #mainSidebar::-webkit-scrollbar-thumb { background: rgba(231,192,149,0.25); border-radius: 10px; }
    #mainSidebar::-webkit-scrollbar-thumb:hover { background: rgba(231,192,149,0.5); }

    /* Dropdown panel */
    .sb-panel {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 180ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sb-panel.open {
        grid-template-rows: 1fr;
    }
    .sb-panel > div {
        overflow: hidden;
    }

    /* Chevron */
    .sb-chevron {
        transition: transform 180ms cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }
    .sb-chevron.open {
        transform: rotate(180deg);
    }

    /* Trigger button */
    .sb-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid transparent;
        cursor: pointer;
        background: none;
        text-align: left;
        transition: background 120ms, border-color 120ms;
        user-select: none;
    }
    .sb-trigger:hover {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.08);
    }
    .sb-trigger.active {
        background: rgba(231,192,149,0.08);
        border-color: rgba(231,192,149,0.25);
    }
    .sb-trigger-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Sub-links */
    .sb-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 13px;
        transition: background 100ms, color 100ms;
        color: #6b7280;
        text-decoration: none;
        line-height: 1.3;
    }
    .sb-link:hover {
        background: rgba(255,255,255,0.05);
        color: #f1f5f9;
    }
    .sb-link.active {
        background: rgba(231,192,149,0.1);
        color: #e7c095;
    }
    .sb-link.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>

<aside class="w-72 h-screen sticky top-0 flex flex-col bg-black/30 backdrop-blur-xl border-r border-white/10 shadow-2xl overflow-y-auto" id="mainSidebar">

    {{-- Logo --}}
    <div class="px-6 pt-8 pb-4 border-b border-white/10 flex-shrink-0">
        <span class="text-[10px] font-semibold tracking-[0.2em] text-[#e7c095] uppercase">Sistema de Control</span>
        <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-[#e7c095] to-[#dbb57a] bg-clip-text text-transparent drop-shadow-[0_1px_4px_rgba(0,0,0,0.8)]">
            KHALEESITAS
        </h1>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-5 space-y-1">

        {{-- INVENTARIO --}}
        <div>
            <button type="button"
                class="sb-trigger {{ request()->routeIs('materia-prima*') || request()->routeIs('inventario*') || request()->routeIs('recetas*') ? 'active' : '' }}"
                data-sb="inventario">
                <div class="sb-trigger-left">
                    <span class="material-symbols-outlined text-xl text-gray-400">inventory</span>
                    <span class="font-medium text-sm text-gray-200">Inventario</span>
                </div>
                <span class="material-symbols-outlined text-gray-500 text-[18px] sb-chevron" data-chevron="inventario">expand_more</span>
            </button>
            <div class="sb-panel" data-panel="inventario">
                <div>
                    <div class="pl-9 pt-1 pb-1 space-y-0.5">
                        <a href="{{ route('materia-prima.index') }}"
                           class="sb-link {{ request()->routeIs('materia-prima*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">grass</span> Materia Prima
                        </a>
                        <a href="{{ route('inventario') }}"
                           class="sb-link {{ request()->routeIs('inventario*') && !request()->routeIs('materia-prima*') && !request()->routeIs('recetas*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">box</span> Productos Terminados
                        </a>
                        <a href="{{ route('recetas.todas') }}"
                           class="sb-link {{ request()->routeIs('recetas.todas') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">receipt_long</span> Recetas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRODUCCIÓN --}}
        @if(in_array(Auth::user()->role, ['admin', 'operario', 'auditor', 'analista']))
        <div>
            <button type="button"
                class="sb-trigger {{ request()->routeIs('produccion_real*') || request()->routeIs('produccion*') || request()->routeIs('control-calidad*') ? 'active' : '' }}"
                data-sb="produccion">
                <div class="sb-trigger-left">
                    <span class="material-symbols-outlined text-xl text-gray-400">factory</span>
                    <span class="font-medium text-sm text-gray-200">Producción</span>
                </div>
                <span class="material-symbols-outlined text-gray-500 text-[18px] sb-chevron" data-chevron="produccion">expand_more</span>
            </button>
            <div class="sb-panel" data-panel="produccion">
                <div>
                    <div class="pl-9 pt-1 pb-1 space-y-0.5">
                        @if(in_array(Auth::user()->role, ['admin', 'operario']))
                        <a href="{{ route('produccion_real.create') }}"
                           class="sb-link {{ request()->routeIs('produccion_real.create') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">add</span> Registrar Producción
                        </a>
                        <a href="{{ route('produccion_real.historial') }}"
                           class="sb-link {{ request()->routeIs('produccion_real.historial') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">history</span> Historial de Producción
                        </a>
                        <a href="{{ route('produccion') }}"
                           class="sb-link {{ request()->routeIs('produccion') && !request()->routeIs('produccion_real*') && !request()->routeIs('control-calidad*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">delete_sweep</span> Merma en Producción
                        </a>
                        @endif
                        <a href="{{ route('control-calidad.index') }}"
                           class="sb-link {{ request()->routeIs('control-calidad*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">science</span> Control de Calidad
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- EMPAQUETADO --}}
        @if(in_array(Auth::user()->role, ['admin', 'operario']))
        <div>
            <button type="button"
                class="sb-trigger {{ request()->routeIs('empaquetado*') ? 'active' : '' }}"
                data-sb="empaquetado">
                <div class="sb-trigger-left">
                    <span class="material-symbols-outlined text-xl text-gray-400">package</span>
                    <span class="font-medium text-sm text-gray-200">Empaquetado</span>
                </div>
                <span class="material-symbols-outlined text-gray-500 text-[18px] sb-chevron" data-chevron="empaquetado">expand_more</span>
            </button>
            <div class="sb-panel" data-panel="empaquetado">
                <div>
                    <div class="pl-9 pt-1 pb-1 space-y-0.5">
                        <a href="{{ route('empaquetado') }}"
                           class="sb-link {{ request()->routeIs('empaquetado') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">delete_sweep</span> Merma en Empaquetado
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- DISTRIBUCIÓN --}}
        @if(in_array(Auth::user()->role, ['admin', 'operario']))
        <div>
            <button type="button"
                class="sb-trigger {{ request()->routeIs('envios*') ? 'active' : '' }}"
                data-sb="distribucion">
                <div class="sb-trigger-left">
                    <span class="material-symbols-outlined text-xl text-gray-400">local_shipping</span>
                    <span class="font-medium text-sm text-gray-200">Distribución</span>
                </div>
                <span class="material-symbols-outlined text-gray-500 text-[18px] sb-chevron" data-chevron="distribucion">expand_more</span>
            </button>
            <div class="sb-panel" data-panel="distribucion">
                <div>
                    <div class="pl-9 pt-1 pb-1 space-y-0.5">
                        <a href="{{ route('envios.create') }}"
                           class="sb-link {{ request()->routeIs('envios.create') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">add</span> Registrar Envío
                        </a>
                        <a href="{{ route('envios.index') }}"
                           class="sb-link {{ request()->routeIs('envios.index') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">list</span> Listado de Envíos
                        </a>
                        <a href="{{ route('envios.historial') }}"
                           class="sb-link {{ request()->routeIs('envios.historial') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">history</span> Historial de Envíos
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- REPORTES --}}
        @if(in_array(Auth::user()->role, ['admin', 'operario', 'auditor', 'analista', 'empaquetador']))
        <div>
            <button type="button"
                class="sb-trigger {{ request()->routeIs('reportes*') || request()->routeIs('trazabilidad*') ? 'active' : '' }}"
                data-sb="reportes">
                <div class="sb-trigger-left">
                    <span class="material-symbols-outlined text-xl text-gray-400">analytics</span>
                    <span class="font-medium text-sm text-gray-200">Reportes</span>
                </div>
                <span class="material-symbols-outlined text-gray-500 text-[18px] sb-chevron" data-chevron="reportes">expand_more</span>
            </button>
            <div class="sb-panel" data-panel="reportes">
                <div>
                    <div class="pl-9 pt-1 pb-1 space-y-0.5">
                        <a href="{{ route('reportes') }}"
                           class="sb-link {{ request()->routeIs('reportes') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">bar_chart</span> Reportes de Merma
                        </a>
                        <a href="{{ route('trazabilidad.index') }}"
                           class="sb-link {{ request()->routeIs('trazabilidad*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">timeline</span> Trazabilidad
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ADMINISTRACIÓN --}}
        @if(Auth::user()->role === 'admin')
        <div>
            <button type="button"
                class="sb-trigger {{ request()->routeIs('admin*') ? 'active' : '' }}"
                data-sb="admin">
                <div class="sb-trigger-left">
                    <span class="material-symbols-outlined text-xl text-gray-400">settings</span>
                    <span class="font-medium text-sm text-gray-200">Administración</span>
                </div>
                <span class="material-symbols-outlined text-gray-500 text-[18px] sb-chevron" data-chevron="admin">expand_more</span>
            </button>
            <div class="sb-panel" data-panel="admin">
                <div>
                    <div class="pl-9 pt-1 pb-1 space-y-0.5">
                        <a href="{{ route('admin.usuarios') }}"
                           class="sb-link {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined text-[15px]">admin_panel_settings</span> Usuarios
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </nav>

    {{-- Usuario + Logout --}}
    <div class="px-3 pb-5 pt-3 border-t border-white/10 flex-shrink-0">
        <div class="flex items-center gap-3 mb-3 p-2.5 rounded-xl bg-white/[0.04]">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#e7c095] to-[#b87a3a] flex items-center justify-center text-black text-xs font-bold shadow flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <p class="text-xs text-gray-400">{{ ucfirst(Auth::user()->role ?? 'Operario') }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition-colors duration-100">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>

<script>
(function () {
    var sidebar = document.getElementById('mainSidebar');

    // Restaurar scroll
    var savedScroll = localStorage.getItem('sidebarScroll');
    if (savedScroll && sidebar) sidebar.scrollTop = parseInt(savedScroll, 10);
    if (sidebar) {
        sidebar.addEventListener('scroll', function () {
            localStorage.setItem('sidebarScroll', sidebar.scrollTop);
        });
    }

    // Toggle panels (accordion)
    var triggers = document.querySelectorAll('[data-sb]');
    var all = {};

    triggers.forEach(function (btn) {
        var key = btn.dataset.sb;
        all[key] = {
            btn:     btn,
            panel:   document.querySelector('[data-panel="' + key + '"]'),
            chevron: document.querySelector('[data-chevron="' + key + '"]')
        };
    });

    function closeAll(except) {
        Object.keys(all).forEach(function (k) {
            if (k === except) return;
            var item = all[k];
            if (!item.panel) return;
            item.panel.classList.remove('open');
            if (item.chevron) item.chevron.classList.remove('open');
            localStorage.setItem('sb_' + k, 'false');
        });
    }

    function setOpen(key, open) {
        var item = all[key];
        if (!item || !item.panel) return;
        if (open) {
            item.panel.classList.add('open');
            if (item.chevron) item.chevron.classList.add('open');
        } else {
            item.panel.classList.remove('open');
            if (item.chevron) item.chevron.classList.remove('open');
        }
        localStorage.setItem('sb_' + key, open ? 'true' : 'false');
    }

    // Estado inicial
    triggers.forEach(function (btn) {
        var key    = btn.dataset.sb;
        var item   = all[key];
        if (!item.panel) return;
        var isOpen = btn.classList.contains('active') || localStorage.getItem('sb_' + key) === 'true';
        if (btn.classList.contains('active')) {
            setOpen(key, true);
        } else if (isOpen) {
            setOpen(key, true);
        }
    });

    // Click
    triggers.forEach(function (btn) {
        var key = btn.dataset.sb;
        var item = all[key];
        if (!item.panel) return;
        btn.addEventListener('click', function () {
            var nowOpen = item.panel.classList.contains('open');
            closeAll(key);
            setOpen(key, !nowOpen);
        });
    });
})();
</script>