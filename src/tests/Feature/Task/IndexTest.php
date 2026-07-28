<?php

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

it('管理者はタスク一覧画面を表示できる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->count(3)->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index'));

    $response
        ->assertOk()
        ->assertSee('タスク管理');
});

it('営業担当は自分が担当する商談のタスクのみ表示される', function () {
    $otherSales = User::factory()->create([
        'role' => 'sales',
    ]);

    $myCustomer = Customer::factory()->create([
        'user_id' => $this->sales->id,
    ]);

    $otherCustomer = Customer::factory()->create([
        'user_id' => $otherSales->id,
    ]);

    $myDeal = Deal::factory()->create([
        'customer_id' => $myCustomer->id,
        'title' => '自分の商談',
    ]);

    $otherDeal = Deal::factory()->create([
        'customer_id' => $otherCustomer->id,
        'title' => '他人の商談',
    ]);

    Task::factory()->create([
        'deal_id' => $myDeal->id,
    ]);

    Task::factory()->create([
        'deal_id' => $otherDeal->id,
    ]);

    $response = $this
        ->actingAs($this->sales)
        ->get(route('tasks.index'));

    $response
        ->assertOk()
        ->assertSee('自分の商談')
        ->assertDontSee('他人の商談');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('tasks.index'));

    $response->assertRedirect(route('login'));
});

it('タスク名で検索できる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'title' => 'Laravel開発',
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'title' => '営業資料作成',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'keyword' => 'Laravel',
        ]));

    $response
        ->assertOk()
        ->assertSee('Laravel開発')
        ->assertDontSee('営業資料作成');
});

it('商談名で検索できる', function () {
    $customer = Customer::factory()->create([
        'company_name' => '株式会社テスト',
        'user_id' => $this->admin->id,
    ]);

    $deal1 = Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'システム導入',
    ]);

    $deal2 = Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'ホームページ制作',
    ]);

    Task::factory()->create([
        'deal_id' => $deal1->id,
        'title' => 'タスクA',
    ]);

    Task::factory()->create([
        'deal_id' => $deal2->id,
        'title' => 'タスクB',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'keyword' => 'システム',
        ]));

    $response
        ->assertOk()
        ->assertSee('システム導入')
        ->assertDontSee('ホームページ制作');
});

it('顧客名で検索できる', function () {
    $customer1 = Customer::factory()->create([
        'company_name' => '株式会社A',
        'user_id' => $this->admin->id,
    ]);

    $customer2 = Customer::factory()->create([
        'company_name' => '株式会社B',
        'user_id' => $this->admin->id,
    ]);

    $deal1 = Deal::factory()->create([
        'customer_id' => $customer1->id,
    ]);

    $deal2 = Deal::factory()->create([
        'customer_id' => $customer2->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal1->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal2->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'keyword' => '株式会社A',
        ]));

    $response
        ->assertOk()
        ->assertSee('株式会社A')
        ->assertDontSee('株式会社B');
});

it('担当営業名で検索できる', function () {
    $otherSales = User::factory()->create([
        'role' => 'sales',
        'name' => '鈴木花子',
    ]);

    $customer1 = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $customer2 = Customer::factory()->create([
        'user_id' => $otherSales->id,
    ]);

    $deal1 = Deal::factory()->create([
        'customer_id' => $customer1->id,
    ]);

    $deal2 = Deal::factory()->create([
        'customer_id' => $customer2->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal1->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal2->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'keyword' => $this->admin->name,
        ]));

    $response
        ->assertOk()
        ->assertSee($this->admin->name)
        ->assertDontSee($otherSales->name);
});

it('存在しないキーワードで検索するとメッセージが表示される', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'keyword' => '存在しないキーワード',
        ]));

    $response
        ->assertOk()
        ->assertSee('タスクが登録されていません。');
});

it('ID昇順で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->count(3)->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'sort' => 'id',
            'direction' => 'asc',
        ]));

    $response
        ->assertOk()
        ->assertViewHas('tasks', fn($tasks) => $tasks->first()->id < $tasks->last()->id);
});

it('ID降順で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->count(3)->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'sort' => 'id',
            'direction' => 'desc',
        ]));

    $response
        ->assertOk()
        ->assertViewHas('tasks', fn($tasks) => $tasks->first()->id > $tasks->last()->id);
});

it('タスク名で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'title' => 'Bタスク',
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'title' => 'Aタスク',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'sort' => 'title',
            'direction' => 'asc',
        ]));

    $response
        ->assertOk()
        ->assertViewHas('tasks', fn($tasks) => $tasks->first()->title === 'Aタスク');
});

it('期限日で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'due_date' => now()->addDays(10),
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'due_date' => now()->addDay(),
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'sort' => 'due_date',
            'direction' => 'asc',
        ]));

    $response
        ->assertOk()
        ->assertViewHas('tasks', fn($tasks) => $tasks->first()->due_date->lt($tasks->last()->due_date));
});

it('登録日で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'created_at' => now()->subDays(5),
    ]);

    Task::factory()->create([
        'deal_id' => $deal->id,
        'created_at' => now(),
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index', [
            'sort' => 'created_at',
            'direction' => 'asc',
        ]));

    $response
        ->assertOk()
        ->assertViewHas('tasks', fn($tasks) => $tasks->first()->created_at->lt($tasks->last()->created_at));
});

it('11件以上でページネーションが表示される', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Task::factory()->count(11)->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.index'));

    $response
        ->assertOk()
        ->assertViewHas('tasks', fn($tasks) => $tasks->total() === 11)
        ->assertSee('?page=2', false);
});
