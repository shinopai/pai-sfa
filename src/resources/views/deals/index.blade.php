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

    @include('partials.search', [
        'action' => route('deals.index'),
        'placeholder' => '商談名・顧客名・担当営業で検索',
    ])

    <div class="deals__table">
      <table class="c-table">
        <thead>
          <tr>

            <th>
              @include('partials.sort-link', [
                  'route' => 'deals.index',
                  'column' => 'id',
                  'label' => 'ID',
              ])
            </th>

            <th>
              @include('partials.sort-link', [
                  'route' => 'deals.index',
                  'column' => 'title',
                  'label' => '商談名',
              ])
            </th>

            <th>顧客名</th>

            <th>担当営業</th>

            <th>金額</th>

            <th>ステータス</th>

            <th>
              @include('partials.sort-link', [
                  'route' => 'deals.index',
                  'column' => 'expected_contract_date',
                  'label' => '契約予定日',
              ])
            </th>

            <th>
              @include('partials.sort-link', [
                  'route' => 'deals.index',
                  'column' => 'created_at',
                  'label' => '登録日',
              ])
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
                @include('partials.table-actions', [
                    'editRoute' => route('deals.edit', $deal),
                    'deleteRoute' => route('deals.destroy', $deal),
                    'confirmMessage' => 'この商談を削除しますか？',
                ])
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
