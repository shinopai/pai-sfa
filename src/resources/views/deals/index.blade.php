@extends('layouts.app')

@section('title', '商談管理')

@section('content')
  <div class="deals">
    @include('partials.alerts')

    <div class="deals__header u-flex">
      <h1 class="deals__title">商談管理</h1>

      <a href="{{ route('deals.create') }}" class="c-button c-button--primary">
        新規登録
      </a>
    </div>

    <div class="c-search">
      <form action="{{ route('deals.index') }}" method="GET" class="c-search__form">

        <div class="c-search__group u-flex">
          <input id="keyword" type="text" name="keyword" value="{{ request('keyword') }}" class="c-search__input"
            placeholder="商談名・顧客名・担当営業・ステータスで検索">
        </div>

        <div class="c-search__actions u-flex">

          <button type="submit" class="c-button c-button--primary">
            検索
          </button>

          <a href="{{ route('deals.index') }}" class="c-button c-button--secondary">
            リセット
          </a>

        </div>

      </form>
    </div>

    <div class="deals__table">
      <table class="c-table">
        <thead>
          <tr>

            <th>
              <a
                href="{{ route(
                    'deals.index',
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

            <th>
              <a
                href="{{ route(
                    'deals.index',
                    array_merge(request()->query(), [
                        'sort' => 'title',
                        'direction' => request('sort') === 'title' && request('direction') === 'asc' ? 'desc' : 'asc',
                    ]),
                ) }}">
                商談名
                @if (request('sort') === 'title')
                  {{ request('direction') === 'asc' ? '▲' : '▼' }}
                @else
                  ⇅
                @endif
              </a>
            </th>

            <th>顧客名</th>

            <th>担当営業</th>

            <th>金額</th>

            <th>ステータス</th>

            <th>
              <a
                href="{{ route(
                    'deals.index',
                    array_merge(request()->query(), [
                        'sort' => 'expected_contract_date',
                        'direction' => request('sort') === 'expected_contract_date' && request('direction') === 'asc' ? 'desc' : 'asc',
                    ]),
                ) }}">
                契約予定日
                @if (request('sort') === 'expected_contract_date')
                  {{ request('direction') === 'asc' ? '▲' : '▼' }}
                @else
                  ⇅
                @endif
              </a>
            </th>

            <th>
              <a
                href="{{ route(
                    'deals.index',
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
          @forelse ($deals as $deal)
            <tr>
              <td>{{ $deal->id }}</td>
              <td>{{ $deal->title }}</td>
              <td>{{ $deal->customer->company_name }}</td>
              <td>{{ $deal->customer->user->name }}</td>
              <td>{{ $deal->amount ? number_format($deal->amount) . '円' : '-' }}</td>
              <td>{{ $deal->status->label() }}</td>
              <td>{{ $deal->expected_contract_date?->format('Y/m/d') ?? '-' }}</td>
              <td>{{ $deal->created_at->format('Y/m/d') }}</td>
              <td>
                <div class="c-table-actions u-flex">

                  <a href="{{ route('deals.edit', $deal) }}" class="c-button c-button--secondary">
                    編集
                  </a>

                  <form action="{{ route('deals.destroy', $deal) }}" method="POST"
                    onsubmit="return confirm('この商談を削除しますか？');">
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
              <td colspan="9" class="deals__empty">
                商談が登録されていません。
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="deals__pagination">
      {{ $deals->links() }}
    </div>

  </div>
@endsection
