<aside class="w-72 h-screen sticky top-0 flex flex-col bg-black/30 backdrop-blur-xl border-r border-white/10 shadow-2xl overflow-y-auto" 
       id="mainSidebar"
       x-data="{ 
           inventarioOpen: {{ request()->routeIs('materia-prima*', 'inventario*') ? 'true' : 'false' }},
           produccionOpen: {{ request()->routeIs('produccion*', 'produccion_real*') ? 'true' : 'false' }} 
       }">
    
    <div class="px-6 pt-8 pb-4 border-b border-white/10">
        <span class="text-[10px] font-semibold tracking-[0.2em] text-[#e7c095] uppercase">Sistema de Control</span>
        <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-[#e7c095] to-[#dbb57a] bg-clip-text text-transparent">KHALEESITAS</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">
        
        <div class="relative">
            <button @click="inventarioOpen = !inventarioOpen" 
                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-200"
                    :class="inventarioOpen ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : 'hover:bg-white/5'">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">inventory</span>
                    <span class="font-medium text-sm">Inventario</span>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-base transition-transform" :class="inventarioOpen ? 'rotate-180' : ''">expand_more</span>
            </button>
            <div x-show="inventarioOpen" x-cloak x-transition class="pl-8 space-y-1 mt-1">
                <a href="{{ route('materia-prima.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('materia-prima*') ? 'text-[#e7c095] bg-[#e7c095]/5' : 'text-gray-400 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">grass</span> Materia Prima
                </a>
                <a href="{{ route('inventario') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('inventario*') ? 'text-[#e7c095] bg-[#e7c095]/5' : 'text-gray-400 hover:text-white' }}">
                    <span class="material-symbols-outlined text-sm">box</span> Productos Terminados
                </a>
            </div>
        </div>

        @if(in_array(Auth::user()->role, ['admin', 'operario']))
        <div class="relative">
            <button @click="produccionOpen = !produccionOpen" 
                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-200"
                    :class="produccionOpen ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : 'hover:bg-white/5'">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">factory</span>
                    <span class="font-medium text-sm">Producción</span>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-base transition-transform" :class="produccionOpen ? 'rotate-180' : ''">expand_more</span>
            </button>
            <div x-show="produccionOpen" x-cloak x-transition class="pl-8 space-y-1 mt-1">
                <a href="{{ route('produccion_real.create') }}" class="block px-4 py-2 text-sm text-gray-400 hover:text-white">Registrar</a>
                <a href="{{ route('produccion_real.historial') }}" class="block px-4 py-2 text-sm text-gray-400 hover:text-white">Historial</a>
            </div>
        </div>
        @endif

        <a href="{{ route('reportes') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 {{ request()->routeIs('reportes') ? 'bg-[#e7c095]/10 border border-[#e7c095]/30' : '' }}">
            <span class="material-symbols-outlined text-2xl">analytics</span> Reportes
        </a>
    </nav>
    
    <div class="px-4 pb-6 border-t border-white/10 pt-4">
        <div class="flex items-center gap-3 mb-4 p-2 rounded-xl bg-white/5">
            <div class="w-10 h-10 rounded-full bg-[#e7c095] flex items-center justify-center text-black font-bold">{{ substr(Auth::user()->name, 0, 2) }}</div>
            <div>
                <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-sm text-gray-400 hover:text-red-400">
                <span class="material-symbols-outlined text-xl">logout</span> Cerrar sesión
            </button>
        </form>
    </div>
</aside>