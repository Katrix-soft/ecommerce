<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800">
    <div class="mb-8 transform hover:scale-105 transition-transform duration-300">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden sm:rounded-2xl border border-white/10">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">¡Bienvenido a Shoply!</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Tu tienda online favorita</p>
        </div>
        {{ $slot }}
    </div>
</div>
