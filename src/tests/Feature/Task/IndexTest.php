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
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
        'user_id' => $this->admin->id,
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
        'user_id' => $this->sales->id,
        'title' => '自分の商談',
    ]);

    $otherDeal = Deal::factory()->create([
        'customer_id' => $otherCustomer->id,
        'user_id' => $otherSales->id,
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
