<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Mensaje de bienvenida interno -->
        <div class="mb-4 text-center lg:text-left">
            <h2 class="text-base font-medium tracking-wide text-[#FFFDF9]/95 uppercase">Ingresar al Sistema</h2>
            <p class="text-xs text-neutral-400 mt-1">Introduce tus credenciales autorizadas</p>
        </div>

        <!-- Correo Electrónico -->
        <div class="group">
            <label for="email" class="block text-xs uppercase tracking-[0.15em] text-[#C5A880] font-semibold mb-2 transition-colors duration-300 group-focus-within:text-[#FFFDF9]">
                Correo Electrónico
            </label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                   class="block w-full px-4 py-3.5 bg-[#111111]/60 border border-[#C5A880]/20 rounded-xl text-[#FFFDF9] text-base font-normal placeholder-neutral-600 focus:outline-none focus:border-[#C5A880] focus:ring-1 focus:ring-[#C5A880]/30 transition-all duration-300 tracking-wide shadow-inner">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-400/80 tracking-wide" />
        </div>

        <!-- Contraseña -->
        <div class="group">
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="block text-xs uppercase tracking-[0.15em] text-[#C5A880] font-semibold transition-colors duration-300 group-focus-within:text-[#FFFDF9]">
                    Contraseña
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-neutral-400 hover:text-[#C5A880] transition-colors duration-200 tracking-wide" href="{{ route('password.request') }}">
                        ¿La olvidaste?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                   class="block w-full px-4 py-3.5 bg-[#111111]/60 border border-[#C5A880]/20 rounded-xl text-[#FFFDF9] text-base font-normal placeholder-neutral-600 focus:outline-none focus:border-[#C5A880] focus:ring-1 focus:ring-[#C5A880]/30 transition-all duration-300 tracking-wide shadow-inner">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-400/80 tracking-wide" />
        </div>

        <!-- Recordarme -->
        <div class="flex items-center justify-between pt-1">
            <label class="inline-flex items-center cursor-pointer select-none group/check">
                <input id="remember_me" type="checkbox" class="rounded bg-[#111111] border-[#C5A880]/40 text-[#C5A880] focus:ring-0 focus:ring-offset-0 w-4 h-4 checked:bg-[#C5A880] transition-all duration-200" name="remember">
                <span class="ms-2 text-xs text-neutral-300 group-hover/check:text-neutral-200 transition-colors duration-200 tracking-wide">Mantener sesión activa</span>
            </label>
        </div>

        <!-- Acciones Inferiores -->
        <div class="pt-4 space-y-4">
            <button type="submit" 
                    class="w-full flex justify-center items-center px-4 py-3.5 bg-gradient-to-r from-[#C5A880] to-[#A3865F] border border-transparent rounded-xl text-xs uppercase tracking-[0.2em] font-bold text-[#111111] hover:brightness-110 focus:outline-none shadow-[0_4px_20px_rgba(197,168,128,0.2)] transition-all duration-300 hover:-translate-y-[1px] active:translate-y-0">
                Iniciar Sesión
            </button>

            <div class="text-center pt-2">
                <span class="text-xs text-neutral-400 tracking-wide">¿No tienes cuenta?</span>
                <a href="{{ route('register') }}" class="text-xs text-[#C5A880] font-semibold hover:underline ms-1 transition duration-200 tracking-wide">
                    Regístrate aquí
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>