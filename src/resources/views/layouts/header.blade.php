<header class="header">
  <div class="header__inner u-wrap u-flex">
    <a class="header__logo" href="{{ route('dashboard') }}">
      <img src="{{ Vite::asset('resources/images/logo/pai-sfa-logo.svg') }}" alt="PaiSFA">
    </a>

    <nav class="header__nav u-flex" aria-label="メニュー">
      <a href="{{ route('dashboard') }}" class="header__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
        ダッシュボード
      </a>

      <a href="#" class="header__link {{ request()->routeIs('customers.*') ? 'is-active' : '' }}">
        顧客
      </a>

      <a href="#" class="header__link {{ request()->routeIs('deals.*') ? 'is-active' : '' }}">
        商談
      </a>

      <a href="#" class="header__link {{ request()->routeIs('activities.*') ? 'is-active' : '' }}">
        営業活動
      </a>

      <a href="#" class="header__link {{ request()->routeIs('tasks.*') ? 'is-active' : '' }}">
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
