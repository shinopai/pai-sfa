@extends('layouts.app')

@section('title', 'タスク管理')

@section('content')
  <div class="tasks">
    @include('partials.alerts')

    <div class="tasks__header u-flex">
      <h1 class="tasks__title">タスク管理</h1>

      <a href="{{ route('tasks.create') }}" class="c-button c-button--primary">
        新規登録
      </a>
    </div>

    <div class="c-search">
      <form action="{{ route('tasks.index') }}" method="GET" class="c-search__form">

        <div class="c-search__group u-flex">
          <input id="keyword" type="text" name="keyword" value="{{ request('keyword') }}" class="c-search__input"
            placeholder="タスク名・商談名・顧客名・担当営業で検索">
        </div>

        <div class="c-search__actions u-flex">

          <button type="submit" class="c-button c-button--primary">
            検索
          </button>

          <a href="{{ route('tasks.index') }}" class="c-button c-button--secondary">
            リセット
          </a>

        </div>

      </form>
    </div>

    <div class="tasks__table">
      <table class="c-table">
        <thead>
          <tr>

            <th>
              <a
                href="{{ route(
                    'tasks.index',
                    array_merge(request()->query(), [
                        'sort' => 'id',
                        'direction' => request('sort') === 'id' && request('direction') === 'asc' ? 'desc' : 'asc',
                    ]),
                ) }}">
                ID
                @if (request('sort') === 'id')
                  {{ request('direction') === 'asc' ? '▲' : '▼' }}
                @else
                  ⇅
                @endif
              </a>
            </th>

            <th>商談名</th>

            <th>顧客名</th>

            <th>担当営業</th>

            <th>
              <a
                href="{{ route(
                    'tasks.index',
                    array_merge(request()->query(), [
                        'sort' => 'title',
                        'direction' => request('sort') === 'title' && request('direction') === 'asc' ? 'desc' : 'asc',
                    ]),
                ) }}">
                タスク名
                @if (request('sort') === 'title')
                  {{ request('direction') === 'asc' ? '▲' : '▼' }}
                @else
                  ⇅
                @endif
              </a>
            </th>

            <th>
              <a
                href="{{ route(
                    'tasks.index',
                    array_merge(request()->query(), [
                        'sort' => 'due_date',
                        'direction' => request('sort') === 'due_date' && request('direction') === 'asc' ? 'desc' : 'asc',
                    ]),
                ) }}">
                期限日
                @if (request('sort') === 'due_date')
                  {{ request('direction') === 'asc' ? '▲' : '▼' }}
                @else
                  ⇅
                @endif
              </a>
            </th>

            <th>優先度</th>

            <th>完了</th>

            <th>
              <a
                href="{{ route(
                    'tasks.index',
                    array_merge(request()->query(), [
                        'sort' => 'created_at',
                        'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc',
                    ]),
                ) }}">
                登録日
                @if (request('sort') === 'created_at')
                  {{ request('direction') === 'asc' ? '▲' : '▼' }}
                @else
                  ⇅
                @endif
              </a>
            </th>

            <th>操作</th>

          </tr>
        </thead>

        <tbody>
          @forelse ($tasks as $task)
            <tr>
              <td>{{ $task->id }}</td>
              <td>{{ $task->deal->title }}</td>
              <td>{{ $task->deal->customer->company_name }}</td>
              <td>{{ $task->deal->customer->user->name }}</td>
              <td>{{ $task->title }}</td>
              <td>{{ $task->due_date->format('Y/m/d') }}</td>
              <td>{{ $task->priority->label() }}</td>
              <td>{{ $task->is_completed ? '完了' : '未完了' }}</td>
              <td>{{ $task->created_at->format('Y/m/d') }}</td>
              <td>
                <div class="c-table-actions u-flex">

                  <a href="{{ route('tasks.edit', $task) }}" class="c-button c-button--secondary">
                    編集
                  </a>

                  <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                    onsubmit="return confirm('このタスクを削除しますか？');">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="c-button c-button--danger">
                      削除
                    </button>

                  </form>

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="tasks__empty">
                タスクが登録されていません。
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="tasks__pagination">
      {{ $tasks->links() }}
    </div>

  </div>
@endsection
