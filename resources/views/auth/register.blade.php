<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Mensaje de bienvenida interno -->
        <div class="mb-2 text-center lg:text-left">
            <h2 class="text-base font-medium tracking-wide text-[#FFFDF9]/95 uppercase">Crear Nueva Cuenta</h2>
            <p class="text-xs text-neutral-400 mt-1">Introduce tus datos para registrarte en el sistema</p>
        </div>

        <!-- Nombre Completo -->
        <div class="group">
            <label for="name" class="block text-xs uppercase tracking-[0.15em] text-[#C5A880] font-semibold mb-1.5 transition-colors duration-300 group-focus-within:text-[#FFFDF9]">
                Nombre Completo
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                   class="block w-full px-4 py-2.5 bg-[#111111]/60 border border-[#C5A880]/20 rounded-xl text-[#FFFDF9] text-base font-normal placeholder-neutral-600 focus:outline-none focus:border-[#C5A880] focus:ring-1 focus:ring-[#C5A880]/30 transition-all duration-300 tracking-wide shadow-inner">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-red-400/80 tracking-wide" />
        </div>

        <!-- Correo Electrónico -->
        <div class="group">
            <label for="email" class="block text-xs uppercase tracking-[0.15em] text-[#C5A880] font-semibold mb-1.5 transition-colors duration-300 group-focus-within:text-[#FFFDF9]">
                Correo Electrónico
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                   class="block w-full px-4 py-2.5 bg-[#111111]/60 border border-[#C5A880]/20 rounded-xl text-[#FFFDF9] text-base font-normal placeholder-neutral-600 focus:outline-none focus:border-[#C5A880] focus:ring-1 focus:ring-[#C5A880]/30 transition-all duration-300 tracking-wide shadow-inner">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-400/80 tracking-wide" />
        </div>

        <!-- Rol Solicitado (requested_role) -->
        <div class="group">
            <label for="requested_role" class="block text-xs uppercase tracking-[0.15em] text-[#C5A880] font-semibold mb-1.5 transition-colors duration-300 group-focus-within:text-[#FFFDF9]">
                Rol Solicitado
            </label>
            <div class="relative">
                <select id="requested_role" name="requested_role" required
                    class="block w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-base font-normal focus:outline-none focus:ring-2 focus:ring-[#e7c095]/70 focus:border-transparent transition-all duration-300 appearance-none cursor-pointer shadow-inner">
                    <option value="operario" {{ old('requested_role') == 'operario' ? 'selected' : '' }} class="bg-gray-900 text-gray-300 py-3">
                        Operario de Producción
                    </option>
                    <option value="empaquetador" {{ old('requested_role') == 'empaquetador' ? 'selected' : '' }} class="bg-gray-900 text-gray-300 py-3">
                        Operario de Empaquetado
                    </option>
                    <option value="auditor" {{ old('requested_role') == 'auditor' ? 'selected' : '' }} class="bg-gray-900 text-gray-300 py-3">
                        Auditor
                    </option>
                    <option value="analista" {{ old('requested_role') == 'analista' ? 'selected' : '' }} class="bg-gray-900 text-gray-300 py-3">
                        Analista de Calidad
                    </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#C5A880] group-focus-within:text-[#FFFDF9]">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('requested_role')" class="mt-1.5 text-xs text-red-400/80 tracking-wide" />
        </div>

        <!-- Contraseña -->
        <div class="group">
            <label for="password" class="block text-xs uppercase tracking-[0.15em] text-[#C5A880] font-semibold mb-1.5 transition-colors duration-300 group-focus-within:text-[#FFFDF9]">
                Contraseña
            </label>
            <input id="password" type="password" name="password" required autocomplete="new-password" 
                   class="block w-full px-4 py-2.5 bg-[#111111]/60 border border-[#C5A880]/20 rounded-xl text-[#FFFDF9] text-base font-normal placeholder-neutral-600 focus:outline-none focus:border-[#C5A880] focus:ring-1 focus:ring-[#C5A880]/30 transition-all duration-300 tracking-wide shadow-inner">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-400/80 tracking-wide" />
        </div>

        <!-- Confirmar Contraseña -->
        <div class="group">
            <label for="password_confirmation" class="block text-xs uppercase tracking-[0.15em] text-[#C5A880] font-semibold mb-1.5 transition-colors duration-300 group-focus-within:text-[#FFFDF9]">
                Confirmar Contraseña
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                   class="block w-full px-4 py-2.5 bg-[#111111]/60 border border-[#C5A880]/20 rounded-xl text-[#FFFDF9] text-base font-normal placeholder-neutral-600 focus:outline-none focus:border-[#C5A880] focus:ring-1 focus:ring-[#C5A880]/30 transition-all duration-300 tracking-wide shadow-inner">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-red-400/80 tracking-wide" />
        </div>

        <!-- Acciones Inferiores -->
        <div class="pt-2 space-y-3">
            <button type="submit" 
                    class="w-full flex justify-center items-center px-4 py-3.5 bg-gradient-to-r from-[#C5A880] to-[#A3865F] border border-transparent rounded-xl text-xs uppercase tracking-[0.2em] font-bold text-[#111111] hover:brightness-110 focus:outline-none shadow-[0_4px_20px_rgba(197,168,128,0.2)] transition-all duration-300 hover:-translate-y-[1px] active:translate-y-0">
                Crear Cuenta
            </button>

            <div class="text-center pt-1">
                <span class="text-xs text-neutral-400 tracking-wide">¿Ya tienes cuenta?</span>
                <a href="{{ route('login') }}" class="text-xs text-[#C5A880] font-semibold hover:underline ms-1 transition duration-200 tracking-wide">
                    Inicia sesión
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>