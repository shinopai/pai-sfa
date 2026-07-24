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

it('ログイン済みユーザーはタスクを削除できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->delete(route('tasks.destroy', $this->task));

    $response
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('tasks', [
        'id' => $this->task->id,
    ]);
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->delete(route('tasks.destroy', $this->task));

    $response->assertRedirect(route('login'));
});
