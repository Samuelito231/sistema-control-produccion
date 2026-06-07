```blade
@php use SimpleSoftwareIO\QrCode\Facades\QrCode; @endphp

@extends('components.panel')

@section('content')
<div class="p-8 space-y-8">

    <!-- Métricas rápidas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Total productos</p>
                <span class="material-symbols-outlined text-2xl text-[#e7c095]">inventory</span>
            </div>
            <p class="text-3xl font-bold text-white mt-2">{{ $productos->total() }}</p>
        </div>
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Stock total</p>
                <span class="material-symbols-outlined text-2xl text-[#e7c095]">box</span>
            </div>
            <p class="text-3xl font-bold text-white mt-2">{{ rtrim(rtrim($stockTotal, '0'), '.') }} kg/uds</p>
        </div>
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Stock crítico</p>
                <span class="material-symbols-outlined text-2xl text-red-400">warning</span>
            </div>
            <p class="text-3xl font-bold text-white mt-2">{{ $productos->filter(fn($p) => $p->stock_actual <= $p->stock_minimo)->count() }}</p>
        </div>
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Valor inventario</p>
                <span class="material-symbols-outlined text-2xl text-[#e7c095]">payments</span>
            </div>
            <p class="text-3xl font-bold text-white mt-2">${{ rtrim(rtrim($valorTotal, '0'), '.') }}</p>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <form method="GET" action="{{ route('inventario') }}" class="space-y-4">
        <div class="flex flex-wrap gap-2">
            <button name="categoria" value="Todos" class="chip px-4 py-1.5 rounded-full text-xs {{ request('categoria') == 'Todos' || !request('categoria') ? 'bg-[#e7c095] text-black' : 'bg-white/5 border border-white/20 text-gray-300' }}">Todos</button>
            <button name="categoria" value="Snacks naturales" class="chip px-4 py-1.5 rounded-full text-xs {{ request('categoria') == 'Snacks naturales' ? 'bg-[#e7c095] text-black' : 'bg-white/5 border border-white/20 text-gray-300' }}">Snacks naturales</button>
            <button name="categoria" value="Galletas horneadas" class="chip px-4 py-1.5 rounded-full text-xs {{ request('categoria') == 'Galletas horneadas' ? 'bg-[#e7c095] text-black' : 'bg-white/5 border border-white/20 text-gray-300' }}">Galletas horneadas</button>
            <button name="categoria" value="Juguetes comestibles" class="chip px-4 py-1.5 rounded-full text-xs {{ request('categoria') == 'Juguetes comestibles' ? 'bg-[#e7c095] text-black' : 'bg-white/5 border border-white/20 text-gray-300' }}">Juguetes comestibles</button>
        </div>
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="relative flex-1 max-w-md">
                <input type="text" name="search" placeholder="Buscar por nombre o SKU..." value="{{ request('search') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-full py-2.5 pl-12 pr-4 text-white">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-white/10 hover:bg-white/20 text-white font-bold py-2.5 px-5 rounded-full">Filtrar</button>
                <a href="{{ route('inventario') }}" class="bg-white/5 hover:bg-white/10 text-gray-300 font-bold py-2.5 px-5 rounded-full">Limpiar</a>
            </div>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('productos.create') }}" class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-2.5 px-6 rounded-full shadow-lg flex items-center gap-2">
                    <span class="material-symbols-outlined">add</span> Nuevo producto
                </a>
            @endif
        </div>
    </form>

    <!-- Tabla de productos -->
    <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-gray-300 text-xs font-semibold uppercase tracking-wider border-b-2 border-[#e7c095]/30">
                    <tr>
                        <th class="px-6 py-4">Producto</th>
                        <th class="px-6 py-4">Categoría</th>
                        <th class="px-6 py-4 text-center">Stock</th>
                        <th class="px-6 py-4">Unidad</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-center">Código QR</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($productos as $producto)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-white">{{ $producto->nombre }}</div>
                            <div class="text-[11px] text-gray-500">SKU: {{ $producto->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $producto->categoria }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="@if($producto->stock_actual <= $producto->stock_minimo) text-red-400 @else text-white @endif font-bold">
                                {{ rtrim(rtrim($producto->stock_actual, '0'), '.') }}
                            </span>
                            <span class="text-[11px] text-gray-500 ml-1">({{ round(($producto->stock_actual / max($producto->stock_minimo, 1)) * 100) }}%)</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ $producto->unidad ?? 'kg' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs @if($producto->stock_actual <= $producto->stock_minimo) bg-red-500/20 text-red-300 @else bg-green-500/20 text-green-300 @endif px-2 py-1 rounded-full">
                                {{ $producto->stock_actual <= $producto->stock_minimo ? 'Crítico' : 'Normal' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(in_array(auth()->user()->role, ['admin', 'operario']))
                                <a href="{{ route('produccion.rapida', $producto->id) }}" target="_blank" class="inline-block" title="Escanear QR para registrar merma rápida">
                                    {!! QrCode::size(48)->generate(route('produccion.rapida', $producto->id)) !!}
                                </a>
                            @else
                                <span class="text-gray-500 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('productos.edit', $producto->id) }}" class="p-1.5 rounded-lg hover:bg-white/10 inline-block" title="Editar producto">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </a>
                            @endif
                            <a href="{{ route('productos.mermas', $producto->id) }}" class="p-1.5 rounded-lg hover:bg-white/10 inline-block" title="Historial de mermas">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('recetas.index', $producto->id) }}" class="p-1.5 rounded-lg hover:bg-white/10 inline-block" title="Receta">
                                    <span class="material-symbols-outlined text-sm">receipt</span>
                                </a>
                                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este producto? Se conservarán sus mermas para el historial.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-white/10 hover:text-red-400 transition" title="Eliminar producto">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            No hay productos terminados registrados.<br>
                            <span class="text-xs">Usa el botón "Nuevo producto" para agregar el primer producto.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-white/10 flex justify-between text-xs text-gray-400">
            <span>Mostrando {{ $productos->firstItem() ?? 0 }} - {{ $productos->lastItem() ?? 0 }} de {{ $productos->total() }} productos</span>
        </div>
    </div>

</div>
@endsection
```