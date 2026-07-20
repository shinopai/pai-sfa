<?php

use App\Models\Customer;
use App\Models\User;

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
});

it('ログイン済みユーザーは顧客を削除できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->delete(route('customers.destroy', $this->customer));

    $response
        ->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('customers', [
        'id' => $this->customer->id,
    ]);
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->delete(route('customers.destroy', $this->customer));

    $response->assertRedirect(route('login'));
});
