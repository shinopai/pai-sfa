<header class="header">
  <div class="header__inner u-wrap u-flex">
    <a class="header__logo" href="{{ route('dashboard') }}">
      <img src="{{ Vite::asset('resources/images/logo/pai-sfa-logo.svg') }}" alt="PaiSFA">
    </a>

    <nav class="header__nav u-flex" aria-label="メニュー">
      <a class="header__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">
        ダッシュボード
      </a>
      <a class="header__link" href="#">
        顧客
      </a>
      <a class="header__link" href="#">
        商談
      </a>
      <a class="header__link" href="#">
        営業活動
      </a>
      <a class="header__link" href="#">
        タスク
      </a>
      {{-- @if (auth()->user()->isAdmin())
            <a class="header__link" href="#">
              ユーザー
            </a>
          @endif --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button class="header__logout" type="submit">
          ログアウト
        </button>
      </form>
    </nav>

  </div>
</header>
