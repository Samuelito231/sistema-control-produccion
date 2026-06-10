@extends('components.panel')

@section('content')
<div class="p-8 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Recetario</h1>
            <p class="text-gray-400 mt-1">Configuración de insumos para: <span class="text-[#e7c095] font-semibold">{{ $producto->nombre ?? 'Producto' }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('recetas.todas') }}" 
               class="bg-[#e7c095]/10 border border-[#e7c095]/30 text-[#e7c095] px-6 py-2 rounded-xl font-medium hover:bg-[#e7c095]/20 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">receipt_long</span>
                Ver todas las recetas
            </a>
            <a href="{{ route('inventario') }}" 
               class="bg-white/5 border border-white/10 text-white px-6 py-2 rounded-xl font-medium hover:bg-white/10 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Volver
            </a>
        </div>
    </div>

    @if(!$producto)
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-6 rounded-2xl text-center">
            Producto no encontrado.
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-black/40 border border-white/5 p-4 rounded-2xl">
                <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">SKU</p>
                <p class="text-white font-mono mt-1">{{ $producto->sku ?? '---' }}</p>
            </div>
            <div class="bg-black/40 border border-white/5 p-4 rounded-2xl">
                <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Stock Actual</p>
                <p class="text-white font-semibold mt-1">{{ number_format($producto->stock_actual, 2) }} <span class="text-xs text-gray-500">{{ $producto->unidad }}</span></p>
            </div>
            <div class="bg-black/40 border border-white/5 p-4 rounded-2xl">
                <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Stock Mínimo</p>
                <p class="text-white font-semibold mt-1">{{ number_format($producto->stock_minimo, 2) }} <span class="text-xs text-gray-500">{{ $producto->unidad }}</span></p>
            </div>
            <div class="bg-black/40 border border-white/5 p-4 rounded-2xl">
                <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Recetas Asignadas</p>
                <p class="text-white font-semibold mt-1">{{ $recetas->count() }}</p>
            </div>
        </div>

        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-6 mb-8">
            <h2 class="text-lg font-bold text-[#e7c095] mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined">add_circle</span> Agregar insumo a la receta
            </h2>
            <form action="{{ route('recetas.store', $producto) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Materia Prima</label>
                    <select name="materia_prima_id" required class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none transition">
                        <option value="">Seleccionar materia prima...</option>
                        @foreach($materiasPrimas as $mp)
                            <option value="{{ $mp->id }}" 
                                    data-stock="{{ $mp->stock_actual }}" 
                                    data-unidad="{{ $mp->unidad }}">
                                {{ $mp->nombre }} (Stock: {{ $mp->stock_actual }} {{ $mp->unidad }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Cantidad Necesaria</label>
                    <input type="number" step="0.01" name="cantidad_necesaria" required class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none" placeholder="0.00">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-3 rounded-xl hover:shadow-[0_0_20px_rgba(231,192,149,0.3)] transition-all">
                        + Agregar Insumo
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">Insumo</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">Cantidad</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">Unidad</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">Stock Disponible</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recetas as $receta)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-white font-medium">{{ $receta->materiaPrima->nombre ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-300 font-mono">{{ number_format($receta->cantidad_necesaria, 2) }}</td>
                        <td class="px-6 py-4 text-gray-400">{{ $receta->unidad ?? $receta->materiaPrima->unidad ?? 'kg' }}</td>
                        <td class="px-6 py-4">
                            @if($receta->materiaPrima)
                                <span class="{{ $receta->materiaPrima->stock_actual < $receta->cantidad_necesaria ? 'text-red-400' : 'text-green-400' }} font-mono">
                                    {{ number_format($receta->materiaPrima->stock_actual, 2) }} {{ $receta->materiaPrima->unidad }}
                                </span>
                            @else
                                <span class="text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('recetas.destroy', [$producto, $receta->materia_prima_id]) }}" method="POST" onsubmit="return confirm('¿Eliminar este insumo de la receta?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-medium transition flex items-center gap-1 ml-auto">
                                    <span class="material-symbols-outlined text-sm">delete</span> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                            No hay materias primas asignadas a este producto.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection