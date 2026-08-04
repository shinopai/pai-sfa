@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
  <div class="dashboard">
    @include('partials.alerts')

    <div class="u-wrap">

      <h2 class="dashboard__title">ダッシュボード</h2>

      <div class="dashboard__cards">

        <a href="{{ route('customers.index') }}" class="dashboard__card">
          <p class="dashboard__card-title">顧客数</p>

          <div class="dashboard__card-value">
            <span class="dashboard__card-number">{{ $customerCount }}</span>
            <span class="dashboard__card-unit">件</span>
          </div>
        </a>

        <a href="{{ route('deals.index') }}" class="dashboard__card">
          <p class="dashboard__card-title">商談数</p>

          <div class="dashboard__card-value">
            <span class="dashboard__card-number">{{ $dealCount }}</span>
            <span class="dashboard__card-unit">件</span>
          </div>
        </a>

        <a href="{{ route('tasks.index') }}" class="dashboard__card">
          <p class="dashboard__card-title">未完了タスク</p>

          <div class="dashboard__card-value">
            <span class="dashboard__card-number">{{ $incompleteTaskCount }}</span>
            <span class="dashboard__card-unit">件</span>
          </div>
        </a>

      </div>

      <div class="dashboard__charts">

        <section class="dashboard__chart-card">
          <div class="dashboard__section-header">
            <h2 class="dashboard__heading">月別商談件数</h2>
          </div>
          <div class="dashboard__chart-wrapper u-flex">
            <canvas id="monthlyDealsChart" data-chart='@json($monthlyDeals)'></canvas>
          </div>
        </section>

        <section class="dashboard__chart-card">
          <div class="dashboard__section-header">
            <h2 class="dashboard__heading">商談ステータス割合</h2>
          </div>
          <div class="dashboard__chart-wrapper u-flex">
            <canvas id="dealStatusChart" data-chart='@json($dealStatus)'></canvas>
          </div>
        </section>

        <section class="dashboard__chart-card">
          <div class="dashboard__section-header">
            <h2 class="dashboard__heading">月別営業活動件数</h2>
          </div>
          <div class="dashboard__chart-wrapper u-flex">
            <canvas id="monthlyActivitiesChart" data-chart='@json($monthlyActivities)'></canvas>
          </div>
        </section>

        <section class="dashboard__chart-card">
          <div class="dashboard__section-header">
            <h2 class="dashboard__heading">タスク完了率</h2>
          </div>
          <div class="dashboard__chart-wrapper u-flex">
            <canvas id="taskCompletionChart" data-chart='@json($taskCompletion)'></canvas>
          </div>
        </section>

        <script>
          window.dashboardCharts = true;
        </script>
      </div>

      <div class="dashboard__contents">

        <section class="dashboard__section">

          <div class="dashboard__section-header">
            <h2 class="dashboard__heading">最近更新した商談</h2>

            <a href="{{ route('deals.index') }}" class="dashboard__link">
              一覧を見る →
            </a>
          </div>

          <div class="dashboard__table">
            <table class="c-table">
              <thead>
                <tr>
                  <th>商談名</th>
                  <th>顧客名</th>
                  <th>ステータス</th>
                  <th>契約予定日</th>
                </tr>
              </thead>

              <tbody>
                @forelse ($recentDeals as $deal)
                  <tr>
                    <td>{{ $deal->title }}</td>
                    <td>{{ $deal->customer->company_name }}</td>
                    <td>{{ $deal->status->label() }}</td>
                    <td>{{ optional($deal->expected_contract_date)->format('Y/m/d') ?? '-' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="dashboard__empty">
                      商談は登録されていません。
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </section>

        <section class="dashboard__section">

          <div class="dashboard__section-header">
            <h2 class="dashboard__heading">今日のタスク</h2>

            <a href="{{ route('tasks.index') }}" class="dashboard__link">
              一覧を見る →
            </a>
          </div>

          <div class="dashboard__table">
            <table class="c-table">
              <thead>
                <tr>
                  <th>商談名</th>
                  <th>タスク名</th>
                  <th>期限日</th>
                  <th>優先度</th>
                  <th>完了</th>
                </tr>
              </thead>

              <tbody>
                @forelse ($todayTasks as $task)
                  <tr>
                    <td>{{ $task->deal->title }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->due_date->format('Y/m/d') }}</td>
                    <td>{{ $task->priority->label() }}</td>
                    <td>{{ $task->is_completed ? '完了' : '未完了' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="dashboard__empty">
                      今日のタスクはありません。
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </section>

      </div>

    </div>
  </div>
@endsection
