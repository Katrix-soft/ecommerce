<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="name" value="Nombre Completo" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <x-input id="name" class="block mt-1 w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Tu nombre" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div class="sm:col-span-1">
                    <x-label for="document_type" value="Tipo" />
                    <select id="document_type" name="document_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm" required>
                        <option value="DNI">DNI</option>
                        <option value="PASAPORTE">PASAPORTE</option>
                        <option value="CUIL">CUIL</option>
                        <option value="CUIT">CUIT</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <x-label for="dni" value="Número de Documento" />
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                            <i class="fas fa-id-card text-gray-400"></i>
                        </div>
                        <x-input id="dni" class="block mt-1 w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg" type="text" name="dni" :value="old('dni')" required placeholder="Número" />
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <x-label for="email" value="Correo Electrónico" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <x-input id="email" class="block mt-1 w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="ejemplo@correo.com" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-label for="password" value="Contraseña" />
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-input id="password" class="block mt-1 w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                </div>

                <div>
                    <x-label for="password_confirmation" value="Repetir Contraseña" />
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                            <i class="fas fa-shield-alt text-gray-400"></i>
                        </div>
                        <x-input id="password_confirmation" class="block mt-1 w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                                {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline hover:text-indigo-600">'.__('Términos de Servicio').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline hover:text-indigo-600">'.__('Política de Privacidad').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex flex-col space-y-4 mt-8">
                <x-button class="w-full justify-center py-3 text-sm">
                    Crear Cuenta
                </x-button>

                <div class="text-center">
                    <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 transition-colors" href="{{ route('login') }}">
                        ¿Ya tienes cuenta? <span class="font-bold">Inicia sesión aquí</span>
                    </a>
                </div>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
