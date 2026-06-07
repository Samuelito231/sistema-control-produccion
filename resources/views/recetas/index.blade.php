@extends('components.panel')

@section('content')
<div class="p-8 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Recetario</h1>
            <p class="text-gray-400 mt-1">Configuración de insumos para: <span class="text-[#e7c095] font-semibold">{{ $producto->nombre ?? 'Producto' }}</span></p>
        </div>
        <a href="{{ route('inventario') }}" 
           class="bg-white/5 border border-white/10 text-white px-6 py-2 rounded-xl font-medium hover:bg-white/10 transition flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Volver
        </a>
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
        </div>

        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-6 mb-8">
            <h2 class="text-lg font-bold text-[#e7c095] mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined">add_circle</span> Nueva asignación
            </h2>
            <form action="{{ route('recetas.store', $producto) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Materia Prima</label>
                    <select name="materia_prima_id" required class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none transition">
                        <option value="">Seleccionar materia prima...</option>
                        @foreach($materiasPrimas as $mp)
                            <option value="{{ $mp->id }}">{{ $mp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Cantidad</label>
                    <input type="number" step="0.01" name="cantidad" required class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none" placeholder="0.00">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-3 rounded-xl hover:shadow-[0_0_20px_rgba(231,192,149,0.3)] transition-all">
                        Agregar Insumo
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
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recetas as $receta)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-white font-medium">{{ $receta->materiaPrima->nombre ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-300 font-mono">{{ $receta->cantidad }}</td>
                        <td class="px-6 py-4 text-gray-400">{{ $receta->unidad ?? $receta->materiaPrima->unidad ?? '---' }}</td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('recetas.destroy', [$producto, $receta->materia_prima_id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-medium transition">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No hay materias primas asignadas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection