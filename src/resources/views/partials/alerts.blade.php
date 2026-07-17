@foreach (['success', 'error'] as $key)
  @if (session($key))
    <div class="c-alert c-alert--{{ $key }}">
      {{ session($key) }}
    </div>
  @endif
@endforeach
