@extends('components.panel')

@section('content')
<div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-8">
        <div class="rounded-3xl border border-white/10 bg-black/20 shadow-[0_0_40px_rgba(0,0,0,0.15)]">
            <div class="p-8" id="materia-prima-content">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#e7c095] tracking-tight">Materia Prima</h1>
                <p class="text-slate-400 mt-1">Control integral de inventario y activos</p>
            </div>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('materia-prima.create') }}" 
               class="bg-[#e7c095] hover:bg-[#d4ad85] text-black px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-[0_0_20px_rgba(231,192,149,0.15)] active:scale-[0.98]">
                + Nueva Materia Prima
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5">
            <p class="text-slate-500 text-[10px] uppercase tracking-widest font-bold mb-1">Total Referencias</p>
            <p class="text-3xl font-light text-white">{{ $materias->total() }}</p>
        </div>
        <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5">
            <p class="text-slate-500 text-[10px] uppercase tracking-widest font-bold mb-1">Stock Total</p>
            <p class="text-3xl font-light text-white">{{ number_format($stockTotal, 2) }}</p>
        </div>
        <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5">
            <p class="text-slate-500 text-[10px] uppercase tracking-widest font-bold mb-1">Valoración Activos</p>
            <p class="text-3xl font-light text-white">${{ number_format($valorTotal, 2) }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-white/10 bg-black/40">
        <table class="w-full text-sm text-left">
            <thead class="bg-white/5 text-slate-400 uppercase text-[10px] tracking-widest font-bold">
                <tr>
                    <th class="px-6 py-4">Producto</th>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4 text-right">Stock</th>
                    <th class="px-6 py-4 text-right">Costo</th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-slate-300">
                @forelse($materias as $mp)
                <tr class="group hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-5">
                        <div class="font-medium text-white">{{ $mp->nombre }}</div>
                        @if($mp->stock_actual <= $mp->stock_minimo)
                            <span class="inline-flex items-center gap-1.5 mt-1.5 px-2 py-0.5 rounded bg-[#e7c095]/10 text-[#e7c095] text-[9px] font-bold uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#e7c095] animate-pulse"></span> Stock Crítico
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-5 font-mono text-slate-500">{{ $mp->sku }}</td>
                    <td class="px-6 py-5 text-right font-mono">{{ number_format($mp->stock_actual, 2) }}</td>
                    <td class="px-6 py-5 text-right font-mono">${{ number_format($mp->costo_unitario, 2) }}</td>
                    <td class="px-6 py-5 text-center">
                        <div class="flex items-center justify-center gap-4 text-xs font-semibold opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('materia-prima.movimientos', $mp) }}" class="text-blue-400 hover:text-blue-300">Log</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('materia-prima.edit', $mp) }}" class="text-slate-300 hover:text-white">Editar</a>
                                <form action="{{ route('materia-prima.destroy', $mp) }}" method="POST" onsubmit="return confirm('¿Eliminar permanentemente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-300">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-600 italic">No hay materias primas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 pagination-links">
        {{ $materias->links() }}
    </div>
    </div>
</div>
@endsection
