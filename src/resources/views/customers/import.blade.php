@extends('layouts.app')

@section('title', '顧客CSVインポート')

@section('content')
  <div class="customers">

    <div class="customers__header u-flex">
      <h2 class="page-title">顧客CSVインポート</h2>
    </div>
    <div class="customers__form u-flex">
      <div class="form-card">

        @include('partials.alerts')

        @if (session('result'))
          @php($result = session('result'))

          <div class="c-alert c-alert--info">
            <p>成功件数：{{ $result['success'] }}件</p>
            <p>失敗件数：{{ $result['failed'] }}件</p>
          </div>
        @endif

        @if (session('result') && !empty(session('result')['errors']))
          <div class="c-alert c-alert--error">
            <ul>
              @foreach (session('result')['errors'] as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('customers.import.store') }}" enctype="multipart/form-data" novalidate>
          @csrf

          <div class="form-group">
            <label for="csv" class="form-label">
              CSVファイル
            </label>

            <input id="csv" class="form-input" type="file" name="csv" accept=".csv" required>

            @error('csv')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-actions u-flex">
            <a href="{{ route('customers.index') }}" class="c-button c-button--secondary">
              戻る
            </a>

            <button type="submit" class="c-button c-button--primary">
              インポート
            </button>
          </div>

        </form>

      </div>
    </div>
  </div>
@endsection
