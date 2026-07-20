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

    /** @var \App\Models\User $this->sales2 */
    $this->sales2 = User::factory()->create([
        'role' => 'sales',
    ]);

    /** @var \App\Models\Customer $this->customer */
    $this->customer = Customer::factory()->create([
        'user_id' => $this->sales->id,
    ]);
});

it('管理者は顧客編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.edit', $this->customer));

    $response
        ->assertOk()
        ->assertSee('顧客編集');
});

it('営業担当は自分の担当顧客の編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('customers.edit', $this->customer));

    $response
        ->assertOk()
        ->assertSee('顧客編集');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('customers.edit', $this->customer));

    $response->assertRedirect(route('login'));
});

it('会社名を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('customers.update', $this->customer), [
            'user_id' => $this->sales->id,
            'company_name' => '更新後株式会社',
            'contact_name' => $this->customer->contact_name,
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'address' => $this->customer->address,
            'industry' => $this->customer->industry,
            'memo' => $this->customer->memo,
        ]);

    $response
        ->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customers', [
        'id' => $this->customer->id,
        'company_name' => '更新後株式会社',
    ]);
});

it('担当者名を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('customers.update', $this->customer), [
            'user_id' => $this->sales->id,
            'company_name' => $this->customer->company_name,
            'contact_name' => '更新担当者',
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'address' => $this->customer->address,
            'industry' => $this->customer->industry,
            'memo' => $this->customer->memo,
        ]);

    $response
        ->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customers', [
        'id' => $this->customer->id,
        'contact_name' => '更新担当者',
    ]);
});

it('管理者は担当営業を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('customers.update', $this->customer), [
            'user_id' => $this->sales2->id,
            'company_name' => $this->customer->company_name,
            'contact_name' => $this->customer->contact_name,
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'address' => $this->customer->address,
            'industry' => $this->customer->industry,
            'memo' => $this->customer->memo,
        ]);

    $response
        ->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customers', [
        'id' => $this->customer->id,
        'user_id' => $this->sales2->id,
    ]);
});

it('営業担当がuser_idを改ざんして更新しても担当営業はログインユーザーのままとなる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->patch(route('customers.update', $this->customer), [
            'user_id' => $this->admin->id,
            'company_name' => $this->customer->company_name,
            'contact_name' => $this->customer->contact_name,
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'address' => $this->customer->address,
            'industry' => $this->customer->industry,
            'memo' => $this->customer->memo,
        ]);

    $response
        ->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customers', [
        'id' => $this->customer->id,
        'user_id' => $this->sales->id,
    ]);
});
