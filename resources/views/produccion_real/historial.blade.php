@extends('components.panel')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Historial de Producción</h1>
            <p class="text-gray-400 mt-1">Monitoreo de lotes, rendimiento y métricas de calidad.</p>
        </div>
        <a href="{{ route('produccion_real.create') }}" 
           class="group bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold px-6 py-3 rounded-xl shadow-lg shadow-[#e7c095]/20 hover:shadow-[#e7c095]/40 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined">add_circle</span> Nueva Producción
        </a>
    </div>

    <div class="bg-black/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/10 text-[10px] uppercase tracking-widest text-gray-400">
                        <th class="px-6 py-4">Lote</th>
                        <th class="px-6 py-4">Producto</th>
                        <th class="px-6 py-4 text-right">Elaborado</th>
                        <th class="px-6 py-4 text-right">Desecho</th>
                        <th class="px-6 py-4 text-center">Eficiencia</th>
                        <th class="px-6 py-4 text-right">MP Desechada</th>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4 text-center">Calidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($producciones as $p)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $p->lote ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-white">{{ $p->producto->nombre ?? 'Producto eliminado' }}</td>
                        <td class="px-6 py-4 text-right font-medium text-green-400">{{ number_format($p->cantidad_producida, 2) }} <span class="text-[10px] text-gray-500">{{ $p->producto->unidad ?? 'kg' }}</span></td>
                        <td class="px-6 py-4 text-right text-red-400 font-medium">{{ number_format($p->producto_desechado, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $ef = $p->eficiencia;
                                $color = $ef >= 90 ? 'text-green-400' : ($ef >= 70 ? 'text-yellow-400' : 'text-red-400');
                            @endphp
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-xs font-bold {{ $color }}">{{ number_format($ef, 1) }}%</span>
                                <div class="w-12 h-1 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full {{ str_replace('text-', 'bg-', $color) }}" style="width: {{ $ef }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-300">{{ number_format($p->materia_prima_desechada, 2) }} <span class="text-[10px] text-gray-500">kg</span></td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ \Carbon\Carbon::parse($p->fecha_produccion)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($p->calidad_observaciones)
                                <div class="relative group cursor-pointer inline-block">
                                    <span class="text-yellow-500/80 hover:text-yellow-400 text-lg">⚠️</span>
                                    <div class="absolute right-0 bottom-full mb-2 hidden group-hover:block w-64 p-3 bg-neutral-900 border border-white/10 rounded-xl text-[11px] text-gray-300 shadow-2xl z-20">
                                        <p class="font-bold text-yellow-500 mb-1">Observaciones de Calidad:</p>
                                        {{ $p->calidad_observaciones }}
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-700">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-16 text-gray-500 italic">No hay registros de producción disponibles.</td>
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