@extends('components.panel')

@section('content')
<div class="max-w-6xl mx-auto p-6 lg:p-8 select-none">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-bold tracking-[0.2em] text-[#e7c095]/60 uppercase mb-1">
                <span>Core Logs</span>
                <span class="text-white/20">•</span>
                <span>Auditoría</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight bg-gradient-to-r from-[#e7c095] via-[#f3dcb3] to-[#dbb57a] bg-clip-text text-transparent drop-shadow-[0_2px_10px_rgba(231,192,149,0.15)]">
                Historial de Notificaciones
            </h1>
            <p class="text-gray-400 text-xs mt-1 leading-relaxed">Registro completo de alertas, métricas de operación y eventos críticos del ecosistema.</p>
        </div>
        
        <button onclick="marcarTodas()" class="group/btn relative flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-b from-[#e7c095] to-[#c29e75] text-black font-bold text-xs tracking-wide shadow-[0_4px_20px_rgba(231,192,149,0.25)] hover:shadow-[0_4px_25px_rgba(231,192,149,0.4)] hover:scale-[1.02] transition-all duration-300 focus:outline-none">
            <span class="material-symbols-outlined text-sm font-black transition-transform group-hover/btn:rotate-12">done_all</span>
            Marcar todo leído
        </button>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-white/[0.04] to-transparent border border-white/[0.06] p-4 shadow-lg transition-all hover:border-white/10">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-[11px] font-semibold tracking-wide uppercase opacity-80">Volumen Total</p>
                <span class="material-symbols-outlined text-gray-500 text-lg">layers</span>
            </div>
            <p class="text-3xl font-black text-white tracking-tight mt-2">{{ $notificaciones->total() }}</p>
        </div>
        
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-white/[0.04] to-transparent border border-white/[0.06] p-4 shadow-lg transition-all hover:border-amber-500/20 group">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-[11px] font-semibold tracking-wide uppercase opacity-80">Por Procesar</p>
                <span class="w-2 h-2 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.6)] group-hover:animate-ping mt-1"></span>
            </div>
            <p class="text-3xl font-black text-amber-400 tracking-tight mt-2 drop-shadow-[0_2px_10px_rgba(251,191,36,0.1)]">
                {{ $notificaciones->where('leida', false)->count() }}
            </p>
        </div>
        
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-white/[0.04] to-transparent border border-white/[0.06] p-4 shadow-lg transition-all hover:border-emerald-500/20">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-[11px] font-semibold tracking-wide uppercase opacity-80">Historial Archivado</p>
                <span class="material-symbols-outlined text-emerald-400 text-lg opacity-80">task_alt</span>
            </div>
            <p class="text-3xl font-black text-emerald-400 tracking-tight mt-2">
                {{ $notificaciones->where('leida', true)->count() }}
            </p>
        </div>
    </div>
    
    <div class="bg-[#0e0f0f]/80 backdrop-blur-xl rounded-2xl border border-white/[0.08] shadow-[0_30px_70px_rgba(0,0,0,0.6)] overflow-hidden">
        
        <div class="p-4 bg-white/[0.01] border-b border-white/[0.06] flex items-center justify-between">
            <span class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">Flujo de registros recientes</span>
            <span class="text-[10px] text-gray-500 font-medium">Página {{ $notificaciones->currentPage() }} de {{ $notificaciones->lastPage() }}</span>
        </div>

        <div class="divide-y divide-white/[0.05]">
            @forelse($notificaciones as $notificacion)
            <div class="group/row flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 lg:p-5 transition-all duration-200 relative overflow-hidden
                {{ !$notificacion->leida ? 'bg-gradient-to-r from-[#e7c095]/[0.03] via-white/[0.01] to-transparent' : 'hover:bg-white/[0.02]' }}">
                
                <div class="absolute left-0 top-0 bottom-0 w-[4px] transition-all
                    {{ $notificacion->nivel == 'danger' ? 'bg-gradient-to-b from-red-500 to-rose-600' : ($notificacion->nivel == 'warning' ? 'bg-gradient-to-b from-amber-400 to-orange-500' : 'bg-gradient-to-b from-blue-500 to-indigo-600') }}">
                </div>

                <div class="flex items-start gap-4 flex-1 min-w-0 pl-1">
                    <div class="p-2.5 rounded-xl shrink-0 mt-0.5
                        {{ $notificacion->nivel == 'danger' ? 'bg-red-500/10 text-red-400' : ($notificacion->nivel == 'warning' ? 'bg-amber-400/10 text-amber-400' : 'bg-blue-500/10 text-blue-400') }}">
                        <span class="material-symbols-outlined text-lg block font-light">
                            {{ $notificacion->nivel == 'danger' ? 'error' : ($notificacion->nivel == 'warning' ? 'warning' : 'info') }}
                        </span>
                    </div>

                    <div class="space-y-0.5 min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-bold text-gray-100 tracking-tight group-hover/row:text-[#e7c095] transition-colors duration-200">
                                {{ $notificacion->titulo }}
                            </h3>
                            
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded uppercase tracking-wide border
                                {{ $notificacion->nivel == 'danger' ? 'bg-red-500/10 text-red-400 border-red-500/20' : ($notificacion->nivel == 'warning' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-blue-500/10 text-blue-400 border-blue-500/20') }}">
                                {{ $notificacion->nivel == 'danger' ? 'Crítico' : ($notificacion->nivel == 'warning' ? 'Atención' : 'Info') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed max-w-3xl pr-4 break-words">
                            {{ $notificacion->mensaje }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-2 border-t md:border-t-0 border-white/5 pt-3 md:pt-0 shrink-0">
                    <div class="text-left md:text-right">
                        <p class="text-xs font-semibold text-gray-300 tracking-tight">{{ $notificacion->created_at->format('d M, Y • H:i') }}</p>
                        <p class="text-[10px] text-gray-500 mt-0.5 font-medium tracking-tight">{{ $notificacion->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="min-w-[100px] flex justify-end">
                        @if(!$notificacion->leida)
                            <button onclick="marcarUna({{ $notificacion->id }})" class="text-[11px] font-bold text-[#e7c095] bg-[#e7c095]/10 hover:bg-[#e7c095] hover:text-black px-3 py-1.5 rounded-xl border border-[#e7c095]/20 hover:border-transparent shadow-sm transition-all duration-200">
                                Marcar leída
                            </button>
                        @else
                            <div class="flex items-center gap-1.5 text-[10px] font-semibold text-gray-500 bg-white/[0.02] border border-white/[0.05] px-2.5 py-1 rounded-lg">
                                <span class="w-1 h-1 rounded-full bg-gray-500"></span>
                                Archivado
                            </div>
                        @endif
                    </div>
                </div>

            </div>
            @empty
            <div class="py-20 text-center text-gray-500 flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-white/[0.02] flex items-center justify-center border border-white/[0.05] shadow-inner mb-4">
                    <span class="material-symbols-outlined text-3xl text-gray-600">notifications_off</span>
                </div>
                <h3 class="text-sm font-bold text-gray-300 tracking-wide">Sin registros en la base de datos</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-normal">Actualmente no existen alertas ni logs generados en el sistema de control.</p>
            </div>
            @endforelse
        </div>
        
        <div class="px-6 py-4 border-t border-white/[0.06] bg-white/[0.01] custom-pagination-wrapper">
            {{ $notificaciones->links() }}
        </div>
    </div>
</div>

<script>
    function marcarUna(id) {
        fetch(`/notificaciones/marcar/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
    
    function marcarTodas() {
        fetch(`/notificaciones/marcar-todas`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
</script>

<style>
    /* Inyecciones CSS para forzar que los enlaces de paginación por defecto de Tailwind adopten el look Premium de Khaleesitas */
    .custom-pagination-wrapper nav { display: flex; justify-between: items-center; width: 100%; }
    .custom-pagination-wrapper flex[role="navigation"] { background: transparent !important; }
    .custom-pagination-wrapper span, .custom-pagination-wrapper a { border-color: rgba(255,255,255,0.06) !important; background-color: rgba(255,255,255,0.02) !important; color: #a1a1aa !important; font-size: 11px !important; font-weight: 600 !important; border-radius: 8px !important; margin: 0 2px; }
    .custom-pagination-wrapper .bg-blue-600, .custom-pagination-wrapper [aria-current="page"] span { background: linear-gradient(180deg, #e7c095 0%, #c29e75 100%) !important; color: #000000 !important; border-color: transparent !important; }
    .custom-pagination-wrapper a:hover { background-color: rgba(231,192,149,0.1) !important; color: #e7c095 !important; border-color: rgba(231,192,149,0.3) !important; }
</style>
@endsection