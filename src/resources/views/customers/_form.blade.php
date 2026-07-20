<div class="form-card">

  <form method="POST" action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}"
    novalidate>
    @csrf

    @isset($customer)
      @method('PUT')
    @endisset

    @if (auth()->user()->role === 'admin')
      <div class="form-group">
        <label for="user_id" class="form-label">担当営業</label>

        <select id="user_id" class="form-select" name="user_id" required>
          @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected(old('user_id', $customer->user_id ?? '') == $user->id)>
              {{ $user->name }}
            </option>
          @endforeach
        </select>

        @error('user_id')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>
    @else
      <input type="hidden" name="user_id" value="{{ old('user_id', $customer->user_id ?? auth()->id()) }}">
    @endif

    <div class="form-group">
      <label for="company_name" class="form-label">会社名</label>

      <input id="company_name" class="form-input" type="text" name="company_name"
        value="{{ old('company_name', $customer->company_name ?? '') }}" autofocus required>

      @error('company_name')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="contact_name" class="form-label">担当者名</label>

      <input id="contact_name" class="form-input" type="text" name="contact_name"
        value="{{ old('contact_name', $customer->contact_name ?? '') }}" required>

      @error('contact_name')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="email" class="form-label">メールアドレス</label>

      <input id="email" class="form-input" type="email" name="email"
        value="{{ old('email', $customer->email ?? '') }}">

      @error('email')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="phone" class="form-label">電話番号</label>

      <input id="phone" class="form-input" type="text" name="phone"
        value="{{ old('phone', $customer->phone ?? '') }}">

      @error('phone')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="address" class="form-label">住所</label>

      <input id="address" class="form-input" type="text" name="address"
        value="{{ old('address', $customer->address ?? '') }}">

      @error('address')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="industry" class="form-label">業種</label>

      <input id="industry" class="form-input" type="text" name="industry"
        value="{{ old('industry', $customer->industry ?? '') }}">

      @error('industry')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="memo" class="form-label">備考</label>

      <textarea id="memo" class="form-textarea" name="memo" rows="5">{{ old('memo', $customer->memo ?? '') }}</textarea>

      @error('memo')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-actions u-flex">
      <a href="{{ route('customers.index') }}" class="c-button c-button--secondary">
        戻る
      </a>

      <button type="submit" class="c-button c-button--primary">
        {{ isset($customer) ? '更新' : '登録' }}
      </button>
    </div>

  </form>
</div>
