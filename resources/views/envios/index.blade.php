@extends('components.panel')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-[#c29e75] bg-clip-text text-transparent">
                Envíos
            </h1>
            <p class="text-gray-400 text-sm mt-1">Gestión de envíos y distribución</p>
        </div>
        <a href="{{ route('envios.create') }}" 
           class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black px-6 py-2 rounded-lg font-bold hover:shadow-lg transition">
            + Nuevo Envío
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-black/40 rounded-lg p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-400 text-sm mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Guía o destinatario..."
                       class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Estado</label>
                <select name="estado" class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_transito" {{ request('estado') == 'en_transito' ? 'selected' : '' }}>En Tránsito</option>
                    <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-[#e7c095]/20 text-[#e7c095] px-4 py-2 rounded-lg hover:bg-[#e7c095]/30 transition">
                    Filtrar
                </button>
                @if(request()->has('search') || request()->has('estado'))
                <a href="{{ route('envios.index') }}" class="ml-2 text-gray-400 hover:text-white px-4 py-2">Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de envíos -->
    <div class="bg-black/30 rounded-lg border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr class="text-left text-gray-400 text-sm">
                        <th class="p-3">Guía</th>
                        <th class="p-3">Destinatario</th>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Transportista</th>
                        <th class="p-3">Costo</th>
                        <th class="p-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($envios as $envio)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="p-3 font-mono text-sm">{{ $envio->numero_guia }}</td>
                        <td class="p-3">{{ $envio->destinatario_nombre }}</td>
                        <td class="p-3">{{ $envio->fecha_envio->format('d/m/Y') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $envio->estado == 'entregado' ? 'bg-green-500/20 text-green-400' : 
                                   ($envio->estado == 'en_transito' ? 'bg-blue-500/20 text-blue-400' : 
                                   ($envio->estado == 'cancelado' ? 'bg-red-500/20 text-red-400' : 
                                   'bg-yellow-500/20 text-yellow-400')) }}">
                                {{ ucfirst(str_replace('_', ' ', $envio->estado)) }}
                            </span>
                        </td>
                        <td class="p-3">{{ $envio->transportista }}</td>
                        <td class="p-3">${{ number_format($envio->costo_envio, 2) }}</td>
                        <td class="p-3">
                            <a href="{{ route('envios.show', $envio) }}" class="text-[#e7c095] hover:text-[#c29e75]">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">
                            No hay envíos registrados
                        </td>
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