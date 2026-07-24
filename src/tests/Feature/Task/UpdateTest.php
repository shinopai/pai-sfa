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

    /** @var \App\Models\Customer $this->customer */
    $this->customer = Customer::factory()->create([
        'user_id' => $this->sales->id,
    ]);

    /** @var \App\Models\Deal $this->deal */
    $this->deal = Deal::factory()->create([
        'customer_id' => $this->customer->id,
        'user_id' => $this->sales->id,
    ]);

    /** @var \App\Models\Task $this->task */
    $this->task = Task::factory()->create([
        'deal_id' => $this->deal->id,
    ]);
});

it('管理者はタスク編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('tasks.edit', $this->task));

    $response
        ->assertOk()
        ->assertSee('タスク編集');
});

it('営業担当は自分が担当する商談のタスク編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('tasks.edit', $this->task));

    $response
        ->assertOk()
        ->assertSee('タスク編集');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('tasks.edit', $this->task));

    $response->assertRedirect(route('login'));
});

it('タスク名を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('tasks.update', $this->task), [
            'deal_id' => $this->deal->id,
            'title' => '更新後のタスク名',
            'description' => $this->task->description,
            'due_date' => $this->task->due_date->format('Y-m-d'),
            'priority' => $this->task->priority->value,
            'is_completed' => $this->task->is_completed,
        ]);

    $response
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => '更新後のタスク名',
    ]);
});

it('期限日を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('tasks.update', $this->task), [
            'deal_id' => $this->deal->id,
            'title' => $this->task->title,
            'description' => $this->task->description,
            'due_date' => '2026-08-01',
            'priority' => $this->task->priority->value,
            'is_completed' => $this->task->is_completed,
        ]);

    $response
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'due_date' => '2026-08-01 00:00:00',
    ]);
});

it('優先度を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('tasks.update', $this->task), [
            'deal_id' => $this->deal->id,
            'title' => $this->task->title,
            'description' => $this->task->description,
            'due_date' => $this->task->due_date->format('Y-m-d'),
            'priority' => TaskPriority::LOW->value,
            'is_completed' => $this->task->is_completed,
        ]);

    $response
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'priority' => TaskPriority::LOW->value,
    ]);
});
