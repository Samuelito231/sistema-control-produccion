@extends('components.panel')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-[#c29e75] bg-clip-text text-transparent">
            Historial de Envíos
        </h1>
        <p class="text-gray-400 text-sm mt-1">Todos los envíos realizados y su estado actual</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Total</p>
            <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Pendientes</p>
            <p class="text-2xl font-bold text-yellow-400">{{ $stats['pendientes'] }}</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">En Tránsito</p>
            <p class="text-2xl font-bold text-blue-400">{{ $stats['en_transito'] }}</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Entregados</p>
            <p class="text-2xl font-bold text-green-400">{{ $stats['entregados'] }}</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Cancelados</p>
            <p class="text-2xl font-bold text-red-400">{{ $stats['cancelados'] }}</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-black/40 rounded-lg p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar por guía o destinatario..."
                       class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#e7c095] transition">
            </div>
            <div>
                <select name="estado" class="bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_transito" {{ request('estado') == 'en_transito' ? 'selected' : '' }}>En Tránsito</option>
                    <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-[#e7c095]/20 text-[#e7c095] px-4 py-2 rounded-lg hover:bg-[#e7c095]/30 transition">
                    Filtrar
                </button>
                @if(request()->has('search') || request()->has('estado'))
                <a href="{{ route('envios.historial') }}" class="ml-2 text-gray-400 hover:text-white px-4 py-2">Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-black/30 rounded-lg border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr class="text-left text-gray-400 text-sm">
                        <th class="p-3">Guía</th>
                        <th class="p-3">Destinatario</th>
                        <th class="p-3">Fecha Envío</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Transportista</th>
                        <th class="p-3">Productos</th>
                        <th class="p-3">Costo</th>
                        <th class="p-3">Acciones</th>
                    比
                </thead>
                <tbody>
                    @forelse($envios as $envio)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="p-3 font-mono text-sm">{{ $envio->numero_guia }}比
                        <td class="p-3">{{ $envio->destinatario_nombre }}比
                        <td class="p-3">{{ $envio->fecha_envio->format('d/m/Y') }}比
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $envio->estado_envio == 'entregado' ? 'bg-green-500/20 text-green-400' : 
                                   ($envio->estado_envio == 'en_transito' ? 'bg-blue-500/20 text-blue-400' : 
                                   ($envio->estado_envio == 'cancelado' ? 'bg-red-500/20 text-red-400' : 
                                   'bg-yellow-500/20 text-yellow-400')) }}">
                                {{ ucfirst(str_replace('_', ' ', $envio->estado_envio)) }}
                            </span>
                        比
                        <td class="p-3">{{ $envio->transportista }}比
                        <td class="p-3">{{ $envio->productos->sum('cantidad') }} uds比
                        <td class="p-3">${{ number_format($envio->costo_envio, 2) }}比
                        <td class="p-3">
                            <a href="{{ route('envios.show', $envio) }}" class="text-[#e7c095] hover:text-[#c29e75] transition">Ver</a>
                        比
                    比
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-500">
                            No hay envíos registrados
                        比
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $envios->links() }}
        </div>
    </div>
</div>
@endsection