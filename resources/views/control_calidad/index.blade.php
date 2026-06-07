@extends('components.panel')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-[#c29e75] bg-clip-text text-transparent">
            Control de Calidad
        </h1>
        <a href="{{ route('control-calidad.create') }}" 
           class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black px-6 py-2 rounded-lg font-bold hover:shadow-lg transition">
            + Nueva Inspección
        </a>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Total Inspecciones</p>
            <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Aprobados</p>
            <p class="text-2xl font-bold text-green-400">{{ $stats['aprobados'] }}</p>
            <p class="text-xs text-gray-500">{{ $stats['tasa_aprobacion'] }}% tasa</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">Rechazados</p>
            <p class="text-2xl font-bold text-red-400">{{ $stats['rechazados'] }}</p>
        </div>
        <div class="bg-black/40 p-4 rounded-lg border border-white/10">
            <p class="text-gray-400 text-sm">En Cuarentena</p>
            <p class="text-2xl font-bold text-yellow-400">{{ $stats['cuarentena'] }}</p>
        </div>
    </div>
    
    <!-- Tabla de inspecciones -->
    <div class="overflow-x-auto bg-black/30 rounded-lg border border-white/10">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/10 text-left">
                    <th class="p-3 text-gray-300">ID</th>
                    <th class="p-3 text-gray-300">Producción</th>
                    <th class="p-3 text-gray-300">Producto</th>
                    <th class="p-3 text-gray-300">Resultado</th>
                    <th class="p-3 text-gray-300">Inspector</th>
                    <th class="p-3 text-gray-300">Fecha</th>
                    <th class="p-3 text-gray-300">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspecciones as $inspeccion)
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="p-3 text-white">{{ $inspeccion->id }}</td>
                    <td class="p-3 text-white">#{{ $inspeccion->produccion_id }}</td>
                    <td class="p-3 text-white">{{ $inspeccion->producto->nombre ?? 'N/A' }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $inspeccion->resultado == 'aprobado' ? 'bg-green-500/20 text-green-400' : 
                               ($inspeccion->resultado == 'rechazado' ? 'bg-red-500/20 text-red-400' : 
                               'bg-yellow-500/20 text-yellow-400') }}">
                            {{ strtoupper($inspeccion->resultado) }}
                        </span>
                    </td>
                    <td class="p-3 text-gray-300">{{ $inspeccion->inspector->name ?? 'N/A' }}</td>
                    <td class="p-3 text-gray-300">{{ $inspeccion->fecha_inspeccion->format('d/m/Y') }}</td>
                    <td class="p-3">
                        <a href="{{ route('control-calidad.show', $inspeccion) }}" 
                           class="text-[#e7c095] hover:text-[#c29e75] transition"
                           title="Ver detalles">
                            Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-500">
                        No hay inspecciones de calidad registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $inspecciones->links() }}
    </div>
</div>
@endsection