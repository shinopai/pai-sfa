@extends('layouts.app')

@section('title', '営業活動管理')

@section('content')
  <div class="activities">
    @include('partials.alerts')

    <div class="activities__header u-flex">
      <h1 class="activities__title">営業活動管理</h1>

      <a href="{{ route('activities.create') }}" class="c-button c-button--primary">
        新規登録
      </a>
    </div>

    <div class="activities__table">
      <table class="c-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>商談名</th>
            <th>顧客名</th>
            <th>担当営業</th>
            <th>活動種別</th>
            <th>活動日時</th>
            <th>登録日</th>
            <th>操作</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($activities as $activity)
            <tr>
              <td>{{ $activity->id }}</td>
              <td>{{ $activity->deal->title }}</td>
              <td>{{ $activity->deal->customer->company_name }}</td>
              <td>{{ $activity->deal->user->name }}</td>
              <td>{{ $activity->activity_type->label() }}</td>
              <td>{{ $activity->activity_date->format('Y/m/d H:i') }}</td>
              <td>{{ $activity->created_at->format('Y/m/d') }}</td>
              <td>
                <div class="c-table-actions u-flex">

                  <a href="{{ route('activities.edit', $activity) }}" class="c-button c-button--secondary">
                    編集
                  </a>

                  <form action="{{ route('activities.destroy', $activity) }}" method="POST"
                    onsubmit="return confirm('この営業活動を削除しますか？');">
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
              <td colspan="8" class="activities__empty">
                営業活動が登録されていません。
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="activities__pagination">
      {{ $activities->links() }}
    </div>

  </div>
@endsection
