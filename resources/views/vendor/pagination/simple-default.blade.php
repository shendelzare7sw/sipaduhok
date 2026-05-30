@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true"><span>Sebelumnya</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a></li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a></li>
            @else
                <li class="disabled" aria-disabled="true"><span>Berikutnya</span></li>
            @endif
        </ul>
    </nav>
@endif
