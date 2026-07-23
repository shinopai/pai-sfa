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

    /** @var \App\Models\Customer $this->customer */
    $this->customer = Customer::factory()->create([
        'user_id' => $this->sales->id,
    ]);

    /** @var \App\Models\Deal $this->deal */
    $this->deal = Deal::factory()->create([
        'customer_id' => $this->customer->id,
        'user_id' => $this->sales->id,
    ]);

    /** @var \App\Models\Activity $this->activity */
    $this->activity = Activity::factory()->create([
        'deal_id' => $this->deal->id,
    ]);
});

it('管理者は営業活動編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('activities.edit', $this->activity));

    $response
        ->assertOk()
        ->assertSee('営業活動編集');
});

it('営業担当は自分が担当する商談の営業活動編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('activities.edit', $this->activity));

    $response
        ->assertOk()
        ->assertSee('営業活動編集');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('activities.edit', $this->activity));

    $response->assertRedirect(route('login'));
});

it('活動種別を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('activities.update', $this->activity), [
            'deal_id' => $this->deal->id,
            'activity_type' => ActivityType::VISIT->value,
            'activity_date' => $this->activity->activity_date->format('Y-m-d H:i:s'),
            'content' => $this->activity->content,
        ]);

    $response
        ->assertRedirect(route('activities.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('activities', [
        'id' => $this->activity->id,
        'activity_type' => ActivityType::VISIT->value,
    ]);
});

it('活動日時を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('activities.update', $this->activity), [
            'deal_id' => $this->deal->id,
            'activity_type' => $this->activity->activity_type->value,
            'activity_date' => '2026-08-01 15:30:00',
            'content' => $this->activity->content,
        ]);

    $response
        ->assertRedirect(route('activities.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('activities', [
        'id' => $this->activity->id,
        'activity_date' => '2026-08-01 15:30:00',
    ]);
});

it('活動内容を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('activities.update', $this->activity), [
            'deal_id' => $this->deal->id,
            'activity_type' => $this->activity->activity_type->value,
            'activity_date' => $this->activity->activity_date->format('Y-m-d H:i:s'),
            'content' => '更新後の営業活動内容',
        ]);

    $response
        ->assertRedirect(route('activities.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('activities', [
        'id' => $this->activity->id,
        'content' => '更新後の営業活動内容',
    ]);
});
