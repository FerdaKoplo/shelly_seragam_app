@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-2 ">
        
        @if ($paginator->onFirstPage())
            <span class="flex items-center justify-center w-10 h-10 border border-gray-300 rounded-md text-gray-300 cursor-not-allowed">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 13L1 7L7 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex items-center justify-center w-10 h-10 border border-black rounded-md text-black hover:bg-gray-50 transition">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 13L1 7L7 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        @endif

        @php
            $start = $paginator->currentPage() - 1; 
            $end = $paginator->currentPage() + 1;   
            if($start < 1) $start = 1;
            if($end > $paginator->lastPage()) $end = $paginator->lastPage();
        @endphp

        <a href="{{ $paginator->url(1) }}" class="flex items-center justify-center w-10 h-10 border {{ $paginator->currentPage() == 1 ? 'border-black bg-black text-white' : 'border-gray-300 text-gray-500 hover:border-black hover:text-black' }} rounded-md transition font-bold">
            1
        </a>

        @if($paginator->currentPage() > 3)
            <span class="flex items-center justify-center w-10 h-10 text-black font-bold text-lg">...</span>
        @endif

        @foreach(range(2, $paginator->lastPage() - 1) as $page)
            @if($page >= $paginator->currentPage() - 1 && $page <= $paginator->currentPage() + 1)
                 @if ($page == $paginator->currentPage())
                    <span aria-current="page" class="flex items-center justify-center w-10 h-10 border border-black rounded-md bg-white text-black font-bold">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="flex items-center justify-center w-10 h-10 border border-gray-300 rounded-md text-gray-500 hover:border-black hover:text-black transition">
                        {{ $page }}
                    </a>
                @endif
            @endif
        @endforeach

        @if($paginator->currentPage() < $paginator->lastPage() - 2)
            <span class="flex items-center justify-center w-10 h-10 text-black font-bold text-lg">...</span>
        @endif

        @if($paginator->lastPage() > 1)
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="flex items-center justify-center w-10 h-10 border {{ $paginator->currentPage() == $paginator->lastPage() ? 'border-black bg-black text-white' : 'border-gray-300 text-gray-500 hover:border-black hover:text-black' }} rounded-md transition font-bold">
                {{ $paginator->lastPage() }}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex items-center justify-center w-10 h-10 border border-black rounded-md text-black hover:bg-gray-50 transition">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 13L7 7L1 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        @else
            <span class="flex items-center justify-center w-10 h-10 border border-gray-300 rounded-md text-gray-300 cursor-not-allowed">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 13L7 7L1 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        @endif
    </nav>
@endif