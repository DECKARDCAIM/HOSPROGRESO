@if ($paginator->hasPages())
    <style>
        .custom-pagination .page-item.active .page-link,
        .pagination .page-item.active .page-link,
        .pagination .active>.page-link {
            background: #1976d2 !important;
            color: #fff !important;
            border-color: #1976d2 !important;
        }
        .custom-pagination .page-link {
            border-radius: 50% !important;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            font-size: 1.1rem;
        }
        .custom-pagination .page-link:hover {
            background: #e3f2fd;
            color: #1976d2;
        }
        .custom-pagination .page-item.disabled .page-link {
            color: #bdbdbd;
            background: #fafafa;
            border: none;
        }
        .custom-pagination {
            gap: 0.25rem;
        }
    </style>
    <nav aria-label="Paginación personalizada">
        <ul class="pagination custom-pagination justify-content-center">
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
                </li>
            @endif

            {{-- Números de página --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif
        </ul>
        <div class="text-center mt-2">
            <small class="text-muted">
                Mostrando <span class="fw-semibold">{{ $paginator->firstItem() }}</span> a <span class="fw-semibold">{{ $paginator->lastItem() }}</span> de <span class="fw-semibold">{{ $paginator->total() }}</span> resultados
            </small>
        </div>
    </nav>
@endif
