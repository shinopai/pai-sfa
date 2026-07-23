<?php

use App\Enums\ActivityType;
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

it('管理者は営業活動登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('activities.create'));

    $response
        ->assertOk()
        ->assertSee('営業活動登録');
});

it('営業担当は営業活動登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('activities.create'));

    $response
        ->assertOk()
        ->assertSee('営業活動登録');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('activities.create'));

    $response->assertRedirect(route('login'));
});

it('営業活動を登録できる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
        'user_id' => $this->admin->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->post(route('activities.store'), [
            'deal_id' => $deal->id,
            'activity_type' => ActivityType::PHONE->value,
            'activity_date' => '2026-07-23 10:00:00',
            'content' => '営業活動テスト',
        ]);

    $response
        ->assertRedirect(route('activities.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('activities', [
        'deal_id' => $deal->id,
        'activity_type' => ActivityType::PHONE->value,
        'content' => '営業活動テスト',
    ]);
});

it('商談未選択の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('activities.create'))
        ->post(route('activities.store'), [
            'deal_id' => '',
            'activity_type' => ActivityType::PHONE->value,
            'activity_date' => '2026-07-23 10:00:00',
            'content' => '営業活動テスト',
        ]);

    $response
        ->assertRedirect(route('activities.create'))
        ->assertSessionHasErrors(['deal_id']);
});

it('不正な活動種別の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('activities.create'))
        ->post(route('activities.store'), [
            'deal_id' => $deal->id,
            'activity_type' => 'invalid',
            'activity_date' => '2026-07-23 10:00:00',
            'content' => '営業活動テスト',
        ]);

    $response
        ->assertRedirect(route('activities.create'))
        ->assertSessionHasErrors(['activity_type']);
});

it('活動日時未入力の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('activities.create'))
        ->post(route('activities.store'), [
            'deal_id' => $deal->id,
            'activity_type' => ActivityType::PHONE->value,
            'activity_date' => '',
            'content' => '営業活動テスト',
        ]);

    $response
        ->assertRedirect(route('activities.create'))
        ->assertSessionHasErrors(['activity_date']);
});

it('活動内容未入力の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('activities.create'))
        ->post(route('activities.store'), [
            'deal_id' => $deal->id,
            'activity_type' => ActivityType::PHONE->value,
            'activity_date' => '2026-07-23 10:00:00',
            'content' => '',
        ]);

    $response
        ->assertRedirect(route('activities.create'))
        ->assertSessionHasErrors(['content']);
});

it('活動内容が2001文字以上の場合はバリデーションエラーとなる', function () {
    $customer = Customer::factory()->create();

    $deal = Deal::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('activities.create'))
        ->post(route('activities.store'), [
            'deal_id' => $deal->id,
            'activity_type' => ActivityType::PHONE->value,
            'activity_date' => '2026-07-23 10:00:00',
            'content' => str_repeat('あ', 2001),
        ]);

    $response
        ->assertRedirect(route('activities.create'))
        ->assertSessionHasErrors(['content']);
});
