@if ($paginator->hasPages())
  <nav class="c-pagination" aria-label="ページネーション">

    {{-- 前へ --}}
    @if ($paginator->onFirstPage())
      <span class="c-pagination__link c-pagination__link--disabled">
        ‹
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="c-pagination__link">
        ‹
      </a>
    @endif

    {{-- ページ番号 --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="c-pagination__ellipsis">
          {{ $element }}
        </span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="c-pagination__link c-pagination__link--active">
              {{ $page }}
            </span>
          @else
            <a href="{{ $url }}" class="c-pagination__link">
              {{ $page }}
            </a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- 次へ --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="c-pagination__link">
        ›
      </a>
    @else
      <span class="c-pagination__link c-pagination__link--disabled">
        ›
      </span>
    @endif

  </nav>
@endif
