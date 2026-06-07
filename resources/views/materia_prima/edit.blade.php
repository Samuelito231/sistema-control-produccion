@extends('components.panel')

@section('content')
<div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-8 max-w-2xl mx-auto bg-black/20 border border-white/10 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.15)]">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white tracking-tight">Editar Materia Prima</h1>
            <p class="text-slate-400 text-sm mt-1">Actualiza los datos del insumo en el sistema.</p>
        </div>

        <form action="{{ route('materia-prima.update', $materia_prima) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Nombre del material *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $materia_prima->nombre) }}" required 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-slate-700 focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">SKU / Identificador *</label>
                    <input type="text" name="sku" value="{{ old('sku', $materia_prima->sku) }}" required 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-mono focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Lote de Compra</label>
                    <input type="text" name="lote_compra" value="{{ old('lote_compra', $materia_prima->lote_compra) }}" 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-mono focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all"
                           placeholder="Ej: L-2025-001">
                    <p class="text-[10px] text-slate-500 mt-1">Código de lote del proveedor</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $materia_prima->fecha_vencimiento) }}" 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                    <p class="text-[10px] text-slate-500 mt-1">Fecha de caducidad</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Unidad</label>
                    <input type="text" name="unidad" value="{{ old('unidad', $materia_prima->unidad) }}" 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Stock Mínimo</label>
                    <input type="number" step="any" name="stock_minimo" value="{{ old('stock_minimo', $materia_prima->stock_minimo) }}" 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Costo (USD)</label>
                    <input type="number" step="any" name="costo_unitario" value="{{ old('costo_unitario', $materia_prima->costo_unitario) }}" 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Proveedor</label>
                <input type="text" name="proveedor" value="{{ old('proveedor', $materia_prima->proveedor) }}" 
                       class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-white/5">
                <a href="{{ route('materia-prima.index') }}" 
                   class="text-slate-400 hover:text-white px-4 py-2 transition-colors">Cancelar</a>
                <button type="submit" 
                        class="bg-[#e7c095] hover:bg-[#d4ad85] text-black px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-[#e7c095]/10 transition-all active:scale-[0.98]">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection