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

    <div class="c-search">
      <form action="{{ route('customers.index') }}" method="GET" class="c-search__form">

        <div class="c-search__group u-flex">
          <input id="keyword" type="text" name="keyword" value="{{ request('keyword') }}" class="c-search__input"
            placeholder="会社名・担当者名・メールアドレス・担当営業で検索">
        </div>

        <div class="c-search__actions u-flex">
          <button type="submit" class="c-button c-button--primary">
            検索
          </button>

          <a href="{{ route('customers.index') }}" class="c-button c-button--secondary">
            リセット
          </a>
        </div>

      </form>
    </div>

    <div class="customers__table">
      <table class="c-table">
        <thead>
          <tr>

            <th>
              <a
                href="{{ route(
                    'customers.index',
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
                    'customers.index',
                    array_merge(request()->query(), [
                        'sort' => 'company_name',
                        'direction' => request('sort') === 'company_name' && request('direction') === 'asc' ? 'desc' : 'asc',
                    ]),
                ) }}">
                会社名
                @if (request('sort') === 'company_name')
                  {{ request('direction') === 'asc' ? '▲' : '▼' }}
                @else
                  ⇅
                @endif
              </a>
            </th>

            <th>担当者名</th>

            <th>メールアドレス</th>

            <th>電話番号</th>

            <th>担当営業</th>

            <th>
              <a
                href="{{ route(
                    'customers.index',
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
                <div class="c-table-actions u-flex">

                  <a href="{{ route('customers.edit', $customer) }}" class="c-button c-button--secondary">
                    編集
                  </a>

                  <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                    onsubmit="return confirm('この顧客を削除しますか？');">

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
