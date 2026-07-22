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
});

it('管理者は商談登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.create'));

    $response
        ->assertOk()
        ->assertSee('商談登録');
});

it('営業担当は商談登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('deals.create'));

    $response
        ->assertOk()
        ->assertSee('商談登録');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('deals.create'));

    $response->assertRedirect(route('login'));
});

it('管理者は商談を登録できる', function () {
    $customer = Customer::factory()->create();

    $response = $this
        ->actingAs($this->admin)
        ->post(route('deals.store'), [
            'customer_id' => $customer->id,
            'user_id' => $this->sales->id,
            'title' => 'ホームページリニューアル',
            'amount' => 500000,
            'status' => DealStatus::NEW->value,
            'expected_contract_date' => '2026-08-31',
            'memo' => 'テストメモ',
        ]);

    $response
        ->assertRedirect(route('deals.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('deals', [
        'customer_id' => $customer->id,
        'user_id' => $this->sales->id,
        'title' => 'ホームページリニューアル',
    ]);
});

it('営業担当は商談を登録すると担当営業がログインユーザーとなる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->sales->id,
    ]);

    $response = $this
        ->actingAs($this->sales)
        ->post(route('deals.store'), [
            'customer_id' => $customer->id,
            'user_id' => $this->sales->id,
            'title' => 'ホームページリニューアル',
            'amount' => 500000,
            'status' => DealStatus::NEW->value,
        ]);

    $response
        ->assertRedirect(route('deals.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('deals', [
        'user_id' => $this->sales->id,
        'title' => 'ホームページリニューアル',
    ]);
});

it('営業担当がuser_idを改ざんして登録した場合は担当営業がログインユーザーとなる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->sales->id,
    ]);

    $response = $this
        ->actingAs($this->sales)
        ->post(route('deals.store'), [
            'customer_id' => $customer->id,
            'user_id' => $this->admin->id,
            'title' => 'ホームページリニューアル',
            'amount' => 500000,
            'status' => DealStatus::NEW->value,
        ]);

    $response
        ->assertRedirect(route('deals.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('deals', [
        'user_id' => $this->sales->id,
        'title' => 'ホームページリニューアル',
    ]);

    $this->assertDatabaseMissing('deals', [
        'user_id' => $this->admin->id,
        'title' => 'ホームページリニューアル',
    ]);
});

it('顧客未選択の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('deals.create'))
        ->post(route('deals.store'), [
            'customer_id' => '',
            'user_id' => $this->sales->id,
            'title' => 'ホームページリニューアル',
            'status' => DealStatus::NEW->value,
        ]);

    $response
        ->assertRedirect(route('deals.create'))
        ->assertSessionHasErrors(['customer_id']);
});

it('商談名未入力の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $response = $this
        ->actingAs($this->admin)
        ->from(route('deals.create'))
        ->post(route('deals.store'), [
            'customer_id' => $customer->id,
            'user_id' => $this->sales->id,
            'title' => '',
            'status' => DealStatus::NEW->value,
        ]);

    $response
        ->assertRedirect(route('deals.create'))
        ->assertSessionHasErrors(['title']);
});

it('商談名が121文字以上の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $response = $this
        ->actingAs($this->admin)
        ->from(route('deals.create'))
        ->post(route('deals.store'), [
            'customer_id' => $customer->id,
            'user_id' => $this->sales->id,
            'title' => str_repeat('あ', 121),
            'status' => DealStatus::NEW->value,
        ]);

    $response
        ->assertRedirect(route('deals.create'))
        ->assertSessionHasErrors(['title']);
});

it('商談金額が0未満の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $response = $this
        ->actingAs($this->admin)
        ->from(route('deals.create'))
        ->post(route('deals.store'), [
            'customer_id' => $customer->id,
            'user_id' => $this->sales->id,
            'title' => 'ホームページリニューアル',
            'amount' => -1,
            'status' => DealStatus::NEW->value,
        ]);

    $response
        ->assertRedirect(route('deals.create'))
        ->assertSessionHasErrors(['amount']);
});

it('不正なステータスの場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $response = $this
        ->actingAs($this->admin)
        ->from(route('deals.create'))
        ->post(route('deals.store'), [
            'customer_id' => $customer->id,
            'user_id' => $this->sales->id,
            'title' => 'ホームページリニューアル',
            'status' => 'invalid',
        ]);

    $response
        ->assertRedirect(route('deals.create'))
        ->assertSessionHasErrors(['status']);
});
