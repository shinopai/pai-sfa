<a
  href="{{ route(
      $route,
      array_merge(request()->query(), [
          'sort' => $column,
          'direction' => request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc',
      ]),
  ) }}">
  {{ $label }}

  @if (request('sort') === $column)
    {{ request('direction') === 'asc' ? '▲' : '▼' }}
  @else
    ⇅
  @endif
</a>
