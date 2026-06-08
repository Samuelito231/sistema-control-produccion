@extends('components.panel')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-[#c29e75] bg-clip-text text-transparent">
                Historial de Notificaciones
            </h1>
            <p class="text-gray-400 text-sm mt-1">Todas las alertas y notificaciones del sistema</p>
        </div>
        <button onclick="marcarTodas()" class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black px-4 py-2 rounded-lg font-bold hover:shadow-lg transition flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">done_all</span>
            Marcar todas como leídas
        </button>
    </div>
    
    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Total Notificaciones</p>
            <p class="text-2xl font-bold text-white">{{ $notificaciones->total() }}</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">No leídas</p>
            <p class="text-2xl font-bold text-yellow-400">{{ $notificaciones->where('leida', false)->count() }}</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Leídas</p>
            <p class="text-2xl font-bold text-green-400">{{ $notificaciones->where('leida', true)->count() }}</p>
        </div>
    </div>
    
    <!-- Tabla de notificaciones -->
    <div class="bg-black/30 rounded-lg border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr class="text-left text-gray-400 text-sm">
                        <th class="p-3">Tipo</th>
                        <th class="p-3">Título</th>
                        <th class="p-3">Mensaje</th>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notificaciones as $notificacion)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition {{ !$notificacion->leida ? 'bg-[#e7c095]/5' : '' }}">
                        <td class="p-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                {{ $notificacion->nivel == 'danger' ? 'bg-red-500/20' : ($notificacion->nivel == 'warning' ? 'bg-yellow-500/20' : 'bg-blue-500/20') }}">
                                <span class="material-symbols-outlined text-sm
                                    {{ $notificacion->nivel == 'danger' ? 'text-red-400' : ($notificacion->nivel == 'warning' ? 'text-yellow-400' : 'text-blue-400') }}">
                                    {{ $notificacion->nivel == 'danger' ? 'error' : ($notificacion->nivel == 'warning' ? 'warning' : 'info') }}
                                </span>
                            </div>
                        </td>
                        <td class="p-3">
                            <p class="font-medium text-white">{{ $notificacion->titulo }}</p>
                        </td>
                        <td class="p-3">
                            <p class="text-gray-300 text-sm">{{ $notificacion->mensaje }}</p>
                        </td>
                        <td class="p-3">
                            <p class="text-gray-400 text-sm">{{ $notificacion->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-500">{{ $notificacion->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="p-3">
                            @if(!$notificacion->leida)
                                <button onclick="marcarUna({{ $notificacion->id }})" class="text-xs bg-[#e7c095]/20 text-[#e7c095] px-3 py-1 rounded-full hover:bg-[#e7c095]/30 transition">
                                    Marcar leída
                                </button>
                            @else
                                <span class="text-xs bg-green-500/20 text-green-400 px-3 py-1 rounded-full">
                                    Leída
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <span class="material-symbols-outlined text-5xl text-gray-600">notifications_none</span>
                            <p class="text-gray-500 mt-2">No hay notificaciones registradas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-white/10">
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
        }).then(() => {
            location.reload();
        });
    }
    
    function marcarTodas() {
        fetch(`/notificaciones/marcar-todas`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            location.reload();
        });
    }
</script>
@endsection