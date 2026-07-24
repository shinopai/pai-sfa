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

    <div class="tasks__table">
      <table class="c-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>商談名</th>
            <th>顧客名</th>
            <th>担当営業</th>
            <th>タスク名</th>
            <th>期限日</th>
            <th>優先度</th>
            <th>完了</th>
            <th>登録日</th>
            <th>操作</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($tasks as $task)
            <tr>
              <td>{{ $task->id }}</td>
              <td>{{ $task->deal->title }}</td>
              <td>{{ $task->deal->customer->company_name }}</td>
              <td>{{ $task->deal->user->name }}</td>
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
