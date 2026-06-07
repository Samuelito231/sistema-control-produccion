<x-guest-layout>
    <div class="relative bg-white/5 backdrop-blur-lg border border-white/10 p-8 sm:p-12 rounded-3xl shadow-2xl flex flex-col items-center text-center max-w-sm w-full mx-4 
                animate-[fade-in-up_0.6s_ease-out] motion-reduce:animate-none">

        <div class="relative mb-6" aria-hidden="true">
            <div class="absolute inset-0 bg-[#e7c095]/30 rounded-full blur-2xl animate-pulse"></div>
            <div class="relative w-20 h-20 bg-[#e7c095]/10 border border-[#e7c095]/30 rounded-full flex items-center justify-center shadow-inner
                        animate-[pop-in_0.5s_ease-out_0.2s_both] motion-reduce:animate-none">
                <span class="material-symbols-outlined text-[#e7c095] text-5xl">pets</span>
            </div>
        </div>

        <div role="alert" class="space-y-3 mb-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight 
                       drop-shadow-[0_2px_10px_rgba(0,0,0,0.5)]">
                ¡Registro exitoso!
            </h1>
            <p class="text-gray-300 text-base sm:text-lg leading-relaxed max-w-[280px] mx-auto">
                Tu cuenta ha sido creada. <span class="text-[#e7c095] font-semibold">Khaleesitas</span> te da la bienvenida al sistema.
            </p>
        </div>

        <div class="w-full">
            <a href="/login" 
               class="group relative inline-flex w-full items-center justify-center gap-2 px-8 py-4 
                      bg-gradient-to-br from-[#e7c095] to-[#d6b087] text-[#0a0c0c] font-bold rounded-2xl 
                      transition-all duration-300 
                      hover:from-[#f0d6b0] hover:to-[#e7c095] hover:shadow-[0_0_40px_rgba(231,192,149,0.5)] 
                      focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#e7c095] focus-visible:ring-offset-2 focus-visible:ring-offset-black 
                      active:scale-[0.97]">
                <span>Ir al inicio de sesión</span>
                <span class="transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true">→</span>
            </a>
        </div>
    </div>

    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pop-in {
            0%   { opacity: 0; transform: scale(0.5); }
            70%  { transform: scale(1.1); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</x-guest-layout>