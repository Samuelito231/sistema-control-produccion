@extends('components.panel')

@section('content')
<div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 max-w-md mx-auto bg-black/20 border border-white/10 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.15)]">
        <h1 class="text-2xl font-bold text-[#e7c095] mb-4">Registrar compra - {{ $materia_prima->nombre }}</h1>
        <p class="text-gray-400 text-sm mb-4">Stock actual: <strong>{{ rtrim(rtrim($materia_prima->stock_actual, '0'), '.') }} {{ $materia_prima->unidad }}</strong></p>

        <form action="{{ route('materia-prima.registrar-entrada', $materia_prima) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-300 mb-1">Cantidad ({{ $materia_prima->unidad }}) *</label>
                <input type="number" step="any" name="cantidad" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-gray-300 mb-1">Costo unitario (opcional)</label>
                <input type="number" step="any" name="costo_unitario" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                <p class="text-xs text-gray-400 mt-1">Si lo deja vacío, se mantiene el costo actual (${{ rtrim(rtrim($materia_prima->costo_unitario, '0'), '.') }})</p>
            </div>
            <div>
                <label class="block text-gray-300 mb-1">Lote de Compra</label>
                <input type="text" name="lote_compra" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white font-mono" placeholder="Ej: L-2025-001">
                <p class="text-xs text-gray-500 mt-1">Código de lote del proveedor (opcional pero recomendado para trazabilidad)</p>
            </div>
            <div>
                <label class="block text-gray-300 mb-1">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                <p class="text-xs text-gray-500 mt-1">Fecha de caducidad del lote (opcional)</p>
            </div>
            <div>
                <label class="block text-gray-300 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white" placeholder="Notas adicionales sobre la compra..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('materia-prima.movimientos', $materia_prima) }}" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg">Cancelar</a>
                <button type="submit" class="bg-green-500/20 text-green-400 px-4 py-2 rounded-lg hover:bg-green-500/30 transition">Registrar compra</button>
            </div>
        </form>
    </div>
</div>
@endsection