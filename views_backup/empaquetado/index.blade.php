@extends('components.panel')

@section('content')

    <div class="w-full min-h-screen flex flex-col">
        <header class="sticky top-0 z-40 bg-black/30 backdrop-blur-md border-b border-white/10 px-8 py-5">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-[#c29e75] bg-clip-text text-transparent">
                Empaquetado / Producto Terminado
            </h1>
            <p class="text-gray-400 text-sm mt-1">Control de mermas en empaque y producto final</p>
        </header>

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
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Total merma (empaque)</p>
                        <span class="material-symbols-outlined text-2xl text-[#e7c095]">inventory</span>
                    </div>
                    <p class="text-3xl font-bold text-white mt-2">{{ number_format($totalMerma, 2) }} kg/uds</p>
                </div>
                <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">% Pérdida vs prod.</p>
                        <span class="material-symbols-outlined text-2xl text-[#e7c095]">package</span>
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

            <!-- Gráfico -->
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
                                @php $maxTotal = $mermaPorProducto->max('total') ?: 1; $width = ($item->total / $maxTotal) * 100; @endphp
                                <div class="bg-red-400 h-2 rounded-full" style="width: {{ min($width, 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center">No hay registros de merma en empaquetado en los últimos 7 días.</p>
                    @endforelse
                </div>
            </div>

            <!-- Tabla de mermas recientes -->
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
                                <td class="px-6 py-4 text-center text-red-400 font-bold">{{ $merma->cantidad }} {{ $merma->unidad }}</td>
                                <td class="px-6 py-4"><span class="text-xs bg-[#e7c095]/20 text-[#e7c095] px-2 py-1 rounded-full">{{ $merma->causa }}</span></td>
                                <td class="px-6 py-4 text-gray-300">{{ $merma->lote ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-300">{{ \Carbon\Carbon::parse($merma->fecha)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('productos.mermas', $merma->producto_id) }}" title="Ver historial" class="p-1.5 rounded-lg hover:bg-white/10 inline-block">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">No hay registros de merma en empaquetado.@{ }
                            <br>Usa el formulario para registrar la primera merma.@{
                                </td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario de registro de merma (visible solo para admin y operario) -->
            @if(in_array(auth()->user()->role, ['admin', 'operario']))
                <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-[#e7c095] mb-4">Registrar nueva merma en empaquetado</h3>
                    <form action="{{ route('empaquetado.merma.store') }}" method="POST" id="mermaForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <select name="producto_id" required class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
                                <option value="">Seleccione producto</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->nombre }} (Stock: {{ $producto->stock_actual }})</option>
                                @endforeach
                            </select>
                            <input type="number" step="any" name="cantidad" placeholder="Cantidad (kg/uds)" required class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
                            <select name="causa" required class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
                                <option value="Sellado defectuoso">Sellado defectuoso</option>
                                <option value="Etiqueta incorrecta">Etiqueta incorrecta</option>
                                <option value="Rotura de bolsa">Rotura de bolsa</option>
                                <option value="Contaminación">Contaminación</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <button type="submit" class="bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-2 rounded-lg shadow-lg" onclick="this.disabled=true; this.form.submit();">
                                Registrar
                            </button>
                        </div>
                        <div class="mt-3">
                            <input type="text" name="lote" placeholder="Lote (opcional)" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white w-full md:w-1/2">
                            <textarea name="observaciones" rows="1" placeholder="Observaciones (opcional)" class="mt-2 bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white w-full"></textarea>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center text-gray-400">
                    <span class="material-symbols-outlined text-2xl align-middle mr-2">lock</span> No tienes permisos para registrar mermas en empaquetado.
                </div>
            @endif
        </div>
    </div>
@endsection
