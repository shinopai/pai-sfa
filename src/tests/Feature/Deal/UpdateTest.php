<?php

use App\Enums\DealStatus;
use App\Models\Customer;
use App\Models\Deal;
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

    /** @var \App\Models\User $this->sales2 */
    $this->sales2 = User::factory()->create([
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
});

it('管理者は商談編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.edit', $this->deal));

    $response
        ->assertOk()
        ->assertSee('商談編集');
});

it('営業担当は自分の担当商談の編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('deals.edit', $this->deal));

    $response
        ->assertOk()
        ->assertSee('商談編集');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('deals.edit', $this->deal));

    $response->assertRedirect(route('login'));
});

it('商談名を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('deals.update', $this->deal), [
            'customer_id' => $this->customer->id,
            'user_id' => $this->sales->id,
            'title' => '更新後商談',
            'amount' => $this->deal->amount,
            'status' => $this->deal->status->value,
            'expected_contract_date' => optional($this->deal->expected_contract_date)->format('Y-m-d'),
            'memo' => $this->deal->memo,
        ]);

    $response
        ->assertRedirect(route('deals.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('deals', [
        'id' => $this->deal->id,
        'title' => '更新後商談',
    ]);
});

it('商談金額を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('deals.update', $this->deal), [
            'customer_id' => $this->customer->id,
            'user_id' => $this->sales->id,
            'title' => $this->deal->title,
            'amount' => 999999,
            'status' => $this->deal->status->value,
            'expected_contract_date' => optional($this->deal->expected_contract_date)->format('Y-m-d'),
            'memo' => $this->deal->memo,
        ]);

    $response
        ->assertRedirect(route('deals.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('deals', [
        'id' => $this->deal->id,
        'amount' => 999999,
    ]);
});

it('管理者は担当営業を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('deals.update', $this->deal), [
            'customer_id' => $this->customer->id,
            'user_id' => $this->sales2->id,
            'title' => $this->deal->title,
            'amount' => $this->deal->amount,
            'status' => $this->deal->status->value,
            'expected_contract_date' => optional($this->deal->expected_contract_date)->format('Y-m-d'),
            'memo' => $this->deal->memo,
        ]);

    $response
        ->assertRedirect(route('deals.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('deals', [
        'id' => $this->deal->id,
        'user_id' => $this->sales2->id,
    ]);
});

it('営業担当がuser_idを改ざんして更新しても担当営業はログインユーザーのままとなる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->patch(route('deals.update', $this->deal), [
            'customer_id' => $this->customer->id,
            'user_id' => $this->admin->id,
            'title' => $this->deal->title,
            'amount' => $this->deal->amount,
            'status' => $this->deal->status->value,
            'expected_contract_date' => optional($this->deal->expected_contract_date)->format('Y-m-d'),
            'memo' => $this->deal->memo,
        ]);

    $response
        ->assertRedirect(route('deals.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('deals', [
        'id' => $this->deal->id,
        'user_id' => $this->sales->id,
    ]);
});
