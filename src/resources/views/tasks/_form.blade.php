<div class="form-card">

  <form method="POST" action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}" novalidate>
    @csrf

    @isset($task)
      @method('PUT')
    @endisset

    <div class="form-group">
      <label for="deal_id" class="form-label">商談</label>

      <select id="deal_id" class="form-select" name="deal_id" required>
        <option value="">選択してください</option>

        @foreach ($deals as $deal)
          <option value="{{ $deal->id }}" @selected(old('deal_id', $task->deal_id ?? '') == $deal->id)>
            {{ $deal->title }}
          </option>
        @endforeach
      </select>

      @error('deal_id')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="title" class="form-label">タスク名</label>

      <input id="title" class="form-input" type="text" name="title"
        value="{{ old('title', $task->title ?? '') }}" required maxlength="120">

      @error('title')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="description" class="form-label">タスク詳細</label>

      <textarea id="description" class="form-textarea" name="description" rows="5">{{ old('description', $task->description ?? '') }}</textarea>

      @error('description')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="due_date" class="form-label">期限日</label>

      <input id="due_date" class="form-input" type="date" name="due_date"
        value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d') : '') }}" required>

      @error('due_date')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="priority" class="form-label">優先度</label>

      <select id="priority" class="form-select" name="priority" required>
        @foreach (\App\Enums\TaskPriority::cases() as $priority)
          <option value="{{ $priority->value }}" @selected(old('priority', $task->priority->value ?? \App\Enums\TaskPriority::MEDIUM->value) === $priority->value)>
            {{ $priority->label() }}
          </option>
        @endforeach
      </select>

      @error('priority')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <input type="hidden" name="is_completed" value="0">
      <label class="form-label">
        <input type="checkbox" name="is_completed" value="1" @checked(old('is_completed', $task->is_completed ?? false))>

        完了
      </label>

      @error('is_completed')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-actions u-flex">
      <a href="{{ route('tasks.index') }}" class="c-button c-button--secondary">
        戻る
      </a>

      <button type="submit" class="c-button c-button--primary">
        {{ isset($task) ? '更新' : '登録' }}
      </button>
    </div>

  </form>

</div>
