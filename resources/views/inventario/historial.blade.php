@extends('components.panel')

@section('content')
<div class="bg-black/30 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-8 bg-black/20 border border-white/10 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.15)]">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#e7c095]">Historial de Mermas</h1>
            <p class="text-gray-300 mt-1">Producto: <span class="font-semibold text-white">{{ $producto->nombre }}</span> (SKU: {{ $producto->sku }})</p>
        </div>

        @if($mermas->count())
            <div class="overflow-x-auto bg-black/30 rounded-xl border border-white/10">
                <table class="w-full text-sm">
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
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-2 text-gray-300">{{ \Carbon\Carbon::parse($merma->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-red-400 font-semibold">{{ $merma->cantidad }} {{ $merma->unidad }}</td>
                            <td class="px-4 py-2"><span class="text-xs bg-[#e7c095]/20 text-[#e7c095] px-2 py-1 rounded-full">{{ $merma->causa }}</span></td>
                            <td class="px-4 py-2 capitalize">{{ $merma->tipo_merma }}</td>
                            <td class="px-4 py-2 font-mono text-gray-400">{{ $merma->lote ?? '—' }}</td>
                            <td class="px-4 py-2 text-gray-400">{{ $merma->usuario->name ?? '—' }}</td>
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
                <span class="material-symbols-outlined text-4xl mb-2">inbox</span>
                <p>No hay registros de merma para este producto.</p>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('inventario') }}" class="text-[#e7c095] hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Volver al inventario
            </a>
        </div>
    </div>
</div>
@endsection