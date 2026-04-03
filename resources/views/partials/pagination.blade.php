@if ($paginator->hasPages())
    <div class="pager">
        <div class="pager-copy">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </div>

        <div class="toolbar-actions">
            @if ($paginator->onFirstPage())
                <span class="btn btn-secondary" style="opacity: .55;">Previous</span>
            @else
                <a class="btn btn-secondary" href="{{ $paginator->previousPageUrl() }}">Previous</a>
            @endif

            @if ($paginator->hasMorePages())
                <a class="btn btn-secondary" href="{{ $paginator->nextPageUrl() }}">Next</a>
            @else
                <span class="btn btn-secondary" style="opacity: .55;">Next</span>
            @endif
        </div>
    </div>
@endif
