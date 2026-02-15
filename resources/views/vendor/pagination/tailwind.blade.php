@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        
        {{-- Mobile View --}}
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-6 py-3 text-[10px] font-black text-slate-300 bg-white border border-slate-100 rounded-2xl cursor-default uppercase">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-6 py-3 text-[10px] font-black text-slate-600 bg-white border border-slate-200 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm uppercase">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-6 py-3 text-[10px] font-black text-slate-600 bg-white border border-slate-200 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm uppercase">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-6 py-3 text-[10px] font-black text-slate-300 bg-white border border-slate-100 rounded-2xl cursor-default uppercase">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-8">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-5">
                    {!! __('Showing') !!}
                    <span class="text-indigo-600 italic font-mono text-sm mx-1">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="text-indigo-600 italic font-mono text-sm mx-1">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="text-indigo-600 italic font-mono text-sm mx-1">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center justify-center w-10 h-10 text-slate-300 bg-white border border-slate-100 cursor-default rounded-xl transition-all">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center justify-center w-10 h-10 text-slate-500 bg-white border border-slate-200 rounded-xl hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm group">
                            <i class="fa-solid fa-chevron-right text-[10px] group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-10 h-10 text-slate-400 font-black text-[10px]">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-10 h-10 text-xs font-black bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-100 z-10">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center justify-center w-10 h-10 text-xs font-black text-slate-500 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center justify-center w-10 h-10 text-slate-500 bg-white border border-slate-200 rounded-xl hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm group">
                            <i class="fa-solid fa-chevron-left text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    @else
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center justify-center w-10 h-10 text-slate-300 bg-white border border-slate-100 cursor-default rounded-xl transition-all">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif