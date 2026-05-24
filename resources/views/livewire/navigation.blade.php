<div x-data="{ open: false }">
    <header class="bg-teal-700 relative z-50">
        <x-container class="px-4 py-4">
          <div class="flex justify-between items-center md:space-x-8">

         
          <div class="flex-1 flex items-center">
              <button x-on:click="open = true" class="text-xl md:text-3xl cursor-pointer relative z-10">
                <i class="fas fa-bars text-white"></i>
              </button>
          </div>
          <div class="flex-shrink-0">
             <h1 class="text-white">
            <a href="/" class="flex flex-col items-center">
              <span class="text-xl md:text-3xl leading-4 md:leading-6 font-semibold">
                Shoply
               </span>
               <span class="text-xs">
                Tienda online
             </span>
            </a>
          </h1>
          </div>
          <div class="flex-1 hidden md:block">
              <x-input onchange="search(this.value)" class="w-full" placeholder="Buscar por producto, tienda o por marca"/>
          </div>
          <div class="flex-1 flex items-center space-x-4 md:space-x-8 justify-end relative z-10">

                   <x-dropdown>

                   <x-slot name="trigger">

                   @auth

                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                      <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                      
                    </button>
                    @else
                    <button class="text-xl md:text-3xl">
                        <i class="fas fa-user text-white "></i>
                     </button>

                   @endauth

                     
                   </x-slot>

                   <x-slot name="content">

                   @guest 
                     <div class="px-4 py-2">
                      <div class="flex justify-center">
                         <a href="{{ route('login')}}" class="btn btn-indigo">
                           Iniciar Sesión 
                         </a>
                      </div>

                      <p class="text-sm text-center mt-4">
                      ¿No tienes cuenta? 
                         <a href="{{ route('register')}}" class="text-teal-600 hover:underline transition-colors">
                          Registrate
                         </a>
                      </p>
                      </div>

                      @else
                      <x-dropdown-link href="{{ route('profile.show')}}">
                        Perfil
                      </x-dropdown-link>
                      
                      @role('admin')
                      <x-dropdown-link href="{{ route('admin.dashboard')}}">
                        Administración
                      </x-dropdown-link>
                      @endrole

                      @role('superadmin')
                      <x-dropdown-link href="{{ route('superadmin.dashboard')}}">
                        Administración General
                      </x-dropdown-link>
                      @endrole

                      <div class="border-t border-gray-200"></div>
                        <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                    @endguest

                   </x-slot>
 
                    </x-dropdown>
            
             
              <div class="relative group" x-data="{ expanded: false }" @mouseleave="expanded = false">
                  <a href="{{ route('cart.index')}}" class="relative block py-1">
                    <i class="fas fa-shopping-cart text-white text-xl md:text-3xl"></i>
                    <span id="cart-count" class="absolute -top-1 -right-2 inline-flex items-center justify-center w-5 h-5 bg-amber-500 rounded-full text-[10px] font-bold text-white"> {{Cart::instance('shopping')->count()}} </span>
                    
                    <!-- Leyenda de productos en carrito constante -->
                    <div id="cart-tooltip" class="hidden md:block absolute top-full right-0 mt-3 w-36 bg-white rounded-lg shadow-lg border border-gray-100 p-2 z-[60] transform origin-top-right transition-all duration-500 group-hover:opacity-0 group-hover:invisible group-hover:animate-none {{ Cart::instance('shopping')->count() > 0 ? 'animate-pulse' : 'opacity-0 pointer-events-none' }}">
                        <div class="absolute -top-1.5 right-3 w-3 h-3 bg-white border-t border-l border-gray-100 transform rotate-45"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center gap-0.5">
                            <i class="fas fa-shopping-basket text-amber-500 text-base mb-0.5"></i>
                            <p class="text-center text-teal-700 text-[11px] font-bold leading-tight">¡Tienes productos en tu carrito!</p>
                            <span class="text-[9px] text-gray-400">Haz clic para verlos</span>
                        </div>
                    </div>
                  </a>

                  <!-- Dropdown Vista Rápida -->
                  @if(Cart::instance('shopping')->count() > 0)
                  <div class="absolute top-full right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right">
                      <!-- Flecha del dropdown -->
                      <div class="absolute -top-2 right-4 w-4 h-4 bg-white border-t border-l border-gray-100 transform rotate-45"></div>
                      
                      @php
                          $cartItems = Cart::instance('shopping')->content()->values();
                      @endphp
                      
                      <div class="p-4 relative z-10">
                          <h3 class="text-sm font-bold text-gray-800 mb-3 border-b border-gray-50 pb-2">Vista Rápida</h3>
                          
                          <ul class="space-y-3">
                              @foreach($cartItems->take(4) as $index => $item)
                                  <li class="{{ $index >= 2 ? 'hidden' : 'flex' }} gap-3 items-center" 
                                      x-bind:class="{ 'hidden': !expanded && {{ $index }} >= 2, 'flex': expanded || {{ $index }} < 2 }">
                                      <img src="{{ $item->options->image ?? asset('img/no-image.png') }}" class="w-12 h-12 rounded-lg object-cover border border-gray-100 bg-gray-50">
                                      <div class="flex-1 overflow-hidden">
                                          <h4 class="text-xs font-bold text-gray-800 truncate" title="{{ $item->name }}">{{ $item->name }}</h4>
                                          <p class="text-teal-600 font-bold text-xs mt-0.5">{{ \App\Models\User::formatPrice($item->price) }} <span class="text-gray-400 font-normal ml-1">x{{ $item->qty }}</span></p>
                                      </div>
                                  </li>
                              @endforeach
                          </ul>
                          
                          @if($cartItems->count() > 2)
                              <div x-show="!expanded" class="pt-2 text-center">
                                  <button @click.prevent="expanded = true" class="bg-gray-50 hover:bg-teal-50 text-gray-400 hover:text-teal-600 px-4 py-1 rounded-full transition-colors cursor-pointer outline-none">
                                      <i class="fas fa-ellipsis-h text-lg"></i>
                                  </button>
                              </div>
                          @endif
                          
                          <a href="{{ route('cart.index') }}" class="block w-full mt-4 bg-teal-600 hover:bg-teal-700 text-white text-center text-xs font-bold py-2.5 rounded-xl transition-colors shadow-sm">
                              Ir al carrito
                          </a>
                      </div>
                  </div>
                  @endif
              </div>
          </div>

        </div>
        <div class="mt-4 md:hidden">
         <x-input onchange="search(this.value)" class="w-full" placeholder="Buscar por producto, tienda o por marca"/>
        </div> 
        </x-container>
    </header>

    <div x-show="open" x-on:click="open = false" class="fixed top-0 left-0 inset-0 bg-black bg-opacity-25 z-10" style="display: none;"></div>

    <div x-show="open" class="fixed top-0 left-0 z-20" style="display: none;">
        <div class="flex">

           <div class="w-screen md:w-80 h-screen bg-white">
            
               <div class="bg-teal-700 px-4 py-3 text-white font-semibold">
                  <div class="flex justify-between items-center">
                      <span class="text-lg"> 
                           ¡Hola!
                      </span>

                     <button x-on:click="open = false" class="cursor-pointer text-gray-200 hover:text-white">
                       <i class="fas fa-times"></i>
                      </button>
                  </div>
               </div>
              
               <div class="h-[calc(100vh-52px)] overflow-auto ">
                
                  <ul>
                      
                      @foreach ( $families as $family )

                          <li wire:key="family-{{ $family->id }}" 
                              x-on:mouseenter="if (window.innerWidth >= 768) $wire.set('family_id', {{ $family->id }})">
                              <div class="flex items-center justify-between px-4 py-4 text-gray-700 hover:bg-teal-50 transition-colors">
                                  <a href="{{ route('families.show', $family)}}" class="flex-1 font-medium">
                                     {{ $family->name }}
                                  </a>
                                  <button wire:click.prevent="toggleFamily({{ $family->id }})" class="px-2 md:hidden">
                                      <i class="fa-solid fa-angle-down" x-show="$wire.family_id == {{ $family->id }}"></i>
                                      <i class="fa-solid fa-angle-right" x-show="$wire.family_id != {{ $family->id }}"></i>
                                  </button>
                                  <i class="fa-solid fa-angle-right hidden md:block"></i>
                              </div>

                              <div class="md:hidden">
                                  @if ($family_id == $family->id)
                                      <ul class="bg-teal-50 pb-4">
                                          @foreach ($this->categories as $category)
                                              <li>
                                                  <a href="{{ route('categories.show', $category)}}" class="block px-8 py-2 text-teal-700 font-semibold text-sm hover:bg-teal-100">
                                                      {{ $category->name }}
                                                  </a>
                                                  <ul class="pb-2 space-y-1">
                                                      @foreach ($category->subcategories as $subcategory)
                                                          <li>
                                                              <a href="{{ route('subcategories.show', $subcategory)}}" class="block px-12 py-1 text-xs text-gray-700 hover:text-teal-700 hover:bg-teal-50 transition-colors">
                                                                  {{ $subcategory->name }}
                                                              </a>
                                                          </li>
                                                      @endforeach
                                                  </ul>
                                              </li>
                                          @endforeach
                                      </ul>
                                  @endif
                              </div>
                          </li>
                        
                      @endforeach
                  </ul>

                </div>

               
            </div>

           <div class="w-80 xl:w-[57rem] pt-[52px] hidden md:block">
                <div class="bg-white h-[calc(100vh-52px)] overflow-auto px-4 py-8">
                     <div class="flex mb-8 justify-between items-center">

                     <p class="border-b-[3px] border-amber-400 uppercase text-xl font-semibold pb-1">
                        
                        {{ $this->familyName }}
                     </p>
                       
                     @if ($family_id)
                         <a href="{{ route('families.show', $family_id)}}" class="btn btn-indigo">
                            Ver todo
                         </a>
                     @endif

                        
                     </div>
                                       
                  <ul class="grid  grid-cols-1 xl:grid-cols-3 gap-8">
                    @foreach ( $this->categories as $category )
                       <li wire:key="category-{{ $category->id }}">

                         <a href="{{ route('categories.show', $category)}}" class="text-teal-700 font-semibold text-lg">
                         {{ $category->name }}
                         </a>
                         <ul class="mt-4 space-y-2">
                            @foreach ($category->subcategories as $subcategory )
                              <li wire:key="subcategory-{{ $subcategory->id }}">
                                 <a href="{{ route('subcategories.show', $subcategory)}}" class="text-sm text-gray-700 hover:text-teal-600 transition-colors">
                                    {{ $subcategory->name }}
                                 </a>
                            </li>
                            @endforeach
                         </ul>

                       </li>
                    @endforeach
                  </ul>  
            

                </div>
           </div>
        </div>
    </div>

   @push('js')
       <script>
           Livewire.on('cartUpdated', (count) => {
               let cartCount = Array.isArray(count) ? count[0] : count;
               document.getElementById('cart-count').innerText = cartCount;
               
               const tooltip = document.getElementById('cart-tooltip');
               if(tooltip) {
                   if(parseInt(cartCount) > 0) {
                       tooltip.classList.remove('opacity-0', 'pointer-events-none');
                       tooltip.classList.add('animate-pulse');
                   } else {
                       tooltip.classList.add('opacity-0', 'pointer-events-none');
                       tooltip.classList.remove('animate-pulse');
                   }
               }
           });

           function search(value) {
              Livewire.dispatch('search', {
                search: value
              });
           }
       </script>
   @endpush

</div>
