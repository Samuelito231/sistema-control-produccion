@extends('components.panel')

@section('content')

    <div class="p-6">
        <!-- Encabezado con botón volver -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.usuarios') }}" class="text-gray-400 hover:text-white transition">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="text-2xl font-bold text-[#e7c095]">Historial de actividades</h1>
            </div>
            <span class="text-xs text-gray-500">Auditoría completa</span>
        </div>

        <!-- Tarjeta resumen del usuario -->
        <div class="bg-black/30 rounded-xl border border-white/10 p-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#e7c095] to-[#c29e75] flex items-center justify-center text-black font-bold">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <p class="text-lg font-semibold text-white">{{ $user->name }}</p>
                    <p class="text-sm text-gray-400">{{ $user->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400">Rol asignado</p>
                    <p class="text-[#e7c095] font-semibold">{{ ucfirst($user->role) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400">Estado</p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs
                        @if($user->status == 'active') bg-green-500/20 text-green-300
                        @elseif($user->status == 'pending') bg-yellow-500/20 text-yellow-300
                        @else bg-red-500/20 text-red-300 @endif">
                        @if($user->status == 'active') Activo
                        @elseif($user->status == 'pending') Pendiente
                        @else Suspendido @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Filtro rápido por acción -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-white">Registro de acciones</h2>
            <div class="text-xs text-gray-500">Total registros: {{ $logs->total() }}</div>
        </div>

        <!-- Tabla de logs -->
        <div class="overflow-x-auto bg-black/30 rounded-xl border border-white/10">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300">Fecha/Hora</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300">Acción</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300">Modelo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300">IP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300">Navegador</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                    @php
                        // Decodificar extra si es necesario (compatible con array y JSON string)
                        $extra = $log->extra;
                        if (is_string($extra)) {
                            $extra = json_decode($extra, true) ?? [];
                        } elseif (!is_array($extra)) {
                            $extra = [];
                        }
                        $ip = $extra['ip'] ?? '—';
                        $userAgent = $extra['user_agent'] ?? '';
                    @endphp
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-300">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                @if($log->action == 'create_producto') bg-green-500/20 text-green-300
                                @elseif($log->action == 'update_producto') bg-blue-500/20 text-blue-300
                                @elseif($log->action == 'delete_producto') bg-red-500/20 text-red-300
                                @elseif($log->action == 'merma_registrada') bg-purple-500/20 text-purple-300
                                @else bg-gray-500/20 text-gray-300 @endif">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400">{{ class_basename($log->model_type) }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $log->model_id }}</td>
                        <td class="px-4 py-3">
                            <code class="text-xs bg-black/50 px-1 py-0.5 rounded">{{ $ip }}</code>
                        </td>
                        <td class="px-4 py-3 text-gray-400 truncate max-w-xs" title="{{ $userAgent }}">
                            {{ $userAgent ? substr($userAgent, 0, 60) . '…' : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            No hay registros de actividad para este usuario.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
