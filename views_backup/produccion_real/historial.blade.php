@extends('components.panel')

@section('content')

    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-[#e7c095]">Historial de producciones</h1>
            <a href="{{ route('produccion_real.create') }}" class="bg-[#e7c095]/20 text-[#e7c095] px-4 py-2 rounded-lg">+ Nueva producción</a>
        </div>

        <div class="overflow-x-auto bg-black/30 rounded-xl border border-white/10">
            <table class="w-full text-sm">
                <thead class="bg-white/5">
                    <tr>
                        <th>Lote</th>
                        <th>Producto</th>
                        <th>Cantidad producida</th>
                        <th>Fecha</th>
                        <th>Registrado por</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($producciones as $p)
                    <tr class="border-t border-white/10">
                        <td class="px-4 py-2">{{ $p->lote ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $p->producto->nombre }}</td>
                        <td class="px-4 py-2">{{ $p->cantidad_producida }} {{ $p->producto->unidad }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($p->fecha_produccion)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $p->usuario->name ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $p->observaciones }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-gray-400 py-4">No hay registros de producción aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $producciones->links() }}
        </div>
    </div>
@endsection
