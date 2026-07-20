<?php

use App\Models\Customer;
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

it('管理者は顧客一覧画面を表示できる', function () {
    Customer::factory()->count(3)->create();

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index'));

    $response
        ->assertOk()
        ->assertSee('顧客管理');
});

it('営業担当は自分が担当する顧客のみ表示される', function () {
    $otherSales = User::factory()->create([
        'role' => 'sales',
    ]);

    $myCustomer = Customer::factory()->create([
        'user_id' => $this->sales->id,
        'company_name' => '自分の顧客',
    ]);

    $otherCustomer = Customer::factory()->create([
        'user_id' => $otherSales->id,
        'company_name' => '他人の顧客',
    ]);

    $response = $this
        ->actingAs($this->sales)
        ->get(route('customers.index'));

    $response
        ->assertOk()
        ->assertSee($myCustomer->company_name)
        ->assertDontSee($otherCustomer->company_name);
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('customers.index'));

    $response->assertRedirect(route('login'));
});
