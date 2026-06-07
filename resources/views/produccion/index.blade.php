@extends('components.panel')

@section('content')
<div class="p-8 space-y-8">
    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-2 rounded-lg">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Total merma</p>
                <span class="material-symbols-outlined text-2xl text-[#e7c095]">delete_sweep</span>
            </div>
            <p class="text-3xl font-bold text-white mt-2">{{ number_format($totalMerma, 2) }} kg/uds</p>
        </div>
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">% Pérdida vs prod.</p>
                <span class="material-symbols-outlined text-2xl text-[#e7c095]">trending_down</span>
            </div>
            <p class="text-3xl font-bold text-white mt-2">{{ $porcentajePerdida }}%</p>
        </div>
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Incidencias activas</p>
                <span class="material-symbols-outlined text-2xl text-[#e7c095]">warning</span>
            </div>
            <p class="text-3xl font-bold text-white mt-2">{{ $incidenciasActivas }}</p>
        </div>
        <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
            <div class="flex justify-between items-start">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Costo estimado</p>
                <span class="material-symbols-outlined text-2xl text-[#e7c095]">payments</span>
            </div>
            <p class="text-3xl font-bold text-white mt-2">${{ number_format($costoMerma, 2) }}</p>
        </div>
    </div>

    <!-- Gráfico de merma por producto -->
    <div class="bg-black/30 backdrop-blur-md rounded-2xl p-6 border border-white/10">
        <h3 class="text-lg font-bold text-[#e7c095] mb-4">Merma por producto (kg/uds) - últimos 7 días</h3>
        <div class="space-y-4">
            @forelse($mermaPorProducto as $item)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-300">{{ $item->nombre }}</span>
                        <span class="text-red-400 font-semibold">{{ number_format($item->total, 2) }} kg/uds</span>
                    </div>
                    <div class="w-full bg-white/10 rounded-full h-2">
                        @php
                            $maxTotal = $mermaPorProducto->max('total') ?: 1;
                            $width = ($item->total / $maxTotal) * 100;
                        @endphp
                        <div class="bg-red-400 h-2 rounded-full" style="width: {{ min($width, 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-center">No hay registros de merma en los últimos 7 días.</p>
            @endforelse
        </div>
    </div>

    <!-- Filtros de causa (estáticos) -->
    <div class="flex flex-wrap gap-2">
        <button class="px-4 py-1.5 rounded-full text-xs bg-[#e7c095] text-black font-semibold">Todas</button>
        <button class="px-4 py-1.5 rounded-full text-xs bg-white/5 border border-white/20 text-gray-300">Sobrepeso</button>
        <button class="px-4 py-1.5 rounded-full text-xs bg-white/5 border border-white/20 text-gray-300">Quemado</button>
        <button class="px-4 py-1.5 rounded-full text-xs bg-white/5 border border-white/20 text-gray-300">Deformación</button>
        <button class="px-4 py-1.5 rounded-full text-xs bg-white/5 border border-white/20 text-gray-300">Contaminación</button>
    </div>

    <!-- Tabla de registros de merma recientes -->
    <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-gray-300 text-xs font-semibold uppercase tracking-wider border-b-2 border-[#e7c095]/30">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Producto</th>
                        <th class="px-6 py-3 text-center">Cantidad</th>
                        <th class="px-6 py-3">Causa</th>
                        <th class="px-6 py-3">Lote</th>
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($mermasRecientes as $merma)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4 font-mono text-gray-300">{{ $merma->id }}</td>
                        <td class="px-6 py-4 font-medium text-white">{{ $merma->producto->nombre ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center text-red-400 font-bold">{{ rtrim(rtrim($merma->cantidad, '0'), '.') }} {{ $merma->unidad }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs bg-[#e7c095]/20 text-[#e7c095] px-2 py-1 rounded-full font-medium">{{ $merma->causa }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-300 font-mono">{{ $merma->lote ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ \Carbon\Carbon::parse($merma->fecha)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('productos.mermas', $merma->producto_id) }}" title="Ver historial" class="p-1.5 rounded-lg hover:bg-white/10 inline-block">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">No hay registros de merma todavía.@{ }
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== FORMULARIO DE MERMA MEJORADO ==================== -->
    @if(in_array(auth()->user()->role, ['admin', 'operario']))
        <div class="bg-gradient-to-br from-black/40 to-black/20 backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-2 mb-5">
                <span class="material-symbols-outlined text-2xl text-[#e7c095]">delete_sweep</span>
                <h3 class="text-lg font-bold text-[#e7c095]">Registrar nueva merma</h3>
            </div>
            
            <form action="{{ route('produccion.merma.store') }}" method="POST" class="space-y-4" id="mermaForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="relative">
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-wider">Producto *</label>
                        <select name="producto_id" required class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] transition-all">
                            <option value="">Seleccione producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" class="bg-black">
                                    {{ $producto->nombre }} (Stock: {{ number_format($producto->stock_actual, 0) }} {{ $producto->unidad }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-wider">Cantidad *</label>
                        <input type="number" step="any" name="cantidad" placeholder="0.00" required
                               class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-[#e7c095] transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-wider">Causa *</label>
                        <select name="causa" required class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:border-[#e7c095] transition-all">
                            <option value="Sobrepeso">⚖️ Sobrepeso</option>
                            <option value="Quemado">🔥 Quemado</option>
                            <option value="Deformación">📐 Deformación</option>
                            <option value="Contaminación">🧫 Contaminación</option>
                            <option value="Otro">❓ Otro</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-2.5 rounded-xl shadow-lg hover:shadow-[0_0_15px_rgba(231,192,149,0.4)] transition-all active:scale-[0.98] flex items-center justify-center gap-2"
                                onclick="this.disabled=true; this.form.submit();">
                            <span class="material-symbols-outlined text-base">check_circle</span> Registrar
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-wider">Lote propio (opcional)</label>
                        <input type="text" name="lote" placeholder="Ej: LOTE-MAN-001" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-[#e7c095] transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-wider">Asociar a lote de producción</label>
                        <select name="produccion_id" class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:border-[#e7c095] transition-all">
                            <option value="">— Sin asociar —</option>
                            @foreach($lotesProduccion as $lote)
                                <option value="{{ $lote->id }}" class="bg-black">
                                    {{ $lote->lote ?? 'Lote #' . $lote->id }} - {{ $lote->producto->nombre }} ({{ $lote->fecha_produccion->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-wider">Observaciones</label>
                    <textarea name="observaciones" rows="1" placeholder="Detalles adicionales..." class="w-full bg-black/60 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-[#e7c095] transition-all resize-none"></textarea>
                </div>
            </form>
        </div>
    @else
        <div class="bg-gradient-to-br from-black/40 to-black/20 backdrop-blur-md border border-white/10 rounded-2xl p-8 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-500 mb-2">lock</span>
            <p class="text-gray-400 text-sm">No tienes permisos para registrar mermas en producción.</p>
            <p class="text-xs text-gray-600 mt-1">Contacta al administrador</p>
        </div>
    @endif
</div>
@endsection