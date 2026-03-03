{{-- Pagination bar with per-page selector --}}
@if($paginator->hasPages() || $paginator->total() > 0)
    <div class="location-pagination">
        <div class="pagination-info">
            <span class="pagination-total">
                Показано {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} из {{ $paginator->total() }}
            </span>
            <div class="pagination-per-page">
                <label>Показывать:</label>
                <select class="pagination-per-page-select" onchange="changePerPage(this.value, '{{ $sectionRoute }}')">
                    @foreach([10, 25, 50, 100] as $option)
                        <option value="{{ $option }}" {{ ($perPage ?? 10) == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($paginator->hasPages())
            <div class="pagination-links">
                {{-- Previous --}}
                @if($paginator->onFirstPage())
                    <span class="pagination-btn disabled">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                @endif

                {{-- Page numbers --}}
                @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 6 15 12 9 18"/></svg>
                    </a>
                @else
                    <span class="pagination-btn disabled">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 6 15 12 9 18"/></svg>
                    </span>
                @endif
            </div>
        @endif
    </div>
@endif
