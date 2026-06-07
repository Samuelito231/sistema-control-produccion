@extends('components.panel')

@section('content')
<div class="p-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Historial de producción</h1>
            <p class="text-gray-400 text-sm">Visualización de lotes y métricas de eficiencia recientes.</p>
        </div>
        <a href="{{ route('produccion_real.create') }}" class="bg-[#e7c095] hover:bg-[#d4ac7f] text-black font-bold px-5 py-2 rounded-xl transition flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-lg">add_circle</span> Nueva producción
        </a>
    </div>

    <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Lote</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Elaborado</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Desecho</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Eficiencia</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">MP Desechada</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Calidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($producciones as $p)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4 font-mono text-xs text-gray-300">{{ $p->lote ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-white">{{ $p->producto->nombre ?? 'Producto eliminado' }}</td>
                        <td class="px-6 py-4 text-right text-green-400 font-semibold">{{ number_format($p->cantidad_producida, 2) }} {{ $p->producto->unidad ?? 'kg' }}</td>
                        <td class="px-6 py-4 text-right text-red-400 text-sm">{{ number_format($p->producto_desechado, 2) }} {{ $p->producto->unidad ?? 'kg' }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $eficienciaColor = $p->eficiencia >= 90 ? 'bg-green-500/20 text-green-300' : ($p->eficiencia >= 70 ? 'bg-yellow-500/20 text-yellow-300' : 'bg-red-500/20 text-red-300');
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $eficienciaColor }}">
                                {{ number_format($p->eficiencia, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-300">{{ number_format($p->materia_prima_desechada, 2) }} kg</td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ \Carbon\Carbon::parse($p->fecha_produccion)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($p->calidad_observaciones)
                                <div class="group relative flex justify-center">
                                    <span class="text-yellow-400 cursor-help">⚠️</span>
                                    <div class="absolute bottom-full mb-2 hidden group-hover:block w-48 bg-black border border-white/20 p-2 rounded text-[10px] text-left text-gray-300 z-10 shadow-xl">
                                        {{ $p->calidad_observaciones }}
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-600">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-12 italic">No hay registros de producción encontrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($producciones->hasPages())
        <div class="px-6 py-4 border-t border-white/10 bg-white/5">
            {{ $producciones->links() }}
        </div>
        @endif
    </div>
</div>
@endsection