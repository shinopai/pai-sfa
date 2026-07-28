<?php

use App\Models\Activity;
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

it('管理者は営業活動一覧画面を表示できる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Activity::factory()->count(3)->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('activities.index'));

    $response
        ->assertOk()
        ->assertSee('営業活動管理');
});

it('営業担当は自分が担当する商談の営業活動のみ表示される', function () {
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

    Activity::factory()->create([
        'deal_id' => $myDeal->id,
    ]);

    Activity::factory()->create([
        'deal_id' => $otherDeal->id,
    ]);

    $response = $this
        ->actingAs($this->sales)
        ->get(route('activities.index'));

    $response
        ->assertOk()
        ->assertSee('自分の商談')
        ->assertDontSee('他人の商談');
});

it('商談名で検索できる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $hitDeal = Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'システム導入',
    ]);

    $missDeal = Deal::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'ホームページ制作',
    ]);

    Activity::factory()->create(['deal_id' => $hitDeal->id]);
    Activity::factory()->create(['deal_id' => $missDeal->id]);

    $this->actingAs($this->admin)
        ->get(route('activities.index', ['keyword' => 'システム']))
        ->assertSee('システム導入')
        ->assertDontSee('ホームページ制作');
});

it('顧客名で検索できる', function () {
    $customer1 = Customer::factory()->create([
        'user_id' => $this->admin->id,
        'company_name' => '株式会社AAA',
    ]);

    $customer2 = Customer::factory()->create([
        'user_id' => $this->admin->id,
        'company_name' => '株式会社BBB',
    ]);

    $deal1 = Deal::factory()->create(['customer_id' => $customer1->id]);
    $deal2 = Deal::factory()->create(['customer_id' => $customer2->id]);

    Activity::factory()->create(['deal_id' => $deal1->id]);
    Activity::factory()->create(['deal_id' => $deal2->id]);

    $this->actingAs($this->admin)
        ->get(route('activities.index', ['keyword' => 'AAA']))
        ->assertSee('株式会社AAA')
        ->assertDontSee('株式会社BBB');
});

it('担当営業名で検索できる', function () {
    $otherSales = User::factory()->create([
        'role' => 'sales',
        'name' => '佐藤',
    ]);

    $customer1 = Customer::factory()->create(['user_id' => $this->admin->id]);
    $customer2 = Customer::factory()->create(['user_id' => $otherSales->id]);

    $deal1 = Deal::factory()->create(['customer_id' => $customer1->id]);
    $deal2 = Deal::factory()->create(['customer_id' => $customer2->id]);

    Activity::factory()->create(['deal_id' => $deal1->id]);
    Activity::factory()->create(['deal_id' => $deal2->id]);

    $this->actingAs($this->admin)
        ->get(route('activities.index', ['keyword' => '田中']))
        ->assertSee('田中')
        ->assertDontSee('佐藤');
});

it('存在しないキーワードでは0件となる', function () {
    $this->actingAs($this->admin)
        ->get(route('activities.index', [
            'keyword' => '存在しない文字列',
        ]))
        ->assertSee('営業活動が登録されていません。');
});

it('ID昇順で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Activity::factory()->count(3)->create([
        'deal_id' => $deal->id
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('activities.index', [
            'sort' => 'id',
            'direction' => 'asc',
        ]));

    $response->assertOk();
});

it('ID降順で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Activity::factory()->count(3)->create([
        'deal_id' => $deal->id
    ]);


    $response = $this->actingAs($this->admin)
        ->get(route('activities.index', [
            'sort' => 'id',
            'direction' => 'desc',
        ]));

    $response->assertOk();
});

it('活動日時で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Activity::factory()->create([
        'deal_id' => $deal->id,
        'activity_date' => now()->addDay(),
    ]);

    Activity::factory()->create([
        'deal_id' => $deal->id,
        'activity_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('activities.index', [
            'sort' => 'activity_date',
            'direction' => 'asc',
        ]));

    $response->assertOk();

    $response = $this->actingAs($this->admin)
        ->get(route('activities.index', [
            'sort' => 'activity_date',
            'direction' => 'desc',
        ]));

    $response->assertOk();
});

it('登録日で並び替えできる', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Activity::factory()->count(3)->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('activities.index', [
            'sort' => 'created_at',
            'direction' => 'asc',
        ]));

    $response->assertOk();

    $response = $this->actingAs($this->admin)
        ->get(route('activities.index', [
            'sort' => 'created_at',
            'direction' => 'desc',
        ]));

    $response->assertOk();
});

it('11件以上でページネーションが表示される', function () {
    $customer = Customer::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    Activity::factory()->count(11)->create([
        'deal_id' => $deal->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->get(route('activities.index'));

    $response
        ->assertOk()
        ->assertSee('?page=2', false);
});
