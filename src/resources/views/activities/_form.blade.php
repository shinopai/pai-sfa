<div class="form-card">

  <form method="POST" action="{{ isset($activity) ? route('activities.update', $activity) : route('activities.store') }}"
    novalidate>
    @csrf

    @isset($activity)
      @method('PUT')
    @endisset

    <div class="form-group">
      <label for="deal_id" class="form-label">商談</label>

      <select id="deal_id" class="form-select" name="deal_id" required>
        <option value="">選択してください</option>

        @foreach ($deals as $deal)
          <option value="{{ $deal->id }}" @selected(old('deal_id', $activity->deal_id ?? '') == $deal->id)>
            {{ $deal->title }}
          </option>
        @endforeach
      </select>

      @error('deal_id')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="activity_type" class="form-label">活動種別</label>

      <select id="activity_type" class="form-select" name="activity_type" required>
        @foreach (\App\Enums\ActivityType::cases() as $type)
          <option value="{{ $type->value }}" @selected(old('activity_type', $activity->activity_type->value ?? \App\Enums\ActivityType::PHONE->value) === $type->value)>
            {{ $type->label() }}
          </option>
        @endforeach
      </select>

      @error('activity_type')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="activity_date" class="form-label">活動日時</label>

      <input id="activity_date" class="form-input" type="datetime-local" name="activity_date"
        value="{{ old('activity_date', isset($activity) && $activity->activity_date ? $activity->activity_date->format('Y-m-d\TH:i') : '') }}"
        required>

      @error('activity_date')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="content" class="form-label">活動内容</label>

      <textarea id="content" class="form-textarea" name="content" rows="5" required>{{ old('content', $activity->content ?? '') }}</textarea>

      @error('content')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-actions u-flex">
      <a href="{{ route('activities.index') }}" class="c-button c-button--secondary">
        戻る
      </a>

      <button type="submit" class="c-button c-button--primary">
        {{ isset($activity) ? '更新' : '登録' }}
      </button>
    </div>

  </form>

</div>
