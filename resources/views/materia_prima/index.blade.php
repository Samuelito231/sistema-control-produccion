@extends('components.panel')

@section('content')
<div class="p-8">
    <!-- Encabezado -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-bold text-[#e7c095] tracking-tight">Materia Prima</h1>
            <p class="text-slate-400 mt-1">Control integral de inventario y activos</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('materia-prima.create') }}" 
               class="bg-[#e7c095] hover:bg-[#d4ad85] text-black px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-[0_0_15px_rgba(231,192,149,0.2)]">
                + Nueva Materia Prima
            </a>
        @endif
    </div>

    <!-- Tarjetas de métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-gradient-to-br from-white/5 to-black/40 border border-white/10 rounded-2xl p-6 backdrop-blur-sm">
            <p class="text-slate-500 text-xs uppercase tracking-widest font-semibold mb-2">Total Referencias</p>
            <p class="text-4xl font-light text-white">{{ $materias->total() }}</p>
        </div>
        <div class="bg-gradient-to-br from-white/5 to-black/40 border border-white/10 rounded-2xl p-6 backdrop-blur-sm">
            <p class="text-slate-500 text-xs uppercase tracking-widest font-semibold mb-2">Stock Total</p>
            <p class="text-4xl font-light text-white">{{ number_format($stockTotal, 2) }}</p>
            <p class="text-[#e7c095] text-xs mt-1">unidades en inventario</p>
        </div>
        <div class="bg-gradient-to-br from-white/5 to-black/40 border border-white/10 rounded-2xl p-6 backdrop-blur-sm">
            <p class="text-slate-500 text-xs uppercase tracking-widest font-semibold mb-2">Valoración Activos</p>
            <p class="text-4xl font-light text-white">${{ number_format($valorTotal, 2) }}</p>
            <p class="text-[#e7c095] text-xs mt-1">valor estimado</p>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-hidden rounded-2xl border border-white/10 bg-black/40 backdrop-blur-sm">
        <table class="w-full text-sm text-left">
            <thead class="bg-white/5 text-slate-400 border-b border-white/10">
                <tr>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[10px]">Producto</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[10px]">SKU</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[10px] text-right">Stock</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[10px] text-right">Costo</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[10px]">Lote Compra</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[10px]">Vencimiento</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[10px] text-center">Gestión</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse($materias as $mp)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-white">{{ $mp->nombre }}</div>
                        @if($mp->stock_actual <= $mp->stock_minimo)
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full bg-red-500/20 text-red-300 text-[10px] font-bold">Stock Crítico</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-mono text-slate-400">{{ $mp->sku }}</td>
                    <td class="px-6 py-4 text-right font-mono text-white">{{ number_format($mp->stock_actual, 2) }}</td>
                    <td class="px-6 py-4 text-right font-mono text-white">${{ number_format($mp->costo_unitario, 2) }}</td>
                    <td class="px-6 py-4 font-mono text-slate-400">{{ $mp->lote_compra ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $mp->fecha_vencimiento ? \Carbon\Carbon::parse($mp->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-3 text-xs">
                            <a href="{{ route('materia-prima.movimientos', $mp) }}" class="text-blue-400 hover:text-blue-300">Log</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('materia-prima.edit', $mp) }}" class="text-slate-300 hover:text-white">Editar</a>
                                <form action="{{ route('materia-prima.destroy', $mp) }}" method="POST" onsubmit="return confirm('¿Eliminar?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                        No hay materias primas registradas.<br>
                        Usa el botón "Nueva Materia Prima" para agregar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $materias->links() }}
    </div>
</div>
@endsection