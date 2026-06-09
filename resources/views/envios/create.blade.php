@extends('components.panel')

@section('content')
<div class="p-8 max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-[#c29e75] bg-clip-text text-transparent">
            Nuevo Envío
        </h1>
        <p class="text-gray-400 text-sm mt-1">Registrar salida de productos a clientes o distribuidores</p>
    </div>

    <form action="{{ route('envios.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Datos del destinatario -->
        <div class="bg-black/40 rounded-lg border border-white/10 p-6">
            <h2 class="text-lg font-bold text-[#e7c095] mb-4">Datos del Destinatario</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-300 text-sm mb-1">Nombre *</label>
                    <input type="text" name="destinatario_nombre" required value="{{ old('destinatario_nombre') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#e7c095] transition">
                    @error('destinatario_nombre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Teléfono</label>
                    <input type="text" name="destinatario_telefono" value="{{ old('destinatario_telefono') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Email</label>
                    <input type="email" name="destinatario_email" value="{{ old('destinatario_email') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
            </div>
        </div>

        <!-- Dirección -->
        <div class="bg-black/40 rounded-lg border border-white/10 p-6">
            <h2 class="text-lg font-bold text-[#e7c095] mb-4">Dirección de Entrega</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-300 text-sm mb-1">Dirección *</label>
                    <input type="text" name="direccion" required value="{{ old('direccion') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Ciudad *</label>
                    <input type="text" name="ciudad" required value="{{ old('ciudad') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Municipio *</label>
                    <input type="text" name="municipio" required value="{{ old('municipio') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Estado/Región *</label>
                    <input type="text" name="estado_region" required value="{{ old('estado_region') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Código Postal</label>
                    <input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
            </div>
        </div>

        <!-- Datos del envío -->
        <div class="bg-black/40 rounded-lg border border-white/10 p-6">
            <h2 class="text-lg font-bold text-[#e7c095] mb-4">Datos del Envío</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Fecha de Envío *</label>
                    <input type="date" name="fecha_envio" value="{{ old('fecha_envio', date('Y-m-d')) }}" required
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Fecha Estimada Entrega</label>
                    <input type="date" name="fecha_estimada_entrega" value="{{ old('fecha_estimada_entrega') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Transportista *</label>
                    <select name="transportista" required class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                        <option value="">Seleccionar...</option>
                        <option value="MRW" {{ old('transportista') == 'MRW' ? 'selected' : '' }}>MRW</option>
                        <option value="Domesa" {{ old('transportista') == 'Domesa' ? 'selected' : '' }}>Domesa</option>
                        <option value="Zoom" {{ old('transportista') == 'Zoom' ? 'selected' : '' }}>Zoom</option>
                        <option value="Propio" {{ old('transportista') == 'Propio' ? 'selected' : '' }}>Propio</option>
                        <option value="Otro" {{ old('transportista') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Guía del Transportista</label>
                    <input type="text" name="numero_guia_transportista" value="{{ old('numero_guia_transportista') }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Costo de Envío</label>
                    <input type="number" step="0.01" name="costo_envio" value="{{ old('costo_envio', 0) }}"
                           class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-1">Pagado por *</label>
                    <select name="costo_pagado_por" required class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                        <option value="empresa" {{ old('costo_pagado_por') == 'empresa' ? 'selected' : '' }}>Empresa</option>
                        <option value="cliente" {{ old('costo_pagado_por') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Productos a enviar -->
        <div class="bg-black/40 rounded-lg border border-white/10 p-6">
            <h2 class="text-lg font-bold text-[#e7c095] mb-4">Productos a Enviar</h2>
            <div id="productos-container" class="space-y-3">
                <div class="producto-row grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-7">
                        <select name="productos[0][id]" required class="producto-select w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                            <option value="">Seleccionar producto...</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" data-stock="{{ $producto->stock_actual }}" data-unidad="{{ $producto->unidad }}">
                                    {{ $producto->nombre }} (Stock: {{ $producto->stock_actual }} {{ $producto->unidad }}) - ${{ number_format($producto->precio_unitario ?? 0, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <input type="number" step="0.01" name="productos[0][cantidad]" placeholder="Cantidad" required
                               class="cantidad-input w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                    </div>
                    <div class="md:col-span-2">
                        <button type="button" class="remove-producto w-full bg-red-500/20 text-red-400 px-3 py-2 rounded-lg hover:bg-red-500/30 transition">Eliminar</button>
                    </div>
                </div>
            </div>
            <button type="button" id="agregar-producto" class="mt-3 text-[#e7c095] hover:text-[#c29e75] transition text-sm flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">add</span> Agregar otro producto
            </button>
            <p class="text-xs text-gray-500 mt-2">⚠️ Los productos se descontarán automáticamente del inventario al registrar el envío.</p>
        </div>

        <!-- Observaciones -->
        <div class="bg-black/40 rounded-lg border border-white/10 p-6">
            <label class="block text-gray-300 text-sm mb-1">Observaciones</label>
            <textarea name="observaciones" rows="3" class="w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">{{ old('observaciones') }}</textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold px-6 py-2 rounded-lg hover:shadow-lg transition">
                Registrar Envío
            </button>
            <a href="{{ route('envios.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">Cancelar</a>
        </div>
    </form>
</div>

<script>
    let productoIndex = 1;

    document.getElementById('agregar-producto').addEventListener('click', function() {
        const container = document.getElementById('productos-container');
        const newRow = document.createElement('div');
        newRow.className = 'producto-row grid grid-cols-1 md:grid-cols-12 gap-3 mt-3';
        newRow.innerHTML = `
            <div class="md:col-span-7">
                <select name="productos[${productoIndex}][id]" required class="producto-select w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
                    <option value="">Seleccionar producto...</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" data-stock="{{ $producto->stock_actual }}" data-unidad="{{ $producto->unidad }}">
                            {{ $producto->nombre }} (Stock: {{ $producto->stock_actual }} {{ $producto->unidad }}) - ${{ number_format($producto->precio_unitario ?? 0, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <input type="number" step="0.01" name="productos[${productoIndex}][cantidad]" placeholder="Cantidad" required
                       class="cantidad-input w-full bg-black/60 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>
            <div class="md:col-span-2">
                <button type="button" class="remove-producto w-full bg-red-500/20 text-red-400 px-3 py-2 rounded-lg hover:bg-red-500/30 transition">Eliminar</button>
            </div>
        `;
        container.appendChild(newRow);
        productoIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-producto')) {
            e.target.closest('.producto-row').remove();
        }
    });
</script>
@endsection