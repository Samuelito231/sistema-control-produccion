<aside class="w-72 h-screen sticky top-0 flex flex-col bg-black/30 backdrop-blur-xl border-r border-white/10 shadow-2xl overflow-y-auto" id="mainSidebar">
    <!-- Header -->
    <div class="px-6 pt-8 pb-4 border-b border-white/10">
        <div class="flex flex-col">
            <span class="text-[10px] font-semibold tracking-[0.2em] text-[#e7c095] uppercase">Sistema de Control</span>
            <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-[#e7c095] to-[#dbb57a] bg-clip-text text-transparent drop-shadow-[0_1px_4px_rgba(0,0,0,0.8)]">
                KHALEESITAS
            </h1>
        </div>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        
        <!-- ==================== INVENTARIO ==================== -->
        <div class="relative">
            <input type="checkbox" id="inventarioDropdown" class="hidden peer">
            <label for="inventarioDropdown" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-200 cursor-pointer
                          {{ request()->routeIs('materia-prima*') || request()->routeIs('inventario*') ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : 'hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">inventory</span>
                    <span class="font-medium text-sm">Inventario</span>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-base transition-transform peer-checked:rotate-180">expand_more</span>
            </label>
            <div class="hidden peer-checked:block pl-8 space-y-1 mt-1">
                <a href="{{ route('materia-prima.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('materia-prima*') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">grass</span> Materia Prima
                </a>
                <a href="{{ route('inventario') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('inventario*') && !request()->routeIs('materia-prima*') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">box</span> Productos Terminados
                </a>
            </div>
        </div>

        <!-- ==================== PRODUCCIÓN ==================== -->
        @if(in_array(Auth::user()->role, ['admin', 'operario']))
        <div class="relative">
            <input type="checkbox" id="produccionDropdown" class="hidden peer">
            <label for="produccionDropdown" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-200 cursor-pointer
                          {{ request()->routeIs('produccion_real*') || request()->routeIs('produccion*') || request()->routeIs('control-calidad*') ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : 'hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">factory</span>
                    <span class="font-medium text-sm">Producción</span>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-base transition-transform peer-checked:rotate-180">expand_more</span>
            </label>
            <div class="hidden peer-checked:block pl-8 space-y-1 mt-1">
                <a href="{{ route('produccion_real.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('produccion_real.create') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">add</span> Registrar Producción
                </a>
                <a href="{{ route('produccion_real.historial') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('produccion_real.historial') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">history</span> Historial de Producción
                </a>
                <a href="{{ route('produccion') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('produccion') && !request()->routeIs('produccion_real*') && !request()->routeIs('control-calidad*') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">delete_sweep</span> Merma en Producción
                </a>
                <a href="{{ route('control-calidad.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('control-calidad*') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">science</span> Control de Calidad
                </a>
            </div>
        </div>
        @endif

        <!-- ==================== EMPAQUETADO ==================== -->
        @if(in_array(Auth::user()->role, ['admin', 'operario']))
        <div class="relative">
            <input type="checkbox" id="empaquetadoDropdown" class="hidden peer">
            <label for="empaquetadoDropdown" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-200 cursor-pointer
                          {{ request()->routeIs('empaquetado*') ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : 'hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">package</span>
                    <span class="font-medium text-sm">Empaquetado</span>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-base transition-transform peer-checked:rotate-180">expand_more</span>
            </label>
            <div class="hidden peer-checked:block pl-8 space-y-1 mt-1">
                <a href="{{ route('empaquetado') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('empaquetado') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">delete_sweep</span> Merma en Empaquetado
                </a>
            </div>
        </div>
        @endif

        <!-- ==================== DISTRIBUCIÓN / ENVÍOS ==================== -->
        @if(in_array(Auth::user()->role, ['admin', 'operario']))
        <div class="relative">
            <input type="checkbox" id="distribucionDropdown" class="hidden peer">
            <label for="distribucionDropdown" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-200 cursor-pointer
                          {{ request()->routeIs('envios*') ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : 'hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">local_shipping</span>
                    <span class="font-medium text-sm">Distribución</span>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-base transition-transform peer-checked:rotate-180">expand_more</span>
            </label>
            <div class="hidden peer-checked:block pl-8 space-y-1 mt-1">
                <a href="#" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 text-gray-500 opacity-60 cursor-not-allowed" title="Próximamente">
                    <span class="material-symbols-outlined text-sm">add</span> Registrar Envío <span class="text-[9px] text-gray-600">(pronto)</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 text-gray-500 opacity-60 cursor-not-allowed" title="Próximamente">
                    <span class="material-symbols-outlined text-sm">history</span> Historial de Envíos <span class="text-[9px] text-gray-600">(pronto)</span>
                </a>
            </div>
        </div>
        @endif

        <!-- ==================== REPORTES ==================== -->
        @if(in_array(Auth::user()->role, ['admin', 'operario', 'auditor', 'analista', 'empaquetador']))
        <div class="relative">
            <input type="checkbox" id="reportesDropdown" class="hidden peer">
            <label for="reportesDropdown" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-200 cursor-pointer
                          {{ request()->routeIs('reportes*') ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : 'hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">analytics</span>
                    <span class="font-medium text-sm">Reportes</span>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-base transition-transform peer-checked:rotate-180">expand_more</span>
            </label>
            <div class="hidden peer-checked:block pl-8 space-y-1 mt-1">
                <a href="{{ route('reportes') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('reportes') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">bar_chart</span> Reportes de Merma
                </a>
                <!-- Trazabilidad (próximamente) -->
                <a href="#" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 text-gray-500 opacity-60 cursor-not-allowed" title="Próximamente">
                    <span class="material-symbols-outlined text-sm">timeline</span> Trazabilidad <span class="text-[9px] text-gray-600">(pronto)</span>
                </a>
            </div>
        </div>
        @endif

        <!-- ==================== ADMINISTRACIÓN ==================== -->
        @if(Auth::user()->role === 'admin')
        <div class="relative">
            <input type="checkbox" id="adminDropdown" class="hidden peer">
            <label for="adminDropdown" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-200 cursor-pointer
                          {{ request()->routeIs('admin*') ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : 'hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">settings</span>
                    <span class="font-medium text-sm">Administración</span>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-base transition-transform peer-checked:rotate-180">expand_more</span>
            </label>
            <div class="hidden peer-checked:block pl-8 space-y-1 mt-1">
                <a href="{{ route('admin.usuarios') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.usuarios*') ? 'bg-[#e7c095]/10 text-[#e7c095]' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">admin_panel_settings</span> Usuarios
                </a>
            </div>
        </div>
        @endif
    </nav>

    <!-- Perfil y Logout -->
    <div class="px-4 pb-6 border-t border-white/10 pt-4">
        <div class="flex items-center gap-3 mb-4 p-2 rounded-xl bg-white/5">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#e7c095] to-[#b87a3a] flex items-center justify-center text-black font-bold shadow-md">
                {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <p class="text-xs text-gray-400">{{ ucfirst(Auth::user()->role ?? 'Operario') }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium text-gray-300 hover:bg-red-500/10 hover:text-red-400 transition-all">
                <span class="material-symbols-outlined text-xl">logout</span> Cerrar sesión
            </button>
        </form>
    </div>
</aside>

<script>
    (function() {
        const sidebar = document.getElementById('mainSidebar');
        
        // Restaurar scroll position
        const savedScroll = localStorage.getItem('sidebarScroll');
        if (savedScroll && sidebar) {
            sidebar.scrollTop = parseInt(savedScroll);
        }
        
        // Guardar scroll al hacer scroll
        if (sidebar) {
            sidebar.addEventListener('scroll', function() {
                localStorage.setItem('sidebarScroll', sidebar.scrollTop);
            });
        }
        
        // Guardar scroll antes de recargar
        window.addEventListener('beforeunload', function() {
            if (sidebar) {
                localStorage.setItem('sidebarScroll', sidebar.scrollTop);
            }
        });
        
        // Guardar estado de los dropdowns
        const inventarioCheckbox = document.getElementById('inventarioDropdown');
        const produccionCheckbox = document.getElementById('produccionDropdown');
        const empaquetadoCheckbox = document.getElementById('empaquetadoDropdown');
        const distribucionCheckbox = document.getElementById('distribucionDropdown');
        const reportesCheckbox = document.getElementById('reportesDropdown');
        const adminCheckbox = document.getElementById('adminDropdown');
        
        function loadCheckboxState(checkbox, key) {
            if (checkbox && localStorage.getItem(key) === 'true') {
                checkbox.checked = true;
            }
        }
        
        function saveCheckboxState(checkbox, key) {
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    localStorage.setItem(key, this.checked);
                });
            }
        }
        
        loadCheckboxState(inventarioCheckbox, 'inventarioOpen');
        loadCheckboxState(produccionCheckbox, 'produccionOpen');
        loadCheckboxState(empaquetadoCheckbox, 'empaquetadoOpen');
        loadCheckboxState(distribucionCheckbox, 'distribucionOpen');
        loadCheckboxState(reportesCheckbox, 'reportesOpen');
        loadCheckboxState(adminCheckbox, 'adminOpen');
        
        saveCheckboxState(inventarioCheckbox, 'inventarioOpen');
        saveCheckboxState(produccionCheckbox, 'produccionOpen');
        saveCheckboxState(empaquetadoCheckbox, 'empaquetadoOpen');
        saveCheckboxState(distribucionCheckbox, 'distribucionOpen');
        saveCheckboxState(reportesCheckbox, 'reportesOpen');
        saveCheckboxState(adminCheckbox, 'adminOpen');
        
        // Abrir dropdown según la URL actual
        const path = window.location.pathname;
        if (path.includes('/materia-prima') || path.includes('/inventario')) {
            if (inventarioCheckbox) inventarioCheckbox.checked = true;
        }
        if (path.includes('/produccion') || path.includes('/control-calidad')) {
            if (produccionCheckbox) produccionCheckbox.checked = true;
        }
        if (path.includes('/empaquetado')) {
            if (empaquetadoCheckbox) empaquetadoCheckbox.checked = true;
        }
        if (path.includes('/envios')) {
            if (distribucionCheckbox) distribucionCheckbox.checked = true;
        }
        if (path.includes('/reportes')) {
            if (reportesCheckbox) reportesCheckbox.checked = true;
        }
        if (path.includes('/admin')) {
            if (adminCheckbox) adminCheckbox.checked = true;
        }
    })();
</script>

<style>
    #mainSidebar::-webkit-scrollbar { width: 4px; }
    #mainSidebar::-webkit-scrollbar-track { background: transparent; }
    #mainSidebar::-webkit-scrollbar-thumb { background: rgba(231, 192, 149, 0.3); border-radius: 10px; }
    #mainSidebar::-webkit-scrollbar-thumb:hover { background: rgba(231, 192, 149, 0.6); }
</style>