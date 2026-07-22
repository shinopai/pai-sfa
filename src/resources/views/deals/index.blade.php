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

    <div class="deals__table">
      <table class="c-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>商談名</th>
            <th>顧客名</th>
            <th>担当営業</th>
            <th>金額</th>
            <th>ステータス</th>
            <th>契約予定日</th>
            <th>登録日</th>
            <th>操作</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($deals as $deal)
            <tr>
              <td>{{ $deal->id }}</td>
              <td>{{ $deal->title }}</td>
              <td>{{ $deal->customer->company_name }}</td>
              <td>{{ $deal->user->name }}</td>
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
