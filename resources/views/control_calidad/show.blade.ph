@extends('components.panel')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-white tracking-tight">Detalle de Inspección</h1>
                <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full text-xs font-mono text-gray-400">#{{ $controlCalidad->id }}</span>
            </div>
            <p class="text-gray-500 text-sm mt-1">Consulta los registros técnicos del proceso de control de calidad.</p>
        </div>
        <a href="{{ route('control-calidad.index') }}" 
           class="flex items-center gap-2 text-gray-400 hover:text-white transition py-2 px-4 rounded-xl border border-transparent hover:border-white/10">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Volver al historial
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-black/40 backdrop-blur-md rounded-2xl border border-white/10 p-8 shadow-xl">
            <h2 class="text-lg font-bold text-[#e7c095] mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">description</span> Datos de Inspección
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                @php
                    $info = [
                        ['label' => 'Producción', 'value' => '#' . $controlCalidad->produccion_id],
                        ['label' => 'Producto', 'value' => $controlCalidad->producto->nombre ?? 'N/A'],
                        ['label' => 'Inspector', 'value' => $controlCalidad->inspector->name ?? 'N/A'],
                        ['label' => 'Fecha de Inspección', 'value' => $controlCalidad->fecha_inspeccion->format('d/m/Y H:i')],
                        ['label' => 'Creado el', 'value' => $controlCalidad->created_at->format('d/m/Y H:i')],
                    ];
                @endphp

                @foreach($info as $item)
                <div class="border-b border-white/5 pb-4">
                    <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">{{ $item['label'] }}</p>
                    <p class="text-sm text-white font-medium">{{ $item['value'] }}</p>
                </div>
                @endforeach

                <div class="border-b border-white/5 pb-4">
                    <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">Resultado</p>
                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                        {{ $controlCalidad->resultado == 'aprobado' ? 'bg-green-500/20 text-green-400' : 
                           ($controlCalidad->resultado == 'rechazado' ? 'bg-red-500/20 text-red-400' : 
                           'bg-yellow-500/20 text-yellow-400') }}">
                        {{ $controlCalidad->resultado }}
                    </span>
                </div>

                @if($controlCalidad->motivo_rechazo)
                <div class="border-b border-white/5 pb-4 md:col-span-2">
                    <p class="text-[10px] uppercase font-bold text-red-400 tracking-wider mb-1">Motivo de Rechazo</p>
                    <p class="text-sm text-red-200 font-medium italic">{{ str_replace('_', ' ', $controlCalidad->motivo_rechazo) }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-black/40 backdrop-blur-md rounded-2xl border border-white/10 p-8 shadow-xl flex flex-col">
            <h2 class="text-lg font-bold text-[#e7c095] mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">notes</span> Observaciones
            </h2>
            <div class="flex-grow bg-black/20 p-4 rounded-xl border border-white/5">
                @if($controlCalidad->observaciones)
                    <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">{{ $controlCalidad->observaciones }}</p>
                @else
                    <p class="text-gray-600 italic text-sm">No se agregaron observaciones adicionales.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection