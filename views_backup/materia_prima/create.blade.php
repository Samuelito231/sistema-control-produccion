@extends('components.panel')

@section('content')
<div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-8 max-w-2xl mx-auto bg-black/20 border border-white/10 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.15)]">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white tracking-tight">Registrar Materia Prima</h1>
            <p class="text-slate-400 text-sm mt-1">Completa los campos para dar de alta un nuevo insumo.</p>
        </div>

    <form action="{{ route('materia-prima.store') }}" method="POST" class="space-y-8">
        @csrf

        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-widest text-[#e7c095]/80 pb-2 border-b border-white/5">Información General</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Nombre del producto *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">SKU único *</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" required 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-mono focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-widest text-[#e7c095]/80 pb-2 border-b border-white/5">Parámetros de Inventario</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Unidad de medida *</label>
                    <input type="text" name="unidad" value="{{ old('unidad', 'kg') }}" required 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Stock Inicial *</label>
                    <input type="number" step="any" name="stock_actual" value="{{ old('stock_actual', 0) }}" required 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Stock Mínimo</label>
                    <input type="number" step="any" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Costo Unitario</label>
                    <input type="number" step="any" name="costo_unitario" value="{{ old('costo_unitario') }}" 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-2">Proveedor</label>
            <input type="text" name="proveedor" value="{{ old('proveedor') }}" 
                   class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
        </div>

        <div class="flex items-center justify-end gap-4 pt-6 border-t border-white/5">
            <a href="{{ route('materia-prima.index') }}" 
               class="text-slate-400 hover:text-white px-4 py-2 transition-colors">Cancelar</a>
            <button type="submit" 
                    class="bg-[#e7c095] hover:bg-[#d4ad85] text-black px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-[#e7c095]/10 transition-all active:scale-[0.98]">
                Crear Materia Prima
            </button>
        </div>
    </form>
</div>
</div>
@endsection
