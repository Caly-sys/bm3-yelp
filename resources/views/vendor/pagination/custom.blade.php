@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
        {{-- Mobile View --}}
        <div class="pagination-mobile">
            @if ($paginator->onFirstPage())
                <span class="pagination-btn pagination-disabled" aria-disabled="true">
                    <svg class="pagination-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Previous</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-btn">
                    <svg class="pagination-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Previous</span>
                </a>
            @endif

            <span class="pagination-page-info">
                Page <span class="font-bold">{{ $paginator->currentPage() }}</span> of <span class="font-bold">{{ $paginator->lastPage() }}</span>
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-btn">
                    <span>Next</span>
                    <svg class="pagination-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span class="pagination-btn pagination-disabled" aria-disabled="true">
                    <span>Next</span>
                    <svg class="pagination-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="pagination-desktop">
            <div class="pagination-summary">
                Showing <span class="font-semibold">{{ $paginator->firstItem() ?? 0 }}</span> to <span class="font-semibold">{{ $paginator->lastItem() ?? 0 }}</span> of <span class="font-semibold">{{ $paginator->total() }}</span> results
            </div>

            <ul class="pagination-list">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="pagination-item disabled" aria-disabled="true">
                        <span class="pagination-link pagination-prev" aria-label="Previous">
                            <svg class="pagination-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Prev</span>
                        </span>
                    </li>
                @else
                    <li class="pagination-item">
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-link pagination-prev" aria-label="Previous">
                            <svg class="pagination-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Prev</span>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="pagination-item disabled" aria-disabled="true">
                            <span class="pagination-link pagination-dots">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="pagination-item active" aria-current="page">
                                    <span class="pagination-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="pagination-item">
                                    <a href="{{ $url }}" class="pagination-link" aria-label="Go to page {{ $page }}">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="pagination-item">
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-link pagination-next" aria-label="Next">
                            <span>Next</span>
                            <svg class="pagination-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </li>
                @else
                    <li class="pagination-item disabled" aria-disabled="true">
                        <span class="pagination-link pagination-next" aria-label="Next">
                            <span>Next</span>
                            <svg class="pagination-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
