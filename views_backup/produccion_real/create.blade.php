@extends('components.panel')

@section('content')

    <div class="p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-[#e7c095] mb-4">Registrar nueva producción</h1>
        <p class="text-gray-400 text-sm mb-6">Al registrar una producción, se descontará automáticamente la materia prima necesaria según la receta del producto y se aumentará el stock del producto terminado.</p>

        <form action="{{ route('produccion_real.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-gray-300 mb-1">Producto terminado *</label>
                <select name="producto_id" id="producto_id" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                    <option value="">Seleccione un producto</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id }}" data-receta="{{ $prod->recetas->isNotEmpty() ? '1' : '0' }}">{{ $prod->nombre }} (SKU: {{ $prod->sku }})</option>
                    @endforeach
                </select>
                <p id="sinRecetaMsg" class="text-red-400 text-xs hidden mt-1">⚠️ Este producto no tiene receta definida. Defina primero sus ingredientes en Recetas.</p>
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Cantidad producida * ({{ $producto->unidad ?? 'uds/kg' }})</label>
                <input type="number" step="any" name="cantidad_producida" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Fecha de producción *</label>
                <input type="date" name="fecha_produccion" value="{{ date('Y-m-d') }}" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Lote (opcional)</label>
                <input type="text" name="lote" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div>
                <label class="block text-gray-300 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-2 px-6 rounded-full shadow-lg">Registrar producción</button>
                <a href="{{ route('produccion_real.historial') }}" class="bg-white/10 hover:bg-white/20 text-white py-2 px-6 rounded-full transition">Ver historial</a>
            </div>
        </form>
    </div>

    <script>
        const productoSelect = document.getElementById('producto_id');
        const sinRecetaMsg = document.getElementById('sinRecetaMsg');
        productoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const tieneReceta = selectedOption.getAttribute('data-receta') === '1';
            if (!tieneReceta) {
                sinRecetaMsg.classList.remove('hidden');
            } else {
                sinRecetaMsg.classList.add('hidden');
            }
        });
    </script>
@endsection
