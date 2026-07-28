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

it('会社名で検索できる', function () {
    Customer::factory()->create([
        'company_name' => '株式会社AAA',
        'user_id' => $this->admin->id,
    ]);

    Customer::factory()->create([
        'company_name' => '株式会社BBB',
        'user_id' => $this->admin->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index', [
            'keyword' => 'AAA',
        ]));

    $response
        ->assertOk()
        ->assertSee('株式会社AAA')
        ->assertDontSee('株式会社BBB');
});

it('担当者名で検索できる', function () {
    Customer::factory()->create([
        'contact_name' => '田中太郎',
        'user_id' => $this->admin->id,
    ]);

    Customer::factory()->create([
        'contact_name' => '佐藤花子',
        'user_id' => $this->admin->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index', [
            'keyword' => '田中',
        ]));

    $response
        ->assertOk()
        ->assertSee('田中太郎')
        ->assertDontSee('佐藤花子');
});

it('担当営業名で検索できる', function () {
    $sales = User::factory()->create([
        'name' => '営業太郎',
        'role' => 'sales',
    ]);

    $otherSales = User::factory()->create([
        'name' => '営業花子',
        'role' => 'sales',
    ]);

    Customer::factory()->create([
        'company_name' => 'AAA',
        'user_id' => $sales->id,
    ]);

    Customer::factory()->create([
        'company_name' => 'BBB',
        'user_id' => $otherSales->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index', [
            'keyword' => '営業太郎',
        ]));

    $response
        ->assertOk()
        ->assertSee('AAA')
        ->assertDontSee('BBB');
});

it('存在しないキーワードの場合は空メッセージを表示する', function () {
    Customer::factory()->create();

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index', [
            'keyword' => '存在しない会社',
        ]));

    $response
        ->assertOk()
        ->assertSee('顧客が登録されていません。');
});

it('ID昇順で並び替えできる', function () {
    $first = Customer::factory()->create([
        'company_name' => 'AAA',
    ]);

    $second = Customer::factory()->create([
        'company_name' => 'BBB',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index', [
            'sort' => 'id',
            'direction' => 'asc',
        ]));

    $response->assertSeeInOrder([
        $first->company_name,
        $second->company_name,
    ]);
});

it('ID降順で並び替えできる', function () {
    $first = Customer::factory()->create([
        'company_name' => 'AAA',
    ]);

    $second = Customer::factory()->create([
        'company_name' => 'BBB',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index', [
            'sort' => 'id',
            'direction' => 'desc',
        ]));

    $response->assertSeeInOrder([
        $second->company_name,
        $first->company_name,
    ]);
});

it('会社名で並び替えできる', function () {
    Customer::factory()->create([
        'company_name' => 'B株式会社',
    ]);

    Customer::factory()->create([
        'company_name' => 'A株式会社',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index', [
            'sort' => 'company_name',
            'direction' => 'asc',
        ]));

    $response->assertSeeInOrder([
        'A株式会社',
        'B株式会社',
    ]);
});

it('登録日で並び替えできる', function () {
    Customer::factory()->create([
        'company_name' => '古い顧客',
        'created_at' => now()->subDay(),
    ]);

    Customer::factory()->create([
        'company_name' => '新しい顧客',
        'created_at' => now(),
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index', [
            'sort' => 'created_at',
            'direction' => 'desc',
        ]));

    $response->assertSeeInOrder([
        '新しい顧客',
        '古い顧客',
    ]);
});

it('11件以上の場合はページネーションされる', function () {
    Customer::factory()->count(11)->create();

    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.index'));

    $response
        ->assertOk()
        ->assertSee('?page=2');
});
