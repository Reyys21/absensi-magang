@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-6">
    <ul class="inline-flex space-x-2 text-sm font-medium">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
        <li>
            <span class="px-3 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                &laquo;
            </span>
        </li>
        @else
        <li>
            <a href="{{ $paginator->previousPageUrl() }}"
                class="px-3 py-2 bg-[#0B849F] text-white rounded-md hover:bg-[#09758A] transition">
                &laquo;
            </a>
        </li>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
        {{-- "..." --}}
        @if (is_string($element))
        <li><span class="px-3 py-2 text-gray-400">{{ $element }}</span></li>
        @endif

        {{-- Page Number Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li>
            <span class="px-3 py-2 bg-[#FFD100] text-black rounded-md">{{ $page }}</span>
        </li>
        @else
        <li>
            <a href="{{ $url }}"
                class="px-3 py-2 bg-[#0B849F] text-white rounded-md hover:bg-[#09758A] transition">{{ $page }}</a>
        </li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
        <li>
            <a href="{{ $paginator->nextPageUrl() }}"
                class="px-3 py-2 bg-[#0B849F] text-white rounded-md hover:bg-[#09758A] transition">
                &raquo;
            </a>
        </li>
        @else
        <li>
            <span class="px-3 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                &raquo;
            </span>
        </li>
        @endif
    </ul>
</nav>
@endif