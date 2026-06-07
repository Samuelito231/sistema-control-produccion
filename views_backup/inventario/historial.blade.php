<div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-8 bg-black/20 border border-white/10 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.15)]">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#e7c095]">Historial de Mermas</h1>
            <p class="text-gray-300 mt-1">Producto: <span class="font-semibold text-white">{{ $producto->name }}</span> (SKU: {{ $producto->code }})</p>
        </div>

        @if($mermas->count())
            <div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-white/5 text-gray-300 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Cantidad</th>
                            <th class="px-4 py-3">Causa</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Lote</th>
                            <th class="px-4 py-3">Registrado por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($mermas as $merma)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($merma->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $merma->cantidad }} {{ $merma->unidad }}</td>
                            <td class="px-4 py-3">{{ $merma->causa }}</td>
                            <td class="px-4 py-3 capitalize">{{ $merma->tipo_merma }}</td>
                            <td class="px-4 py-3">{{ $merma->lote ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $merma->usuario->name ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $mermas->links() }}
            </div>
        @else
            <div class="bg-black/30 rounded-xl p-6 text-center text-gray-400">
                No hay registros de merma para este producto.
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('inventario') }}" class="text-[#e7c095] hover:underline flex items-center gap-1">
                ← Volver al inventario
            </a>
        </div>
    </div>
</div>