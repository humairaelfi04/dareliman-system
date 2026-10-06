@if ($paginator->hasPages())
    <div class="px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
        
        {{-- Mobile view (only showing Prev/Next) --}}
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination mb-0 gap-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link border rounded text-slate-400 bg-slate-50">@lang('pagination.previous')</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link border rounded text-dark bg-white" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link border rounded text-dark bg-white" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link border rounded text-slate-400 bg-slate-50">@lang('pagination.next')</span>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Desktop view --}}
        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <p class="small text-slate-500 mb-0 fw-medium">
                    Showing
                    <span class="fw-bold text-dark">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</span>
                    of
                    <span class="fw-bold text-dark">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            <div>
                <ul class="pagination mb-0 align-items-center" style="gap: 0.35rem;">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link border text-slate-400 bg-slate-50 d-flex align-items-center justify-content-center" style="border-radius: 4px; width: 34px; height: 34px; padding: 0;" aria-hidden="true">
                                <i class="bi bi-chevron-left small"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link border text-dark bg-white d-flex align-items-center justify-content-center" style="border-radius: 4px; width: 34px; height: 34px; padding: 0;" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                                <i class="bi bi-chevron-left small"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link border-0 bg-transparent text-slate-500 d-flex align-items-center justify-content-center fw-medium" style="width: 34px; height: 34px; padding: 0;">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link border bg-emerald-700 text-white shadow-sm d-flex align-items-center justify-content-center fw-bold" style="border-color: #065f46; background-color: #065f46; border-radius: 4px; width: 34px; height: 34px; padding: 0;">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link border text-slate-700 bg-white d-flex align-items-center justify-content-center fw-medium hover-bg-slate-50" style="border-radius: 4px; width: 34px; height: 34px; padding: 0; transition: all 0.2s;" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link border text-dark bg-white d-flex align-items-center justify-content-center" style="border-radius: 4px; width: 34px; height: 34px; padding: 0;" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                                <i class="bi bi-chevron-right small"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link border text-slate-400 bg-slate-50 d-flex align-items-center justify-content-center" style="border-radius: 4px; width: 34px; height: 34px; padding: 0;" aria-hidden="true">
                                <i class="bi bi-chevron-right small"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif
