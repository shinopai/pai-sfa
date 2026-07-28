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
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

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
        'title' => '自分の商談',
    ]);

    $otherDeal = Deal::factory()->create([
        'customer_id' => $otherCustomer->id,
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

it('商談名で検索できる', function () {
    $customer = Customer::factory()->create();

    $target = Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'ホームページ制作',
    ]);

    $other = Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => '営業支援システム',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'keyword' => 'ホームページ',
        ]));

    $response
        ->assertOk()
        ->assertSee($target->title)
        ->assertDontSee($other->title);
});

it('顧客名で検索できる', function () {
    $targetCustomer = Customer::factory()->create([
        'company_name' => '株式会社パイ',
    ]);

    $otherCustomer = Customer::factory()->create([
        'company_name' => '株式会社テスト',
    ]);

    $targetDeal = Deal::factory()->create([
        'customer_id' => $targetCustomer->id,
    ]);

    $otherDeal = Deal::factory()->create([
        'customer_id' => $otherCustomer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'keyword' => 'パイ',
        ]));

    $response
        ->assertOk()
        ->assertSee($targetDeal->title)
        ->assertDontSee($otherDeal->title);
});

it('担当営業名で検索できる', function () {
    $sales = User::factory()->create([
        'name' => '田中太郎',
        'role' => 'sales',
    ]);

    $otherSales = User::factory()->create([
        'name' => '佐藤花子',
        'role' => 'sales',
    ]);

    $customer1 = Customer::factory()->create([
        'user_id' => $sales->id,
    ]);

    $customer2 = Customer::factory()->create([
        'user_id' => $otherSales->id,
    ]);

    $deal1 = Deal::factory()->create([
        'customer_id' => $customer1->id,
    ]);

    $deal2 = Deal::factory()->create([
        'customer_id' => $customer2->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'keyword' => '田中',
        ]));

    $response
        ->assertOk()
        ->assertSee($deal1->title)
        ->assertDontSee($deal2->title);
});

it('存在しないキーワードで検索すると一覧が空になる', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'keyword' => '存在しない',
        ]));

    $response
        ->assertOk()
        ->assertSee('商談が登録されていません。');
});

it('ID昇順で並び替えできる', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->count(3)->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'sort' => 'id',
            'direction' => 'asc',
        ]));

    $response->assertOk();
});

it('ID降順で並び替えできる', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->count(3)->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'sort' => 'id',
            'direction' => 'desc',
        ]));

    $response->assertOk();
});

it('商談名で並び替えできる', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'BBB',
    ]);

    Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'AAA',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'sort' => 'title',
            'direction' => 'asc',
        ]));

    $response->assertOk();
});

it('契約予定日で並び替えできる', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->create([
        'customer_id' => $customer->id,
        'expected_contract_date' => now()->addDays(10),
    ]);

    Deal::factory()->create([
        'customer_id' => $customer->id,
        'expected_contract_date' => now()->addDays(1),
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'sort' => 'expected_contract_date',
            'direction' => 'asc',
        ]));

    $response->assertOk();
});

it('登録日で並び替えできる', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->count(3)->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index', [
            'sort' => 'created_at',
            'direction' => 'desc',
        ]));

    $response->assertOk();
});

it('11件以上ある場合はページネーションが表示される', function () {
    $customer = Customer::factory()->create();

    Deal::factory()->count(11)->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('deals.index'));

    $response
        ->assertOk()
        ->assertSee('?page=2');
});
