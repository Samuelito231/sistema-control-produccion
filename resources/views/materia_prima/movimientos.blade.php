@extends('components.panel')

@section('content')
<div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 bg-black/20 border border-white/10 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.15)]">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-[#e7c095]">Movimientos de Materia Prima</h1>
                <p class="text-gray-400">{{ $materia_prima->nombre }} (SKU: {{ $materia_prima->sku }})</p>
                @if($materia_prima->lote_compra)
                    <p class="text-xs text-slate-500 mt-1">📦 Lote actual: <span class="font-mono text-[#e7c095]">{{ $materia_prima->lote_compra }}</span></p>
                @endif
                @if($materia_prima->fecha_vencimiento)
                    <p class="text-xs text-slate-500">⏰ Vence: <span class="text-amber-400">{{ \Carbon\Carbon::parse($materia_prima->fecha_vencimiento)->format('d/m/Y') }}</span></p>
                @endif
            </div>
            <div class="flex gap-2">
                @can('manage-products')
                    <a href="{{ route('materia-prima.entrada', $materia_prima) }}" class="bg-green-500/20 text-green-400 px-3 py-1 rounded-lg flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">add_box</span> Compra
                    </a>
                    <a href="{{ route('materia-prima.salida', $materia_prima) }}" class="bg-red-500/20 text-red-400 px-3 py-1 rounded-lg flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">remove</span> Salida
                    </a>
                @endcan
                <a href="{{ route('materia-prima.index') }}" class="bg-white/10 text-white px-3 py-1 rounded-lg">← Volver</a>
            </div>
        </div>

        <div class="bg-black/30 rounded-xl p-4 mb-4 border border-white/10">
            <p class="text-sm">📦 <strong>Stock actual:</strong> <span class="text-lg font-bold text-white">{{ rtrim(rtrim($materia_prima->stock_actual, '0'), '.') }} {{ $materia_prima->unidad }}</span></p>
            <p class="text-xs text-gray-400 mt-1">Última actualización: {{ $materia_prima->updated_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="overflow-x-auto bg-black/30 rounded-xl border border-white/10">
            <table class="w-full text-sm">
                <thead class="bg-white/5 text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-center">Cantidad</th>
                        <th class="px-4 py-3 text-left">Motivo</th>
                        <th class="px-4 py-3 text-left">Costo unitario</th>
                        <th class="px-4 py-3 text-left">Lote</th>
                        <th class="px-4 py-3 text-left">Vencimiento</th>
                        <th class="px-4 py-3 text-left">Usuario</th>
                        <th class="px-4 py-3 text-left">Observaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($movimientos as $mov)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs @if($mov->tipo == 'entrada') bg-green-500/20 text-green-300 @else bg-red-500/20 text-red-300 @endif">
                                {{ ucfirst($mov->tipo) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">{{ rtrim(rtrim($mov->cantidad, '0'), '.') }} {{ $materia_prima->unidad }}</td>
                        <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $mov->motivo)) }}</td>
                        <td class="px-4 py-2">${{ rtrim(rtrim($mov->costo_unitario_momento, '0'), '.') }}</td>
                        <td class="px-4 py-2 font-mono text-slate-400">{{ $mov->lote_compra ?? '—' }}</td>
                        <td class="px-4 py-2 text-slate-400">{{ $mov->fecha_vencimiento ? \Carbon\Carbon::parse($mov->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                        <td class="px-4 py-2">{{ $mov->usuario->name ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $mov->observaciones ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-400">No hay movimientos registrados.@{ }
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $movimientos->links() }}
        </div>
    </div>
</div>
@endsection