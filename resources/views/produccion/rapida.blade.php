@extends('components.panel')

@section('content')
<div class="p-6 max-w-sm mx-auto">
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold text-[#e7c095]">Registrar merma</h1>
        <p class="text-gray-500 text-xs mt-1">Acción rápida de inventario</p>
    </div>

    <div class="bg-black/40 border border-white/10 rounded-2xl p-4 mb-6 backdrop-blur-sm">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-500">Producto</p>
                <p class="text-sm font-semibold text-white">{{ $producto->nombre }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] uppercase font-bold text-gray-500">Stock Actual</p>
                <p class="text-sm font-bold text-[#e7c095]">{{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('produccion.merma.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="producto_id" value="{{ $producto->id }}">

        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Cantidad a mermar *</label>
                <input type="number" step="any" name="cantidad" required 
                       class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] transition-all"
                       placeholder="0.00">
            </div>

            <div class="col-span-2">
                <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Causa *</label>
                <select name="causa" required 
                        class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] transition-all">
                    <option value="Sobrepeso">⚖️ Sobrepeso</option>
                    <option value="Quemado">🔥 Quemado</option>
                    <option value="Deformación">📐 Deformación</option>
                    <option value="Contaminación">🧫 Contaminación</option>
                    <option value="Otro">❓ Otro</option>
                </select>
            </div>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Lote (Opcional)</label>
                <input type="text" name="lote" 
                       class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] transition-all"
                       placeholder="ID de lote...">
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="2" 
                          class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] transition-all resize-none"
                          placeholder="Detalles adicionales..."></textarea>
            </div>
        </div>

        <button type="submit" 
                class="w-full bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-3.5 rounded-xl shadow-lg hover:shadow-[#e7c095]/20 transition-all active:scale-[0.98] mt-4"
                onclick="this.disabled=true; this.form.submit();">
            Confirmar registro
        </button>
    </form>
</div>
@endsection