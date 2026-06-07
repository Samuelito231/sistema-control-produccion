@extends('components.panel')

@section('content')
<div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-8 max-w-2xl bg-black/20 border border-white/10 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.15)]">
        <h1 class="text-2xl font-bold text-[#e7c095] mb-6">Editar Producto</h1>

        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-2 rounded-lg mb-4">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('productos.update', $producto->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-300 mb-1">SKU (Código) *</label>
                <input type="text" name="sku" value="{{ old('sku', $producto->sku) }}" required
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-[#e7c095]">
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Categoría *</label>
                <select name="categoria" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                    <option value="Snacks naturales" {{ (old('categoria', $producto->categoria) == 'Snacks naturales') ? 'selected' : '' }}>Snacks naturales</option>
                    <option value="Galletas horneadas" {{ (old('categoria', $producto->categoria) == 'Galletas horneadas') ? 'selected' : '' }}>Galletas horneadas</option>
                    <option value="Juguetes comestibles" {{ (old('categoria', $producto->categoria) == 'Juguetes comestibles') ? 'selected' : '' }}>Juguetes comestibles</option>
                    <option value="Materia prima" {{ (old('categoria', $producto->categoria) == 'Materia prima') ? 'selected' : '' }}>Materia prima</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Stock actual *</label>
                <input type="number" step="any" name="stock_actual" value="{{ old('stock_actual', $producto->stock_actual) }}" required
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Stock mínimo *</label>
                <input type="number" step="any" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Unidad (kg, uds, etc.)</label>
                <input type="text" name="unidad" value="{{ old('unidad', $producto->unidad ?? 'kg') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Precio unitario (opcional)</label>
                <input type="number" step="0.01" name="precio_unitario" value="{{ old('precio_unitario', $producto->precio_unitario ?? 0) }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-2 px-6 rounded-full shadow-lg">
                    Actualizar
                </button>
                <a href="{{ route('inventario') }}" class="bg-white/10 hover:bg-white/20 text-white py-2 px-6 rounded-full transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection