@extends('components.panel')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-[#e7c095]">Registrar nueva producción</h1>
        <p class="text-gray-400 text-sm">Gestiona la producción, el consumo de insumos y el control de calidad en un solo paso.</p>
    </div>

    <form action="{{ route('produccion_real.store') }}" method="POST" class="space-y-6" id="produccionForm">
        @csrf

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="text-[#e7c095] font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">inventory_2</span> Producto y Cantidades
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-gray-300 text-xs font-bold uppercase mb-1">Producto terminado *</label>
                    <select name="producto_id" id="producto_id" required class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none">
                        <option value="">Seleccione un producto</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id }}" data-receta="{{ $prod->recetas->isNotEmpty() ? '1' : '0' }}"
                                    data-receta-detalle="{{ json_encode($prod->recetas->map(fn($r) => ['nombre' => $r->materiaPrima->nombre, 'cantidad' => $r->cantidad_necesaria])) }}">
                                {{ $prod->nombre }} (SKU: {{ $prod->sku }})
                            </option>
                        @endforeach
                    </select>
                    <p id="sinRecetaMsg" class="text-red-400 text-xs mt-2 hidden">⚠️ Este producto no tiene receta definida.</p>
                </div>

                <div>
                    <label class="block text-gray-300 text-xs font-bold uppercase mb-1">Cantidad ELABORADA *</label>
                    <input type="number" step="any" name="cantidad_producida" id="cantidad_producida" required class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-xs font-bold uppercase mb-1">Cantidad DESECHADA</label>
                    <input type="number" step="any" name="producto_desechado" id="producto_desechado" value="0" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-white">
                </div>
                <div class="flex items-end">
                    <div class="w-full bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-3 text-center">
                        <p class="text-[10px] text-yellow-500 uppercase font-bold">Eficiencia</p>
                        <p class="text-xl text-yellow-100 font-bold"><span id="eficienciaEstimada">0</span>%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <h3 class="text-[#e7c095] font-semibold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">restaurant_menu</span> Receta y Consumo
                </h3>
                <div id="recetaDetalle" class="text-sm text-gray-400 max-h-40 overflow-y-auto">
                    <p class="text-gray-500 italic">Seleccione un producto para cargar su receta...</p>
                </div>
                <div class="mt-4 pt-4 border-t border-white/10">
                    <label class="block text-gray-300 text-xs font-bold uppercase mb-1">MP Consumida Real</label>
                    <input type="number" step="any" name="mp_consumida_real" id="mp_consumida_real" class="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-white">
                </div>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
                <h3 class="text-[#e7c095] font-semibold mb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">edit_note</span> Registro y Notas
                </h3>
                <div>
                    <label class="block text-gray-300 text-xs font-bold uppercase mb-1">Lote</label>
                    <input type="text" name="lote" class="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-white" placeholder="Ej: LOTE-2025-001">
                </div>
                <div>
                    <label class="block text-gray-300 text-xs font-bold uppercase mb-1">Fecha *</label>
                    <input type="date" name="fecha_produccion" value="{{ date('Y-m-d') }}" required class="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-xs font-bold uppercase mb-1">Observaciones</label>
                    <textarea name="observaciones" rows="2" class="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-white"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('produccion_real.historial') }}" class="text-gray-400 hover:text-white px-6 py-2 transition">Cancelar</a>
            <button type="submit" class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-2.5 px-8 rounded-full shadow-lg hover:shadow-orange-500/20 transition-all">
                Registrar producción
            </button>
        </div>
    </form>
</div>

<script>
    const productoSelect = document.getElementById('producto_id');
    const sinRecetaMsg = document.getElementById('sinRecetaMsg');
    const recetaDetalle = document.getElementById('recetaDetalle');
    const cantidadProducida = document.getElementById('cantidad_producida');
    const productoDesechado = document.getElementById('producto_desechado');
    const eficienciaSpan = document.getElementById('eficienciaEstimada');
    const mpConsumidaReal = document.getElementById('mp_consumida_real');
    let recetaData = {};

    function calcularEficiencia() {
        const elaborado = parseFloat(cantidadProducida.value) || 0;
        const desechado = parseFloat(productoDesechado.value) || 0;
        const total = elaborado + desechado;
        const eficiencia = total > 0 ? (elaborado / total) * 100 : 0;
        eficienciaSpan.textContent = eficiencia.toFixed(1);
        
        // Calcular MP consumida teórica
        if (recetaData.recetas && elaborado > 0) {
            let totalMp = 0;
            recetaData.recetas.forEach(r => {
                totalMp += r.cantidad * elaborado;
            });
            mpConsumidaReal.value = totalMp.toFixed(2);
        }
    }

    productoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const tieneReceta = selectedOption.getAttribute('data-receta') === '1';
        const recetaJson = selectedOption.getAttribute('data-receta-detalle');
        
        if (!tieneReceta) {
            sinRecetaMsg.classList.remove('hidden');
            recetaDetalle.innerHTML = '<p class="text-red-400 text-sm">⚠️ Este producto no tiene receta definida</p>';
            recetaData = {};
            return;
        }
        
        sinRecetaMsg.classList.add('hidden');
        
        if (recetaJson) {
            try {
                const recetas = JSON.parse(recetaJson);
                recetaData = { producto_id: selectedOption.value, recetas: recetas };
                
                let html = '<table class="w-full text-sm"><thead><tr class="text-left border-b border-white/10"><th class="py-2">Insumo</th><th class="py-2">Cant./u</th></tr></thead><tbody>';
                recetas.forEach(r => {
                    html += `<tr class="border-b border-white/5"><td class="py-2 text-white">${r.nombre}</td><td class="py-2">${r.cantidad}</td></tr>`;
                });
                html += '</tbody></table>';
                recetaDetalle.innerHTML = html;
                calcularEficiencia();
            } catch(e) {
                console.error('Error procesando receta', e);
            }
        }
    });
    
    cantidadProducida.addEventListener('input', calcularEficiencia);
    productoDesechado.addEventListener('input', calcularEficiencia);
</script>
@endsection