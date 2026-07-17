<div class="form-card">

  <form method="POST" action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" novalidate>
    @csrf

    @isset($user)
      @method('PUT')
    @endisset

    <div class="form-group">
      <label for="name" class="form-label">氏名</label>

      <input id="name" class="form-input" type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
        autofocus required>

      @error('name')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="email" class="form-label">メールアドレス</label>

      <input id="email" class="form-input" type="email" name="email"
        value="{{ old('email', $user->email ?? '') }}" required>

      @error('email')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="role" class="form-label">権限</label>

      <select id="role" class="form-select" name="role" required>
        <option value="sales" @selected(old('role', $user->role ?? 'sales') === 'sales')>
          営業担当
        </option>

        <option value="admin" @selected(old('role', $user->role ?? '') === 'admin')>
          管理者
        </option>
      </select>

      @error('role')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="password" class="form-label">
        パスワード
      </label>

      <input id="password" class="form-input" type="password" name="password" {{ isset($user) ? '' : 'required' }}>

      @error('password')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="password_confirmation" class="form-label">
        パスワード（確認）
      </label>

      <input id="password_confirmation" class="form-input" type="password" name="password_confirmation"
        {{ isset($user) ? '' : 'required' }}>
    </div>

    <div class="form-actions u-flex">
      <a href="{{ route('users.index') }}" class="c-button c-button--secondary">
        戻る
      </a>

      <button type="submit" class="c-button c-button--primary">
        {{ isset($user) ? '更新' : '登録' }}
      </button>
    </div>

  </form>
</div>
