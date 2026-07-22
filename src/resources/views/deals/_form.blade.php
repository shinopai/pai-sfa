<div class="form-card">

  <form method="POST" action="{{ isset($deal) ? route('deals.update', $deal) : route('deals.store') }}" novalidate>
    @csrf

    @isset($deal)
      @method('PUT')
    @endisset

    <div class="form-group">
      <label for="customer_id" class="form-label">顧客</label>

      <select id="customer_id" class="form-select" name="customer_id" required>
        <option value="">選択してください</option>

        @foreach ($customers as $customer)
          <option value="{{ $customer->id }}" @selected(old('customer_id', $deal->customer_id ?? '') == $customer->id)>
            {{ $customer->company_name }}
          </option>
        @endforeach
      </select>

      @error('customer_id')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    @if (auth()->user()->isAdmin())
      <div class="form-group">
        <label for="user_id" class="form-label">担当営業</label>

        <select id="user_id" class="form-select" name="user_id" required>
          @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected(old('user_id', $deal->user_id ?? '') == $user->id)>
              {{ $user->name }}
            </option>
          @endforeach
        </select>

        @error('user_id')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>
    @else
      <input type="hidden" name="user_id" value="{{ old('user_id', $deal->user_id ?? auth()->id()) }}">
    @endif

    <div class="form-group">
      <label for="title" class="form-label">商談名</label>

      <input id="title" class="form-input" type="text" name="title"
        value="{{ old('title', $deal->title ?? '') }}" autofocus required>

      @error('title')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="amount" class="form-label">商談金額</label>

      <input id="amount" class="form-input" type="number" name="amount" min="0"
        value="{{ old('amount', $deal->amount ?? '') }}">

      @error('amount')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="status" class="form-label">ステータス</label>

      <select id="status" class="form-select" name="status" required>
        @foreach (\App\Enums\DealStatus::cases() as $status)
          <option value="{{ $status->value }}" @selected(old('status', $deal->status->value ?? \App\Enums\DealStatus::NEW->value) === $status->value)>
            {{ $status->label() }}
          </option>
        @endforeach
      </select>

      @error('status')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="expected_contract_date" class="form-label">契約予定日</label>

      <input id="expected_contract_date" class="form-input" type="date" name="expected_contract_date"
        value="{{ old('expected_contract_date', isset($deal) && $deal->expected_contract_date ? $deal->expected_contract_date->format('Y-m-d') : '') }}">

      @error('expected_contract_date')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="memo" class="form-label">備考</label>

      <textarea id="memo" class="form-textarea" name="memo" rows="5">{{ old('memo', $deal->memo ?? '') }}</textarea>

      @error('memo')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-actions u-flex">
      <a href="{{ route('deals.index') }}" class="c-button c-button--secondary">
        戻る
      </a>

      <button type="submit" class="c-button c-button--primary">
        {{ isset($deal) ? '更新' : '登録' }}
      </button>
    </div>

  </form>

</div>
