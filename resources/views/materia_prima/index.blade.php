@extends('components.panel')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto space-y-8 animate-in fade-in duration-700">
    
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Materia Prima</h1>
            <p class="text-gray-400 text-sm">Gestión analítica de activos y existencias en tiempo real</p>
        </div>
        
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('materia-prima.create') }}" 
               class="flex items-center gap-2 bg-[#e7c095]/10 border border-[#e7c095]/20 px-6 py-2.5 rounded-xl text-[#e7c095] hover:bg-[#e7c095]/20 transition-all font-bold text-xs uppercase tracking-widest">
                <span class="material-symbols-outlined text-sm">add</span> Nueva Referencia
            </a>
        @endif
    </header>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $stats = [
                ['Total Referencias', $materias->total(), 'ítems', 'inventory'],
                ['Stock Total', number_format($stockTotal, 2), 'unidades', 'box'],
                ['Valor Activos', '$' . number_format($valorTotal, 2), 'inversión', 'analytics']
            ];
        @endphp
        
        @foreach($stats as $stat)
        <div class="bg-black/40 backdrop-blur-md border border-white/10 p-6 rounded-2xl flex justify-between items-center group hover:border-[#e7c095]/30 transition-all">
            <div>
                <h3 class="text-[10px] font-bold uppercase text-slate-500 mb-2 tracking-widest">{{ $stat[0] }}</h3>
                <span class="text-2xl font-black text-white">{{ $stat[1] }}</span>
                <p class="text-[10px] text-[#e7c095]/80 mt-1 uppercase font-bold">{{ $stat[2] }}</p>
            </div>
            <span class="material-symbols-outlined text-3xl text-white/10 group-hover:text-[#e7c095]/40 transition-colors">{{ $stat[3] }}</span>
        </div>
        @endforeach
    </section>

    <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-white/5 flex justify-between items-center">
            <h3 class="font-bold text-white uppercase text-[10px] tracking-widest">Inventario Actual</h3>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white/5 text-[10px] uppercase tracking-widest text-gray-400">
                <tr>
                    <th class="p-6">Producto</th>
                    <th class="p-6">SKU</th>
                    <th class="p-6">Stock</th>
                    <th class="p-6">Costo</th>
                    <th class="p-6">Vencimiento</th>
                    <th class="p-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($materias as $mp)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gray-500">grass</span>
                            <div class="flex flex-col">
                                <span class="text-white font-semibold">{{ $mp->nombre }}</span>
                                @if($mp->stock_actual <= $mp->stock_minimo)
                                    <span class="text-[9px] uppercase font-bold text-amber-500">Stock bajo</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="p-6 text-gray-400 font-mono">{{ $mp->sku }}</td>
                    <td class="p-6 text-white font-mono font-bold">{{ number_format($mp->stock_actual, 2) }}</td>
                    <td class="p-6 text-white font-mono">${{ number_format($mp->costo_unitario, 2) }}</td>
                    <td class="p-6 text-gray-400 text-xs">{{ $mp->fecha_vencimiento ? \Carbon\Carbon::parse($mp->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                    <td class="p-6 text-right">
                        <div class="flex justify-end gap-2 text-gray-500">
                            <a href="{{ route('materia-prima.movimientos', $mp) }}" class="p-2 hover:text-[#e7c095] transition-colors"><span class="material-symbols-outlined text-lg">history</span></a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('materia-prima.edit', $mp) }}" class="p-2 hover:text-white transition-colors"><span class="material-symbols-outlined text-lg">edit</span></a>
                                <form action="{{ route('materia-prima.destroy', $mp) }}" method="POST" onsubmit="return confirm('¿Seguro?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 hover:text-red-400 transition-colors"><span class="material-symbols-outlined text-lg">delete</span></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-10 text-center text-gray-600 italic">No existen registros disponibles.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4">
        {{ $materias->links() }}
    </div>
</div>
@endsection