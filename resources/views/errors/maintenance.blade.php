<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitio en Mantenimiento - Shoply</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top left, #0b0f19, #020617);
        }
        .square-card {
            border-radius: 0px !important;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 text-slate-100 overflow-hidden relative">
    <!-- Abstract background decorations -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl -z-10 animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl -z-10 animate-pulse" style="animation-delay: 2s;"></div>

    <div class="max-w-md w-full bg-slate-900/60 backdrop-blur-md border border-slate-800 p-8 text-center square-card shadow-2xl relative">
        <!-- Accent line -->
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-violet-500 to-indigo-600"></div>

        <!-- Maintenance Icon -->
        <div class="w-16 h-16 bg-gradient-to-br from-violet-500/10 to-indigo-500/10 border border-violet-500/30 flex items-center justify-center mx-auto mb-6 square-card">
            <i class="fa-solid fa-screwdriver-wrench text-violet-500 text-2xl animate-spin" style="animation-duration: 4s;"></i>
        </div>

        <h1 class="text-xl font-extrabold uppercase tracking-wider bg-gradient-to-r from-violet-400 to-indigo-400 bg-clip-text text-transparent mb-3">
            Sitio en Mantenimiento
        </h1>

        <p class="text-sm text-slate-400 font-semibold leading-relaxed mb-6">
            {{ $message ?? 'La tienda se encuentra en mantenimiento temporal. Volveremos muy pronto!' }}
        </p>

        @if(isset($ends_at) && $ends_at)
            <!-- Countdown Section -->
            <div class="mb-6 p-4 bg-slate-950/40 border border-slate-800/80 square-card">
                <p class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-2">Reapertura en:</p>
                <div class="grid grid-cols-4 gap-2 text-center" id="countdown-timer" data-date="{{ $ends_at->toIso8601String() }}">
                    <div class="bg-slate-900/60 p-2 square-card border border-slate-800/50">
                        <span class="block text-base font-bold text-violet-400" id="cd-days">00</span>
                        <span class="text-[8px] uppercase text-slate-500 font-bold">Días</span>
                    </div>
                    <div class="bg-slate-900/60 p-2 square-card border border-slate-800/50">
                        <span class="block text-base font-bold text-violet-400" id="cd-hours">00</span>
                        <span class="text-[8px] uppercase text-slate-500 font-bold">Horas</span>
                    </div>
                    <div class="bg-slate-900/60 p-2 square-card border border-slate-800/50">
                        <span class="block text-base font-bold text-indigo-400" id="cd-minutes">00</span>
                        <span class="text-[8px] uppercase text-slate-500 font-bold">Minutos</span>
                    </div>
                    <div class="bg-slate-900/60 p-2 square-card border border-slate-800/50">
                        <span class="block text-base font-bold text-indigo-400" id="cd-seconds">00</span>
                        <span class="text-[8px] uppercase text-slate-500 font-bold">Segundos</span>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const timerElement = document.getElementById('countdown-timer');
                    const targetDateStr = timerElement.getAttribute('data-date');
                    const targetDate = new Date(targetDateStr).getTime();

                    function updateTimer() {
                        const now = new Date().getTime();
                        const difference = targetDate - now;

                        if (difference <= 0) {
                            clearInterval(intervalId);
                            window.location.reload();
                            return;
                        }

                        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                        document.getElementById('cd-days').innerText = String(days).padStart(2, '0');
                        document.getElementById('cd-hours').innerText = String(hours).padStart(2, '0');
                        document.getElementById('cd-minutes').innerText = String(minutes).padStart(2, '0');
                        document.getElementById('cd-seconds').innerText = String(seconds).padStart(2, '0');
                    }

                    const intervalId = setInterval(updateTimer, 1000);
                    updateTimer();
                });
            </script>
        @endif

        @php
            $tenant = \App\Models\User::getTenant();
        @endphp

        <div class="border-t border-slate-800/80 pt-6 space-y-4">
            <p class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">¿Necesitás ayuda?</p>
            
            <div class="flex flex-col gap-2">
                @if($tenant && $tenant->store_email)
                    <a href="mailto:{{ $tenant->store_email }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 bg-slate-850 hover:bg-slate-800 text-xs font-bold uppercase tracking-wider border border-slate-700 transition duration-150 square-card">
                        <i class="fa-solid fa-envelope text-slate-400"></i> Correo de Soporte
                    </a>
                @endif

                @if($tenant && $tenant->store_whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->store_whatsapp) }}" target="_blank" class="inline-flex items-center justify-center gap-2 w-full py-2.5 bg-emerald-600/10 hover:bg-emerald-650/20 text-emerald-400 hover:text-emerald-300 text-xs font-bold uppercase tracking-wider border border-emerald-500/20 transition duration-150 square-card">
                        <i class="fa-brands fa-whatsapp"></i> Contactar por WhatsApp
                    </a>
                @endif
                
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-750 hover:to-indigo-750 text-white text-xs font-bold uppercase tracking-wider transition duration-150 square-card shadow-lg shadow-violet-950/50">
                    <i class="fa-solid fa-right-to-bracket"></i> Acceso Administrativo
                </a>
            </div>
        </div>
    </div>
</body>
</html>
