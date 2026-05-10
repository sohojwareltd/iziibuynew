@if ($paginator->hasPages())
    {{--
    The $paginator variable is automatically injected when using the links() method.
    We are using the custom classes defined in the index.blade.php CSS.
    --}}
    <nav class="custom-pagination" role="navigation" aria-label="{{ __('pagination.pagination') }}">
        <ul class="pagination mb-0 gap-2">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="{{ __('words.previous') }}">
                    {{-- Using __('words.previous') based on existing usage of __('words...') in index.blade.php --}}
                    <span class="page-link" aria-hidden="true">{{ __('words.previous') }}</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('words.previous') }}">
                        {{ __('words.previous') }}
                    </a>
                </li>
            @endif

            {{-- Pagination Elements (Page numbers and ellipsis) --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('words.next') }}">
                        {{ __('words.next') }}
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="{{ __('words.next') }}">
                    <span class="page-link" aria-hidden="true">{{ __('words.next') }}</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
