@extends('layouts.app')

@section('content')
  <section class="p-login">
    <div class="form-card">
      <h1 class="form-title">ログイン</h1>

      <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="form-group">
          <label for="email" class="form-label">メールアドレス</label>

          <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}"
            autocomplete="username" autofocus required>

          @error('email')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group">
          <label for="password" class="form-label">パスワード</label>

          <input id="password" class="form-input" type="password" name="password" autocomplete="current-password"
            required>

          @error('password')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-check">
            <input class="form-checkbox" type="checkbox" name="remember">
            <span class="form-check-label">ログイン状態を保持</span>
          </label>
        </div>

        <div class="form-actions">
          <button type="submit" class="form-button">
            ログイン
          </button>
        </div>
      </form>
    </div>
  </section>
@endsection
