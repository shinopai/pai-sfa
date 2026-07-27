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

    <div class="c-search">
      <form action="{{ route('activities.index') }}" method="GET" class="c-search__form">

        <div class="c-search__group u-flex">
          <input id="keyword" type="text" name="keyword" value="{{ request('keyword') }}" class="c-search__input"
            placeholder="商談名・顧客名・担当営業で検索">
        </div>

        <div class="c-search__actions u-flex">
          <button type="submit" class="c-button c-button--primary">
            検索
          </button>

          <a href="{{ route('activities.index') }}" class="c-button c-button--secondary">
            リセット
          </a>
        </div>

      </form>
    </div>

    <div class="activities__table">
      <table class="c-table">
        <thead>
          <tr>

            <th>
              <a
                href="{{ route(
                    'activities.index',
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

            <th>活動種別</th>

            <th>
              <a
                href="{{ route(
                    'activities.index',
                    array_merge(request()->query(), [
                        'sort' => 'activity_date',
                        'direction' => request('sort') === 'activity_date' && request('direction') === 'asc' ? 'desc' : 'asc',
                    ]),
                ) }}">
                活動日時
                @if (request('sort') === 'activity_date')
                  {{ request('direction') === 'asc' ? '▲' : '▼' }}
                @else
                  ⇅
                @endif
              </a>
            </th>

            <th>
              <a
                href="{{ route(
                    'activities.index',
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
          @forelse ($activities as $activity)
            <tr>
              <td>{{ $activity->id }}</td>
              <td>{{ $activity->deal->title }}</td>
              <td>{{ $activity->deal->customer->company_name }}</td>
              <td>{{ $activity->deal->customer->user->name }}</td>
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
