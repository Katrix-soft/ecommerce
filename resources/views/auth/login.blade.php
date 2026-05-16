<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <x-label for="email" value="Correo Electrónico" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <x-input id="email" class="block mt-1 w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="tu@correo.com" />
                </div>
            </div>

            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <x-label for="password" value="Contraseña" />
                    @if (Route::has('password.request'))
                        <a class="text-xs text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <x-input id="password" class="block mt-1 w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Recordarme</span>
                </label>
            </div>

            <div class="flex flex-col space-y-4 pt-4">
                <x-button class="w-full justify-center py-3 text-sm">
                    Iniciar Sesión
                </x-button>

                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ¿Aún no tienes cuenta? 
                        <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:underline">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
