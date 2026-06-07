@extends('components.panel')

@section('content')

    <div class="p-6">
        <!-- Encabezado y tarjetas de resumen -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#e7c095]">Gestión de Usuarios</h1>
            <a href="{{ route('admin.usuarios') }}" class="text-sm text-gray-400 hover:text-white transition">⟳ Refrescar</a>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-black/40 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-400">pending</span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Pendientes</p>
                    <p class="text-2xl font-bold text-white">{{ $usuarios->where('status', 'pending')->count() }}</p>
                </div>
            </div>
            <div class="bg-black/40 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-400">check_circle</span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Activos</p>
                    <p class="text-2xl font-bold text-white">{{ $usuarios->where('status', 'active')->count() }}</p>
                </div>
            </div>
            <div class="bg-black/40 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-400">block</span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Suspendidos</p>
                    <p class="text-2xl font-bold text-white">{{ $usuarios->where('status', 'suspended')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <form method="GET" class="flex flex-wrap gap-3 items-center justify-between mb-6">
            <div class="flex gap-2">
                <select name="status" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:ring-[#e7c095] focus:border-[#e7c095]">
                    <option value="">Todos los estados</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pendientes</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Activos</option>
                    <option value="suspended" {{ request('status')=='suspended' ? 'selected' : '' }}>Suspendidos</option>
                </select>
                <input type="text" name="search" placeholder="Buscar por nombre o email" value="{{ request('search') }}" 
                       class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-400 w-64">
                <button type="submit" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm transition">Filtrar</button>
                <a href="{{ route('admin.usuarios') }}" class="bg-white/5 hover:bg-white/10 text-gray-300 px-4 py-2 rounded-lg text-sm transition">Limpiar</a>
            </div>
        </form>

        <!-- Tabla de usuarios -->
        <div class="overflow-x-auto bg-black/30 rounded-xl border border-white/10">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rol solicitado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rol actual</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($usuarios as $user)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#e7c095] to-[#c29e75] flex items-center justify-center text-black font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-white">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-300">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-gray-500/20 text-gray-300">
                                {{ $user->requested_role ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs
                                @if($user->role == 'admin') bg-red-500/20 text-red-300
                                @elseif($user->role == 'auditor') bg-blue-500/20 text-blue-300
                                @elseif($user->role == 'analista') bg-purple-500/20 text-purple-300
                                @else bg-green-500/20 text-green-300 @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                @if($user->status == 'pending') bg-yellow-500/20 text-yellow-300
                                @elseif($user->status == 'active') bg-green-500/20 text-green-300
                                @else bg-red-500/20 text-red-300 @endif">
                                @if($user->status == 'pending') 
                                    <span class="material-symbols-outlined text-sm">schedule</span> Pendiente
                                @elseif($user->status == 'active')
                                    <span class="material-symbols-outlined text-sm">check_circle</span> Activo
                                @else
                                    <span class="material-symbols-outlined text-sm">block</span> Suspendido
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex justify-end gap-2">
                                @if($user->status == 'pending')
                                    <a href="{{ route('admin.usuarios.approve', $user) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-500/20 text-green-400 hover:bg-green-500/30 transition text-sm"
                                       title="Aprobar usuario">
                                        <span class="material-symbols-outlined text-sm">checklist</span> Aprobar
                                    </a>
                                    <button type="button" onclick="confirmarRechazo({{ $user->id }})" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 transition text-sm"
                                            title="Rechazar usuario">
                                        <span class="material-symbols-outlined text-sm">close</span> Rechazar
                                    </button>
                                @else
                                    <form action="{{ route('admin.usuarios.toggle-suspend', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-orange-500/20 text-orange-400 hover:bg-orange-500/30 transition text-sm"
                                                title="{{ $user->status == 'active' ? 'Suspender usuario' : 'Activar usuario' }}">
                                            <span class="material-symbols-outlined text-sm">{{ $user->status == 'active' ? 'block' : 'check_circle' }}</span>
                                            {{ $user->status == 'active' ? 'Suspender' : 'Activar' }}
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.usuarios.historial', $user) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 transition text-sm"
                                   title="Ver historial de actividades">
                                    <span class="material-symbols-outlined text-sm">history</span> Historial
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            No hay usuarios registrados con los filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $usuarios->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- SweetAlert para confirmar rechazo (opcional, pero mejora UX) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarRechazo(userId) {
            Swal.fire({
                title: 'Rechazar usuario',
                input: 'text',
                inputLabel: 'Motivo del rechazo',
                inputPlaceholder: 'Escribe el motivo...',
                showCancelButton: true,
                confirmButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'bg-black/90 text-white border border-white/10',
                    input: 'bg-white/5 text-white border border-white/10',
                    confirmButton: 'bg-red-500/20 text-red-400 hover:bg-red-500/30',
                    cancelButton: 'bg-white/10 text-gray-300 hover:bg-white/20'
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/usuarios/${userId}/reject`;
                    form.innerHTML = `@csrf<input type="hidden" name="reason" value="${result.value}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
