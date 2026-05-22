<footer class="bg-gray-900 text-white">
    <div class="mx-auto w-full max-w-screen-xl p-4 py-8 lg:py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1">
                <a href="/" class="flex items-center mb-6">
                    <div class="flex flex-col">
                        <span class="text-3xl font-black tracking-tighter text-white">Shoply<span class="text-teal-400">.</span></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-teal-400/80">Tu tienda online</span>
                    </div>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    Llevamos lo mejor de la tecnología, moda y hogar a la puerta de tu casa. Calidad garantizada por Shoply.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-teal-600 transition-all duration-300">
                        <i class="fab fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-teal-600 transition-all duration-300">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-teal-600 transition-all duration-300">
                        <i class="fab fa-twitter text-sm"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-teal-600 transition-all duration-300">
                        <i class="fab fa-tiktok text-sm"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h3 class="text-white font-bold uppercase tracking-wider text-sm mb-6">Comprar</h3>
                <ul class="text-gray-400 font-medium space-y-3 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-teal-500 mr-2 opacity-0 hover:opacity-100 transition-opacity"></span>Tecnología</a></li>
                    <li><a href="#" class="hover:text-white transition-colors flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-teal-500 mr-2 opacity-0 hover:opacity-100 transition-opacity"></span>Moda Hombre</a></li>
                    <li><a href="#" class="hover:text-white transition-colors flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-teal-500 mr-2 opacity-0 hover:opacity-100 transition-opacity"></span>Moda Mujer</a></li>
                    <li><a href="#" class="hover:text-white transition-colors flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-teal-500 mr-2 opacity-0 hover:opacity-100 transition-opacity"></span>Hogar y Decoración</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold uppercase tracking-wider text-sm mb-6">Soporte</h3>
                <ul class="text-gray-400 font-medium space-y-3 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Centro de Ayuda</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Seguimiento de Pedido</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Envíos y Devoluciones</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Preguntas Frecuentes</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold uppercase tracking-wider text-sm mb-6">Boletín</h3>
                <p class="text-gray-400 text-sm mb-4">Suscríbete para recibir ofertas exclusivas.</p>
                <form class="flex flex-col space-y-2">
                    <input type="email" placeholder="tu@email.com" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                    <button class="bg-teal-600 hover:bg-teal-500 text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors">
                        Suscribirse
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center">
            <p class="text-xs text-gray-500 mb-4 md:mb-0">
                &copy; {{ date('Y') }} <span class="text-teal-400 font-bold">Shoply™</span>. Todos los derechos reservados.
            </p>
            <div class="flex space-x-6 text-xs text-gray-500">
                <a href="#" class="hover:text-white">Privacidad</a>
                <a href="#" class="hover:text-white">Términos</a>
                <a href="#" class="hover:text-white">Cookies</a>
            </div>
        </div>
    </div>
</footer>
