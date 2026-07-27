<div class="c-search">
  <form action="{{ $action }}" method="GET" class="c-search__form">

    <div class="c-search__group u-flex">

      <input id="keyword" type="text" name="keyword" value="{{ request('keyword') }}" class="c-search__input"
        placeholder="{{ $placeholder }}">

    </div>

    <div class="c-search__actions u-flex">

      <button type="submit" class="c-button c-button--primary">
        検索
      </button>

      <a href="{{ $action }}" class="c-button c-button--secondary">
        リセット
      </a>

    </div>

  </form>
</div>
