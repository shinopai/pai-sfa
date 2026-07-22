<?php

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
});

it('管理者は商談一覧画面を表示できる', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->count(3)->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index'));

    $response
        ->assertOk()
        ->assertSee('商談管理');
});

it('営業担当は自分が担当する商談のみ表示される', function () {
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

    $response = $this
        ->actingAs($this->sales)
        ->get(route('deals.index'));

    $response
        ->assertOk()
        ->assertSee($myDeal->title)
        ->assertDontSee($otherDeal->title);
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('deals.index'));

    $response->assertRedirect(route('login'));
});