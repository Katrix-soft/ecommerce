@if (count($breadcrumbs))
    

    <nav class="mb-4">
        <ol class="flex flex-wrap">
            @foreach ($breadcrumbs as $item)
                <li class="text-sm leading-normal text-slate-700 {{ !$loop->first ? "pl-2 before:float-left before:pr-2 before:content-['/']" : '' }}">
                    {{-- <a href="" class="opacity-50">
                        {{ $item['name'] }}
                    </a> --}}

                    @isset($item['route'])
                        <a href="{{ $item['route'] }}" class="opacity-50">
                            {{ $item['name'] }}
                        </a>
                    @else
                        {{ $item['name'] }}
                    @endisset
                </li> 
            @endforeach
        </ol>
        @if (count($breadcrumbs) > 1)
            <h6 class="font-bold text-slate-700">
                {{ end($breadcrumbs)['name'] }} 
            </h6>
        @endif
    </nav> 

@endif