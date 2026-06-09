@extends('components.panel')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-[#c29e75] bg-clip-text text-transparent">
            Trazabilidad
        </h1>
        <p class="text-gray-400 text-sm mt-1">Historial completo de movimientos de productos y materia prima</p>
    </div>

    <!-- Buscador -->
    <div class="bg-black/40 rounded-lg border border-white/10 p-6 mb-8">
        <form method="GET" class="space-y-4">
            <div class="flex flex-wrap gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Tipo</label>
                    <select name="tipo" class="bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                        <option value="producto" {{ $tipo == 'producto' ? 'selected' : '' }}>Producto Terminado</option>
                        <option value="materia_prima" {{ $tipo == 'materia_prima' ? 'selected' : '' }}>Materia Prima</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[300px]">
                    <label class="block text-gray-300 text-sm mb-1">Buscar</label>
                    <div class="relative">
                        <input type="text" id="search-input" 
                               placeholder="Buscar por nombre o SKU..."
                               class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#e7c095] transition">
                        <div id="search-results" class="absolute z-10 w-full mt-1 bg-gray-900 border border-white/10 rounded-lg hidden"></div>
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-[#e7c095]/20 text-[#e7c095] px-4 py-2 rounded-lg hover:bg-[#e7c095]/30 transition">
                        Buscar
                    </button>
                </div>
            </div>
            <input type="hidden" name="buscar" id="selected-id" value="{{ request('buscar') }}">
        </form>
    </div>

    @if($producto || $materiaPrima)
    <!-- Resumen del producto -->
    <div class="bg-gradient-to-r from-[#e7c095]/10 to-transparent rounded-lg p-6 mb-8 border border-[#e7c095]/30">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-white">
                    {{ $producto ? $producto->nombre : ($materiaPrima ? $materiaPrima->nombre : '') }}
                </h2>
                <p class="text-gray-400">SKU: {{ $producto ? $producto->sku : ($materiaPrima ? $materiaPrima->sku : '') }}</p>
                <p class="text-gray-400">Stock actual: 
                    <span class="font-bold text-[#e7c095]">{{ $producto ? $producto->stock_actual : ($materiaPrima ? $materiaPrima->stock_actual : 0) }}</span>
                    {{ $producto ? $producto->unidad : ($materiaPrima ? $materiaPrima->unidad : 'kg') }}
                </p>
            </div>
            <div>
                <span class="px-3 py-1 bg-[#e7c095]/20 text-[#e7c095] rounded-full text-sm">
                    {{ $producto ? 'Producto Terminado' : 'Materia Prima' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Tabla de trazabilidad -->
    <div class="bg-black/30 rounded-lg border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr class="text-left text-gray-400 text-sm">
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Tipo</th>
                        <th class="p-3">Concepto</th>
                        <th class="p-3">Detalle</th>
                        <th class="p-3">Cantidad</th>
                        <th class="p-3">Stock Resultante</th>
                        <th class="p-3">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trazabilidad as $mov)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="p-3 text-sm">{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $mov->tipo == 'entrada' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $mov->tipo == 'entrada' ? 'Entrada +' : 'Salida -' }}
                            </span>
                        </td>
                        <td class="p-3">{{ $mov->concepto }}</td>
                        <td class="p-3 text-sm text-gray-300">{{ $mov->detalle }}</td>
                        <td class="p-3 font-mono">
                            {{ $mov->cantidad }} {{ $producto ? ($producto->unidad ?? 'kg') : ($materiaPrima ? $materiaPrima->unidad : 'kg') }}
                        </td>
                        <td class="p-3 font-mono font-bold text-[#e7c095]">{{ $mov->stock_resultante }}</td>
                        <td class="p-3 text-sm text-gray-400">{{ $mov->usuario }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">
                            No hay movimientos registrados para este {{ $producto ? 'producto' : 'material' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($trazabilidad->count() > 0)
                <tfoot class="bg-white/5">
                    <tr>
                        <td colspan="4" class="p-3 text-right font-bold">Total movimientos: </td>
                        <td colspan="3" class="p-3 font-bold text-[#e7c095]">{{ $trazabilidad->count() }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @elseif(request('buscar'))
    <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-6 text-center">
        <span class="material-symbols-outlined text-4xl text-red-400">search</span>
        <p class="text-red-400 mt-2">No se encontró ningún {{ $tipo == 'producto' ? 'producto' : 'material' }} con ese ID</p>
    </div>
    @else
    <div class="bg-black/30 rounded-lg border border-white/10 p-12 text-center">
        <span class="material-symbols-outlined text-5xl text-gray-600">timeline</span>
        <h3 class="text-xl font-semibold text-white mt-4">Buscar trazabilidad</h3>
        <p class="text-gray-400 mt-2">Seleccione un producto o materia prima para ver su historial completo</p>
    </div>
    @endif
</div>

<script>
    const searchInput = document.getElementById('search-input');
    const resultsDiv = document.getElementById('search-results');
    const selectedIdInput = document.getElementById('selected-id');
    const tipoSelect = document.querySelector('select[name="tipo"]');
    const form = document.querySelector('form');

    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            const tipo = tipoSelect.value;
            
            fetch(`/trazabilidad/buscar?tipo=${tipo}&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        resultsDiv.innerHTML = data.map(item => `
                            <div class="px-4 py-2 hover:bg-white/10 cursor-pointer border-b border-white/5 last:border-0"
                                 onclick="seleccionarResultado(${item.id}, '${item.nombre} (${item.sku})')">
                                <div class="font-medium text-white">${item.nombre}</div>
                                <div class="text-xs text-gray-400">SKU: ${item.sku}</div>
                            </div>
                        `).join('');
                        resultsDiv.classList.remove('hidden');
                    } else {
                        resultsDiv.innerHTML = '<div class="px-4 py-2 text-gray-400">No se encontraron resultados</div>';
                        resultsDiv.classList.remove('hidden');
                    }
                });
        }, 300);
    });

    function seleccionarResultado(id, nombre) {
        searchInput.value = nombre;
        selectedIdInput.value = id;
        resultsDiv.classList.add('hidden');
        form.submit();
    }

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
    });
</script>
@endsection