<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Suspendida - Shoply</title>
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
            background: radial-gradient(circle at top left, #0f172a, #020617);
        }
        .square-card {
            border-radius: 0px !important;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 text-slate-100 overflow-hidden relative">
    <!-- Abstract background decorations -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl -z-10 animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl -z-10 animate-pulse" style="animation-delay: 2s;"></div>

    <div class="max-w-md w-full bg-slate-900/60 backdrop-blur-md border border-slate-800 p-8 text-center square-card shadow-2xl relative">
        <!-- Accent line -->
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-red-500 to-violet-600"></div>

        <!-- Lock Icon -->
        <div class="w-16 h-16 bg-gradient-to-br from-red-500/10 to-violet-500/10 border border-red-500/30 flex items-center justify-center mx-auto mb-6 square-card">
            <i class="fa-solid fa-lock text-red-500 text-2xl animate-bounce"></i>
        </div>

        <h1 class="text-xl font-extrabold uppercase tracking-wider bg-gradient-to-r from-red-400 to-violet-400 bg-clip-text text-transparent mb-3">
            Servicio Suspendido
        </h1>

        <p class="text-sm text-slate-400 font-semibold leading-relaxed mb-6">
            {{ $message ?? 'Esta tienda ha sido pausada temporalmente por administración de cuenta o falta de pago.' }}
        </p>

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
                    <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
                </a>
            </div>
        </div>
    </div>
</body>
</html>
