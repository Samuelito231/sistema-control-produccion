@extends('components.panel')

@section('content')

    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-[#e7c095]">Receta: {{ $producto->nombre }}</h1>
            <a href="{{ route('inventario') }}" class="text-gray-400 hover:text-white">← Volver a inventario</a>
        </div>

        <h2 class="text-lg font-semibold text-white mb-2">Ingredientes</h2>
        <div class="overflow-x-auto bg-black/30 rounded-xl border border-white/10">
            <table class="w-full text-sm">
                <thead class="bg-white/5">
                    <tr><th>Materia prima</th><th>Cantidad por unidad</th><th>Unidad</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse($recetas as $receta)
                    <tr>
                        <td>{{ $receta->materiaPrima->nombre }}</td>
                        <td>{{ $receta->cantidad_necesaria }} {{ $receta->materiaPrima->unidad }}</td>
                        <td>{{ $receta->materiaPrima->unidad }}</td>
                        <td>
                            <form action="{{ route('recetas.destroy', [$producto, $receta->materiaPrima->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar ingrediente?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-gray-400 py-4">No hay ingredientes definidos para este producto.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 bg-black/30 rounded-xl p-4">
            <h2 class="text-lg font-semibold text-white mb-2">Agregar/Editar ingrediente</h2>
            <form action="{{ route('recetas.store', $producto) }}" method="POST" class="flex gap-4 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-gray-300 text-sm">Materia prima</label>
                    <select name="materia_prima_id" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2">
                        <option value="">Seleccionar</option>
                        @foreach($materiasPrimas as $mp)
                            <option value="{{ $mp->id }}">{{ $mp->nombre }} ({{ $mp->unidad }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-gray-300 text-sm">Cantidad necesaria (por unidad de producto)</label>
                    <input type="number" step="any" name="cantidad_necesaria" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2">
                </div>
                <div>
                    <button type="submit" class="bg-[#e7c095]/20 text-[#e7c095] px-4 py-2 rounded-lg">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
