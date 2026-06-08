@extends('components.panel')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-bold text-white">Nueva Inspección de Calidad</h1>
        <p class="text-gray-400 text-sm mt-1">Completa los datos técnicos para registrar la evaluación de un lote.</p>
    </div>
    
    <form action="{{ route('control-calidad.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold uppercase text-gray-500 mb-2 tracking-wider">Lote de Producción *</label>
                    <select name="produccion_id" required 
                            class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none transition-all">
                        <option value="">Seleccionar lote...</option>
                        @foreach($producciones as $produccion)
                            <option value="{{ $produccion->id }}">
                                Lote #{{ $produccion->id }} - {{ $produccion->producto->nombre ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">El producto se obtiene automáticamente del lote seleccionado.</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold uppercase text-gray-500 mb-2 tracking-wider">Resultado de la inspección *</label>
                    <select name="resultado" id="resultado" required 
                            class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none transition-all">
                        <option value="aprobado">✅ Aprobado</option>
                        <option value="rechazado">❌ Rechazado</option>
                        <option value="cuarentena">⚠️ Cuarentena</option>
                    </select>
                </div>
                
                <div id="motivo_container" class="md:col-span-2 hidden transition-all duration-300 ease-in-out">
                    <label class="block text-[10px] font-bold uppercase text-red-400 mb-2 tracking-wider">Motivo de Rechazo *</label>
                    <select name="motivo_rechazo" id="motivo_select" 
                            class="w-full bg-red-500/10 border border-red-500/30 rounded-xl px-4 py-3 text-white focus:border-red-500 outline-none">
                        <option value="">Seleccionar motivo...</option>
                        <option value="problemas_peso">Problemas de peso</option>
                        <option value="aspecto_inadecuado">Aspecto inadecuado</option>
                        <option value="olor_anormal">Olor anormal</option>
                        <option value="color_incorrecto">Color incorrecto</option>
                        <option value="textura_inadecuada">Textura inadecuada</option>
                        <option value="contaminacion">Contaminación detectada</option>
                        <option value="fecha_vencimiento">Problemas con fecha de vencimiento</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-2 tracking-wider">Observaciones adicionales</label>
                <textarea name="observaciones" rows="3" 
                          class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none transition-all resize-none"
                          placeholder="Detalles adicionales sobre la inspección..."></textarea>
            </div>
        </div>
        
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('control-calidad.index') }}" 
               class="text-gray-400 hover:text-white font-bold transition">Cancelar</a>
            <button type="submit" 
                    class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-[#e7c095]/20 active:scale-[0.98] transition-all">
                Guardar Inspección
            </button>
        </div>
    </form>
</div>

<script>
    const resultadoSelect = document.getElementById('resultado');
    const motivoContainer = document.getElementById('motivo_container');
    const motivoSelect = document.getElementById('motivo_select');

    resultadoSelect.addEventListener('change', function() {
        if (this.value === 'rechazado') {
            motivoContainer.classList.remove('hidden');
            motivoSelect.required = true;
        } else {
            motivoContainer.classList.add('hidden');
            motivoSelect.required = false;
        }
    });
</script>
@endsection