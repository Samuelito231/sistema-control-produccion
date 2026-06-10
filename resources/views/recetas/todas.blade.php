@extends('components.panel')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-[#c29e75] bg-clip-text text-transparent">
                Todas las Recetas
            </h1>
            <p class="text-gray-400 text-sm mt-1">Gestión completa de recetas del sistema</p>
        </div>
        <a href="{{ route('inventario') }}" 
           class="bg-gray-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-700 transition">
            ← Volver a Inventario
        </a>
    </div>

    <!-- Formulario para agregar nueva receta -->
    <div class="bg-black/40 rounded-lg border border-white/10 p-6 mb-8">
        <h2 class="text-xl font-bold text-[#e7c095] mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined">add_box</span>
            Agregar Nueva Receta
        </h2>
        <form action="{{ route('recetas.storeGeneral') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-gray-300 text-sm mb-1">Producto *</label>
                <select name="producto_id" required class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#e7c095] transition">
                    <option value="">Seleccionar producto...</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }} ({{ $producto->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Materia Prima *</label>
                <select name="materia_prima_id" required class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#e7c095] transition">
                    <option value="">Seleccionar materia prima...</option>
                    @foreach($materiasPrimas as $mp)
                        <option value="{{ $mp->id }}">{{ $mp->nombre }} (Stock: {{ $mp->stock_actual }} {{ $mp->unidad }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Cantidad Necesaria *</label>
                <input type="number" step="0.01" name="cantidad_necesaria" required 
                       placeholder="Ej: 0.5" 
                       class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#e7c095] transition">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold px-4 py-2 rounded-lg w-full hover:shadow-lg transition flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Agregar Receta
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de todas las recetas -->
    <div class="bg-black/30 rounded-lg border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr class="text-left text-gray-400 text-sm">
                        <th class="p-3">Producto</th>
                        <th class="p-3">Materia Prima</th>
                        <th class="p-3">Cantidad Necesaria</th>
                        <th class="p-3">Unidad</th>
                        <th class="p-3">Stock Disponible</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Acciones</th>
                    
                </thead>
                <tbody>
                    @forelse($recetas as $receta)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="p-3 font-medium text-white">
                            {{ $receta->producto->nombre ?? 'N/A' }}
                            <span class="text-xs text-gray-500 block">{{ $receta->producto->sku ?? '' }}</span>
                        
                        <td class="p-3">{{ $receta->materiaPrima->nombre ?? 'N/A' }}比
                        <td class="p-3 font-mono">{{ number_format($receta->cantidad_necesaria, 2) }}
                        <td class="p-3">{{ $receta->unidad ?? $receta->materiaPrima->unidad ?? 'kg' }}
                        <td class="p-3">
                            @if($receta->materiaPrima)
                                <span class="{{ $receta->materiaPrima->stock_actual < $receta->cantidad_necesaria ? 'text-red-400' : 'text-green-400' }} font-mono">
                                    {{ number_format($receta->materiaPrima->stock_actual, 2) }} {{ $receta->materiaPrima->unidad }}
                                </span>
                            @else
                                <span class="text-gray-500">N/A</span>
                            @endif
                        
                        <td class="p-3">
                            @if($receta->materiaPrima)
                                @if($receta->materiaPrima->stock_actual < $receta->cantidad_necesaria)
                                    <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-semibold">
                                        Stock Insuficiente
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">
                                        Stock Suficiente
                                    </span>
                                @endif
                            @else
                                <span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded-full text-xs font-semibold">
                                    Sin datos
                                </span>
                            @endif
                        
                        <td class="p-3">
                            <form action="{{ route('recetas.destroyGeneral', [$receta->producto_id, $receta->materia_prima_id]) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta receta?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 transition flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Eliminar
                                </button>
                            </form>
                        
                    
                    @empty
                    <tr class="border-b border-white/5">
                        <td colspan="7" class="p-12 text-center">
                            <span class="material-symbols-outlined text-5xl text-gray-600">receipt</span>
                            <p class="text-gray-500 mt-2">No hay recetas registradas en el sistema</p>
                            <p class="text-xs text-gray-600 mt-1">Agrega tu primera receta usando el formulario superior</p>
                        
                    
                    @endforelse
                </tbody>
                @if($recetas->count() > 0)
                <tfoot class="bg-white/5">
                    <tr>
                        <td colspan="7" class="p-3 text-right text-gray-400">
                            Total de recetas: <span class="font-bold text-[#e7c095]">{{ $recetas->count() }}</span>
                        
                    
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection