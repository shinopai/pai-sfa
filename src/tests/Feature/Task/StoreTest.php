<?php

use App\Enums\TaskPriority;
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

it('管理者はタスク登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.create'));

    $response
        ->assertOk()
        ->assertSee('タスク登録');
});

it('営業担当はタスク登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('tasks.create'));

    $response
        ->assertOk()
        ->assertSee('タスク登録');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('tasks.create'));

    $response->assertRedirect(route('login'));
});

it('タスクを登録できる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
        'user_id' => $this->admin->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->post(route('tasks.store'), [
            'deal_id' => $deal->id,
            'title' => 'タスクテスト',
            'description' => 'タスク詳細テスト',
            'due_date' => '2026-07-31',
            'priority' => TaskPriority::HIGH->value,
            'is_completed' => false,
        ]);

    $response
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'deal_id' => $deal->id,
        'title' => 'タスクテスト',
        'description' => 'タスク詳細テスト',
        'priority' => TaskPriority::HIGH->value,
        'is_completed' => false,
    ]);
});

it('商談未選択の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('tasks.create'))
        ->post(route('tasks.store'), [
            'deal_id' => '',
            'title' => 'タスクテスト',
            'description' => 'タスク詳細テスト',
            'due_date' => '2026-07-31',
            'priority' => TaskPriority::HIGH->value,
            'is_completed' => false,
        ]);

    $response
        ->assertRedirect(route('tasks.create'))
        ->assertSessionHasErrors(['deal_id']);
});

it('不正な優先度の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('tasks.create'))
        ->post(route('tasks.store'), [
            'deal_id' => $deal->id,
            'title' => 'タスクテスト',
            'description' => 'タスク詳細テスト',
            'due_date' => '2026-07-31',
            'priority' => 'invalid',
            'is_completed' => false,
        ]);

    $response
        ->assertRedirect(route('tasks.create'))
        ->assertSessionHasErrors(['priority']);
});

it('期限日未入力の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('tasks.create'))
        ->post(route('tasks.store'), [
            'deal_id' => $deal->id,
            'title' => 'タスクテスト',
            'description' => 'タスク詳細テスト',
            'due_date' => '',
            'priority' => TaskPriority::HIGH->value,
            'is_completed' => false,
        ]);

    $response
        ->assertRedirect(route('tasks.create'))
        ->assertSessionHasErrors(['due_date']);
});

it('タスク名未入力の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('tasks.create'))
        ->post(route('tasks.store'), [
            'deal_id' => $deal->id,
            'title' => '',
            'description' => 'タスク詳細テスト',
            'due_date' => '2026-07-31',
            'priority' => TaskPriority::HIGH->value,
            'is_completed' => false,
        ]);

    $response
        ->assertRedirect(route('tasks.create'))
        ->assertSessionHasErrors(['title']);
});

it('タスク詳細が2001文字以上の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('tasks.create'))
        ->post(route('tasks.store'), [
            'deal_id' => $deal->id,
            'title' => 'タスクテスト',
            'description' => str_repeat('あ', 2001),
            'due_date' => '2026-07-31',
            'priority' => TaskPriority::HIGH->value,
            'is_completed' => false,
        ]);

    $response
        ->assertRedirect(route('tasks.create'))
        ->assertSessionHasErrors(['description']);
});
