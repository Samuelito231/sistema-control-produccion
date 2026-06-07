@extends('components.panel')

@section('content')

    <div class="p-4 max-w-md mx-auto">
        <h1 class="text-2xl font-bold text-[#e7c095] mb-4">Registrar merma rápida en empaquetado</h1>
        <div class="bg-black/30 rounded-xl p-4 mb-4">
            <p><strong>Producto:</strong> {{ $producto->nombre }}</p>
            <p><strong>Stock actual:</strong> {{ $producto->stock_actual }} {{ $producto->unidad }}</p>
        </div>

        <form action="{{ route('empaquetado.merma.store') }}" method="POST">
            @csrf
            <input type="hidden" name="producto_id" value="{{ $producto->id }}">

            <div class="mb-4">
                <label class="block text-gray-300 mb-1">Cantidad *</label>
                <input type="number" step="any" name="cantidad" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-1">Causa *</label>
                <select name="causa" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                    <option value="Sellado defectuoso">Sellado defectuoso</option>
                    <option value="Etiqueta incorrecta">Etiqueta incorrecta</option>
                    <option value="Rotura de bolsa">Rotura de bolsa</option>
                    <option value="Contaminación">Contaminación</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-1">Lote (opcional)</label>
                <input type="text" name="lote" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white"></textarea>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-2 rounded-lg shadow-lg" onclick="this.disabled=true; this.form.submit();">
                Registrar merma
            </button>
        </form>
    </div>
@endsection
