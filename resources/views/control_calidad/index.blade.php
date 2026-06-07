@extends('components.panel')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-8">
    
    @if(session('success') || $errors->any())
        <div class="space-y-2">
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/50 text-green-400 px-6 py-3 rounded-xl flex items-center gap-3 backdrop-blur-md">
                    <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/50 text-red-400 px-6 py-3 rounded-xl backdrop-blur-md">
                    <ul class="list-disc pl-4 text-sm"> @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                </div>
            @endif
        </div>
    @endif

    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter">Control de Mermas</h1>
            <p class="text-gray-500 mt-1">Análisis de pérdidas y registro de incidencias en producción.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php 
            $stats = [
                ['label' => 'Total Merma', 'val' => number_format($totalMerma, 2) . ' kg', 'icon' => 'delete_sweep', 'color' => 'text-[#e7c095]'],
                ['label' => '% Pérdida', 'val' => $porcentajePerdida . '%', 'icon' => 'trending_down', 'color' => 'text-blue-400'],
                ['label' => 'Incidencias', 'val' => $incidenciasActivas, 'icon' => 'warning', 'color' => 'text-yellow-400'],
                ['label' => 'Costo Est.', 'val' => '$' . number_format($costoMerma, 2), 'icon' => 'payments', 'color' => 'text-green-400']
            ];
        @endphp
        @foreach($stats as $s)
        <div class="bg-black/40 backdrop-blur-md border border-white/5 rounded-2xl p-6 hover:border-white/10 transition-all">
            <div class="flex justify-between items-start mb-4">
                <p class="text-[10px] font-bold uppercase text-gray-500 tracking-wider">{{ $s['label'] }}</p>
                <span class="material-symbols-outlined {{ $s['color'] }}">{{ $s['icon'] }}</span>
            </div>
            <p class="text-3xl font-black text-white">{{ $s['val'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 bg-black/40 backdrop-blur-md rounded-2xl p-6 border border-white/5">
            <h3 class="text-sm font-bold text-white mb-6 uppercase tracking-wider">Distribución semanal</h3>
            <div class="space-y-6">
                @forelse($mermaPorProducto as $item)
                <div>
                    <div class="flex justify-between text-xs mb-2">
                        <span class="text-gray-400 truncate w-3/5">{{ $item->nombre }}</span>
                        <span class="text-white font-bold">{{ number_format($item->total, 2) }}</span>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                        @php $width = (($item->total / ($mermaPorProducto->max('total') ?: 1)) * 100); @endphp
                        <div class="bg-[#e7c095] h-full rounded-full" style="width: {{ $width }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-xs text-center py-10">Sin datos de merma.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-2 bg-black/40 backdrop-blur-md rounded-2xl border border-white/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/5 text-gray-400 uppercase text-[10px]">
                        <tr>
                            <th class="px-6 py-4">Producto</th>
                            <th class="px-6 py-4">Causa</th>
                            <th class="px-6 py-4 text-right">Cantidad</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($mermasRecientes as $merma)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4 text-white font-medium">{{ $merma->producto->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-white/5 rounded-md text-xs text-gray-300">{{ $merma->causa }}</span></td>
                            <td class="px-6 py-4 text-right font-mono text-red-400">{{ $merma->cantidad }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('productos.mermas', $merma->producto_id) }}" class="text-[#e7c095] hover:underline">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(in_array(auth()->user()->role, ['admin', 'operario']))
    <div class="bg-black/60 backdrop-blur-xl border border-[#e7c095]/20 rounded-2xl p-8">
        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#e7c095]">add_box</span> Registrar Nueva Incidencia
        </h2>
        <form action="{{ route('produccion.merma.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf
            <select name="producto_id" class="bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-[#e7c095] transition">
                <option value="">Seleccionar Producto...</option>
                @foreach($productos as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option> @endforeach
            </select>
            <input type="number" name="cantidad" placeholder="Cantidad..." class="bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-[#e7c095]">
            <select name="causa" class="bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-[#e7c095]">
                <option value="Sobrepeso">Sobrepeso</option>
                <option value="Quemado">Quemado</option>
                <option value="Contaminación">Contaminación</option>
            </select>
            <div class="md:col-span-3">
                <textarea name="observaciones" placeholder="Detalles de la incidencia..." class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-[#e7c095] h-24"></textarea>
            </div>
            <button type="submit" class="md:col-span-3 bg-[#e7c095] text-black font-bold py-4 rounded-xl hover:bg-white transition-all">
                Confirmar registro de merma
            </button>
        </form>
    </div>
    @endif
</div>
@endsection