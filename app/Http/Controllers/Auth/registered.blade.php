<x-guest-layout>
    <div class="text-center space-y-8 flex flex-col items-center">
        <div class="relative">
            <div class="absolute inset-0 bg-[#e7c095]/20 rounded-full blur-2xl"></div>
            <div class="relative w-20 h-20 bg-[#0a0c0c] border border-[#e7c095]/30 rounded-full flex items-center justify-center shadow-2xl">
                <span class="material-symbols-outlined text-[#e7c095] text-4xl">check_circle</span>
            </div>
        </div>

        <div class="space-y-3">
            <h2 class="text-3xl font-bold text-white tracking-tight">¡Registro exitoso!</h2>
            <p class="text-[#d1d1d1] text-base leading-relaxed max-w-xs mx-auto">
                Tu cuenta ha sido creada correctamente. Ya puedes acceder al sistema.
            </p>
        </div>

        <div class="pt-2">
            <a href="{{ route('login') }}"
               class="group relative inline-flex items-center px-10 py-4 bg-[#e7c095] text-[#0a0c0c] font-bold rounded-2xl transition-all duration-300 hover:shadow-[0_0_30px_rgba(231,192,149,0.3)] hover:scale-105 active:scale-95">
                Iniciar sesión
                <span class="material-symbols-outlined ml-2 text-lg">arrow_forward</span>
            </a>
        </div>
    </div>
</x-guest-layout>