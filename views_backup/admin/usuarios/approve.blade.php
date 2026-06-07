@extends('components.panel')

@section('content')

    <div class="p-6 max-w-2xl mx-auto">
        <!-- Encabezado -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.usuarios') }}" class="text-gray-400 hover:text-white transition">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold text-[#e7c095]">Aprobar Usuario</h1>
        </div>

        <!-- Datos del usuario -->
        <div class="bg-black/30 rounded-xl border border-white/10 p-6 mb-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#e7c095] to-[#c29e75] flex items-center justify-center text-black font-bold text-2xl">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm text-gray-400">Usuario</p>
                    <p class="text-xl font-semibold text-white">{{ $user->name }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400">Email</p>
                    <p class="text-white">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Rol solicitado</p>
                    <p class="text-[#e7c095] font-semibold">{{ ucfirst($user->requested_role ?? '—') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Fecha de registro</p>
                    <p class="text-white">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Estado actual</p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-yellow-500/20 text-yellow-300">
                        <span class="material-symbols-outlined text-sm">schedule</span> Pendiente de aprobación
                    </span>
                </div>
            </div>
        </div>

        <!-- Formulario de aprobación -->
        <div class="bg-black/30 rounded-xl border border-white/10 p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Asignar rol final</h2>
            <form action="{{ route('admin.usuarios.approve.post', $user) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Rol a asignar</label>
                    <select name="role" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-[#e7c095] focus:border-transparent">
                        <option value="operario">👨‍🔧 Operario de producción</option>
                        <option value="empaquetador">📦 Operario de empaquetado</option>
                        <option value="auditor">🔍 Auditor</option>
                        <option value="analista">📊 Analista de calidad</option>
                        <option value="admin">👑 Administrador (solo personal autorizado)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Este rol determinará los permisos del usuario en el sistema.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.usuarios') }}" class="px-4 py-2 rounded-lg bg-white/10 text-gray-300 hover:bg-white/20 transition">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-green-500/20 text-green-400 hover:bg-green-500/30 transition flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">check_circle</span> Aprobar usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
