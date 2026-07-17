@extends('layouts.app')

@section('title', 'ユーザー管理')

@section('content')
  <div class="users">
    @include('partials.alerts')

    <div class="users__header u-flex">
      <h1 class="users__title">ユーザー管理</h1>

      <a href="{{ route('users.create') }}" class="c-button c-button--primary">
        新規登録
      </a>
    </div>

    <div class="users__table">
      <table class="c-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>氏名</th>
            <th>メールアドレス</th>
            <th>権限</th>
            <th>登録日</th>
            <th>操作</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($users as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->role === 'admin' ? '管理者' : '営業担当' }}</td>
              <td>{{ $user->created_at->format('Y/m/d') }}</td>
              <td>
                <div class="c-table-actions u-flex">

                  <a href="{{ route('users.edit', $user) }}" class="c-button c-button--secondary">
                    編集
                  </a>

                  @if (auth()->id() !== $user->id)
                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('このユーザーを削除しますか？');">
                      @csrf
                      @method('DELETE')

                      <button type="submit" class="c-button c-button--danger">
                        削除
                      </button>
                    </form>
                  @endif

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="users__empty">
                ユーザーが登録されていません。
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="users__pagination">
      {{ $users->links() }}
    </div>

  </div>
@endsection
