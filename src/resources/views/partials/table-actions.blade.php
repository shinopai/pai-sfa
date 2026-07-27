<div class="c-table-actions u-flex">

  <a href="{{ $editRoute }}" class="c-button c-button--secondary">
    編集
  </a>

  <form action="{{ $deleteRoute }}" method="POST" onsubmit="return confirm('{{ $confirmMessage }}');">

    @csrf
    @method('DELETE')

    <button type="submit" class="c-button c-button--danger">
      削除
    </button>

  </form>

</div>
