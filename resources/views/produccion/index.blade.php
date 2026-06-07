@extends('components.panel')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-8">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Gestión de Mermas</h1>
            <p class="text-gray-400 text-sm">Control de pérdidas y ajustes de stock en tiempo real</p>
        </div>
        <a href="{{ route('inventario') }}" 
           class="flex items-center gap-2 bg-white/5 border border-white/10 px-6 py-2.5 rounded-xl text-white hover:bg-white/10 transition-all group">
            <span class="material-symbols-outlined text-sm group-hover:-translate-x-1 transition-transform">arrow_back</span> 
            Volver a Inventario
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-[#e7c095]/10 rounded-lg text-[#e7c095]">
                    <span class="material-symbols-outlined">delete_sweep</span>
                </div>
                <h2 class="text-lg font-bold text-white">Registrar nueva pérdida</h2>
            </div>
            
            <form action="{{ route('produccion.merma.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-2">Producto *</label>
                        <select name="producto_id" required class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] focus:ring-1 focus:ring-[#e7c095] outline-none transition-all">
                            <option value="">Seleccione el producto afectado...</option>
                            @foreach($productos as $p) 
                                <option value="{{ $p->id }}">{{ $p->nombre }} (Stock: {{ (float)$p->stock_actual }})</option> 
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-2">Cantidad *</label>
                        <input type="number" step="0.01" name="cantidad" required placeholder="0.00" class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-2">Causa *</label>
                        <select name="causa" required class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none">
                            <option value="defecto_calidad">Defecto de calidad</option>
                            <option value="sobreproduccion">Sobreproducción</option>
                            <option value="error_proceso">Error en proceso</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>
                
                <textarea name="observaciones" placeholder="Detalles o notas adicionales sobre la merma..." class="w-full bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#e7c095] outline-none h-24"></textarea>
                
                <button type="submit" class="w-full bg-gradient-to-r from-[#e7c095] to-[#c29e75] text-black font-bold py-4 rounded-xl hover:shadow-xl hover:shadow-[#e7c095]/20 transition-all">
                    Confirmar Registro de Merma
                </button>
            </form>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-6">
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4">Actividad Reciente</h3>
                <div class="space-y-4">
                    @forelse($mermasRecientes->take(4) as $m)
                    <div class="flex justify-between items-center border-b border-white/5 pb-3">
                        <div>
                            <p class="text-white text-sm font-semibold">{{ $m->producto->nombre ?? 'N/A' }}</p>
                            <p class="text-[10px] text-gray-500">{{ $m->created_at->format('d M, H:i') }}</p>
                        </div>
                        <span class="text-red-400 font-bold text-sm">-{{ $m->cantidad }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-600">No hay movimientos registrados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-white/5 flex justify-between items-center">
            <h3 class="font-bold text-white">Historial Completo</h3>
            <span class="text-xs text-gray-500">{{ $mermasRecientes->count() }} registros totales</span>
        </div>
        <table class="w-full text-left">
            <thead class="bg-white/5 text-[10px] uppercase tracking-widest text-gray-400">
                <tr>
                    <th class="p-4">Producto</th>
                    <th class="p-4">Causa</th>
                    <th class="p-4 text-right">Cantidad</th>
                    <th class="p-4 text-center">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($mermasRecientes as $merma)
                <tr class="hover:bg-white/5 transition">
                    <td class="p-4 text-white font-medium">{{ $merma->producto->nombre ?? 'N/A' }}</td>
                    <td class="p-4 text-gray-300 text-sm">{{ str_replace('_', ' ', ucfirst($merma->causa)) }}</td>
                    <td class="p-4 text-right text-red-400 font-bold">{{ $merma->cantidad }}</td>
                    <td class="p-4 text-center text-gray-400 text-sm">{{ $merma->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-10 text-center text-gray-500 italic">No hay registros para mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection