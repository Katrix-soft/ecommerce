<x-app-layout>

      @push('css')
     <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"
      />
      @endpush

    <!-- Slider main container -->
<div class="swiper mb-12 !z-0">
  <!-- Additional required wrapper -->
  <div class="swiper-wrapper">
    <!-- Slides -->
     @foreach($covers as $cover)
          <div class="swiper-slide">
            <img src="{{ $cover->image }}" class="w-full aspect-[3/1] object-cover object-center" alt="{{ $cover->title }}"></div>
     @endforeach
  </div>
  <!-- If we need pagination -->
  <div class="swiper-pagination"></div>

  <!-- If we need navigation buttons -->
  <div class="swiper-button-prev"></div>
  <div class="swiper-button-next"></div>

</div>
<x-container>
  <h1 class="text-2xl font-bold text-gray-700 mb-4"> 
    Ultimos productos
 </h1>
 <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
  @foreach($lastProducts as $product)
    <article class="bg-white shadow-md hover:shadow-xl transition-shadow duration-300 rounded-xl overflow-hidden border border-gray-100 flex flex-col">
      <div class="relative aspect-square overflow-hidden group">
        <img src="{{ $product->image }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
      </div>

      <div class="p-5 flex-1 flex flex-col">
        <h2 class="text-base font-bold text-gray-800 line-clamp-2 min-h-[48px] mb-2 hover:text-indigo-600 transition-colors">
            <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
        </h2>
        <div class="mt-auto">
            <p class="text-xl font-black text-indigo-600 mb-4">
                $ {{ number_format($product->price, 0, ',', '.') }}
            </p>
            <a href="{{ route('products.show', $product) }}" class="btn btn-indigo block w-full text-center transition-all hover:scale-[1.02] active:scale-[0.98]">
                 Ver detalles
            </a>
        </div>
      </div>
    </article>
  @endforeach

 </div>
</x-container>
    @push('js')
    
      <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
      <script>

        const swiper = new Swiper('.swiper', {
        // Optional parameters
         
         loop: true,

         autoplay: {
             delay: 8000,
         },
        pagination: {
            el: '.swiper-pagination',
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        });
      </script>
    @endpush
    
</x-app-layout>