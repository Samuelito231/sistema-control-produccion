@extends('components.panel')

@section('content')

    <div class="w-full min-h-screen flex flex-col">
        <header class="sticky top-0 z-40 bg-black/30 backdrop-blur-md border-b border-white/10 px-8 py-5">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-[#e7c095] to-#c29e75 bg-clip-text text-transparent">
                Reportes y Auditoría
            </h1>
            <p class="text-gray-400 text-sm mt-1">Balances de merma, eficiencia y trazabilidad</p>
        </header>

        <div class="p-8 space-y-8">
            <!-- KPIs con variación porcentual -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Movimientos (mes)</p>
                        <span class="material-symbols-outlined text-2xl text-#e7c095">timeline</span>
                    </div>
                    <p class="text-3xl font-bold text-white mt-2">{{ number_format($movimientosMes) }}</p>
                    @php
                        $movClass = $movimientosVariacion >= 0 ? 'text-green-400' : 'text-red-400';
                        $movIcon = $movimientosVariacion >= 0 ? '▲' : '▼';
                    @endphp
                    <p class="text-xs {{ $movClass }} mt-1">{{ $movIcon }} {{ abs($movimientosVariacion) }}% vs mes anterior</p>
                </div>
                <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Total merma</p>
                        <span class="material-symbols-outlined text-2xl text-#e7c095">delete_sweep</span>
                    </div>
                    <p class="text-3xl font-bold text-white mt-2">{{ number_format($totalMerma, 2) }} kg/uds</p>
                    @php
                        $mermaClass = $mermaVariacion >= 0 ? 'text-red-400' : 'text-green-400';
                        $mermaIcon = $mermaVariacion >= 0 ? '▲' : '▼';
                    @endphp
                    <p class="text-xs {{ $mermaClass }} mt-1">{{ $mermaIcon }} {{ abs($mermaVariacion) }}% vs mes anterior</p>
                </div>
                <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Costo de merma</p>
                        <span class="material-symbols-outlined text-2xl text-#e7c095">attach_money</span>
                    </div>
                    <p class="text-3xl font-bold text-white mt-2">${{ number_format($costoMerma, 2) }}</p>
                    @php
                        $costoClass = $costoVariacion >= 0 ? 'text-red-400' : 'text-green-400';
                        $costoIcon = $costoVariacion >= 0 ? '▲' : '▼';
                    @endphp
                    <p class="text-xs {{ $costoClass }} mt-1">{{ $costoIcon }} {{ abs($costoVariacion) }}% vs mes anterior</p>
                </div>
                <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Lotes cerrados</p>
                        <span class="material-symbols-outlined text-2xl text-#e7c095">inventory</span>
                    </div>
                    <p class="text-3xl font-bold text-white mt-2">{{ number_format($lotesCerrados) }}</p>
                    @php
                        $lotesClass = $lotesVariacion >= 0 ? 'text-green-400' : 'text-red-400';
                        $lotesIcon = $lotesVariacion >= 0 ? '▲' : '▼';
                    @endphp
                    <p class="text-xs {{ $lotesClass }} mt-1">{{ $lotesIcon }} {{ abs($lotesVariacion) }}% vs mes anterior</p>
                </div>
            </div>

            <!-- Filtro de fechas y botones -->
            <form method="GET" action="{{ route('reportes') }}" id="filtrosForm" class="flex flex-wrap gap-4 items-center bg-black/30 rounded-xl px-4 py-3 border border-white/10">
                <div class="flex gap-2 items-center">
                    <label for="from" class="text-xs text-gray-400">Desde:</label>
                    <input type="date" id="from" name="from" value="{{ $from }}" class="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white">
                </div>
                <div class="flex gap-2 items-center">
                    <label for="to" class="text-xs text-gray-400">Hasta:</label>
                    <input type="date" id="to" name="to" value="{{ $to }}" class="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white">
                </div>
                <button type="submit" class="bg-[#e7c095]/20 text-[#e7c095] px-4 py-1.5 rounded-full text-sm font-medium hover:bg-[#e7c095]/30 transition">
                    Aplicar filtro
                </button>
                <a href="{{ route('reportes') }}" class="bg-white/10 text-white px-4 py-1.5 rounded-full text-sm hover:bg-white/20 transition">
                    Limpiar
                </a>
                <button type="button" onclick="toggleFiltrosAvanzados()" class="bg-white/10 text-gray-300 px-4 py-1.5 rounded-full text-sm hover:bg-white/20 transition">
                    Filtros avanzados
                </button>
                <div class="flex gap-2 ml-auto">
                    <a href="{{ route('reportes.export.pdf', request()->query()) }}" class="bg-red-500/20 text-red-400 px-4 py-1.5 rounded-full text-sm hover:bg-red-500/30 transition">
                        <span class="material-symbols-outlined text-base align-middle">picture_as_pdf</span> PDF
                    </a>
                    <a href="{{ route('reportes.export.csv', request()->query()) }}" class="bg-green-500/20 text-green-400 px-4 py-1.5 rounded-full text-sm hover:bg-green-500/30 transition">
                        <span class="material-symbols-outlined text-base align-middle">table_rows</span> CSV (Excel)
                    </a>
                    <a href="{{ route('reportes.export.excel', request()->query()) }}" class="bg-blue-500/20 text-blue-400 px-4 py-1.5 rounded-full text-sm hover:bg-blue-500/30 transition">
                        <span class="material-symbols-outlined text-base align-middle">table_chart</span> Excel con diseño
                    </a>
                </div>
            </form>

            <!-- Filtros avanzados (colapsable) -->
            <div id="filtrosAvanzados" class="hidden bg-black/20 rounded-xl p-4 border border-white/10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Producto</label>
                        <select name="producto_id" form="filtrosForm" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white">
                            <option value="">Todos</option>
                            @foreach($productos as $p)
                                <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Causa</label>
                        <select name="causa" form="filtrosForm" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white">
                            <option value="">Todas</option>
                            @foreach($causasDisponibles as $c)
                                <option value="{{ $c }}" {{ request('causa') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Tipo de merma</label>
                        <select name="tipo_merma" form="filtrosForm" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white">
                            <option value="">Todos</option>
                            @foreach($tiposDisponibles as $t)
                                <option value="{{ $t }}" {{ request('tipo_merma') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Registrado por</label>
                        <select name="usuario_id" form="filtrosForm" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white">
                            <option value="">Todos</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <button type="submit" form="filtrosForm" class="bg-[#e7c095]/20 text-[#e7c095] px-4 py-1 rounded-full text-sm">Aplicar filtros avanzados</button>
                </div>
            </div>

            <!-- Métricas adicionales: Merma por tipo y Top causas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-black/30 rounded-xl p-6 border border-white/10">
                    <h3 class="text-lg font-bold text-[#e7c095] mb-2">Merma por tipo</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-300">Producción</span>
                            <span class="text-red-400 font-semibold">{{ number_format($mermaProduccion, 2) }} kg/uds</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Empaquetado</span>
                            <span class="text-red-400 font-semibold">{{ number_format($mermaEmpaquetado, 2) }} kg/uds</span>
                        </div>
                    </div>
                </div>

                <div class="bg-black/30 rounded-xl p-6 border border-white/10">
                    <h3 class="text-lg font-bold text-[#e7c095] mb-2">Top causas de merma</h3>
                    <div class="space-y-2">
                        @forelse($topCausas as $causaItem)
                            <div class="flex justify-between">
                                <span class="text-gray-300">{{ $causaItem->causa }}</span>
                                <span class="text-red-400 font-semibold">{{ number_format($causaItem->total, 2) }} kg/uds</span>
                            </div>
                        @empty
                            <p class="text-gray-400">No hay datos</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top 5 productos -->
            <div class="bg-black/30 rounded-xl p-6 border border-white/10">
                <h3 class="text-lg font-bold text-[#e7c095] mb-2">Top 5 productos con mayor merma</h3>
                <div class="space-y-2">
                    @forelse($topProductos as $item)
                        <div class="flex justify-between">
                            <span class="text-gray-300">{{ $item->nombre }}</span>
                            <span class="text-red-400 font-bold">{{ number_format($item->total, 2) }} {{ $item->unidad }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400">No hay datos</p>
                    @endforelse
                </div>
            </div>

            <!-- Gráfico de evolución diaria (Chart.js) -->
            <div class="bg-black/30 rounded-xl p-6 border border-white/10">
                <h3 class="text-lg font-bold text-[#e7c095] mb-4">Evolución diaria de merma (últimos 30 días)</h3>
                <canvas id="mermaDiariaChart" height="100"></canvas>
            </div>

            <!-- Tabla de trazabilidad PAGINADA con modal de detalle -->
            <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#e7c095] text-2xl">history</span>
                    <h2 class="text-xl font-bold text-[#e7c095]">Trazabilidad - Mermas registradas</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/5 text-gray-300 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3 text-center">Cantidad</th>
                                <th class="px-4 py-3">Causa</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Lote</th>
                                <th class="px-4 py-3">Registrado por</th>
                                <th class="px-4 py-3 text-center">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($mermasRecientes as $merma)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-2 text-gray-300 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($merma->fecha)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-2 font-medium text-white">
                                    {{ $merma->nombre_estado }}
                                </td>
                                <td class="px-4 py-2 text-center text-red-400 font-semibold">
                                    {{ $merma->cantidad }} {{ $merma->unidad }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-xs bg-[#e7c095]/20 text-[#e7c095] px-2 py-1 rounded-full">{{ $merma->causa }}</span>
                                </td>
                                <td class="px-4 py-2 capitalize text-gray-300">{{ $merma->tipo_merma }}</td>
                                <td class="px-4 py-2 text-gray-400">{{ $merma->lote ?? '—' }}</td>
                                <td class="px-4 py-2 text-gray-300">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">person</span>
                                        {{ $merma->usuario->name ?? 'Usuario desconocido' }}
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button onclick="verDetalleMerma(
                                        {{ json_encode($merma->nombre_estado) }},
                                        {{ json_encode($merma->cantidad) }},
                                        {{ json_encode($merma->unidad) }},
                                        {{ json_encode($merma->causa) }},
                                        {{ json_encode($merma->tipo_merma) }},
                                        {{ json_encode($merma->lote ?? 'N/A') }},
                                        {{ json_encode($merma->usuario->name ?? 'N/A') }},
                                        {{ json_encode($merma->observaciones ?? 'Sin observaciones') }}
                                    )" class="text-[#e7c095] hover:text-white transition">
                                        <span class="material-symbols-outlined text-sm">info</span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                    No hay registros de merma en el período seleccionado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 border-t border-white/10">
                    {{ $mermasRecientes->appends(request()->query())->links() }}
                </div>
            </div>

            <!-- Sección de exportación -->
            <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#e7c095] text-2xl">file_download</span>
                    <h2 class="text-xl font-bold text-[#e7c095]">Exportar reportes</h2>
                </div>
                <div class="p-6 text-center text-gray-400">
                    Puedes descargar los reportes actuales (aplicando el filtro de fechas) en formato:
                    <div class="flex flex-wrap justify-center gap-4 mt-3">
                        <a href="{{ route('reportes.export.pdf', request()->query()) }}" class="inline-flex items-center gap-1 bg-red-500/20 text-red-400 px-4 py-2 rounded-full hover:bg-red-500/30 transition">
                            <span class="material-symbols-outlined text-sm">picture_as_pdf</span> PDF profesional
                        </a>
                        <a href="{{ route('reportes.export.csv', request()->query()) }}" class="inline-flex items-center gap-1 bg-green-500/20 text-green-400 px-4 py-2 rounded-full hover:bg-green-500/30 transition">
                            <span class="material-symbols-outlined text-sm">table_rows</span> CSV (Excel)
                        </a>
                        <a href="{{ route('reportes.export.excel', request()->query()) }}" class="inline-flex items-center gap-1 bg-blue-500/20 text-blue-400 px-4 py-2 rounded-full hover:bg-blue-500/30 transition">
                            <span class="material-symbols-outlined text-sm">table_chart</span> Excel con diseño
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalle -->
    <div id="detalleMermaModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-all">
        <div class="bg-black/90 border border-[#e7c095]/30 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-[#e7c095]">Detalle de merma</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="space-y-3 text-gray-300 text-sm">
                <p><strong class="text-white">Producto:</strong> <span id="modalProducto"></span></p>
                <p><strong class="text-white">Cantidad:</strong> <span id="modalCantidad"></span></p>
                <p><strong class="text-white">Causa:</strong> <span id="modalCausa"></span></p>
                <p><strong class="text-white">Tipo:</strong> <span id="modalTipo"></span></p>
                <p><strong class="text-white">Lote:</strong> <span id="modalLote"></span></p>
                <p><strong class="text-white">Registrado por:</strong> <span id="modalUsuario"></span></p>
                <p><strong class="text-white">Observaciones:</strong> <span id="modalObservaciones"></span></p>
            </div>
            <div class="mt-6 text-right">
                <button onclick="cerrarModal()" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-full text-sm transition">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de evolución diaria
        const ctx = document.getElementById('mermaDiariaChart').getContext('2d');
        const fechas = @json($mermaPorDia->pluck('fecha')->map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m')));
        const totales = @json($mermaPorDia->pluck('total'));
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: fechas,
                datasets: [{
                    label: 'Merma (kg/uds)',
                    data: totales,
                    borderColor: '#e7c095',
                    backgroundColor: 'rgba(231, 192, 149, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { labels: { color: '#ccc' } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.raw.toFixed(1)} kg/uds` } }
                },
                scales: {
                    y: { grid: { color: '#333' }, ticks: { color: '#ccc' } },
                    x: { grid: { display: false }, ticks: { color: '#ccc' } }
                }
            }
        });

        // Toggle filtros avanzados
        function toggleFiltrosAvanzados() {
            const div = document.getElementById('filtrosAvanzados');
            div.classList.toggle('hidden');
        }

        // Función para mostrar modal con detalles
        function verDetalleMerma(producto, cantidad, unidad, causa, tipo, lote, usuario, observaciones) {
            document.getElementById('modalProducto').innerText = producto;
            document.getElementById('modalCantidad').innerText = `${cantidad} ${unidad}`;
            document.getElementById('modalCausa').innerText = causa;
            document.getElementById('modalTipo').innerText = tipo;
            document.getElementById('modalLote').innerText = lote;
            document.getElementById('modalUsuario').innerText = usuario;
            document.getElementById('modalObservaciones').innerText = observaciones;
            document.getElementById('detalleMermaModal').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('detalleMermaModal').classList.add('hidden');
        }

        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarModal(); });
    </script>
@endsection
