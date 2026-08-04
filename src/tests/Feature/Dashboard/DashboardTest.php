<?php

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    /** @var \App\Models\User $this->admin */
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    /** @var \App\Models\User $this->sales */
    $this->sales = User::factory()->create([
        'role' => 'sales',
    ]);
});

it('管理者はダッシュボードを表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('ダッシュボード');
});

it('営業担当はダッシュボードを表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('ダッシュボード');
});

it('未ログインユーザーはログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

it('管理者は件数カードに全件の集計結果を表示する', function () {
    Customer::factory()->count(3)->create();

    $customers = Customer::all();

    foreach ($customers as $customer) {
        $deal = Deal::factory()->create([
            'customer_id' => $customer->id,
        ]);

        Task::factory()->create([
            'deal_id' => $deal->id,
            'is_completed' => false,
        ]);
    }

    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('3');
});

it('営業担当は自分が担当する件数のみ表示する', function () {
    $myCustomer = Customer::factory()->create([
        'user_id' => $this->sales->id,
    ]);

    $otherSales = User::factory()->create([
        'role' => 'sales',
    ]);

    $otherCustomer = Customer::factory()->create([
        'user_id' => $otherSales->id,
    ]);

    $myDeal = Deal::factory()->create([
        'customer_id' => $myCustomer->id,
    ]);

    Deal::factory()->create([
        'customer_id' => $otherCustomer->id,
    ]);

    Task::factory()->create([
        'deal_id' => $myDeal->id,
        'is_completed' => false,
    ]);

    $otherDeal = Deal::where('customer_id', $otherCustomer->id)->first();

    Task::factory()->create([
        'deal_id' => $otherDeal->id,
        'is_completed' => false,
    ]);

    $response = $this
        ->actingAs($this->sales)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('1');
});

it('最近更新した商談を表示する', function () {
    $customer = Customer::factory()->create();

    $latestDeal = Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => '最新商談',
    ]);

    Deal::factory()->count(2)->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('最近更新した商談')
        ->assertSee($latestDeal->title);
});

it('今日が期限の未完了タスクを表示する', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $todayTask = Task::factory()->create([
        'deal_id' => $deal->id,
        'title' => '今日のタスク',
        'due_date' => today(),
        'is_completed' => false,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'title' => '完了済みタスク',
        'due_date' => today(),
        'is_completed' => true,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'title' => '明日のタスク',
        'due_date' => today()->addDay(),
        'is_completed' => false,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('今日のタスク')
        ->assertSee($todayTask->title)
        ->assertDontSee('完了済みタスク')
        ->assertDontSee('明日のタスク');
});

it('月別商談件数グラフ用データを出力する', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->count(3)->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('monthlyDealsChart')
        ->assertSee('data-chart=', false);
});

it('商談ステータス割合グラフ用データを出力する', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->create([
        'customer_id' => $customer->id,
        'status' => 'new',
    ]);

    Deal::factory()->create([
        'customer_id' => $customer->id,
        'status' => 'won',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('dealStatusChart')
        ->assertSee('data-chart=', false);
});

it('月別営業活動件数グラフ用データを出力する', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Activity::factory()->count(2)->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('monthlyActivitiesChart')
        ->assertSee('data-chart=', false);
});

it('タスク完了率グラフ用データを出力する', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'is_completed' => true,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'is_completed' => false,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('taskCompletionChart')
        ->assertSee('data-chart=', false);
});

it('データが存在しなくてもダッシュボードを表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('ダッシュボード')
        ->assertSee('monthlyDealsChart')
        ->assertSee('dealStatusChart')
        ->assertSee('monthlyActivitiesChart')
        ->assertSee('taskCompletionChart');
});
