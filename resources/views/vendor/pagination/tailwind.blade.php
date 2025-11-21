@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-rounded">

            {{-- First Page --}}
            @if ($paginator->onFirstPage())
                <li class="page-item first disabled">
                    <span class="page-link waves-effect">
                        <i class="tf-icon ri-skip-back-mini-line ri-20px"></i>
                    </span>
                </li>
            @else
                <li class="page-item first">
                    <a class="page-link waves-effect" href="{{ $paginator->url(1) }}">
                        <i class="tf-icon ri-skip-back-mini-line ri-20px"></i>
                    </a>
                </li>
            @endif

            {{-- Previous Page --}}
            @if ($paginator->onFirstPage())
                <li class="page-item prev disabled">
                    <span class="page-link waves-effect">
                        <i class="tf-icon ri-arrow-left-s-line ri-20px"></i>
                    </span>
                </li>
            @else
                <li class="page-item prev">
                    <a class="page-link waves-effect" href="{{ $paginator->previousPageUrl() }}">
                        <i class="tf-icon ri-arrow-left-s-line ri-20px"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)

                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link waves-effect">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link waves-effect">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link waves-effect" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif

            @endforeach

            {{-- Next Page --}}
            @if ($paginator->hasMorePages())
                <li class="page-item next">
                    <a class="page-link waves-effect" href="{{ $paginator->nextPageUrl() }}">
                        <i class="tf-icon ri-arrow-right-s-line ri-20px"></i>
                    </a>
                </li>
            @else
                <li class="page-item next disabled">
                    <span class="page-link waves-effect">
                        <i class="tf-icon ri-arrow-right-s-line ri-20px"></i>
                    </span>
                </li>
            @endif

            {{-- Last Page --}}
            @if ($paginator->hasMorePages())
                <li class="page-item last">
                    <a class="page-link waves-effect" href="{{ $paginator->url($paginator->lastPage()) }}">
                        <i class="tf-icon ri-skip-forward-mini-line ri-20px"></i>
                    </a>
                </li>
            @else
                <li class="page-item last disabled">
                    <span class="page-link waves-effect">
                        <i class="tf-icon ri-skip-forward-mini-line ri-20px"></i>
                    </span>
                </li>
            @endif

        </ul>
    </nav>
@endif
