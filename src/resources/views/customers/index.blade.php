@extends('layouts.app')

@section('title', '顧客管理')

@section('content')
  <div class="customers">
    @include('partials.alerts')

    <div class="customers__header u-flex">
      <h1 class="customers__title">顧客管理</h1>

      <a href="{{ route('customers.create') }}" class="c-button c-button--primary">
        新規登録
      </a>
    </div>

    @include('partials.search', [
        'action' => route('customers.index'),
        'placeholder' => '会社名・担当者名・担当営業で検索',
    ])

    <div class="customers__table">
      <table class="c-table">
        <thead>
          <tr>

            <th>
              @include('partials.sort-link', [
                  'route' => 'customers.index',
                  'column' => 'id',
                  'label' => 'ID',
              ])
            </th>

            <th>
              @include('partials.sort-link', [
                  'route' => 'customers.index',
                  'column' => 'company_name',
                  'label' => '会社名',
              ])
            </th>

            <th>担当者名</th>

            <th>メールアドレス</th>

            <th>電話番号</th>

            <th>担当営業</th>

            <th>
              @include('partials.sort-link', [
                  'route' => 'customers.index',
                  'column' => 'created_at',
                  'label' => '登録日',
              ])
            </th>

            <th>操作</th>

          </tr>
        </thead>

        <tbody>
          @forelse ($customers as $customer)
            <tr>
              <td>{{ $customer->id }}</td>
              <td>{{ $customer->company_name }}</td>
              <td>{{ $customer->contact_name }}</td>
              <td>{{ $customer->email ?? '-' }}</td>
              <td>{{ $customer->phone ?? '-' }}</td>
              <td>{{ $customer->user->name }}</td>
              <td>{{ $customer->created_at->format('Y/m/d') }}</td>
              <td>
                @include('partials.table-actions', [
                    'editRoute' => route('customers.edit', $customer),
                    'deleteRoute' => route('customers.destroy', $customer),
                    'confirmMessage' => 'この顧客を削除しますか？',
                ])
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="customers__empty">
                顧客が登録されていません。
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="customers__pagination">
      {{ $customers->links() }}
    </div>

  </div>
@endsection
