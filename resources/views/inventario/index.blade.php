@php use SimpleSoftwareIO\QrCode\Facades\QrCode; @endphp

@extends('components.panel')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-8">

    <div class="flex justify-between items-end">
        <h1 class="text-3xl font-black text-white tracking-tighter">Control de Inventario</h1>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('productos.create') }}" class="bg-[#e7c095] text-black font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-[#e7c095]/20 hover:scale-[1.02] transition-transform flex items-center gap-2">
                <span class="material-symbols-outlined">add</span> Nuevo Producto
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $kpis = [
                ['label' => 'Total Productos', 'value' => $productos->total(), 'icon' => 'inventory', 'color' => 'text-[#e7c095]'],
                ['label' => 'Stock Total', 'value' => rtrim(rtrim($stockTotal, '0'), '.'), 'icon' => 'box', 'color' => 'text-white'],
                ['label' => 'Stock Crítico', 'value' => $productos->filter(fn($p) => $p->stock_actual <= $p->stock_minimo)->count(), 'icon' => 'warning', 'color' => 'text-red-400'],
                ['label' => 'Valor Inventario', 'value' => '$' . number_format($valorTotal, 2), 'icon' => 'payments', 'color' => 'text-green-400']
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-black/40 backdrop-blur-md border border-white/5 rounded-2xl p-6">
            <div class="flex justify-between items-start mb-2">
                <p class="text-[10px] font-bold uppercase text-gray-500 tracking-widest">{{ $kpi['label'] }}</p>
                <span class="material-symbols-outlined {{ $kpi['color'] }}">{{ $kpi['icon'] }}</span>
            </div>
            <p class="text-3xl font-black text-white">{{ $kpi['value'] }}</p>
        </div>
        @endforeach
    </div>

    <form method="GET" action="{{ route('inventario') }}" class="bg-black/20 p-4 rounded-2xl border border-white/5 flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Buscar por nombre o SKU..." value="{{ request('search') }}"
                   class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white placeholder:text-gray-600 focus:border-[#e7c095] outline-none transition">
        </div>
        <select name="categoria" class="bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white outline-none">
            <option value="">Todas las categorías</option>
            <option value="Snacks naturales" {{ request('categoria') == 'Snacks naturales' ? 'selected' : '' }}>Snacks naturales</option>
            <option value="Galletas horneadas" {{ request('categoria') == 'Galletas horneadas' ? 'selected' : '' }}>Galletas horneadas</option>
        </select>
        <button type="submit" class="bg-white/10 text-white font-bold py-3 px-6 rounded-xl hover:bg-white/20 transition">Filtrar</button>
        <a href="{{ route('inventario') }}" class="text-gray-500 hover:text-white transition px-4">Limpiar</a>
    </form>

    <div class="bg-black/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-white/5 text-[10px] uppercase tracking-widest text-gray-400">
                <tr>
                    <th class="px-6 py-4">Producto</th>
                    <th class="px-6 py-4">Stock</th>
                    <th class="px-6 py-4 text-center">Estado</th>
                    <th class="px-6 py-4 text-center">QR</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($productos as $producto)
                <tr class="hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4">
                        <p class="font-bold text-white">{{ $producto->nombre }}</p>
                        <p class="text-xs text-gray-500 font-mono">{{ $producto->sku }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-lg {{ $producto->stock_actual <= $producto->stock_minimo ? 'text-red-400' : 'text-white' }}">
                                {{ (float)$producto->stock_actual }}
                            </span>
                            <div class="w-24 h-1.5 bg-white/10 rounded-full overflow-hidden">
                                @php $pct = min(100, ($producto->stock_actual / max($producto->stock_minimo, 1)) * 100); @endphp
                                <div class="h-full {{ $pct < 50 ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-[10px] px-2 py-1 rounded-full border {{ $producto->stock_actual <= $producto->stock_minimo ? 'border-red-500/30 text-red-400' : 'border-green-500/30 text-green-400' }}">
                            {{ $producto->stock_actual <= $producto->stock_minimo ? 'CRÍTICO' : 'NORMAL' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if(in_array(auth()->user()->role, ['admin', 'operario']))
                            <div class="opacity-60 group-hover:opacity-100 transition-opacity">
                                {!! QrCode::size(40)->color(255,255,255)->backgroundColor(0,0,0,0)->generate(route('produccion.rapida', $producto->id)) !!}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('productos.mermas', $producto->id) }}" class="text-gray-400 hover:text-[#e7c095]"><span class="material-symbols-outlined text-sm">visibility</span></a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('productos.edit', $producto->id) }}" class="text-gray-400 hover:text-white"><span class="material-symbols-outlined text-sm">edit</span></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-500">No hay productos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection